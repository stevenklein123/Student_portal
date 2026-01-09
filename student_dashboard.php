<?php
session_start();
require 'function/db.php'; 

// 1. SECURITY CHECK
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_role'] !== 'student') {
    header("Location: log-in.php");
    exit;
}

// 2. FETCH USER DATA
$stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = :sid");
$stmt->execute(['sid' => $_SESSION['student_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: log-in.php");
    exit;
}

// --- DATABASE AUTO-UPDATE LOGIC ---

// A. Kalkulahin ang Document Progress (Max 50%)
$docs_fields = ['grades_status', 'cor_status', 'recognition_status'];
$verified_count = 0;
foreach ($docs_fields as $field) {
    if (($user[$field] ?? '') === 'Verified') {
        $verified_count++;
    }
}

// Logic: 3 verified docs = 50%. Each verified doc = 16.67% (50 / 3)
if ($verified_count === 0) {
    $doc_progress = 0; // Start at 0% if no documents verified
} else {
    $doc_progress = ($verified_count / 3) * 50; // 16.67% per document
}

// B. Kalkulahin ang Appointment Progress (Another 50%)
$appointment_progress = 0;
if (!empty($user['appointment_date']) && $user['appointment_date'] !== NULL) {
    $appointment_progress = 50; 
}

$total_calculated_progress = $doc_progress + $appointment_progress;

// C. Tukuyin ang Application Step Name
$new_step_text = "Step 1: Document Submission";
if ($total_calculated_progress >= 50 && $total_calculated_progress < 100) {
    $new_step_text = "Step 2: For Appointment/Interview";
} elseif ($total_calculated_progress >= 100) {
    $new_step_text = "Step 3: Process Completed";
}

// D. I-UPDATE ANG DATABASE (Sync only if changed)
if ($total_calculated_progress != $user['progress_percentage'] || $new_step_text != $user['application_step']) {
    $update_stmt = $pdo->prepare("UPDATE users SET progress_percentage = :prog, application_step = :step WHERE student_id = :sid");
    $update_stmt->execute([
        'prog' => $total_calculated_progress,
        'step' => $new_step_text,
        'sid'  => $_SESSION['student_id']
    ]);
    
    // Refresh local variables para updated ang display
    $user['progress_percentage'] = $total_calculated_progress;
    $user['application_step'] = $new_step_text;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP-SIS | Student Dashboard</title>
    <title>PUP-SIS | Home</title>
    <link rel="icon" type="image/png" href="assets/PUPLogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .pup-maroon { color: #800000; }
        .bg-pup-maroon { background-color: #800000; }
        body { background-color: #f4f7f6; }
        .main-container { min-height: calc(100vh - 120px); }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta http-equiv="refresh" content="5">
</head>
<body class="font-sans">

    <?php
    // Show alerts for upload status
    if (isset($_GET['upload']) && $_GET['upload'] === 'success') {
        echo "<script>
            // Show success alert
            Swal.fire({
                icon: 'success',
                title: 'Upload Successful!',
                text: 'Your document has been uploaded and is now awaiting verification.',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                // Force page reload after alert closes
                window.location.href = 'student_dashboard.php';
            });
        </script>";
    } elseif (isset($_GET['error'])) {
        $errorMsg = $_GET['error'];
        $customMsg = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
        $messages = [
            'invalid_format' => 'Invalid file format. Allowed: PDF, JPG, PNG, WebP',
            'upload_failed' => 'File upload failed: ' . $customMsg,
            'database_error' => 'Database error occurred. Please try again.',
            'database_update_failed' => 'Failed to update database. Please check console logs.',
            'file_too_large' => 'File is too large. Maximum size is 10MB.',
        ];
        $msg = $messages[$errorMsg] ?? 'An error occurred.';
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: '$msg'
            });
        </script>";
    }
    ?>

    <nav class="bg-white border-b border-gray-200 px-6 py-2 flex items-center justify-between sticky top-0 z-50 shadow-sm">
        <div class="flex items-center space-x-4">
            <img src="assets/PUPLogo.png" alt="PUP Logo" class="w-8 h-8">
            <span class="text-xl font-black pup-maroon tracking-tighter">PUPSIS</span>
            <div class="hidden lg:flex space-x-6 text-[10px] font-bold text-gray-400 uppercase ml-6">
                <a href="#" class="text-gray-900 border-b-2 border-[#800000] pb-1">Home</a>
                <a href="appointments.php" class="hover:text-red-700 transition">Online Appointments</a>
                <a href="faqs.php" class="hover:text-red-700 transition">FAQs</a>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="log-in.php" class="text-gray-400 hover:text-red-600 transition p-2 bg-gray-50 rounded-full">
                <i class="fa-solid fa-power-off text-xs"></i>
            </a>
        </div>
    </nav>

    <div class="main-container flex flex-col justify-center py-12 px-4">
        <div class="max-w-5xl mx-auto w-full">
            
            <div class="mb-8 text-center lg:text-left">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Student Dashboard</h1>
                <div class="bg-white shadow-sm border border-gray-200 rounded-sm p-5 mt-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-[#b32d2d] font-bold text-base uppercase tracking-wide">
                        <i class="fa-solid fa-user-graduate mr-2"></i> <?php echo htmlspecialchars($user['full_name']); ?>
                    </p>
                    <span class="text-[10px] bg-green-100 text-green-700 px-4 py-1.5 rounded-full font-bold uppercase tracking-widest border border-green-200">Account Active</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-xs font-bold text-gray-600 uppercase tracking-widest italic flex items-center">
                                <i class="fa-solid fa-file-invoice mr-2 text-gray-400"></i> Online Requirements
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[12px]">
                                <thead class="bg-gray-50 text-gray-400 uppercase border-b border-gray-100">
                                    <tr>
                                        <th class="py-4 px-6 tracking-wider">Document Type</th>
                                        <th class="py-4 px-6 text-center tracking-wider">Status</th>
                                        <th class="py-4 px-6 text-right tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php 
                                    $docs = [
                                        ['name' => 'Student Grades Report', 'key' => 'grades'],
                                        ['name' => 'Certificate of Registration (COR)', 'key' => 'cor'],
                                        ['name' => 'Certification of Recognition', 'key' => 'recognition']
                                    ];
                                    foreach($docs as $doc): 
                                        $status = $user[$doc['key'].'_status'] ?? 'Pending';
                                    ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-5 px-6 font-bold text-gray-700"><?php echo $doc['name']; ?></td>
                                        <td class="py-5 px-6 text-center">
                                            <span class="px-3 py-1 rounded-sm font-bold italic underline <?php echo $status == 'Verified' ? 'text-green-600' : 'text-orange-500'; ?>">
                                                <?php echo $status; ?>
                                            </span>
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <form action="function/upload_function.php" method="POST" enctype="multipart/form-data" class="flex justify-end items-center space-x-2">
                                                <input type="hidden" name="doc_type" value="<?php echo $doc['key']; ?>">
                                                <input type="file" id="file_<?php echo $doc['key']; ?>" name="document" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="hidden" onchange="this.closest('form').submit();">
                                                <label for="file_<?php echo $doc['key']; ?>" class="cursor-pointer bg-white border border-gray-300 hover:border-red-800 p-2 rounded transition shadow-sm inline-block">
                                                    <i class="fa-solid fa-file-pdf text-red-600 text-lg"></i>
                                                </label>
                                                <button type="submit" class="bg-[#800000] text-white px-4 py-2 rounded-sm hover:bg-black transition shadow-sm text-[12px] font-bold uppercase">
                                                    <i class="fa-solid fa-upload mr-2"></i> Upload
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-sm shadow-sm p-6">
                    <h3 class="text-xs font-bold text-gray-600 uppercase border-b border-gray-100 pb-3 mb-5 italic">⏳ Application Progress</h3>
                    <div class="mb-6">
                        <div class="flex justify-between text-[11px] font-bold mb-2">
                            <span class="pup-maroon uppercase italic"><?php echo htmlspecialchars($user['application_step']); ?></span>
                            <span class="text-gray-400"><?php echo $user['progress_percentage']; ?>%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full transition-all duration-700 shadow-sm" 
                                 style="width: <?php echo $user['progress_percentage']; ?>%"></div>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-[10px] uppercase font-bold">
                            <i class="fa-solid <?php echo ($verified_count === 3) ? 'fa-circle-check text-green-500' : 'fa-circle-dot text-gray-300'; ?> mr-2"></i>
                            <span class="<?php echo ($verified_count === 3) ? 'text-gray-700' : 'text-gray-400'; ?>">Requirements (50%)</span>
                        </div>
                        <div class="flex items-center text-[10px] uppercase font-bold">
                            <i class="fa-solid <?php echo ($appointment_progress === 50) ? 'fa-circle-check text-green-500' : 'fa-circle-dot text-gray-300'; ?> mr-2"></i>
                            <span class="<?php echo ($appointment_progress === 50) ? 'text-gray-700' : 'text-gray-400'; ?>">Appointment (50%)</span>
                        </div>
                    </div>

                    <?php if ($verified_count < 3): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <p class="text-[11px] text-blue-700 leading-relaxed italic italic">
                                <b>Requirement:</b> Please ensure all 3 documents are <b>Verified</b> by the admin to reach 50%.
                            </p>
                        </div>
                    <?php elseif ($user['progress_percentage'] < 100): ?>
                        <div class="bg-orange-50 border-l-4 border-orange-400 p-4">
                            <p class="text-[11px] text-orange-700 leading-relaxed italic">
                                <b>Action Required:</b> Requirements verified! Go to <b>Online Appointments</b> to book your interview.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="bg-green-50 border-l-4 border-green-400 p-4">
                            <p class="text-[11px] text-green-700 leading-relaxed italic">
                                <b>Done:</b> You have successfully completed all steps for your application.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="mt-20 text-center border-t border-gray-200 pt-8 mb-10">
                <p class="text-[9px] text-gray-400 uppercase tracking-[0.4em] font-bold italic">© 2026 Polytechnic University of the Philippines</p>
            </footer>
        </div>
    </div>
</body>
</html>