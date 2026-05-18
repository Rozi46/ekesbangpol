@ECHO OFF

cd /d D:\@systemR\local\ekesbangpol

start "Laravel Server" cmd /k php artisan serve --host=127.0.0.1 --port=8030

echo Waiting for server...

:loop
timeout /t 2 >nul
curl -s http://127.0.0.1:8030 >nul
if errorlevel 1 (
    goto loop
)

echo Server is ready!

start "" "C:\Program Files\Mozilla Firefox\firefox.exe" http://127.0.0.1:8030/admin/administration

exit