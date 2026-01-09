<?php
session_start();
require 'function/db.php'; 

// SECURITY CHECK
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_role'] !== 'student') {
    header("Location: log-in.php");
    exit;
}

// FETCH USER DATA
$stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = :sid");
$stmt->execute(['sid' => $_SESSION['student_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP-SIS | Frequently Asked Questions</title>
    <link rel="icon" type="image/png" href="assets/PUPLogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .pup-maroon { color: #800000; }
        .faq-drawer { margin-bottom: 1rem; border-radius: 8px; background: white; border: 1px solid #e5e7eb; }
        .faq-drawer__trigger { width: 100%; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 600; color: #374151; font-size: 14px; }
        .faq-drawer__content { max-height: 0; overflow: hidden; transition: all 0.3s ease-in-out; background: #f9fafb; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }
        .faq-drawer--opened .faq-drawer__content { max-height: 500px; padding: 1.25rem; border-top: 1px solid #f3f4f6; }
        .faq-drawer--opened i { transform: rotate(180deg); }
        .category-btn.active { background-color: #800000; color: white; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <nav class="bg-white border-b border-gray-200 px-6 py-2 flex items-center justify-between sticky top-0 z-50 shadow-sm">
        <div class="flex items-center space-x-4">
            <img src="assets/PUPLogo.png" alt="PUP Logo" class="w-8 h-8">
            <span class="text-xl font-black pup-maroon tracking-tighter">PUPSIS</span>
            <div class="hidden lg:flex space-x-6 text-[10px] font-bold text-gray-400 uppercase ml-6">
                <a href="student_dashboard.php" class="hover:text-red-700 transition">Home</a>
                <a href="appointments.php" class="hover:text-red-700 transition">Online Appointments</a>
                <a href="faqs.php" class="text-gray-900 border-b-2 border-[#800000] pb-1">FAQs</a>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="function/logout.php" class="text-gray-400 hover:text-red-600 transition p-2 bg-gray-50 rounded-full">
                <i class="fa-solid fa-power-off text-xs"></i>
            </a>
        </div>
    </nav>

    <div class="flex-grow py-16 px-4">
        <div class="max-w-3xl mx-auto">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-[#2d3748] mb-6">Frequently Asked Questions</h1>
                
                <div class="relative max-w-2xl mx-auto mb-8">
                    <input type="text" id="faqSearch" placeholder="Search questions..." class="w-full pl-12 pr-24 py-4 rounded-md border border-gray-200 shadow-sm outline-none focus:ring-2 focus:ring-blue-100">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-[#800000] text-white px-6 py-2.5 rounded-md hover:bg-blue-900 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-4 mb-10">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="bg-[#800000] text-white w-6 h-6 rounded-full flex items-center justify-center mr-3 text-[10px]">1</span>
                    About the Appointment System
                </h3>

                <div class="faq-drawer">
                    <div class="faq-drawer__trigger" onclick="toggleFAQ(this)">
                        What is the purpose of this appointment system?
                        <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
                    </div>
                    <div class="faq-drawer__content text-sm text-gray-600 leading-relaxed">
                        This system is designed for student scholars to book schedules for scholarship-related concerns such as interviews, document verification, and allowance inquiries.
                    </div>
                </div>

                <div class="faq-drawer">
                    <div class="faq-drawer__trigger" onclick="toggleFAQ(this)">
                        Do I need an appointment before visiting the office?
                        <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
                    </div>
                    <div class="faq-drawer__content text-sm text-gray-600 leading-relaxed">
                        Yes. To ensure efficient service and avoid long queues, students are required to secure an appointment slot before visiting the scholarship office.
                    </div>
                </div>
            </div>

            <div class="space-y-4 mb-10">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="bg-[#800000] text-white w-6 h-6 rounded-full flex items-center justify-center mr-3 text-[10px]">1</span>
                    Booking an Appointment
                </h3>

                <div class="faq-drawer">
                    <div class="faq-drawer__trigger" onclick="toggleFAQ(this)">
                        How do I book an appointment?
                        <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
                    </div>
                    <div class="faq-drawer__content text-sm text-gray-600">
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Login to your student account.</li>
                            <li>Navigate to "Online Appointments".</li>
                            <li>Select your required service.</li>
                            <li>Choose your preferred date and time.</li>
                            <li>Confirm your booking.</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-drawer">
                    <div class="faq-drawer__trigger" onclick="toggleFAQ(this)">
                        Can I book more than one appointment?
                        <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
                    </div>
                    <div class="faq-drawer__content text-sm text-gray-600">
                        No. Each student is allowed only one active appointment at a time. You must complete or cancel your current one before booking another.
                    </div>
                </div>
            </div>

            <div class="space-y-4 mb-10">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="bg-[#800000] text-white w-6 h-6 rounded-full flex items-center justify-center mr-3 text-[10px]">2</span>
                    Schedule & Availability
                </h3>

                <div class="faq-drawer">
                    <div class="faq-drawer__trigger" onclick="toggleFAQ(this)">
                        Why are there no available time slots?
                        <i class="fa-solid fa-chevron-down text-xs transition-transform"></i>
                    </div>
                    <div class="faq-drawer__content text-sm text-gray-600">
                        Slots are limited per day. If no slots are available, it means the schedule is full. New slots are typically released at the start of each week.
                    </div>
                </div>
            </div>

            <div class="mt-16 bg-gray-50 border border-gray-200 rounded-lg p-8 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <span class="text-lg font-bold text-gray-700">Still need help?</span>
                <div class="flex space-x-3">
                    <button onclick="openModal()" class="bg-[#800000] text-white px-8 py-3 rounded-md text-[11px] font-bold uppercase tracking-widest hover:bg-black transition shadow-lg">
                        Contact Support
                    </button>
                    <button class="bg-white text-[#800000] px-8 py-3 rounded-md text-[11px] font-bold uppercase tracking-widest border border-[#800000] hover:bg-[#800000] hover:text-white transition shadow-lg">
                        FAQ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="supportModal" class="fixed inset-0 bg-black/50 hidden z-[100] flex items-center justify-center p-4">
        <div class="bg-white rounded-lg w-full max-w-md overflow-hidden shadow-2xl">
            <div class="bg-[#800000] p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-[11px] uppercase tracking-widest">Contact Support</h3>
                <button onclick="closeModal()" class="hover:text-gray-300"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="jsInquiryForm" class="p-8 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Inquiry Category</label>
                    <select id="supportCat" required class="w-full border border-gray-200 p-3 rounded text-sm outline-none focus:border-red-800">
                        <option>Technical Issue</option>
                        <option>Appointment Correction</option>
                        <option>Document Inquiry</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Message</label>
                    <textarea id="supportMsg" rows="4" required class="w-full border border-gray-200 p-3 rounded text-sm outline-none focus:border-red-800 resize-none" placeholder="Describe your concern..."></textarea>
                </div>
                <button type="submit" class="w-full bg-[#800000] text-white py-4 rounded font-bold text-[11px] uppercase tracking-[0.2em] hover:bg-black transition shadow-lg">
                    Submit Message
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleFAQ(trigger) {
            const drawer = trigger.parentElement;
            drawer.classList.toggle('faq-drawer--opened');
        }

        function openModal() { document.getElementById('supportModal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('supportModal').classList.add('hidden'); }

        // JAVASCRIPT SUCCESS HANDLER
        document.getElementById('jsInquiryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const category = document.getElementById('supportCat').value;

            Swal.fire({
                icon: 'success',
                title: 'Inquiry Submitted',
                text: 'Your question about "' + category + '" has been received. Our team will review it shortly.',
                confirmButtonColor: '#800000',
                timer: 4000
            });

            this.reset();
            closeModal();
        });
    </script>

    <footer class="py-10 text-center">
        <p class="text-[9px] text-gray-400 uppercase tracking-[0.4em] font-bold italic opacity-60">© 2026 Polytechnic University of the Philippines</p>
    </footer>
</body>
</html>