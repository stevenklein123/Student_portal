<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['student_id'])) {
        header("Location: ../log-in.php");
        exit;
    }

    $student_id = $_SESSION['student_id'];
    $service_type = $_POST['service_type'];
    $appointment_date = $_POST['appointment_date'];

    if (!empty($service_type) && !empty($appointment_date)) {
        try {
            $pdo->beginTransaction();

            // 1. I-insert sa history table
            $stmt1 = $pdo->prepare("INSERT INTO appointments (student_id, service_type, appointment_date, status) VALUES (?, ?, ?, 'Pending')");
            $stmt1->execute([$student_id, $service_type, $appointment_date]);

            // 2. I-update ang users table (Main status)
            $stmt2 = $pdo->prepare("UPDATE users SET appointment_date = :adate, service_type = :stype, appointment_status = 'Pending' WHERE student_id = :sid");
            $stmt2->execute([
                'adate' => $appointment_date,
                'stype' => $service_type,
                'sid' => $student_id
            ]);

            $pdo->commit();
            header("Location: ../appointments.php?tab=my&msg=success");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>