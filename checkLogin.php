<?php
session_start();
include 'config.php'; // Sertakan file konfigurasi database Anda

function checkRole($allowedRoles) {
    checkLogin(); // Pastikan pengguna sudah login

    if (!isset($_SESSION['jabatan']) || !in_array($_SESSION['jabatan'], $allowedRoles)) {
        header('Location: noAccess.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Pastikan nama kolom sesuai dengan yang ada di database
    $sql = "SELECT id_pengguna, password, jabatan FROM pengguna WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $row['password']) || $password === $row['password']) {
                $_SESSION['id_pengguna'] = $row['id_pengguna'];
                $_SESSION['username'] = $username;
                $_SESSION['jabatan'] = $row['jabatan'];

                // Redirect berdasarkan role
                switch ($row['jabatan']) {
                    case 'admin':
                        header('Location: admin/index.php');
                        break;
                    case 'Penanggung Jawab Farmasi':
                        header('Location: penanggungJawab/index.php');
                        break;
                    case 'direktur utama':
                        header('Location: direkturutama/index.php');
                        break;
                    default:
                        header('Location: login.php'); // Redirect kembali jika role tidak dikenali
                        break;
                }
                exit;
            } else {
                echo "<script>alert('Username atau password salah!!'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Username atau password salah!'); window.location.href='login.php';</script>";
        }
        $stmt->close();
    } else {
        echo "Query error: " . $conn->error;
    }
    $conn->close();
}
?>
