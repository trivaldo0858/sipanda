from pathlib import Path
import json

import pandas as pd


# ==================================================
# 1. PENGATURAN LOKASI FILE
# ==================================================

ROOT = Path(__file__).resolve().parents[2]

INPUT_FILE = ROOT / "ml/data/simulation/dataset_simulasi.csv"
OUTPUT_FILE = ROOT / "ml/data/simulation/dataset_simulasi_processed.csv"
REPORT_FILE = ROOT / "ml/reports/simulation_quality_report.json"


# ==================================================
# 2. KOLOM YANG DIPERLUKAN
# ==================================================

REQUIRED_COLUMNS = [
    "group_id",
    "umur_hari_awal",
    "jenis_kelamin",
    "berat_badan_awal_kg",
    "tinggi_badan_awal_cm",
    "haz_awal",
    "waz_awal",
    "whz_awal",
    "baz_awal",
    "jarak_pemeriksaan_sebelumnya_hari",
    "perubahan_berat_badan_kg",
    "perubahan_tinggi_badan_cm",
    "perubahan_haz",
    "jarak_hari_target",
    "haz_target",
    "label_risiko",
]

NUMERIC_COLUMNS = [
    "umur_hari_awal",
    "berat_badan_awal_kg",
    "tinggi_badan_awal_cm",
    "haz_awal",
    "waz_awal",
    "whz_awal",
    "baz_awal",
    "jarak_pemeriksaan_sebelumnya_hari",
    "perubahan_berat_badan_kg",
    "perubahan_tinggi_badan_cm",
    "perubahan_haz",
    "jarak_hari_target",
    "haz_target",
]

REQUIRED_VALUES = [
    "group_id",
    "umur_hari_awal",
    "jenis_kelamin",
    "berat_badan_awal_kg",
    "tinggi_badan_awal_cm",
    "haz_awal",
    "waz_awal",
    "whz_awal",
    "baz_awal",
    "jarak_hari_target",
    "haz_target",
    "label_risiko",
]


# ==================================================
# 3. MENENTUKAN LABEL DARI HAZ TARGET
# ==================================================

def tentukan_label(haz):

    if haz >= -2:
        return "rendah"

    elif haz >= -3:
        return "sedang"

    return "tinggi"


# ==================================================
# 4. MEMBANGUN DATASET
# ==================================================

def build_dataset():

    print("=" * 55)
    print("POSYANDU-IN | SIMULATION DATASET BUILDER")
    print("=" * 55)

    if not INPUT_FILE.exists():
        raise FileNotFoundError(
            f"File simulasi tidak ditemukan: {INPUT_FILE}"
        )

    df = pd.read_csv(INPUT_FILE)

    if df.empty:
        raise ValueError("Dataset simulasi kosong.")

    # Memastikan seluruh kolom tersedia.
    kolom_hilang = [
        kolom
        for kolom in REQUIRED_COLUMNS
        if kolom not in df.columns
    ]

    if kolom_hilang:
        raise ValueError(
            f"Kolom tidak ditemukan: {kolom_hilang}"
        )

    # Mengambil kolom sesuai struktur dataset.
    df = df[REQUIRED_COLUMNS].copy()

    # Mengubah kolom numerik menjadi tipe angka.
    for kolom in NUMERIC_COLUMNS:
        df[kolom] = pd.to_numeric(
            df[kolom],
            errors="coerce"
        )

    # Memastikan kolom penting tidak kosong.
    if df[REQUIRED_VALUES].isnull().any().any():
        raise ValueError(
            "Terdapat nilai kosong pada kolom wajib."
        )

    # Memastikan satu anak simulasi memiliki satu ID unik.
    if df["group_id"].duplicated().any():
        raise ValueError("Terdapat group_id yang duplikat.")

    # Memeriksa jenis kelamin.
    if not df["jenis_kelamin"].isin(["L", "P"]).all():
        raise ValueError("Jenis kelamin tidak valid.")

    # Memeriksa label.
    label_valid = {"rendah", "sedang", "tinggi"}

    if not set(df["label_risiko"]).issubset(label_valid):
        raise ValueError("Ditemukan label yang tidak valid.")

    # Memastikan label sesuai dengan HAZ target.
    label_hasil = df["haz_target"].apply(
        tentukan_label
    )

    jumlah_tidak_cocok = int(
        (label_hasil != df["label_risiko"]).sum()
    )

    if jumlah_tidak_cocok > 0:
        raise ValueError(
            f"Ada {jumlah_tidak_cocok} label yang tidak sesuai HAZ."
        )

    # Memeriksa batas struktural sesuai generator.
    if not df["umur_hari_awal"].between(180, 1721).all():
        raise ValueError("Ditemukan umur di luar batas generator.")

    if not df["jarak_hari_target"].between(75, 105).all():
        raise ValueError("Jarak target di luar rentang generator.")

    if not df["berat_badan_awal_kg"].between(2.5, 30).all():
        raise ValueError("Berat badan di luar batas generator.")

    if not df["tinggi_badan_awal_cm"].between(45, 120).all():
        raise ValueError("Tinggi badan di luar batas generator.")

    # ==================================================
    # 5. MENYIMPAN DATASET HASIL PEMROSESAN
    # ==================================================

    OUTPUT_FILE.parent.mkdir(
        parents=True,
        exist_ok=True
    )

    df.to_csv(
        OUTPUT_FILE,
        index=False,
        encoding="utf-8-sig"
    )

    # ==================================================
    # 6. MEMBUAT LAPORAN KUALITAS DATA
    # ==================================================

    laporan = {
        "jenis_dataset": "data_simulasi",
        "jumlah_sampel": int(len(df)),
        "jumlah_anak_simulasi": int(df["group_id"].nunique()),
        "distribusi_label": {
            str(kunci): int(nilai)
            for kunci, nilai in
            df["label_risiko"].value_counts().items()
        },
        "jumlah_label_tidak_cocok": jumlah_tidak_cocok,
        "nilai_kosong_per_kolom": {
            str(kunci): int(nilai)
            for kunci, nilai in
            df.isnull().sum().items()
        },
        "rentang_nilai": {}
    }

    for kolom in NUMERIC_COLUMNS:
        nilai = df[kolom].dropna()

        if not nilai.empty:
            laporan["rentang_nilai"][kolom] = {
                "minimum": round(float(nilai.min()), 4),
                "maksimum": round(float(nilai.max()), 4)
            }

    REPORT_FILE.parent.mkdir(
        parents=True,
        exist_ok=True
    )

    with open(REPORT_FILE, "w", encoding="utf-8") as file:
        json.dump(
            laporan,
            file,
            indent=4,
            ensure_ascii=False
        )

    # ==================================================
    # 7. MENAMPILKAN HASIL
    # ==================================================

    print("\nDataset simulasi berhasil diproses.")

    print(f"Jumlah sampel: {len(df)}")
    print(f"Jumlah anak: {df['group_id'].nunique()}")

    print("\nDistribusi label:")
    print(df["label_risiko"].value_counts())

    print("\nJumlah label tidak cocok:", jumlah_tidak_cocok)

    print("\nFile hasil:")
    print(OUTPUT_FILE)

    print("\nLaporan kualitas:")
    print(REPORT_FILE)

    print("=" * 55)


if __name__ == "__main__":
    try:
        build_dataset()

    except Exception as error:
        print("\nGAGAL MEMPROSES DATASET")
        print(f"Penyebab: {error}")