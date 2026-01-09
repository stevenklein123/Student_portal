<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $studentId = $_SESSION['student_id'];
    $docType = $_POST['doc_type'];
    
    // ENSURE UPLOADS DIRECTORY EXISTS
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // VALIDATE FILE
    if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server limits',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limits',
            UPLOAD_ERR_PARTIAL => 'File upload incomplete',
            UPLOAD_ERR_NO_FILE => 'No file selected',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporary directory error',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by extension'
        ];
        $errorMsg = $uploadErrors[$_FILES['document']['error']] ?? 'Unknown error';
        header("Location: ../student_dashboard.php?error=upload_failed&msg=" . urlencode($errorMsg));
        exit;
    }

    $fileExtension = strtolower(pathinfo($_FILES["document"]["name"], PATHINFO_EXTENSION));
    
    // ALLOWED FILE TYPES
    $allowed = ["pdf", "jpg", "jpeg", "png", "webp"];
    if (!in_array($fileExtension, $allowed)) {  
        header("Location: ../student_dashboard.php?error=invalid_format");
        exit;
    }

    // FILE SIZE CHECK (Max 10MB)
    if ($_FILES['document']['size'] > 10 * 1024 * 1024) {
        header("Location: ../student_dashboard.php?error=file_too_large");
        exit;
    }

    $newFileName = $studentId . "_" . $docType . "_" . time() . "." . $fileExtension;
    $targetFilePath = $targetDir . $newFileName;

    // MOVE FILE
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $targetFilePath)) {
        
        $statusColumn = $docType . "_status";
        $fileColumn = $docType . "_file";

        try {
            // Log the update attempt
            error_log("Updating: $statusColumn and $fileColumn for student: $studentId");
            
            // UPDATE DATABASE
            $sql = "UPDATE users SET 
                    $statusColumn = 'For Verification', 
                    $fileColumn = :file_path 
                    WHERE student_id = :sid";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'file_path' => 'uploads/' . $newFileName,
                'sid' => $studentId
            ]);

            $rowCount = $stmt->rowCount();
            error_log("Update result: " . ($result ? "SUCCESS" : "FAILED") . ", Rows affected: $rowCount");

            // VERIFY UPDATE
            if ($result && $rowCount > 0) {
                error_log("Upload successful for student: $studentId, doc_type: $docType");
                // Add timestamp to force page reload and bypass cache
                header("Location: ../student_dashboard.php?upload=success&t=" . time());
            } else {
                // File was uploaded but database didn't update
                error_log("Database update failed - no rows affected");
                @unlink($targetFilePath); // Delete the uploaded file
                header("Location: ../student_dashboard.php?error=database_update_failed&t=" . time());
            }
            exit;
        } catch (PDOException $e) {
            error_log("Upload Error: " . $e->getMessage());
            @unlink($targetFilePath); // Delete the uploaded file
            header("Location: ../student_dashboard.php?error=database_error");
            exit;
        }
    } else {
        error_log("File move failed: " . $targetFilePath);
        header("Location: ../student_dashboard.php?error=upload_failed");
        exit;
    }
} else {
    header("Location: ../student_dashboard.php");
    exit;
}
?>