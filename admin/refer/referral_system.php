<?php
// Simple Referral System in Pure PHP (No Database)
// All data is stored in arrays within this file.

// -----------------------------
// Data Storage (Arrays)
// -----------------------------

// Users array: each user has id, name, and wallet
$users = [
    [ 'id' => 1, 'name' => 'Alice', 'wallet' => 0 ],
    [ 'id' => 2, 'name' => 'Bob',   'wallet' => 0 ],
    // Add more users as needed
];

// Referral links array: each link has token, user_id (referrer), is_used
$referralLinks = [];

// -----------------------------
// Helper Functions
// -----------------------------

// Find user index by ID in $users array
function findUserIndexById($userId) {
    global $users;
    foreach ($users as $index => $user) {
        if ($user['id'] === $userId) {
            return $index;
        }
    }
    return false;
}

// Find referral link index by token in $referralLinks array
function findReferralLinkIndexByToken($token) {
    global $referralLinks;
    foreach ($referralLinks as $index => $link) {
        if ($link['token'] === $token) {
            return $index;
        }
    }
    return false;
}

// Generate a random, unique token
function generateUniqueToken() {
    return bin2hex(random_bytes(8)); // 16-char hex string
}

// -----------------------------
// Core Functions
// -----------------------------

// 1. Generate a unique, one-time-use referral link for a user
function generateReferralLink($userId) {
    global $referralLinks;
    // Generate a unique token
    do {
        $token = generateUniqueToken();
    } while (findReferralLinkIndexByToken($token) !== false);

    // Add the referral link to the array
    $referralLinks[] = [
        'token' => $token,
        'user_id' => $userId,
        'is_used' => false
    ];

    // Return the referral link (for demo, just the token)
    return $token;
}

// 2. Register a new user using a referral link
function registerUserWithReferral($token, $newUserName) {
    global $referralLinks, $users;
    $linkIndex = findReferralLinkIndexByToken($token);
    if ($linkIndex === false) {
        return 'Invalid referral link.';
    }
    if ($referralLinks[$linkIndex]['is_used']) {
        return 'Referral link already used.';
    }
    // Mark the link as used
    $referralLinks[$linkIndex]['is_used'] = true;
    $referrerId = $referralLinks[$linkIndex]['user_id'];
    // Credit ₹100 to referrer
    $userIndex = findUserIndexById($referrerId);
    if ($userIndex !== false) {
        $users[$userIndex]['wallet'] += 100;
    }
    // Add the new user (auto-increment id)
    $newId = end($users)['id'] + 1;
    $users[] = [ 'id' => $newId, 'name' => $newUserName, 'wallet' => 0 ];
    return "User '$newUserName' registered with referral. Referrer credited ₹100.";
}

// 3. Get wallet balance for a user
function getWalletBalance($userId) {
    global $users;
    $userIndex = findUserIndexById($userId);
    if ($userIndex === false) {
        return null;
    }
    return $users[$userIndex]['wallet'];
}

// -----------------------------
// Example Usage
// -----------------------------

echo "--- Referral System Demo ---\n";

// Alice (id=1) generates a referral link
$aliceReferralToken = generateReferralLink(1);
echo "Alice's referral link token: $aliceReferralToken\n";

// New user 'Charlie' registers using Alice's referral link
echo registerUserWithReferral($aliceReferralToken, 'Charlie') . "\n";

// Check Alice's wallet balance
echo "Alice's wallet balance: ₹" . getWalletBalance(1) . "\n";

// Try to use the same referral link again (should fail)
echo registerUserWithReferral($aliceReferralToken, 'David') . "\n";

// Bob (id=2) generates a referral link
$bobReferralToken = generateReferralLink(2);
echo "Bob's referral link token: $bobReferralToken\n";

// New user 'Eve' registers using Bob's referral link
echo registerUserWithReferral($bobReferralToken, 'Eve') . "\n";

// Check Bob's wallet balance
echo "Bob's wallet balance: ₹" . getWalletBalance(2) . "\n";

// List all users and their wallets
echo "\nAll users:\n";
foreach ($users as $user) {
    echo "ID: {$user['id']}, Name: {$user['name']}, Wallet: ₹{$user['wallet']}\n";
}

// List all referral links
echo "\nAll referral links:\n";
foreach ($referralLinks as $link) {
    echo "Token: {$link['token']}, Referrer ID: {$link['user_id']}, Used: " . ($link['is_used'] ? 'Yes' : 'No') . "\n";
}

// End of demo 