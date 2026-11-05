<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/services/EmailService.php';

echo "Testing EmailService Configuration...\n";
echo "RESEND_API_KEY: " . substr(RESEND_API_KEY, 0, 10) . "...\n";
echo "RESEND_FROM_EMAIL: " . RESEND_FROM_EMAIL . "\n";

$service = new EmailService();

// Test method exists
if (method_exists($service, 'send') && method_exists($service, 'sendOtpEmail')) {
    echo "Methods exist: OK\n";
} else {
    echo "Methods missing: FAIL\n";
    exit(1);
}

// Test send with delivered@resend.dev (Resend test recipient that always succeeds without actual delivery)
echo "Sending test OTP to delivered@resend.dev...\n";
$res = $service->sendOtpEmail('delivered@resend.dev', 'Sultan Tester', '849201');

echo "Result:\n";
print_r($res);

if (!empty($res['success'])) {
    echo "SUCCESS: Email sent via Resend API! ID: " . ($res['id'] ?? 'none') . "\n";
} else {
    echo "NOTICE: Email response error: " . ($res['error'] ?? 'unknown') . "\n";
}
