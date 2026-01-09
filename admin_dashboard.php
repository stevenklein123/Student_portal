<?php
session_start();
require 'function/db.php'; 

$_SESSION['role'] = 'admin'; 
$search = $_GET['search'] ?? '';

try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'student' AND (full_name LIKE :s OR student_id LIKE :s) ORDER BY full_name ASC");
        $stmt->execute(['s' => "%$search%"]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'student' ORDER BY full_name ASC");
        $stmt->execute();
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP-SIS | Admin Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .pup-maroon { color: #800000; }
        .bg-pup-maroon { background-color: #800000; }
        .update-flash { animation: flash 1s ease-out; }
        @keyframes flash {
            0% { background-color: #fef3c7; }
            100% { background-color: transparent; }
        }
    </style>
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

    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">Student Applicant Folders</h2>
                <p class="text-xs text-gray-400 uppercase tracking-[0.2em]">Masterlist of Applications</p>
            </div>

            <form method="GET" id="searchForm" class="flex w-full md:w-96 shadow-sm">
                <input type="text" name="search" id="searchInput" placeholder="Search Name or Student ID..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       class="w-full px-4 py-2 rounded-l border border-gray-300 focus:ring-1 focus:ring-[#800000] outline-none text-sm">
                <button type="submit" class="bg-[#800000] text-white px-5 py-2 rounded-r hover:bg-black transition">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200 text-[11px] font-black uppercase text-gray-400 tracking-widest">
                    <tr>
                        <th class="p-5">Applicant Name</th>
                        <th class="p-5">Grades</th>
                        <th class="p-5">COR</th>
                        <th class="p-5">Recognition</th> 
                        <th class="p-5">Schedule Status</th>
                        <th class="p-5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="admin-table-body" class="divide-y divide-gray-100">
                    <?php foreach ($students as $s): ?>
                    <tr class="hover:bg-blue-50/30 transition text-[11px]" id="row-<?php echo $s['student_id']; ?>">
                        <td class="p-5 flex items-center space-x-3">
                            <i class="fa-solid fa-folder text-amber-500 text-xl"></i>
                            <div>
                                <p class="font-bold text-gray-800 uppercase"><?php echo htmlspecialchars($s['full_name']); ?></p>
                                <p class="text-[10px] text-gray-400"><?php echo htmlspecialchars($s['student_id']); ?></p>
                            </div>
                        </td>
                        <td class="p-5 italic text-gray-500"><?php echo $s['grades_status'] ?? 'Pending'; ?></td>
                        <td class="p-5 italic text-gray-500"><?php echo $s['cor_status'] ?? 'Pending'; ?></td>
                        <td class="p-5 italic text-gray-500"><?php echo $s['recognition_status'] ?? 'Pending'; ?></td>
                        
                        <td class="p-5">
                            <?php if (!empty($s['appointment_date'])): ?>
                                <div class="flex items-center justify-between min-w-[150px]">
                                    <div>
                                        <p class="font-bold text-gray-700"><?php echo date('M d, Y', strtotime($s['appointment_date'])); ?></p>
                                        <?php 
                                            $current_status = $s['appointment_status'] ?? 'Pending';
                                            if ($current_status === 'Approved'): 
                                        ?>
                                            <span class="text-[9px] bg-green-100 text-green-600 px-2 py-0.5 rounded font-black uppercase">Approved</span>
                                        <?php elseif ($current_status === 'Done'): ?>
                                            <span class="text-[9px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded font-black uppercase">Completed</span>
                                        <?php elseif ($current_status === 'Rejected'): ?>
                                            <span class="text-[9px] bg-red-100 text-red-600 px-2 py-0.5 rounded font-black uppercase">Rejected</span>
                                        <?php else: ?>
                                            <p class="text-[9px] text-[#800000] font-bold"><?php echo date('g:i A', strtotime($s['appointment_date'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex space-x-1">
                                        <?php if ($current_status === 'Pending'): ?>
                                            <a href="function/update_appointment_status.php?id=<?php echo $s['student_id']; ?>&status=Approved" 
                                               class="w-6 h-6 bg-green-100 text-green-600 rounded flex items-center justify-center hover:bg-green-600 hover:text-white transition shadow-sm">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </a>
                                            <a href="function/update_appointment_status.php?id=<?php echo $s['student_id']; ?>&status=Rejected" 
                                               onclick="return confirm('Reject this appointment?')"
                                               class="w-6 h-6 bg-red-100 text-red-600 rounded flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </a>
                                        <?php elseif ($current_status === 'Approved'): ?>
                                            <a href="function/update_appointment_status.php?id=<?php echo $s['student_id']; ?>&status=Done" 
                                               onclick="return confirm('Mark this transaction as completed?')"
                                               class="w-6 h-6 bg-blue-100 text-blue-600 rounded flex items-center justify-center hover:bg-blue-600 hover:text-white transition shadow-sm">
                                                <i class="fa-solid fa-flag-checkered text-[10px]"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-300 italic">No Schedule</span>
                            <?php endif; ?>
                        </td>

                        <td class="p-5 text-center">
                            <a href="admin_view_student.php?id=<?php echo $s['student_id']; ?>" 
                               class="inline-block bg-[#800000] text-white px-4 py-2 rounded text-[10px] font-black uppercase hover:bg-black transition tracking-widest">
                                Open Folder
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Action Success', text: '<?php echo htmlspecialchars($_GET['success']); ?>', confirmButtonColor: '#800000' });
    </script>
    <?php endif; ?>

    <script>
    function loadApplications() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchVal = urlParams.get('search') || '';

        fetch('admin_dashboard.php?search=' + encodeURIComponent(searchVal))
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableContent = doc.getElementById('admin-table-body').innerHTML;
                const currentTable = document.getElementById('admin-table-body');

                if (currentTable.innerHTML.trim() !== newTableContent.trim()) {
                    currentTable.innerHTML = newTableContent;
                }
            })
            .catch(err => console.warn("Auto-sync error:", err));
    }

    setInterval(() => {
        if (document.activeElement.id !== 'searchInput') {
            loadApplications();
        }
    }, 4000);
    </script>
</body>
</html>