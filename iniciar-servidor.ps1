# ============================================================
#  Inicia o ambiente de desenvolvimento da Plataforma de Treinamentos
#  - Sobe o MySQL do Laragon (se ainda nao estiver rodando)
#  - Inicia o servidor Laravel em http://127.0.0.1:8000
#
#  Uso: clique com o botao direito > "Executar com o PowerShell"
#       ou rode no terminal:  ./iniciar-servidor.ps1
# ============================================================

$ErrorActionPreference = "Stop"

$phpDir   = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64"
$mysqlBin = "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin"
$mysqlData = "C:\laragon\data\mysql-8.4.3-winx64"

$env:Path = "$phpDir;$mysqlBin;" + $env:Path

# --- MySQL ---
if (-not (Get-Process mysqld -ErrorAction SilentlyContinue)) {
    Write-Host "Iniciando MySQL..." -ForegroundColor Cyan
    Start-Process -FilePath "$mysqlBin\mysqld.exe" -ArgumentList "--datadir=`"$mysqlData`"" -WindowStyle Hidden
    Start-Sleep -Seconds 4
} else {
    Write-Host "MySQL ja esta rodando." -ForegroundColor Green
}

# --- Laravel ---
Set-Location $PSScriptRoot
Write-Host "Servidor em http://127.0.0.1:8000  (Ctrl+C para parar)" -ForegroundColor Green
php artisan serve --host=127.0.0.1 --port=8000
