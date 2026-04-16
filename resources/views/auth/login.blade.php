<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-xl font-bold text-white mb-2">Selamat Datang.</h2>
    <p class="text-zinc-500 text-xs mb-8">Masuk ke akun Anda untuk mengelola laporan keuangan.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase tracking-widest">Email Address</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                class="w-full bg-black/40 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition text-sm text-zinc-200">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-xs" />
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase tracking-widest">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full bg-black/40 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition text-sm text-zinc-200">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 text-xs" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded-lg bg-zinc-900 border-zinc-800 text-blue-600 focus:ring-blue-500/20" name="remember">
                <span class="ms-2 text-xs text-zinc-500 font-medium">Tetap masuk di perangkat ini</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-xs text-zinc-500 hover:text-blue-400 font-medium transition"
                    href="{{ route('password.request') }}">Lupa pass?</a>
            @endif
        </div>

        <div class="space-y-4">
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white p-4 rounded-2xl font-bold transition duration-300 shadow-lg shadow-blue-900/30 active:scale-[0.98]">
                Masuk Sekarang
            </button>

            <p class="text-center text-xs text-zinc-500">
                Belum punya akun? <a href="{{ route('register') }}"
                    class="text-blue-400 font-bold hover:underline">Daftar</a>
            </p>
        </div>
    </form>
</x-guest-layout>