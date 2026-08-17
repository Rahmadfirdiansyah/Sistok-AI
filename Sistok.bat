@echo off
echo Membuka Aplikasi Sistok... Tolong tunggu sebentar...

:: Mematikan sisa-sisa server lama jika sebelumnya ditutup paksa (menggunakan tanda X Chrome)
taskkill /F /IM sistok-server.exe > NUL 2>&1

:: Menyalakan server menggunakan PHP portabel dari dalam folder environment secara tersembunyi
powershell -Command "Start-Process -WindowStyle Hidden -FilePath '%~dp0environment\php 8.4.22\sistok-server.exe' -ArgumentList 'artisan', 'serve', '--host=0.0.0.0', '--port=8888'"

:: Jeda 3 detik agar server siap sebelum browser terbuka
timeout /t 3 /nobreak > NUL

:: Membuka Chrome dalam mode Aplikasi (Tanpa tab, tanpa url bar)
start chrome --app="http://localhost:8888"
