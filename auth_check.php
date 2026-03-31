<?php
session_start();

if (!isset($_SESSION['climber_id'])) {
    header('Location: login.html');
    exit;
}
?>
