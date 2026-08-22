#!/bin/bash

# =========================================================
# SCRIPT DEPLOYMENT OTOMATIS KE VPS
# Proyek: Kid Health Monitoring
# =========================================================

# Konfigurasi VPS (Berdasarkan Manual Book)
VPS_IP="157.15.124.206"
VPS_USER="root"
VPS_PASS="Q4NNLug6DJTC3fqgXPV@"
VPS_DIR="/opt/apps/kid-health-monitoring"

echo "========================================================="
echo "🚀 MEMULAI PROSES DEPLOYMENT KE VPS ($VPS_IP)"
echo "========================================================="

# Memastikan sshpass terinstall di lokal untuk bypass password SSH
if ! command -v sshpass &> /dev/null
then
    echo "[!] sshpass belum terinstall di komputer lokal Anda."
    echo "[!] Menginstall sshpass (mungkin memerlukan password sudo lokal Anda)..."
    sudo apt-get update && sudo apt-get install -y sshpass
fi

# Rangkaian perintah yang akan dieksekusi secara remote di VPS
REMOTE_COMMAND="
    echo '---------------------------------------------------' &&
    echo '📂 Masuk ke direktori proyek...' &&
    cd $VPS_DIR &&
    echo '⬇️  Menarik kode terbaru (git pull)...' &&
    git pull origin main &&
    echo '🧹 Membersihkan cache Laravel via Docker...' &&
    docker compose exec -T app php artisan optimize:clear &&
    echo '✅ Deployment berhasil diselesaikan!' &&
    echo '---------------------------------------------------'
"

# Menjalankan perintah remote menggunakan sshpass
echo "Menghubungkan ke server $VPS_IP..."
sshpass -p "$VPS_PASS" ssh -o StrictHostKeyChecking=no "$VPS_USER@$VPS_IP" "$REMOTE_COMMAND"

echo "========================================================="
echo "🎉 PROSES SELESAI"
echo "========================================================="
