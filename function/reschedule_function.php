<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $new_date = $_POST['appointment_date'];

    if (!empty($new_date)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET appointment_date = :adate WHERE student_id = :sid");
            $stmt->execute([
                'adate' => $new_date,
                'sid' => $student_id
            ]);

            $_SESSION['msg'] = "Rescheduled";
            header("Location: ../appointments.php?tab=my");
            exit;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}