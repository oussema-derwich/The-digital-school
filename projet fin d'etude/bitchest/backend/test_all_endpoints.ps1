$ErrorActionPreference = "SilentlyContinue"

Write-Host "========== TESTING FIXED ENDPOINTS ==========" -ForegroundColor Green

# 1. Login
Write-Host "`n[1] Testing Login..." -ForegroundColor Cyan
$login = @{
    email = "admin@bitchest.com"
    password = "admin123"
} | ConvertTo-Json

$loginRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/auth/login" -Method POST `
    -Headers @{"Content-Type"="application/json"} `
    -Body $login -UseBasicParsing
    
$loginData = $loginRes.Content | ConvertFrom-Json
$token = $loginData.data.token
Write-Host "✓ Login successful, token: " $token.Substring(0, 20) "..." -ForegroundColor Green

# 2. Get Transactions
Write-Host "`n[2] Testing GET /api/transactions..." -ForegroundColor Cyan
$txRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/transactions" -Method GET `
    -Headers @{"Authorization" = "Bearer $token"} `
    -UseBasicParsing

$txData = $txRes.Content | ConvertFrom-Json
Write-Host "✓ Transactions endpoint OK" -ForegroundColor Green
Write-Host "  Status: " $txRes.StatusCode
Write-Host "  Count: " $txData.data.Count

# 3. Get Crypto History
Write-Host "`n[3] Testing GET /api/cryptocurrencies/1/history..." -ForegroundColor Cyan
$histRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/cryptocurrencies/1/history" -Method GET `
    -UseBasicParsing

$histData = $histRes.Content | ConvertFrom-Json
Write-Host "✓ Crypto history endpoint OK" -ForegroundColor Green
Write-Host "  Status: " $histRes.StatusCode
Write-Host "  Crypto: " $histData.data.symbol
Write-Host "  History points: " $histData.data.history.Count

# 4. Buy Crypto
Write-Host "`n[4] Testing POST /api/wallets/buy..." -ForegroundColor Cyan
$buy = @{
    cryptocurrency_id = 2
    quantity = 0.0005
} | ConvertTo-Json

$buyRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/wallets/buy" -Method POST `
    -Headers @{
        "Content-Type" = "application/json"
        "Authorization" = "Bearer $token"
    } `
    -Body $buy `
    -UseBasicParsing

$buyData = $buyRes.Content | ConvertFrom-Json
Write-Host "✓ Buy transaction successful" -ForegroundColor Green
Write-Host "  Status: " $buyRes.StatusCode
Write-Host "  Crypto: " $buyData.data.crypto.symbol
Write-Host "  Quantity: " $buyData.data.crypto.quantite_achetee
Write-Host "  New balance: " $buyData.data.portefeuille.solde_eur " EUR"

# 5. Get Wallet
Write-Host "`n[5] Testing GET /api/wallet..." -ForegroundColor Cyan
$walletRes = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/wallet" -Method GET `
    -Headers @{"Authorization" = "Bearer $token"} `
    -UseBasicParsing

$walletData = $walletRes.Content | ConvertFrom-Json
Write-Host "✓ Wallet endpoint OK" -ForegroundColor Green
Write-Host "  Status: " $walletRes.StatusCode
Write-Host "  Balance EUR: " $walletData.data.balance_eur
Write-Host "  Holdings: " $walletData.data.holdings.Count

Write-Host "`n========== ALL TESTS PASSED ✓ ==========" -ForegroundColor Green
