<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DuitTracker | Access</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(63, 63, 70, 0.4);
        }
    </style>
</head>

<body class="bg-[#09090b] text-zinc-100 antialiased selection:bg-blue-500/30">
    <div
        class="fixed top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-blue-600/10 blur-[120px] -z-10 pointer-events-none">
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <a href="/" class="text-3xl font-extrabold tracking-tight text-white">
                Duit<span
                    class="bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">Tracker.</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-8 p-8 glass rounded-[2.5rem] shadow-2xl shadow-blue-500/5">
            {{ $slot }}
        </div>

        <p class="mt-8 text-zinc-600 text-[10px] font-bold uppercase tracking-[0.2em]">Secure Authentication System</p>
    </div>
</body>

</html>