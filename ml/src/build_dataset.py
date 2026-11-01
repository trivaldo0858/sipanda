
from pathlib import Path
import json
import sys

import pandas as pd


# ==================================================
# KONFIGURASI
# ==================================================

ROOT_DIR = Path(__file__).resolve().parents[2]

RAW_FILE = ROOT_DIR / "ml/data/raw/dataset_prediksi_stunting.csv"
PROCESSED_DIR = ROOT_DIR / "ml/data/processed"
REPORT_DIR = ROOT_DIR / "ml/reports"

OUTPUT_FILE = PROCESSED_DIR / "dataset_prediksi_stunting.csv"
REPORT_FILE = REPORT_DIR / "dataset_quality_report.json"


# Kolom yang wajib tersedia.
REQUIRED_COLUMNS = [
    "group_id",
    "umur_hari_awal",
    "jenis_kelamin",
    "berat_badan_awal_kg",
    "tinggi_badan_awal_cm",
    "haz_awal",
    "jarak_hari_target",
    "haz_target",
    "label_risiko",
]

# Kolom numerik.
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

# Kolom yang digunakan sebagai fitur model.
# Metadata dan target tidak dimasukkan sebagai fitur.
FEATURE_COLUMNS = [
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
]

TARGET_COLUMN = "label_risiko"

ALLOWED_LABELS = {
    "rendah",
    "sedang",
    "tinggi",
}


# ==================================================
# FUNGSI PEMBACAAN DATA
# ==================================================

def read_dataset() -> pd.DataFrame:
    if not RAW_FILE.exists():
        raise FileNotFoundError(
            f"File dataset tidak ditemukan: {RAW_FILE}"
        )

    df = pd.read_csv(RAW_FILE)

    return df


# ==================================================
# VALIDASI STRUKTUR DATASET
# ==================================================

def validate_columns(df: pd.DataFrame) -> None:
    missing_columns = [
        column
        for column in REQUIRED_COLUMNS
        if column not in df.columns
    ]

    if missing_columns:
        raise ValueError(
            f"Kolom wajib tidak ditemukan: {missing_columns}"
        )


# ==================================================
# PEMBERSIHAN DATA
# ==================================================

def clean_dataset(df: pd.DataFrame):
    df = df.copy()

    initial_rows = len(df)

    # Konversi kolom numerik.
    for column in NUMERIC_COLUMNS:
        if column in df.columns:
            df[column] = pd.to_numeric(
                df[column],
                errors="coerce"
            )

    # Normalisasi teks kategori.
    df["jenis_kelamin"] = (
        df["jenis_kelamin"]
        .astype("string")
        .str.strip()
        .str.upper()
    )

    df["label_risiko"] = (
        df["label_risiko"]
        .astype("string")
        .str.strip()
        .str.lower()
    )

    # Kolom wajib tidak boleh kosong.
    mandatory_valid = (
        df["group_id"].notna()
        & df["group_id"].astype("string").str.strip().ne("")
        & df["umur_hari_awal"].notna()
        & df["jenis_kelamin"].isin(["L", "P"])
        & df["berat_badan_awal_kg"].notna()
        & df["tinggi_badan_awal_cm"].notna()
        & df["haz_awal"].notna()
        & df["jarak_hari_target"].notna()
        & df["haz_target"].notna()
        & df["label_risiko"].isin(ALLOWED_LABELS)
    )

    # Validasi rentang dasar.
    range_valid = (
        df["umur_hari_awal"].between(0, 1826)
        & (df["berat_badan_awal_kg"] > 0)
        & (df["tinggi_badan_awal_cm"] > 0)
        & df["jarak_hari_target"].between(75, 105)
        & df["haz_awal"].between(-6, 6)
        & df["haz_target"].between(-6, 6)
    )

    # Pastikan label sesuai dengan HAZ target.
    expected_labels = pd.Series(
        index=df.index,
        dtype="string"
    )

    expected_labels.loc[df["haz_target"] >= -2] = "rendah"

    expected_labels.loc[
        (df["haz_target"] >= -3)
        & (df["haz_target"] < -2)
    ] = "sedang"

    expected_labels.loc[df["haz_target"] < -3] = "tinggi"

    label_valid = df["label_risiko"].eq(expected_labels)

    # Hapus data yang tidak memenuhi persyaratan.
    valid_mask = (
        mandatory_valid
        & range_valid
        & label_valid
    )

    invalid_rows = int((~valid_mask).sum())

    df = df.loc[valid_mask].copy()

    # Hapus baris duplikat yang benar-benar identik.
    before_deduplication = len(df)

    df = df.drop_duplicates()

    duplicate_rows = before_deduplication - len(df)

    removed_rows = initial_rows - len(df)

    quality = {
        "jumlah_baris_awal": initial_rows,
        "jumlah_baris_valid": len(df),
        "jumlah_baris_dengan_data_tidak_valid": invalid_rows,
        "jumlah_baris_duplikat": duplicate_rows,
        "jumlah_total_baris_dihapus": removed_rows,
    }

    return df, quality


# ==================================================
# PEMBUATAN LAPORAN
# ==================================================

def create_quality_report(
    df: pd.DataFrame,
    quality: dict
) -> dict:

    label_distribution = {
        label: int(count)
        for label, count in (
            df[TARGET_COLUMN]
            .value_counts()
            .reindex(
                ["rendah", "sedang", "tinggi"],
                fill_value=0
            )
            .items()
        )
    }

    jumlah_anak = int(df["group_id"].nunique()) if not df.empty else 0

    report = {
        "nama_dataset": "Dataset Prediksi Stunting Posyandu-In",
        "versi": "1.0",
        "jumlah_sampel": len(df),
        "jumlah_anak": jumlah_anak,
        "fitur_model": FEATURE_COLUMNS,
        "target_model": TARGET_COLUMN,
        "distribusi_label": label_distribution,
        "data_kosong_per_kolom": {
            column: int(df[column].isna().sum())
            for column in df.columns
        },
        "pemeriksaan_kualitas": quality,
    }

    # Dataset kosong belum dapat digunakan untuk pelatihan.
    if df.empty:
        status = "BELUM_SIAP"
        alasan = "Belum tersedia sampel pemeriksaan berpasangan."

    elif any(value == 0 for value in label_distribution.values()):
        status = "BELUM_SIAP"
        alasan = "Belum semua kelas risiko memiliki sampel."

    else:
        status = "PERLU_EVALUASI"
        alasan = (
            "Seluruh kelas sudah ditemukan. "
            "Jumlah sampel, distribusi kelas, dan "
            "pemisahan data perlu dievaluasi sebelum training."
        )

    report["status_dataset"] = status
    report["alasan"] = alasan
    report["model_boleh_dilatih"] = False

    return report


# ==================================================
# PROSES UTAMA
# ==================================================

def main() -> int:
    print("=" * 55)
    print("POSYANDU-IN | PYTHON DATASET BUILDER")
    print("=" * 55)

    PROCESSED_DIR.mkdir(parents=True, exist_ok=True)
    REPORT_DIR.mkdir(parents=True, exist_ok=True)

    try:
        print("\n[1] Membaca dataset Laravel...")

        df = read_dataset()

        print(f"    Jumlah kolom: {len(df.columns)}")
        print(f"    Jumlah sampel: {len(df)}")

        print("\n[2] Memeriksa struktur kolom...")

        validate_columns(df)

        print("    Struktur kolom sesuai.")

        print("\n[3] Membersihkan dan memvalidasi data...")

        df_clean, quality = clean_dataset(df)

        print(f"    Data awal: {quality['jumlah_baris_awal']}")
        print(f"    Data valid: {quality['jumlah_baris_valid']}")
        print(f"    Data dihapus: {quality['jumlah_total_baris_dihapus']}")

        print("\n[4] Menyimpan dataset hasil pengolahan...")

        df_clean.to_csv(
            OUTPUT_FILE,
            index=False
        )

        print(f"    File: {OUTPUT_FILE}")

        print("\n[5] Membuat laporan kualitas dataset...")

        report = create_quality_report(
            df_clean,
            quality
        )

        with open(
            REPORT_FILE,
            "w",
            encoding="utf-8"
        ) as file:
            json.dump(
                report,
                file,
                indent=4,
                ensure_ascii=False
            )

        print(f"    File: {REPORT_FILE}")

        print("\n" + "=" * 55)
        print(f"STATUS DATASET: {report['status_dataset']}")
        print(report["alasan"])
        print("=" * 55)

        print("\nDistribusi label:")

        for label, jumlah in report["distribusi_label"].items():
            print(f"    {label.capitalize():<10}: {jumlah}")

        print("\nDataset berhasil diproses.")

        return 0

    except (FileNotFoundError, ValueError, pd.errors.ParserError) as error:
        print(f"\nERROR: {error}")

        return 1


if __name__ == "__main__":
    sys.exit(main())
