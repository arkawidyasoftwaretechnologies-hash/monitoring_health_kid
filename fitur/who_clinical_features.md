# Dokumentasi Integrasi Standar Antropometri WHO

Dokumen ini merangkum seluruh parameter, algoritma, dan fitur klinis yang telah kita bangun dan integrasikan ke dalam aplikasi **Monitoring Stunting Kid Health** berdasarkan standar *World Health Organization (WHO) Child Growth Standards* dan pedoman Kementerian Kesehatan RI (Permenkes No. 2 Tahun 2020).

## 1. Parameter Pengukuran (Antropometri)
Sistem kini tidak hanya mengukur Berat Badan (BB) dan Tinggi Badan (TB), tetapi telah diekspansi untuk mengakomodasi parameter diagnostik lengkap:
- **Berat Badan (kg)** & **Tinggi/Panjang Badan (cm)**: Metrik absolut dasar.
- **Lingkar Kepala / Head Circumference (cm)**: Diukur untuk mendeteksi dini anomali pada perkembangan volume otak anak.
- **Lingkar Lengan Atas / MUAC (cm)**: (Middle Upper Arm Circumference), indikator darurat (*proxy*) terbaik untuk mendeteksi kekurangan energi kronis atau gizi buruk secara cepat di lapangan tanpa perlu menimbang.

---

## 2. Kalkulator Medis Z-Score (Engine Algoritma)
Sistem telah dibekali dengan servis perhitungan (*GrowthCalculationService* & *NutritionService*) yang secara instan mengonversi angka absolut menjadi **Z-Score (SD / Standar Deviasi)** dengan merujuk pada tabel interpolasi WHO:

| Indeks (WHO) | Deskripsi Sistem | Klasifikasi Diagnosis |
| :--- | :--- | :--- |
| **WAZ (BB/U)** | Berat Badan menurut Umur. | Mengklasifikasikan status: Berat Badan Sangat Kurang, Kurang, Normal, atau Risiko Lebih. |
| **HAZ (TB/U)** | Tinggi Badan menurut Umur. | Mengklasifikasikan status kronis: Sangat Pendek (*Severely Stunted*), Pendek (*Stunted*), Normal, atau Tinggi. |
| **BMIZ (IMT/U)**| Indeks Massa Tubuh (IMT) menurut Umur. | Sistem menghitung `BB / (TB dalam meter)^2` lalu dicocokkan dengan Z-Score untuk deteksi: Gizi Buruk, Kurang, Baik, Berisiko Obesitas, hingga Obesitas. |
| **HCFA (LK/U)** | Lingkar Kepala menurut Umur. | Mendeteksi *Mikrosefali* (Z-Score < -2 SD, ukuran otak kecil) atau *Makrosefali* (Z-Score > +2 SD). |

---

## 3. Sistem Peringatan Dini (Red Flag)
Berdasarkan protokol deteksi klinis, sistem akan secara otomatis memancarkan status **Red Flag (Peringatan Merah)** beserta pop-up diagnosis jika pada saat kader meng-input data, ditemukan kondisi gawat darurat berikut:

> [!CAUTION]
> **Pemicu Red Flag:**
> 1. **LiLA (Lingkar Lengan Atas) < 11.5 cm:** Indikasi kuat *Severe Acute Malnutrition (SAM)* atau Gizi Buruk Akut Berat yang memiliki risiko kematian tinggi.
> 2. **Z-Score Lingkar Kepala (HCFA) < -2 SD atau > 2 SD:** Mengindikasikan gangguan neurologis atau penumpukan cairan di otak (*hidrosefalus/makrosefali*).
> 3. **Z-Score HAZ < -3 SD:** Menandakan *Severely Stunted* (Sangat Pendek / Stunting Kronis Ekstrem).

*Jika Red Flag menyala, UI aplikasi secara otomatis merekomendasikan rujukan segera ke fasilitas kesehatan tingkat lanjut (Rumah Sakit/Dokter Spesialis Anak).*

---

## 4. Visualisasi & Rekam Medis (Frontend Features)
Semua data klinis di atas telah diwujudkan dalam fitur-fitur UI berikut:
- **Tabel Data Balita Terpadu:** Modul pencarian menampilkan rangkuman absolut dari pengukuran terakhir (TB, BB, LK, LiLA) dan status gizi gabungan, menghilangkan keharusan kader untuk mencetak kartu fisik.
- **Dashboard Grafik Kurva WHO:** Kurva *real-time* berbasis `Chart.js` yang memetakan jalur pertumbuhan WAZ (Garis Hijau) dan HAZ (Garis Biru) melintasi garis peringatan normal, ambang stunting (-2 SD), hingga batas merah gizi buruk (-3 SD).
- **Modul Pelaporan (PDF/Excel):** Mengekspor seluruh metrik indikator klinis ke dalam *spreadsheet* yang dikurasi secara otomatis, berguna bagi pemangku kebijakan (Dinas Kesehatan/Puskesmas) untuk analisis klasterisasi lanjutan (e.g. *Data Mining*).
