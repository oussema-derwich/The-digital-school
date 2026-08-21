# Start Laravel queue worker and schedule in separate background jobs (PowerShell)
# Usage: Open two PowerShell windows from backend folder and run this script in both

param(
    [string]$mode = 'queue' # 'queue' or 'schedule'
)

if ($mode -eq 'queue') {
    Write-Host "Starting queue worker..."
    php artisan queue:work --sleep=3 --tries=3
} elseif ($mode -eq 'schedule') {
    Write-Host "Starting schedule worker..."
    php artisan schedule:work
} else {
    Write-Host "Unknown mode. Use 'queue' or 'schedule'"
}
