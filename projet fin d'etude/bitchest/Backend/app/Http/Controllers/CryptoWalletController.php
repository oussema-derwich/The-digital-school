<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\CryptoWalletService;

class CryptoWalletController extends Controller
{
    protected $cryptoWalletService;

    public function __construct(CryptoWalletService $cryptoWalletService)
    {
        $this->cryptoWalletService = $cryptoWalletService;
    }

    public function getCryptoPurchases($cryptoId)
    {
        $result = $this->cryptoWalletService->getCryptoPurchases($cryptoId);

        // If there's an error message in the result
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 404);
        }

        return response()->json($result);
    }
}
