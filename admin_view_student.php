<?php
session_start();
require 'function/db.php'; 

$id = $_GET['id'] ?? '';

// Kunin ang detalye ng estudyante
$stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student not found.");
}

// UPDATE LOGIC
if (isset($_POST['update_status'])) {
    $doc_type = $_POST['doc_type']; 
    $new_status = $_POST['update_status']; 
    
    $allowed = ['grades_status', 'cor_status', 'recognition_status'];
    if (in_array($doc_type, $allowed)) {
        $update = $pdo->prepare("UPDATE users SET $doc_type = ? WHERE student_id = ?");
        $update->execute([$new_status, $id]);
        
        header("Location: admin_view_student.php?id=$id&msg=updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review: <?php echo htmlspecialchars($student['full_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#f4f7f6] font-sans min-h-screen">

    <nav class="bg-[#800000] text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="assets/PUPLogo.png" alt="PUP Logo" class="w-10 h-10 border-2 border-white rounded-full">
                <h1 class="font-black tracking-tighter text-xl">PUPSIS ADMIN</h1>
            </div>
            <a href="function/logout.php" class="bg-black/20 hover:bg-black/40 px-4 py-2 rounded text-xs font-bold transition">
                <i class="fa-solid fa-power-off mr-2"></i> Exit
            </a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-10 px-6 md:px-10">
        <div class="bg-white p-8 shadow-2xl rounded-xl border border-gray-100">
        
        <div class="flex justify-between items-center mb-8 border-b pb-6">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Folder: <?php echo htmlspecialchars($student['full_name']); ?></h2>
                <p class="text-xs text-slate-400 uppercase tracking-[0.2em] mt-1">STUDENT ID: <?php echo htmlspecialchars($student['student_id']); ?></p>
            </div>
            <a href="admin_dashboard.php" class="bg-[#800000] text-white px-5 py-2 rounded shadow hover:bg-black transition flex items-center text-xs font-bold">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="space-y-4">
            <?php 
            $docs = [
                ['label' => 'Grades Report', 'key' => 'grades_status', 'file' => $student['grades_file'] ?? ''],
                ['label' => 'Certificate of Registration (COR)', 'key' => 'cor_status', 'file' => $student['cor_file'] ?? ''],
                ['label' => 'Certificate of Recognition', 'key' => 'recognition_status', 'file' => $student['recognition_file'] ?? '']
            ];

            foreach ($docs as $d):
                $current_status = trim($student[$d['key']] ?? '');
                $file_path = trim($d['file'] ?? '');
                
                // FIXED: Gagana na ito kahit anong extension (PDF/JPG) basta may file path
                $is_finalized = (strtoupper($current_status) == 'VERIFIED' || strtoupper($current_status) == 'REJECTED');
            ?>
            <div class="flex flex-col md:flex-row md:items-center justify-between p-6 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all">
                <div class="mb-4 md:mb-0">
                    <p class="font-black text-slate-700 uppercase text-xs tracking-wider mb-2"><?php echo $d['label']; ?></p>
                    <div class="flex items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase mr-2">Status:</span>
                        <span class="px-3 py-1 rounded text-[10px] font-black uppercase <?php 
                            if (strtoupper($current_status) == 'VERIFIED') echo 'bg-emerald-100 text-emerald-700';
                            elseif (strtoupper($current_status) == 'REJECTED') echo 'bg-rose-100 text-rose-700';
                            else echo 'bg-orange-100 text-orange-600'; 
                        ?>">
                            <?php echo $current_status ?: 'For Verification'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="mr-2">
                        <?php if(!empty($file_path)): ?>
                            <a href="<?php echo $file_path; ?>" target="_blank" 
                               class="text-blue-600 hover:text-blue-800 text-xs font-bold flex items-center group bg-blue-50 px-3 py-2 rounded-lg border border-blue-100 transition">
                               <i class="fa-solid fa-file-export mr-2 text-xl group-hover:scale-110 transition"></i> Preview
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!$is_finalized): ?>
                        <form method="POST" class="flex gap-2">
                            <input type="hidden" name="doc_type" value="<?php echo $d['key']; ?>">
                            <button name="update_status" value="Verified" 
                                    class="bg-emerald-500 text-white px-5 py-2.5 rounded-lg text-[10px] font-black uppercase hover:bg-emerald-600 shadow-sm transition active:scale-95">
                                Approve
                            </button>
                            <button name="update_status" value="Rejected" 
                                    class="bg-rose-500 text-white px-5 py-2.5 rounded-lg text-[10px] font-black uppercase hover:bg-rose-600 shadow-sm transition active:scale-95">
                                Reject
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="flex items-center text-slate-400 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                            <i class="fa-solid fa-lock text-slate-300 mr-2 text-xs"></i>
                            <span class="text-[10px] font-bold uppercase italic">Response Recorded</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <p class="mt-10 text-center text-slate-300 text-[9px] uppercase tracking-[0.4em] font-bold">
            PUP Student Information System Admin
        </p>
        </div>
    </div>
</body>
<script>
function refreshDocStatus() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    fetch(`admin_view_student.php?id=${id}`)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newDocs = doc.querySelector('.space-y-4').innerHTML;
            document.querySelector('.space-y-4').innerHTML = newDocs;
        });
}
setInterval(refreshDocStatus, 5000);
</script>
</html>