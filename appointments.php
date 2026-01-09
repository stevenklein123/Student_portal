<?php
session_start();
require 'function/db.php'; 

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_role'] !== 'student') {
    header("Location: log-in.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = :sid");
$stmt->execute(['sid' => $_SESSION['student_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: log-in.php");
    exit;
}

$stmtHist = $pdo->prepare("SELECT * FROM appointments WHERE student_id = ? ORDER BY created_at DESC");
$stmtHist->execute([$_SESSION['student_id']]);
$history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

$app_stat = $user['appointment_status'] ?? '';
$can_book = empty($app_stat) || in_array($app_stat, ['Rejected', 'Done', 'Cancelled']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP-SIS | Online Appointments</title>
    <link rel="icon" type="image/png" href="assets/PUPLogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .pup-maroon { color: #800000; }
        /* Ginawa nating full height ang body para sa vertical centering */
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .tab-btn { padding-bottom: 1rem; border-bottom: 2px solid transparent; transition: all 0.3s ease; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; cursor: pointer; }
        .tab-btn.active { border-bottom: 2px solid #800000; color: #800000; }
        .nav-active { border-bottom: 2px solid #800000; color: #111827; }
        #history-table-body tr { transition: background-color 0.5s ease; }
        
        /* Main wrapper para sa centering */
        .main-content { flex: 1; display: flex; align-items: center; justify-content: center; width: 100%; padding: 20px; }
    </style>
</head>
<body>

    <nav class="bg-white border-b border-gray-200 px-6 py-2 flex items-center justify-between sticky top-0 z-50 shadow-sm w-full">
        <div class="flex items-center space-x-4">
            <img src="assets/PUPLogo.png" alt="PUP Logo" class="w-8 h-8">
            <span class="text-xl font-black pup-maroon tracking-tighter">PUPSIS</span>
            <div class="hidden lg:flex space-x-6 text-[10px] font-bold text-gray-400 uppercase ml-6">
                <a href="student_dashboard.php" class="hover:text-red-700 transition">Home</a>
                <a href="appointments.php" class="nav-active pb-1">Online Appointments</a>
                <a href="faqs.php" class="hover:text-red-700 transition">FAQs</a>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="function/logout.php" class="text-gray-400 hover:text-red-600 transition p-2 bg-gray-50 rounded-full">
                <i class="fa-solid fa-power-off text-xs"></i>
            </a>
        </div>
    </nav>

    <main class="main-content">
        <div class="max-w-4xl w-full">
            
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Online Appointments</h1>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic mt-2">Manage your scholarship schedules</p>
            </div>

            <div class="flex justify-center space-x-12 mb-8 border-b border-gray-200">
                <button onclick="showTab('book')" id="btn-book" class="tab-btn active">1. Book New</button>
                <button onclick="showTab('my')" id="btn-my" class="tab-btn">2. Appointment History</button>
            </div>

            <div id="tab-book" class="tab-content">
                <div class="max-w-xl mx-auto bg-white rounded-sm border border-gray-200 shadow-sm p-8 md:p-10">
                    <div id="booking-container">
                        <?php if ($can_book): ?>
                            <form action="function/appointment_function.php" method="POST" class="space-y-6">
                                <div class="space-y-3">
                                    <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest italic">Selected Service</h4>
                                    <select name="service_type" required class="w-full border border-gray-200 p-3 rounded-sm bg-gray-50 text-sm outline-none focus:ring-1 focus:ring-[#800000]">
                                        <option value="Scholarship Interview">Scholarship Interview</option>
                                        <option value="Scholarship Renewal">Scholarship Renewal</option>
                                        <option value="Document Verification">Document Verification</option>
                                    </select>
                                </div>
                                <div class="space-y-3">
                                    <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest italic">Choose Date and Time</h4>
                                    <input type="datetime-local" name="appointment_date" required class="w-full border border-gray-200 p-3 rounded-sm bg-gray-50 text-sm outline-none focus:ring-1 focus:ring-[#800000]">
                                </div>
                                <button type="submit" class="w-full bg-[#800000] text-white py-4 rounded-sm font-bold text-[11px] uppercase tracking-[0.2em] hover:bg-black transition shadow-md">
                                    Confirm Appointment
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100">
                                    <i class="fa-solid fa-hourglass-half text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-700 uppercase tracking-tighter italic">Ongoing Request</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2">You currently have an active appointment being processed.</p>
                                <button onclick="showTab('my')" class="mt-8 text-[#800000] text-[10px] font-black uppercase underline tracking-[0.2em]">View Status &rarr;</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="tab-my" class="tab-content hidden">
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden max-w-2xl mx-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Service</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Schedule</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="divide-y divide-gray-50">
                            <?php if (count($history) > 0): ?>
                                <?php foreach ($history as $row): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-6">
                                        <?php 
                                            $s = $row['status'];
                                            $badge = 'bg-amber-100 text-amber-600 border-amber-200';
                                            if ($s === 'Approved') $badge = 'bg-green-100 text-green-600 border-green-200';
                                            if ($s === 'Rejected') $badge = 'bg-red-100 text-red-600 border-red-200';
                                            if ($s === 'Done') $badge = 'bg-blue-100 text-blue-600 border-blue-200';
                                            if ($s === 'Cancelled') $badge = 'bg-gray-100 text-gray-500 border-gray-200';
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-sm <?php echo $badge; ?> text-[9px] font-black uppercase tracking-widest border italic">
                                            <?php echo $s; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-[11px] font-bold text-gray-700 uppercase italic">
                                        <?php echo htmlspecialchars($row['service_type']); ?>
                                    </td>
                                    <td class="px-6 py-6">
                                        <p class="text-[11px] font-bold text-gray-800"><?php echo date('M j, Y', strtotime($row['appointment_date'])); ?></p>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase"><?php echo date('g:i A', strtotime($row['appointment_date'])); ?></p>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-16 text-center text-gray-300 text-[10px] uppercase tracking-widest italic">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="mt-12 text-center opacity-50">
                <p class="text-[9px] text-gray-400 uppercase tracking-[0.4em] font-bold italic">© 2026 Polytechnic University of the Philippines</p>
            </footer>
        </div>
    </main>

    <script>
        function showTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + id).classList.remove('hidden');
            document.getElementById('btn-' + id).classList.add('active');
        }

        function refreshData() {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newHistory = doc.getElementById('history-table-body').innerHTML;
                    document.getElementById('history-table-body').innerHTML = newHistory;
                    
                    const bookingContainer = document.getElementById('booking-container');
                    const newBookingHTML = doc.getElementById('booking-container').innerHTML;
                    const isTyping = document.activeElement.tagName === 'INPUT' || 
                                     document.activeElement.tagName === 'SELECT';

                    if (!isTyping && bookingContainer.innerHTML.trim() !== newBookingHTML.trim()) {
                        bookingContainer.innerHTML = newBookingHTML;
                    }
                })
                .catch(err => console.warn('Sync error:', err));
        }

        setInterval(refreshData, 5000);

        const params = new URLSearchParams(window.location.search);
        if(params.get('tab') === 'my') { showTab('my'); }
    </script>
</body>
</html>