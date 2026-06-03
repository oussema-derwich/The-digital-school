<?php

namespace App\Http\Controllers;

use App\Models\PlatformFinance;

class PlatformFinanceController extends Controller
{
    public function show()
    {
        $finance = PlatformFinance::first();
        return response()->json(['total_income' => $finance->total_income ?? 0]);
    }
}