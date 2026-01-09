<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['student_id'])) {
        header("Location: ../log-in.php");
        exit;
    }

    $student_id = $_SESSION['student_id'];

    try {
        // Palitan ang status sa 'Cancelled' para may history pa rin
        $stmt = $pdo->prepare("UPDATE users SET appointment_status = 'Cancelled' WHERE student_id = :sid");
        $stmt->execute(['sid' => $student_id]);

        header("Location: ../appointments.php?tab=my");
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>