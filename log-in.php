<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/PUPLogo.png">
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .pup-maroon { color: #800000; }
        .pup-bg-maroon { background-color: #800000; }
    </style>
</head>
<body class="bg-[#f3f4f6] h-screen flex flex-col items-center justify-center p-4 font-sans">

    <div class="bg-[#f9f9f9] border border-gray-300 border-l-[6px] border-l-[#800000] rounded shadow-lg w-full max-w-[450px] overflow-hidden">
        
        <div class="p-10 text-center">
            <div class="flex justify-center mb-6">
                <img src="assets/PUPLogo.png" alt="PUP Logo" class="w-24 h-24 object-contain">
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">PUP-SIS Student Module <span class="text-xs font-normal text-gray-400 italic">beta</span></h1>
            <p class="text-xs text-gray-500 mt-1 uppercase font-bold tracking-wider">Sign in to start your session</p>

            <form action="function/login_function.php" method="POST" class="mt-8 space-y-4">
                <div class="text-left">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1 ml-1">Student Number</label>
                    <input type="text" name="student_id" placeholder="0000-00000-MN-0" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded shadow-inner focus:outline-none focus:border-[#800000] bg-white text-sm transition-all">
                </div>

                <div class="text-left">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1 ml-1">Password</label>
                    <input type="password" name="password" placeholder="Enter password" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded shadow-inner focus:outline-none focus:border-[#800000] bg-white text-sm transition-all">
                </div>

                <button type="submit" 
                    class="w-full pup-bg-maroon hover:bg-red-900 text-white font-bold py-2.5 rounded shadow-md transition-all uppercase text-xs mt-2 active:scale-95">
                    Sign in
                </button>
            </form>

            <div class="mt-8 pt-4 border-t border-gray-200">
                <a href="#" class="text-[#d32f2f] hover:underline text-[11px] font-bold uppercase tracking-tight">I forgot my password</a>
            </div>
        </div>
    </div>
    
    <p class="mt-6 text-[10px] text-gray-400 uppercase tracking-[0.2em] text-center font-medium">
        © 2026 Polytechnic University of the Philippines
    </p>

</body>
</html>