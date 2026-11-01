
from pathlib import Path

import numpy as np
import pandas as pd


# ==================================================
# 1. KONFIGURASI
# ==================================================

ROOT_DIR = Path(__file__).resolve().parents[2]

OUTPUT_DIR = ROOT_DIR / "ml/data/simulation"

OUTPUT_FILE = OUTPUT_DIR / "dataset_simulasi.csv"

JUMLAH_PER_KELAS = 30

# Seed menjaga hasil acak tetap konsisten.
RANDOM_SEED = 42

rng = np.random.default_rng(RANDOM_SEED)


# ==================================================
# 2. PEMBENTUKAN LABEL
# ==================================================

def tentukan_label(haz_target: float) -> str:
    """
    Menentukan label internal berdasarkan HAZ target.

    Label ini hanya mengikuti rancangan pengelompokan
    internal Posyandu-In untuk pengujian pipeline.
    """

    if haz_target >= -2:
        return "rendah"

    if haz_target >= -3:
        return "sedang"

    return "tinggi"


# ==================================================
# 3. MEMBUAT SATU SAMPEL SIMULASI
# ==================================================

def buat_sampel(
    nomor: int,
    kelompok: str
) -> dict:

    # Umur awal dibatasi agar umur target
    # tetap berada dalam rentang 0-1826 hari.
    umur_awal = int(rng.integers(180, 1722))

    # Jarak pemeriksaan target sekitar tiga bulan.
    jarak_target = int(rng.integers(75, 106))

    jenis_kelamin = rng.choice(["L", "P"])

    # ID fiktif, tidak menggunakan NIK.
    group_id = f"SIM-{nomor:04d}"

    # ----------------------------------------------
    # Membuat HAZ target sesuai kelas simulasi
    # ----------------------------------------------

    if kelompok == "rendah":
        haz_target = rng.uniform(-1.95, 1.50)

        waz = rng.normal(0.0, 0.7)
        whz = rng.normal(0.0, 0.7)
        baz = rng.normal(0.0, 0.7)

    elif kelompok == "sedang":
        haz_target = rng.uniform(-2.95, -2.05)

        waz = rng.normal(-0.8, 0.8)
        whz = rng.normal(-0.7, 0.8)
        baz = rng.normal(-0.7, 0.8)

    else:
        haz_target = rng.uniform(-4.50, -3.05)

        waz = rng.normal(-1.5, 0.9)
        whz = rng.normal(-1.4, 0.9)
        baz = rng.normal(-1.4, 0.9)

    # Memastikan label benar-benar ditentukan dari HAZ target.
    label_risiko = tentukan_label(haz_target)

    # ----------------------------------------------
    # Membuat fitur pemeriksaan awal
    # ----------------------------------------------

    haz_awal = np.clip(
        haz_target + rng.normal(0.0, 0.30),
        -5.5,
        5.5
    )

        # Umur diubah menjadi perkiraan bulan.
    umur_bulan = umur_awal / 30.4375

    # ----------------------------------------------
    # Perkiraan median tinggi berdasarkan umur
    # ----------------------------------------------

    median_tinggi = (
        50
        + 2.0 * min(umur_bulan, 12)
        + 0.8 * max(umur_bulan - 12, 0)
    )

    # Gunakan HAZ awal untuk membentuk tinggi sintetis.
    simpangan_tinggi = median_tinggi * 0.03

    tinggi_badan = np.clip(
        median_tinggi + haz_awal * simpangan_tinggi,
        45,
        120
    )

    # ----------------------------------------------
    # Perkiraan median berat berdasarkan umur
    # ----------------------------------------------

    median_berat = (
        3.3
        + 0.55 * min(umur_bulan, 12)
        + 0.22 * max(umur_bulan - 12, 0)
    )

    # Gunakan WAZ untuk membentuk berat sintetis.
    berat_badan = np.clip(
        median_berat + waz * 1.2,
        2.5,
        30
    )

    # ----------------------------------------------
    # Riwayat pemeriksaan sebelumnya
    # ----------------------------------------------

    if rng.random() < 0.25:
        jarak_sebelumnya = np.nan
        perubahan_bb = np.nan
        perubahan_tb = np.nan
        perubahan_haz = np.nan

    else:
        jarak_sebelumnya = int(rng.integers(60, 181))

        perubahan_bb = rng.uniform(0.1, 0.8)
        perubahan_tb = rng.uniform(0.5, 2.2)
        perubahan_haz = rng.normal(0.0, 0.20)
    # ----------------------------------------------
    # Menyusun satu baris dataset
    # ----------------------------------------------

    return {
        "group_id": group_id,
        "umur_hari_awal": umur_awal,
        "jenis_kelamin": jenis_kelamin,
        "berat_badan_awal_kg": round(berat_badan, 2),
        "tinggi_badan_awal_cm": round(tinggi_badan, 2),

        "haz_awal": round(float(haz_awal), 4),
        "waz_awal": round(float(waz), 4),
        "whz_awal": round(float(whz), 4),
        "baz_awal": round(float(baz), 4),

        "jarak_pemeriksaan_sebelumnya_hari": jarak_sebelumnya,
        "perubahan_berat_badan_kg": (
            round(float(perubahan_bb), 2)
            if pd.notna(perubahan_bb)
            else np.nan
        ),
        "perubahan_tinggi_badan_cm": (
            round(float(perubahan_tb), 2)
            if pd.notna(perubahan_tb)
            else np.nan
        ),
        "perubahan_haz": (
            round(float(perubahan_haz), 4)
            if pd.notna(perubahan_haz)
            else np.nan
        ),

        "jarak_hari_target": jarak_target,
        "haz_target": round(float(haz_target), 4),
        "label_risiko": label_risiko,
    }


# ==================================================
# 4. MEMBUAT SELURUH DATASET
# ==================================================

def generate_dataset() -> pd.DataFrame:

    daftar_data = []

    nomor = 1

    kelompok_risiko = [
        "rendah",
        "sedang",
        "tinggi",
    ]

    for kelompok in kelompok_risiko:

        for _ in range(JUMLAH_PER_KELAS):

            sampel = buat_sampel(
                nomor,
                kelompok
            )

            daftar_data.append(sampel)

            nomor += 1

    df = pd.DataFrame(daftar_data)

    # Acak urutan baris agar kelas tidak berurutan.
    df = df.sample(
        frac=1,
        random_state=RANDOM_SEED
    ).reset_index(drop=True)

    return df


# ==================================================
# 5. PROSES UTAMA
# ==================================================

def main():

    print("=" * 55)
    print("POSYANDU-IN | SIMULATION DATA GENERATOR")
    print("=" * 55)

    OUTPUT_DIR.mkdir(
        parents=True,
        exist_ok=True
    )

    df = generate_dataset()

    # Pemeriksaan dasar.
    assert len(df) == JUMLAH_PER_KELAS * 3

    assert df["group_id"].nunique() == len(df)

    assert df["label_risiko"].value_counts().to_dict() == {
        "rendah": JUMLAH_PER_KELAS,
        "sedang": JUMLAH_PER_KELAS,
        "tinggi": JUMLAH_PER_KELAS,
    }

    # Simpan sebagai CSV.
    df.to_csv(
        OUTPUT_FILE,
        index=False
    )

    print("\nDataset simulasi berhasil dibuat.")

    print(f"Jumlah sampel: {len(df)}")

    print(
        f"Jumlah anak simulasi: {df['group_id'].nunique()}"
    )

    print("\nDistribusi label:")

    print(
        df["label_risiko"].value_counts()
        .reindex(["rendah", "sedang", "tinggi"])
    )

    print("\nLokasi file:")
    print(OUTPUT_FILE)

    print("\nCatatan:")
    print(
        "Data ini sepenuhnya sintetis dan hanya "
        "untuk pengujian pipeline Machine Learning."
    )

    print("=" * 55)


if __name__ == "__main__":
    main()
