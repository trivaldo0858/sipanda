from pathlib import Path
import json

import pandas as pd

from sklearn.compose import ColumnTransformer
from sklearn.impute import SimpleImputer
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler, OneHotEncoder

from sklearn.linear_model import LogisticRegression
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier
from sklearn.dummy import DummyClassifier

from sklearn.model_selection import StratifiedGroupKFold
from sklearn.base import clone

from sklearn.metrics import (
    accuracy_score,
    balanced_accuracy_score,
    precision_score,
    recall_score,
    f1_score,
    confusion_matrix,
)


# ==================================================
# 1. PENGATURAN FILE
# ==================================================

ROOT = Path(__file__).resolve().parents[2]

INPUT_FILE = (
    ROOT / "ml/data/simulation/dataset_simulasi_processed.csv"
)

REPORT_CSV = (
    ROOT / "ml/reports/simulation_model_comparison.csv"
)

REPORT_JSON = (
    ROOT / "ml/reports/simulation_model_comparison.json"
)

JUMLAH_FOLD = 5

LABEL = ["rendah", "sedang", "tinggi"]


# ==================================================
# 2. MENENTUKAN FITUR INPUT
# ==================================================

FITUR_NUMERIK = [
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
]

FITUR_KATEGORIK = [
    "jenis_kelamin"
]

FITUR = FITUR_NUMERIK + FITUR_KATEGORIK

TARGET = "label_risiko"

GROUP = "group_id"


# ==================================================
# 3. MEMBANGUN PREPROCESSING
# ==================================================

def buat_preprocessing():

    proses_numerik = Pipeline(
        steps=[
            ("imputer", SimpleImputer(strategy="median")),
            ("scaler", StandardScaler()),
        ]
    )

    proses_kategorik = Pipeline(
        steps=[
            ("imputer", SimpleImputer(strategy="most_frequent")),
            (
                "encoder",
                OneHotEncoder(handle_unknown="ignore")
            ),
        ]
    )

    preprocessing = ColumnTransformer(
        transformers=[
            (
                "numerik",
                proses_numerik,
                FITUR_NUMERIK
            ),
            (
                "kategorik",
                proses_kategorik,
                FITUR_KATEGORIK
            ),
        ]
    )

    return preprocessing


# ==================================================
# 4. MENYIAPKAN MODEL
# ==================================================

def buat_model():

    preprocessing = buat_preprocessing()

    daftar_model = {

        "Logistic Regression": LogisticRegression(
            max_iter=2000,
            class_weight="balanced",
            random_state=42
        ),

        "Decision Tree": DecisionTreeClassifier(
            max_depth=4,
            min_samples_leaf=3,
            class_weight="balanced",
            random_state=42
        ),

        "Random Forest": RandomForestClassifier(
            n_estimators=300,
            max_depth=6,
            min_samples_leaf=2,
            class_weight="balanced_subsample",
            random_state=42,
            n_jobs=-1
        ),

        "Dummy Classifier": DummyClassifier(
            strategy="most_frequent"
        ),
    }

    model_pipeline = {}

    for nama, algoritma in daftar_model.items():

        model_pipeline[nama] = Pipeline(
            steps=[
                ("preprocessing", clone(preprocessing)),
                ("model", algoritma),
            ]
        )

    return model_pipeline


# ==================================================
# 5. MEMUAT DAN MEMERIKSA DATASET
# ==================================================

def muat_dataset():

    if not INPUT_FILE.exists():
        raise FileNotFoundError(
            f"Dataset tidak ditemukan: {INPUT_FILE}"
        )

    df = pd.read_csv(INPUT_FILE)

    if df.empty:
        raise ValueError("Dataset simulasi kosong.")

    kolom_wajib = FITUR + [TARGET, GROUP]

    kolom_hilang = [
        kolom
        for kolom in kolom_wajib
        if kolom not in df.columns
    ]

    if kolom_hilang:
        raise ValueError(
            f"Kolom berikut tidak ditemukan: {kolom_hilang}"
        )

    if df[GROUP].isnull().any():
        raise ValueError("Ditemukan group_id kosong.")

    if df[TARGET].isnull().any():
        raise ValueError("Ditemukan label kosong.")

    if set(df[TARGET].unique()) != set(LABEL):
        raise ValueError(
            "Dataset harus memiliki ketiga kelas risiko."
        )

    X = df[FITUR].copy()
    y = df[TARGET].copy()
    groups = df[GROUP].copy()

    # Memastikan jumlah anak simulasi cukup
    # untuk pembagian 5-fold.
    jumlah_per_kelas = (
        df.groupby(TARGET)[GROUP].nunique()
    )

    if (jumlah_per_kelas < JUMLAH_FOLD).any():
        raise ValueError(
            "Jumlah kelompok anak pada salah satu kelas "
            "tidak cukup untuk 5-fold."
        )

    return X, y, groups


# ==================================================
# 6. EVALUASI MODEL DENGAN GROUP CROSS VALIDATION
# ==================================================

def evaluasi_model(X, y, groups):

    pembagian = StratifiedGroupKFold(
        n_splits=JUMLAH_FOLD,
        shuffle=True,
        random_state=42
    )

    daftar_model = buat_model()

    hasil_fold = []
    hasil_confusion = {}

    for nama, model_awal in daftar_model.items():

        print(f"\nMenguji: {nama}")

        hasil_confusion[nama] = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ]

        for nomor_fold, (train_idx, test_idx) in enumerate(
            pembagian.split(X, y, groups),
            start=1
        ):

            X_train = X.iloc[train_idx]
            X_test = X.iloc[test_idx]

            y_train = y.iloc[train_idx]
            y_test = y.iloc[test_idx]

            groups_train = set(groups.iloc[train_idx])
            groups_test = set(groups.iloc[test_idx])

            # Memastikan anak tidak muncul pada
            # data training dan testing sekaligus.
            if groups_train.intersection(groups_test):
                raise ValueError(
                    "Ditemukan group_id yang sama "
                    "pada data training dan testing."
                )

            model = clone(model_awal)

            model.fit(X_train, y_train)

            prediksi = model.predict(X_test)

            recall_tinggi = recall_score(
                y_test,
                prediksi,
                labels=["tinggi"],
                average=None,
                zero_division=0
            )[0]

            hasil_fold.append({
                "model": nama,
                "fold": nomor_fold,

                "accuracy": accuracy_score(
                    y_test, prediksi
                ),

                "balanced_accuracy": balanced_accuracy_score(
                    y_test, prediksi
                ),

                "precision_macro": precision_score(
                    y_test,
                    prediksi,
                    average="macro",
                    zero_division=0
                ),

                "recall_macro": recall_score(
                    y_test,
                    prediksi,
                    average="macro",
                    zero_division=0
                ),

                "f1_macro": f1_score(
                    y_test,
                    prediksi,
                    average="macro",
                    zero_division=0
                ),

                "recall_tinggi": recall_tinggi,
            })

            matriks = confusion_matrix(
                y_test,
                prediksi,
                labels=LABEL
            )

            hasil_confusion[nama] = (
                pd.DataFrame(hasil_confusion[nama])
                .add(pd.DataFrame(matriks), fill_value=0)
                .astype(int)
                .values
                .tolist()
            )

            print(
                f"  Fold {nomor_fold} selesai."
            )

    return hasil_fold, hasil_confusion


# ==================================================
# 7. MERANGKUM HASIL EVALUASI
# ==================================================

def buat_ringkasan(hasil_fold):

    df_fold = pd.DataFrame(hasil_fold)

    kolom_metrik = [
        "accuracy",
        "balanced_accuracy",
        "precision_macro",
        "recall_macro",
        "f1_macro",
        "recall_tinggi",
    ]

    ringkasan = (
        df_fold
        .groupby("model")[kolom_metrik]
        .agg(["mean", "std"])
    )

    ringkasan.columns = [
        f"{metrik}_{statistik}"
        for metrik, statistik in ringkasan.columns
    ]

    ringkasan = ringkasan.reset_index()

    return df_fold, ringkasan


# ==================================================
# 8. MENYIMPAN LAPORAN
# ==================================================

def simpan_laporan(
    df_fold,
    ringkasan,
    hasil_confusion
):

    REPORT_CSV.parent.mkdir(
        parents=True,
        exist_ok=True
    )

    df_fold.to_csv(
        REPORT_CSV,
        index=False,
        encoding="utf-8-sig"
    )

    laporan = {
        "jenis_dataset": "data_simulasi",
        "jumlah_fold": JUMLAH_FOLD,
        "jumlah_sampel": 90,
        "catatan": (
            "Hasil hanya untuk pengujian pipeline. "
            "Tidak menunjukkan performa klinis "
            "pada data anak sebenarnya."
        ),
        "ringkasan_model": (
            ringkasan.round(4).to_dict(orient="records")
        ),
        "confusion_matrix": hasil_confusion,
    }

    REPORT_JSON.parent.mkdir(
        parents=True,
        exist_ok=True
    )

    with open(REPORT_JSON, "w", encoding="utf-8") as file:

        json.dump(
            laporan,
            file,
            indent=4,
            ensure_ascii=False
        )


# ==================================================
# 9. PROGRAM UTAMA
# ==================================================

def main():

    print("=" * 60)
    print("POSYANDU-IN | SIMULATION MODEL COMPARISON")
    print("=" * 60)

    X, y, groups = muat_dataset()

    print(f"\nJumlah sampel: {len(X)}")
    print(f"Jumlah anak: {groups.nunique()}")

    print("\nDistribusi kelas:")
    print(y.value_counts())

    print("\nFitur yang digunakan:")
    for fitur in FITUR:
        print(f"- {fitur}")

    print("\nKolom yang TIDAK digunakan:")
    print("- group_id")
    print("- haz_target")
    print("- jarak_hari_target")
    print("- label_risiko sebagai fitur")

    hasil_fold, hasil_confusion = evaluasi_model(
        X, y, groups
    )

    df_fold, ringkasan = buat_ringkasan(
        hasil_fold
    )

    simpan_laporan(
        df_fold,
        ringkasan,
        hasil_confusion
    )

    print("\n" + "=" * 60)
    print("RINGKASAN PERBANDINGAN MODEL")
    print("=" * 60)

    print(
        ringkasan.round(4).to_string(index=False)
    )

    print("\nFile laporan:")
    print(REPORT_CSV)
    print(REPORT_JSON)

    print("\nPengujian simulasi selesai.")
    print("=" * 60)


if __name__ == "__main__":

    try:
        main()

    except Exception as error:

        print("\nPENGUJIAN GAGAL")
        print(f"Penyebab: {error}")

        raise