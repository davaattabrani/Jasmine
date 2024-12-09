<?php
include 'config.php';
session_start();

function checkLogin() {
    if (!isset($_SESSION['id_pengguna'])) {
        header('Location: login.php');
        exit();
    }
}
?>
