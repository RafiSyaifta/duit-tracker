<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DuitTracker | Personal Finance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        [x-cloak] {
            display: none !important;
        }

        @keyframes pulse-soft {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .animate-pulse-soft {
            animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        ::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-[#09090b] text-zinc-100 antialiased selection:bg-blue-500/30" x-data="financeApp()">

    <div x-cloak x-show="toast.show" x-transition
        class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-6 py-4 rounded-2xl glass shadow-2xl ring-1 ring-white/10">
        <p class="text-sm font-bold tracking-tight text-white" x-text="toast.message"></p>
    </div>

    <div
        class="fixed top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-blue-600/10 blur-[120px] -z-10 pointer-events-none">
    </div>

    <div class="max-w-5xl mx-auto p-6 lg:p-10 min-h-screen">
        <header class="flex justify-between items-end py-10">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white mb-2">Duit<span
                        class="text-blue-500">Tracker.</span></h1>
                <p class="text-zinc-500 text-sm italic">Laporan untuk <span
                        class="text-zinc-300 font-bold underline">{{ Auth::user()->name }}</span>.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="exportExcel()"
                    class="glass hover:bg-zinc-800 px-5 py-2.5 rounded-2xl text-xs font-semibold transition">Ekspor</button>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"
                        class="bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition">Keluar</button>
                </form>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
            <div class="lg:col-span-8 space-y-6">
                <div class="glass p-8 rounded-[2rem] relative overflow-hidden group">
                    <p class="text-zinc-500 text-sm font-semibold uppercase tracking-widest mb-2">Saldo Terfilter</p>
                    <h2 class="text-5xl font-extrabold text-white tracking-tight">Rp <span
                            x-text="formatRupiah(totalSaldo())"></span></h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="glass p-6 rounded-[2rem] border-l-4 border-l-emerald-500/50">
                        <p class="text-zinc-500 text-xs font-bold uppercase mb-1">Total Pemasukan</p>
                        <p class="text-xl font-bold text-emerald-400">Rp <span
                                x-text="formatRupiah(totalIncome())"></span></p>
                    </div>
                    <div class="glass p-6 rounded-[2rem] border-l-4 border-l-rose-500/50">
                        <p class="text-zinc-500 text-xs font-bold uppercase mb-1">Total Pengeluaran</p>
                        <p class="text-xl font-bold text-rose-400">Rp <span
                                x-text="formatRupiah(totalExpense())"></span></p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-4 glass p-8 rounded-[2rem] flex flex-col items-center justify-center relative">
                <div class="relative w-full aspect-square">
                    <canvas id="myChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Kondisi</span>
                        <span class="text-blue-400 text-sm font-bold"
                            x-text="filteredData().length === 0 ? 'Data Kosong' : (totalIncome() >= totalExpense() ? 'Aman' : 'Defisit')"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass rounded-[2rem] p-8 mb-6 ring-1 ring-blue-500/20 shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-3">
                <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                <span x-text="isEdit ? 'Perbarui Data Transaksi' : 'Catat Transaksi Baru'"></span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                <div class="md:col-span-7 space-y-2">
                    <label class="text-xs text-zinc-500 font-bold ml-2 uppercase tracking-wider">Keterangan</label>
                    <input type="text" x-model="formData.title" placeholder="Cth: Nasi Goreng Gila"
                        class="w-full bg-black/50 border border-zinc-800 py-3.5 px-4 rounded-2xl focus:border-blue-500 outline-none text-sm text-zinc-100 transition">

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button @click="quickAdd('Makan Siang', 'expense', 'makan')"
                            class="text-[10px] font-bold px-3 py-1.5 rounded-full bg-zinc-800 hover:bg-zinc-700 transition">🍔
                            MAKAN</button>
                        <button @click="quickAdd('Gaji Bulanan', 'income', 'gaji')"
                            class="text-[10px] font-bold px-3 py-1.5 rounded-full bg-zinc-800 hover:bg-zinc-700 transition">💰
                            GAJI</button>
                        <button @click="quickAdd('Isi Bensin', 'expense', 'transport')"
                            class="text-[10px] font-bold px-3 py-1.5 rounded-full bg-zinc-800 hover:bg-zinc-700 transition">🚗
                            TRANSPORT</button>
                    </div>
                </div>

                <div class="md:col-span-5 space-y-2">
                    <label class="text-xs text-zinc-500 font-bold ml-2 uppercase tracking-wider">Nominal (Rp)</label>
                    <input type="number" x-model="formData.amount" placeholder="0"
                        class="w-full bg-black/50 border border-zinc-800 py-3.5 px-4 rounded-2xl focus:border-blue-500 outline-none text-base font-bold text-blue-400 transition">
                </div>

                <div class="md:col-span-5 space-y-2">
                    <label class="text-xs text-zinc-500 font-bold ml-2 uppercase tracking-wider">Kategori</label>
                    <select x-model="formData.category"
                        class="w-full bg-black/50 border border-zinc-800 py-3.5 px-4 rounded-2xl focus:border-blue-500 outline-none text-zinc-300 text-sm cursor-pointer transition appearance-none">
                        <option value="makan">🍔 Makan & Minum</option>
                        <option value="transport">🚗 Transportasi</option>
                        <option value="tagihan">📄 Tagihan & Cicilan</option>
                        <option value="hiburan">🎬 Hiburan</option>
                        <option value="gaji">💰 Gaji & Bonus</option>
                        <option value="lainnya">✨ Lainnya</option>
                    </select>
                </div>

                <div class="md:col-span-4 space-y-2">
                    <label class="text-xs text-zinc-500 font-bold ml-2 uppercase tracking-wider">Tanggal</label>
                    <input type="date" x-model="formData.transaction_date"
                        class="w-full bg-black/50 border border-zinc-800 py-3.5 px-4 rounded-2xl focus:border-blue-500 outline-none text-sm text-zinc-300 cursor-pointer transition">
                </div>

                <div class="md:col-span-3 flex flex-col gap-2 pt-7">
                    <button @click="saveData()" class="w-full py-3.5 rounded-2xl font-bold transition text-sm shadow-lg"
                        :class="isEdit ? 'bg-orange-600 hover:bg-orange-500 text-white' : 'bg-blue-600 hover:bg-blue-500 text-white'"
                        x-text="isEdit ? 'Update' : 'Simpan'"></button>
                    <button x-show="isEdit" @click="cancelEdit()"
                        class="w-full bg-zinc-800 py-2 rounded-2xl hover:bg-zinc-700 transition text-xs font-bold text-zinc-400 hover:text-white mt-1">Batal
                        Edit</button>
                </div>
            </div>
        </div>

        <div class="glass p-6 rounded-[2rem] mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="relative w-full md:w-1/2">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-zinc-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari transaksi..."
                    class="w-full bg-black/50 border border-zinc-800 py-3 pl-12 pr-4 rounded-xl focus:border-blue-500 outline-none transition text-sm text-zinc-200">
            </div>
            <select x-model="filterPeriod"
                class="w-full md:w-48 bg-black/50 border border-zinc-800 p-3 rounded-xl focus:border-blue-500 outline-none text-zinc-400 text-sm cursor-pointer">
                <option value="all">Semua Waktu</option>
                <option value="this_month">Bulan Ini</option>
                <option value="last_month">Bulan Lalu</option>
            </select>
        </div>

        <div class="glass rounded-[2rem] overflow-hidden mb-12">
            <div class="p-6 border-b border-zinc-800 flex justify-between items-center bg-zinc-900/30">
                <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-500">Riwayat Keuangan</h3>
                <span class="text-[10px] bg-zinc-800 px-3 py-1 rounded-full text-zinc-400 font-bold"
                    x-text="filteredData().length + ' Entri Data'"></span>
            </div>

            <div class="overflow-x-auto min-h-[200px]">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-zinc-800/50">
                        <tr x-show="isLoading" class="animate-pulse-soft">
                            <td colspan="3" class="p-10 text-center text-zinc-600 font-bold text-sm">Memuat data...</td>
                        </tr>
                        <tr x-show="!isLoading && filteredData().length === 0" x-cloak>
                            <td colspan="3" class="p-20 text-center opacity-25">
                                <p class="text-sm font-bold uppercase tracking-widest">Tidak ada data ditemukan</p>
                            </td>
                        </tr>

                        <template x-for="(item, index) in filteredData()" :key="item.id || index">
                            <tr class="hover:bg-zinc-800/40 transition group">
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div :class="item.type == 'income' ? 'bg-emerald-500/10' : 'bg-rose-500/10'"
                                            class="w-12 h-12 flex items-center justify-center rounded-2xl text-2xl shadow-inner">
                                            <span x-text="getCategoryIcon(item.category)"></span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-zinc-100 text-sm" x-text="item.title"></div>
                                            <div
                                                class="text-[10px] text-zinc-500 font-bold uppercase flex gap-2 items-center mt-1.5">
                                                <span
                                                    class="bg-zinc-800/80 text-zinc-300 px-2 py-0.5 rounded shadow-sm ring-1 ring-white/5"
                                                    x-text="getCategoryLabel(item.category)"></span>
                                                <span class="opacity-50">&bull;</span>
                                                <span
                                                    x-text="new Date(item.transaction_date || item.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="text-base font-extrabold"
                                        :class="item.type == 'income' ? 'text-emerald-400' : 'text-rose-400'">
                                        <span x-text="item.type == 'income' ? '+' : '-'"></span> Rp <span
                                            x-text="formatRupiah(item.amount)"></span>
                                    </div>
                                </td>
                                <td class="p-6 text-center w-40">
                                    <div
                                        class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition duration-300">
                                        <button @click="editData(item)"
                                            class="bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white px-3 py-1.5 rounded-xl text-[10px] font-bold transition">Edit</button>
                                        <button @click="deleteData(item.id)"
                                            class="bg-zinc-800 hover:bg-rose-600 text-zinc-500 hover:text-white px-3 py-1.5 rounded-xl text-[10px] font-bold transition">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="text-center py-10 opacity-30">
            <p class="text-[9px] font-bold tracking-[0.5em] uppercase">Built with precision by Rafi Syaifta &mdash; 2026
            </p>
        </footer>
    </div>

    <script>
        function financeApp() {
            return {
                transactions: [],
                formData: {
                    title: '',
                    amount: '',
                    type: 'expense',
                    category: 'makan',
                    transaction_date: new Date().toISOString().split('T')[0]
                },
                isEdit: false,
                editId: null,
                chart: null,
                isLoading: true,
                searchQuery: '',
                filterPeriod: 'all',
                toast: { show: false, message: '', type: 'success' },

                getCategoryIcon(cat) {
                    const icons = { 'makan': '🍔', 'transport': '🚗', 'tagihan': '📄', 'hiburan': '🎬', 'gaji': '💰', 'lainnya': '✨' };
                    return icons[cat] || '✨';
                },

                getCategoryLabel(cat) {
                    const labels = { 'makan': 'Makan & Minum', 'transport': 'Transportasi', 'tagihan': 'Tagihan & Cicilan', 'hiburan': 'Hiburan', 'gaji': 'Gaji & Bonus', 'lainnya': 'Lainnya' };
                    return labels[cat] || 'Lainnya';
                },

                showToast(msg, type = 'success') {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                },

                quickAdd(title, type, category) {
                    this.formData.title = title;
                    this.formData.type = type;
                    this.formData.category = category;
                    this.formData.transaction_date = new Date().toISOString().split('T')[0];
                    this.showToast('Kategori dipilih: ' + title);
                },

                formatRupiah(n) { return new Intl.NumberFormat('id-ID').format(n || 0); },

                async init() {
                    this.isLoading = true;
                    try {
                        const res = await fetch('/api/transactions');
                        const data = await res.json();
                        this.transactions = data.filter(t => t.title && t.title.trim().length > 0 && parseFloat(t.amount) > 0);

                        this.$watch('formData.category', (newVal) => {
                            if (newVal === 'gaji') this.formData.type = 'income';
                            else if (['makan', 'transport', 'tagihan', 'hiburan'].includes(newVal)) this.formData.type = 'expense';
                        });

                        this.renderChart();
                    } finally {
                        setTimeout(() => { this.isLoading = false; }, 500);
                    }

                    this.$watch('searchQuery', () => { this.renderChart(); });
                    this.$watch('filterPeriod', () => { this.renderChart(); });
                },

                filteredData() {
                    return this.transactions.filter(t => {
                        const matchSearch = t.title.toLowerCase().includes(this.searchQuery.toLowerCase());
                        let matchPeriod = true;
                        if (this.filterPeriod !== 'all') {
                            const tDate = new Date(t.transaction_date || t.created_at);
                            const now = new Date();
                            if (this.filterPeriod === 'this_month') {
                                matchPeriod = tDate.getMonth() === now.getMonth() && tDate.getFullYear() === now.getFullYear();
                            } else if (this.filterPeriod === 'last_month') {
                                let lastMonth = now.getMonth() - 1;
                                let year = now.getFullYear();
                                if (lastMonth < 0) { lastMonth = 11; year -= 1; }
                                matchPeriod = tDate.getMonth() === lastMonth && tDate.getFullYear() === year;
                            }
                        }
                        return matchSearch && matchPeriod;
                    });
                },

                editData(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    this.formData = {
                        title: item.title,
                        amount: item.amount,
                        type: item.type,
                        category: item.category || 'lainnya',
                        transaction_date: item.transaction_date || (item.created_at ? item.created_at.split('T')[0] : new Date().toISOString().split('T')[0])
                    };
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async saveData() {
                    if (!this.formData.title || !this.formData.amount) return this.showToast('Lengkapi semua data!', 'error');
                    const url = this.isEdit ? `/api/transactions/${this.editId}` : '/api/transactions';
                    const method = this.isEdit ? 'PUT' : 'POST';

                    const res = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) { await this.init(); this.cancelEdit(); this.showToast('Berhasil disimpan'); }
                },

                cancelEdit() {
                    this.isEdit = false;
                    this.editId = null;
                    this.formData = { title: '', amount: '', type: 'expense', category: 'makan', transaction_date: new Date().toISOString().split('T')[0] };
                },

                async deleteData(id) {
                    if (!confirm('Hapus transaksi ini?')) return;
                    await fetch(`/api/transactions/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                    this.init();
                    this.showToast('Data dihapus');
                },

                totalIncome() { return this.filteredData().filter(t => t.type === 'income').reduce((a, b) => a + parseFloat(b.amount), 0); },
                totalExpense() { return this.filteredData().filter(t => t.type === 'expense').reduce((a, b) => a + parseFloat(b.amount), 0); },
                totalSaldo() { return this.totalIncome() - this.totalExpense(); },

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
                        options: { cutout: '88%', plugins: { legend: { display: false } }, animation: { duration: 500 } }
                    });
                },
                exportExcel() { window.location.href = '/api/export'; }
            }
        }
    </script>
</body>

</html>