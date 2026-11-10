<?php require __DIR__.'/../koneksi.php';unset($_SESSION['admin_id']);session_regenerate_id(true);redirect('login.php');
