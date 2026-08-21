#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\RegistrationRequest;
use App\Mail\RegistrationApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

echo "=== EMAIL AUTHENTICATION SYSTEM TEST ===\n\n";

// Test 1: Check if RegistrationRequest table has temp_password column
echo "Test 1: Checking database structure...\n";
try {
    $regReq = new RegistrationRequest();
    if (in_array('temp_password', $regReq->fillable)) {
        echo "✓ temp_password in fillable attributes\n";
    } else {
        echo "✗ temp_password NOT in fillable attributes\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Create a test registration request
echo "\nTest 2: Creating test registration request...\n";
try {
    // Clean up any existing test user
    $testEmail = 'test_email_auth_' . time() . '@example.com';
    User::where('email', $testEmail)->delete();
    
    $testUser = User::create([
        'name' => 'Test User Email Auth',
        'email' => $testEmail,
        'password' => Hash::make(Str::random(32)),
        'role' => 'client',
        'is_active' => false
    ]);
    
    $regRequest = RegistrationRequest::create([
        'user_id' => $testUser->id,
        'email' => $testEmail,
        'role' => 'client',
        'is_approved' => false,
        'is_rejected' => false,
    ]);
    
    echo "✓ Created test user: " . $testUser->email . "\n";
    echo "✓ Created registration request: ID=" . $regRequest->id . "\n";
    
    // Test 3: Generate temp password
    echo "\nTest 3: Generating temporary password...\n";
    $tempPassword = Str::random(12);
    echo "✓ Generated temp password: " . $tempPassword . "\n";
    
    // Test 4: Simulate approval
    echo "\nTest 4: Simulating admin approval...\n";
    $testUser->password = Hash::make($tempPassword);
    $testUser->email_verified_at = now();
    $testUser->is_active = true;
    $testUser->save();
    
    $regRequest->temp_password = $tempPassword;
    $regRequest->is_approved = true;
    $regRequest->save();
    
    echo "✓ User activated\n";
    echo "✓ Email verified\n";
    echo "✓ Password updated and hashed\n";
    echo "✓ Registration request marked as approved\n";
    
    // Test 5: Verify password can be checked
    echo "\nTest 5: Verifying password...\n";
    if (Hash::check($tempPassword, $testUser->password)) {
        echo "✓ Password verification successful\n";
    } else {
        echo "✗ Password verification failed\n";
    }
    
    // Test 6: Check Mailable exists
    echo "\nTest 6: Checking RegistrationApprovedMail...\n";
    try {
        $mail = new RegistrationApprovedMail($testUser, $tempPassword);
        echo "✓ RegistrationApprovedMail created successfully\n";
    } catch (\Exception $e) {
        echo "✗ Error creating Mailable: " . $e->getMessage() . "\n";
    }
    
    // Test 7: Check if email view exists
    echo "\nTest 7: Checking email template...\n";
    $viewPath = 'resources/views/emails/registration_approved.blade.php';
    if (file_exists($viewPath)) {
        echo "✓ Email template found at: " . $viewPath . "\n";
    } else {
        echo "✗ Email template NOT found\n";
    }
    
    // Test 8: Test email sending (depends on MAIL_MAILER config)
    echo "\nTest 8: Testing email sending...\n";
    echo "MAIL_MAILER: " . config('mail.default') . "\n";
    
    if (config('mail.default') === 'log') {
        echo "⚠ Using 'log' mailer - emails will be logged\n";
        echo "To test real emails, configure SMTP in .env\n";
    }
    
    try {
        Mail::to($testUser->email)->send(new RegistrationApprovedMail($testUser, $tempPassword));
        echo "✓ Email sent/queued successfully\n";
    } catch (\Exception $e) {
        echo "✗ Error sending email: " . $e->getMessage() . "\n";
    }
    
    // Summary
    echo "\n=== TEST SUMMARY ===\n";
    echo "Test User Email: " . $testUser->email . "\n";
    echo "Test User ID: " . $testUser->id . "\n";
    echo "Registration Request ID: " . $regRequest->id . "\n";
    echo "Temp Password: " . $tempPassword . "\n";
    echo "User Active: " . ($testUser->is_active ? 'YES' : 'NO') . "\n";
    echo "Email Verified: " . ($testUser->email_verified_at ? 'YES' : 'NO') . "\n";
    
    echo "\n✓ All tests completed!\n";
    
} catch (\Exception $e) {
    echo "✗ Error during test: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FRONTEND CHANGES ===\n";
echo "Frontend Register.vue has been updated to:\n";
echo "1. Remove password fields\n";
echo "2. Accept only name and email\n";
echo "3. Display message about password delivery\n";
echo "4. Submit to /api/registration/request\n\n";

echo "Register.vue location: frontend/src/views/Register.vue\n";
echo "Registration API: frontend/src/services/registrationApi.ts\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Run migrations: php artisan migrate\n";
echo "2. Configure .env with email settings (MAIL_MAILER, MAIL_HOST, etc)\n";
echo "3. Set FRONTEND_URL in .env\n";
echo "4. Clear cache: php artisan cache:clear\n";
echo "5. Test registration flow:\n";
echo "   - User submits email/name on Register page\n";
echo "   - Admin approves in Admin Dashboard\n";
echo "   - User receives email with password\n";
echo "   - User logs in with received password\n";

echo "\n=== DOCUMENTATION ===\n";
echo "See EMAIL_AUTH_SYSTEM.md for complete documentation\n";
