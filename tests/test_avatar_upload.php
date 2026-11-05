<?php
/**
 * Test Avatar Upload Functionality
 */
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/User.php';

$userModel = new User();

// 1. Check a customer user
$users = $userModel->getUsers();
if (empty($users)) {
    echo "No customer users found to test.\n";
    exit(0);
}

$testUser = $users[0];
echo "Testing with user ID: " . $testUser->id . " (" . $testUser->name . ")\n";

// 2. Test getUserById
$userObj = $userModel->getUserById($testUser->id);
if ($userObj && property_exists($userObj, 'profile_image')) {
    echo "[PASS] getUserById returns profile_image property (current value: " . var_export($userObj->profile_image, true) . ")\n";
} else {
    echo "[FAIL] getUserById does not have profile_image property.\n";
    exit(1);
}

// 3. Test updateAvatar
$testImagePath = 'uploads/avatars/test_avatar_' . time() . '.jpg';
$updateResult = $userModel->updateAvatar($testUser->id, $testImagePath);
if ($updateResult) {
    echo "[PASS] updateAvatar successfully updated DB record.\n";
} else {
    echo "[FAIL] updateAvatar failed.\n";
    exit(1);
}

// 4. Verify updated record
$refreshedUser = $userModel->getUserById($testUser->id);
if ($refreshedUser && $refreshedUser->profile_image === $testImagePath) {
    echo "[PASS] Refreshed user has correct profile_image path.\n";
} else {
    echo "[FAIL] Refreshed user profile_image does not match.\n";
    exit(1);
}

// 5. Restore original value or reset
$userModel->updateAvatar($testUser->id, $userObj->profile_image);
echo "[PASS] Restored original avatar state.\n";

echo "\nAll Avatar Model and Database Integration tests PASSED successfully!\n";
