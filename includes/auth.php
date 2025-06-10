<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function validatePassword($password) {
    $length = strlen($password) >= PASSWORD_MIN_LENGTH;
    $uppercase = PASSWORD_REQUIRE_UPPER ? preg_match('/[A-Z]/', $password) : true;
    $lowercase = PASSWORD_REQUIRE_LOWER ? preg_match('/[a-z]/', $password) : true;
    $number = PASSWORD_REQUIRE_NUMBER ? preg_match('/[0-9]/', $password) : true;
    $special = PASSWORD_REQUIRE_SPECIAL ? preg_match('/[^a-zA-Z0-9]/', $password) : true;
    
    return $length && $uppercase && $lowercase && $number && $special;
}

function logoutUser() {
    unset($_SESSION['user_id']);
}
?>