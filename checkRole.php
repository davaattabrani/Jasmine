<?php
include 'config.php';
include 'checkAccount.php';

function checkRole($allowedRoles) {
    checkLogin(); // Pastikan pengguna sudah login

    if (!isset($_SESSION['jabatan']) || !in_array($_SESSION['jabatan'], $allowedRoles)) {
        header('Location: ../pages-misc-error.html');
        exit();
    }
}
?>
