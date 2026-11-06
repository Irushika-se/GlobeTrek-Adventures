<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
 
// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /globetrek/login.php");
        exit();
    }
}
 
// Check user role and redirect if wrong
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        header("Location: /globetrek/index.php");
        exit();
    }
}
 
// Get current logged in user ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}
 
// Get current user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}
?>