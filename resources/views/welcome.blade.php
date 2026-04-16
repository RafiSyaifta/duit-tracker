<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DuitTracker | Personal Finance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(63, 63, 70, 0.4); }
        [x-cloak] { display: none !important; }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .animate-pulse-soft { animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="bg-[#09090b] text-zinc-100 antialiased selection:bg-blue-500/30" x-data="financeApp()">

    <div x-cloak x-show="toast.show" 
         x-transition class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-6 py-4 rounded-2xl glass shadow-2xl ring-1 ring-white/10">
        <p class="text-sm font-bold tracking-tight text-white" x-text="toast.message"></p>
    </div>

    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-blue-600/10 blur-[120px] -z-10 pointer-events-none"></div>

    <div class="max-w-5xl mx-auto p-6 lg:p-10 min-h-screen">
        <header class="flex justify-between items-end py-10">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white mb-2">Duit<span class="text-blue-500">Tracker.</span></h1>
                <p class="text-zinc-500 text-sm italic">Laporan untuk <span class="text-zinc-300 font-bold underline">{{ Auth::user()->name }}</span>.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="exportExcel()" class="glass hover:bg-zinc-800 px-5 py-2.5 rounded-2xl text-xs font-semibold transition">Ekspor</button>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition">Keluar</button></form>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
            <div class="lg:col-span-8 space-y-6">
                <div class="glass p-8 rounded-[2rem] relative overflow-hidden group">
                    <p class="text-zinc-500 text-sm font-semibold uppercase tracking-widest mb-2">Saldo Keseluruhan</p>
                    <h2 class="text-5xl font-extrabold text-white tracking-tight">Rp <span x-text="formatRupiah(totalSaldo())"></span></h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="glass p-6 rounded-[2rem] border-l-4 border-l-emerald-500/50">
                        <p class="text-zinc-500 text-xs font-bold uppercase mb-1">Total Pemasukan</p>
                        <p class="text-xl font-bold text-emerald-400">Rp <span x-text="formatRupiah(totalIncome())"></span></p>
                    </div>
                    <div class="glass p-6 rounded-[2rem] border-l-4 border-l-rose-500/50">
                        <p class="text-zinc-500 text-xs font-bold uppercase mb-1">Total Pengeluaran</p>
                        <p class="text-xl font-bold text-rose-400">Rp <span x-text="formatRupiah(totalExpense())"></span></p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4 glass p-8 rounded-[2rem] flex flex-col items-center justify-center relative">
                <div class="relative w-full aspect-square">
                    <canvas id="myChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Kondisi</span>
                        <span class="text-blue-400 text-sm font-bold" x-text="transactions.length === 0 ? 'Siap Mencatat' : (totalIncome() >= totalExpense() ? 'Aman' : 'Defisit')"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass rounded-[2rem] p-8 mb-10 ring-1 ring-blue-500/20 shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-3">
                <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                <span x-text="isEdit ? 'Perbarui Data' : 'Catatan Baru'"></span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase">Keterangan</label>
                        <input type="text" x-model="formData.title" class="w-full bg-black/50 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 outline-none text-sm text-zinc-100">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="quickAdd('Makan', 'expense')" class="text-[9px] font-bold px-3 py-1.5 rounded-full bg-zinc-800 hover:bg-zinc-700 transition">MAKAN</button>
                        <button @click="quickAdd('Gaji', 'income')" class="text-[9px] font-bold px-3 py-1.5 rounded-full bg-zinc-800 hover:bg-zinc-700 transition">GAJI</button>
                        <button @click="quickAdd('Transport', 'expense')" class="text-[9px] font-bold px-3 py-1.5 rounded-full bg-zinc-800 hover:bg-zinc-700 transition">TRANSPORT</button>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase">Nominal</label>
                    <input type="number" x-model="formData.amount" class="w-full bg-black/50 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 outline-none text-sm font-bold text-blue-400">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-bold ml-2 uppercase">Kategori</label>
                    <select x-model="formData.type" class="w-full bg-black/50 border border-zinc-800 p-4 rounded-2xl focus:border-blue-500 outline-none text-zinc-400 text-sm cursor-pointer">
                        <option value="income">Pemasukan (Kredit)</option>
                        <option value="expense">Pengeluaran (Debit)</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button @click="saveData()" class="flex-1 p-4 rounded-2xl font-bold transition text-sm shadow-lg" :class="isEdit ? 'bg-orange-600 hover:bg-orange-500' : 'bg-blue-600 hover:bg-blue-500'" x-text="isEdit ? 'Update' : 'Catat Transaksi'"></button>
                    <button x-show="isEdit" @click="cancelEdit()" class="bg-zinc-800 p-4 rounded-2xl transition">✕</button>
                </div>
            </div>
        </div>

        <div class="glass rounded-[2rem] overflow-hidden mb-12">
            <div class="p-6 border-b border-zinc-800 flex justify-between items-center bg-zinc-900/30">
                <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-500">Riwayat Keuangan</h3>
                <span class="text-[10px] bg-zinc-800 px-3 py-1 rounded-full text-zinc-400 font-bold" x-text="transactions.length + ' Entri Data'"></span>
            </div>
            
            <div class="overflow-x-auto min-h-[200px]">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-zinc-800/50">
                        <tr x-show="isLoading" class="animate-pulse-soft"><td colspan="3" class="p-10 text-center text-zinc-600">Memuat data...</td></tr>

                        <tr x-show="!isLoading && transactions.length === 0" x-cloak>
                            <td colspan="3" class="p-20 text-center opacity-25">
                                <p class="text-sm font-bold uppercase tracking-widest">Belum ada data tercatat</p>
                            </td>
                        </tr>

                        <template x-for="(item, index) in transactions" :key="item.id || index">
                            <tr class="hover:bg-zinc-800/40 transition group">
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div :class="item.type == 'income' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'" class="p-3 rounded-2xl">
                                            <svg x-show="item.type == 'income'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <svg x-show="item.type == 'expense'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-zinc-100 text-sm" x-text="item.title"></div>
                                            <div class="text-[10px] text-zinc-600 font-bold uppercase" x-text="new Date(item.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'long'})"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="text-base font-extrabold" :class="item.type == 'income' ? 'text-emerald-400' : 'text-rose-400'">
                                        <span x-text="item.type == 'income' ? '+' : '-'"></span> Rp <span x-text="formatRupiah(item.amount)"></span>
                                    </div>
                                </td>
                                <td class="p-6 text-center w-40">
                                    <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition duration-300">
                                        <button @click="editData(item)" class="bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white px-3 py-1.5 rounded-xl text-[10px] font-bold transition">Edit</button>
                                        <button @click="deleteData(item.id)" class="bg-zinc-800 hover:bg-rose-600 text-zinc-500 hover:text-white px-3 py-1.5 rounded-xl text-[10px] font-bold transition">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="text-center py-10 opacity-30"><p class="text-[9px] font-bold tracking-[0.5em] uppercase">Built with precision by Rafi Syaifta &mdash; 2026</p></footer>
    </div>

    <script>
        function financeApp() {
            return {
                transactions: [],
                formData: { title: '', amount: '', type: 'income' },
                isEdit: false,
                editId: null,
                chart: null,
                isLoading: true,
                toast: { show: false, message: '', type: 'success' },

                showToast(msg, type = 'success') {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                },

                quickAdd(title, type) {
                    this.formData.title = title;
                    this.formData.type = type;
                    this.showToast('Kategori dipilih: ' + title);
                },

                async init() {
                    this.isLoading = true;
                    try {
                        const res = await fetch('/api/transactions');
                        const data = await res.json();
                        // Filter super ketat buat ngusir hantu
                        this.transactions = data.filter(t => t.title && t.title.trim().length > 0 && parseFloat(t.amount) > 0);
                        this.renderChart();
                    } finally {
                        setTimeout(() => { this.isLoading = false; }, 500);
                    }
                },

                editData(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    this.formData = { title: item.title, amount: item.amount, type: item.type };
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async saveData() {
                    if(!this.formData.title || !this.formData.amount) return this.showToast('Lengkapi data!', 'error');
                    const url = this.isEdit ? `/api/transactions/${this.editId}` : '/api/transactions';
                    const method = this.isEdit ? 'PUT' : 'POST';
                    const res = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.formData)
                    });
                    if(res.ok) { await this.init(); this.cancelEdit(); this.showToast('Berhasil disimpan'); }
                },

                cancelEdit() { this.isEdit = false; this.editId = null; this.formData = { title: '', amount: '', type: 'income' }; },

                async deleteData(id) {
                    if(!confirm('Hapus?')) return;
                    await fetch(`/api/transactions/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                    this.init();
                    this.showToast('Dihapus');
                },

                renderChart() {
                    const ctx = document.getElementById('myChart').getContext('2d');
                    if (this.chart) this.chart.destroy();
                    const hasData = this.totalIncome() > 0 || this.totalExpense() > 0;
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Masuk', 'Keluar'],
                            datasets: [{
                                data: hasData ? [this.totalIncome(), this.totalExpense()] : [1, 0],
                                backgroundColor: hasData ? ['#10b981', '#f43f5e'] : ['#18181b', '#18181b'],
                                borderColor: hasData ? ['#059669', '#e11d48'] : ['#27272a', '#27272a'],
                                borderWidth: 3
                            }]
                        },
                        options: { cutout: '88%', plugins: { legend: { display: false } } }
                    });
                },

                totalIncome() { return this.transactions.filter(t => t.type === 'income').reduce((a, b) => a + parseFloat(b.amount), 0); },
                totalExpense() { return this.transactions.filter(t => t.type === 'expense').reduce((a, b) => a + parseFloat(b.amount), 0); },
                totalSaldo() { return this.totalIncome() - this.totalExpense(); },
                formatRupiah(n) { return new Intl.NumberFormat('id-ID').format(n); },
                exportExcel() { window.location.href = '/api/export'; }
            }
        }
    </script>
</body>
</html>