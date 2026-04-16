<x-guest-layout>
    <h2 class="text-xl font-bold text-white mb-2">Buat Akun.</h2>
    <p class="text-zinc-500 text-xs mb-8">Mulai optimalkan manajemen keuangan Kamu hari ini.</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase tracking-widest">Full Name</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus
                class="w-full bg-black/40 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition text-sm text-zinc-200">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-rose-500 text-xs" />
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase tracking-widest">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required
                class="w-full bg-black/40 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition text-sm text-zinc-200">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-xs" />
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase tracking-widest">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full bg-black/40 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition text-sm text-zinc-200">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 text-xs" />
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase tracking-widest">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full bg-black/40 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition text-sm text-zinc-200">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-500 text-xs" />
        </div>

        <div class="pt-4 space-y-4">
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white p-4 rounded-2xl font-bold transition duration-300 shadow-lg shadow-blue-900/30 active:scale-[0.98]">
                Buat Akun Sekarang
            </button>

            <p class="text-center text-xs text-zinc-500">
                Sudah punya akun? <a href="{{ route('login') }}"
                    class="text-blue-400 font-bold hover:underline">Masuk</a>
            </p>
        </div>
    </form>
</x-guest-layout>