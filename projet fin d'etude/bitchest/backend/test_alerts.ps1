$ErrorActionPreference = "SilentlyContinue"

Write-Host "========== TESTING ALERTS/NOTIFICATIONS ==========" -ForegroundColor Green

# 1. Login
Write-Host "`n[1] Login..." -ForegroundColor Cyan
$login = @{
    email = "admin@bitchest.com"
    password = "admin123"
} | ConvertTo-Json

$loginRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/auth/login" -Method POST `
    -Headers @{"Content-Type"="application/json"} `
    -Body $login -UseBasicParsing
    
$loginData = $loginRes.Content | ConvertFrom-Json
$token = $loginData.data.token
Write-Host "✓ Token: $($token.Substring(0, 20))..." -ForegroundColor Green

# 2. Get Alerts/Notifications
Write-Host "`n[2] GET /api/alerts..." -ForegroundColor Cyan
try {
    $alertRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/alerts" -Method GET `
        -Headers @{"Authorization" = "Bearer $token"} `
        -UseBasicParsing
    
    Write-Host "✓ Status: $($alertRes.StatusCode)" -ForegroundColor Green
    $alertData = $alertRes.Content | ConvertFrom-Json
    Write-Host "  Count: $($alertData.data.Count)" -ForegroundColor Green
} catch {
    Write-Host "✗ Error: $($_.Exception.Response.StatusCode)" -ForegroundColor Red
    Write-Host $_.Exception.Response.Content
}

Write-Host "`n========== TEST COMPLETE ==========" -ForegroundColor Green
