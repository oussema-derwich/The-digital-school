$ErrorActionPreference = "SilentlyContinue"

$loginUrl = "http://127.0.0.1:8000/api/auth/login"
$loginData = @{
    email = "admin@bitchest.com"
    password = "admin123"
} | ConvertTo-Json

Write-Host "Testing POST $loginUrl" -ForegroundColor Green
Write-Host "Data: $loginData`n"

try {
    $response = Invoke-WebRequest -Uri $loginUrl -Method POST `
        -Headers @{"Content-Type"="application/json"} `
        -Body $loginData `
        -UseBasicParsing
    
    Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Response:" -ForegroundColor Green
    $response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5
    
    # Extract token if login successful
    $jsonResp = $response.Content | ConvertFrom-Json
    if ($jsonResp.data.token) {
        Write-Host "`nToken obtained: $($jsonResp.data.token)" -ForegroundColor Cyan
        
        # Now test BUY endpoint
        Write-Host "`n--- Testing BUY endpoint ---" -ForegroundColor Green
        
        $buyUrl = "http://127.0.0.1:8000/api/wallets/buy"
        $buyData = @{
            cryptocurrency_id = 1
            quantity = 0.001
        } | ConvertTo-Json
        
        Write-Host "Testing POST $buyUrl" -ForegroundColor Green
        Write-Host "Data: $buyData`n"
        
        $buyResponse = Invoke-WebRequest -Uri $buyUrl -Method POST `
            -Headers @{
                "Content-Type" = "application/json"
                "Authorization" = "Bearer $($jsonResp.data.token)"
            } `
            -Body $buyData `
            -UseBasicParsing
        
        Write-Host "Status: $($buyResponse.StatusCode)" -ForegroundColor Green
        Write-Host "Response:" -ForegroundColor Green
        $buyResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5
    }
} catch {
    $err = $_.Exception.Response
    Write-Host "Status: $($err.StatusCode)" -ForegroundColor Red
    
    $stream = $err.GetResponseStream()
    $reader = [System.IO.StreamReader]::new($stream)
    $content = $reader.ReadToEnd()
    $reader.Dispose()
    
    Write-Host "Error Response:" -ForegroundColor Red
    $content | ConvertFrom-Json | ConvertTo-Json -Depth 5
}
