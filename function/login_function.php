<?php
session_start();
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = :sid");
    $stmt->execute(['sid' => $student_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {
        
        if ($user['role'] === 'admin') {
            // ADMIN UNIQUE SESSIONS
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['student_id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = 'admin';
            header("Location: ../admin_dashboard.php");
        } else {
            // STUDENT UNIQUE SESSIONS
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['student_name'] = $user['full_name'];
            $_SESSION['student_role'] = 'student';
            header("Location: ../student_dashboard.php");
        }
        exit;
    } else {
        header("Location: ../log-in.php?error=wrong_credentials");
        exit;
    }
}