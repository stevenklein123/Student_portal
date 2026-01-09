<?php
session_start();
require 'db.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Unauthorized Access");
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $sid = $_GET['id'];
    $status = $_GET['status'];

    try {
        // Simulan ang Transaction para siguradong parehong table ang ma-uupdate
        $pdo->beginTransaction();

        // A. I-update ang main status sa USERS table (para sa Admin Dashboard view)
        $stmt1 = $pdo->prepare("UPDATE users SET appointment_status = :status WHERE student_id = :sid");
        $stmt1->execute(['status' => $status, 'sid' => $sid]);

        // B. I-update ang status sa APPOINTMENTS table (para sa Student History)
        // Hinahanap natin ang pinakabagong appointment record ng student na ito
        $stmt2 = $pdo->prepare("UPDATE appointments 
                                SET status = :status 
                                WHERE student_id = :sid 
                                ORDER BY created_at DESC LIMIT 1");
        $stmt2->execute(['status' => $status, 'sid' => $sid]);

        // I-save ang lahat ng changes
        $pdo->commit();

        $msg = "Appointment status updated to $status.";
        header("Location: ../admin_dashboard.php?success=" . urlencode($msg));
        exit;

    } catch (Exception $e) {
        // Kapag may error, bawiin lahat ng changes (Rollback)
        $pdo->rollBack();
        die("Error updating status: " . $e->getMessage());
    }
} else {
    header("Location: ../admin_dashboard.php");
    exit;
}
?>