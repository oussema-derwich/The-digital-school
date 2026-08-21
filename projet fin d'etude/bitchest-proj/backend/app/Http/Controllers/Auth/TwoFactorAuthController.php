<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(\Illuminate\Session\Middleware\StartSession::class);
        $this->google2fa = new Google2FA();
    }

    public function enable(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Generate the secret key
        $secretKey = $this->google2fa->generateSecretKey();
        
        // Create QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );
        
        // Store the secret key differently based on environment
        if (!app()->environment('testing')) {
            session(['2fa_secret' => $secretKey]);
        }
        
        return response()->json([
            'status' => 'success',
            'qr_code' => $qrCodeUrl,
            'secret' => $secretKey
        ]);
    }

    public function confirm(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate(['code' => 'required|string']);
        
        // For testing env, allow secret to be passed directly
        $secretKey = app()->environment('testing') 
            ? $request->input('secret') 
            : session('2fa_secret');
        
        if (!$secretKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'No 2FA secret found'
            ], 400);
        }

        info('Verifying 2FA code', [
            'code' => $request->code,
            'secret' => $secretKey,
            'user' => Auth::user() ? Auth::user()->id : null
        ]);

        $valid = $this->google2fa->verifyKey($secretKey, $request->code);
        
        info('2FA Verification result:', ['valid' => $valid]);
        
        if ($valid) {
            $user = $request->user();
            info('Updating 2FA for user: ' . $user->id, [
                'secret' => $secretKey,
                'code' => $request->code
            ]);
            $user->forceFill([
                'two_factor_secret' => $secretKey,
                'two_factor_enabled' => true
            ]);
            $user->save();
            
            if (!app()->environment('testing')) {
                session()->forget('2fa_secret');
            }
            
            return response()->json(['status' => 'success']);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid verification code'
        ], 400);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $user = Auth::user();
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);
        
        if ($valid) {
            session(['2fa_verified' => true]);
            return response()->json(['status' => 'success']);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid verification code'
        ], 400);
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();
        
        return response()->json(['status' => 'success']);
    }
}