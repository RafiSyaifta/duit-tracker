<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DuitTracker | Personal Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-black text-zinc-100 font-sans antialiased" x-data="financeApp()">

    <div class="max-w-4xl mx-auto p-6 min-h-screen">
        <header class="flex justify-between items-center py-10 border-b border-blue-900/30">
            <div>
                <h1 class="text-3xl font-bold tracking-tighter text-blue-500">Duit<span
                        class="text-white">Tracker</span></h1>
                <p class="text-zinc-500 text-sm">Kelola keuangan dengan elegan.</p>
            </div>
            <button @click="exportExcel()"
                class="bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-900/20">Ekspor
                Excel</button>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 my-10">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-zinc-900/50 p-8 rounded-3xl border border-zinc-800 backdrop-blur-sm">
                    <p class="text-zinc-500 text-sm uppercase tracking-widest mb-1">Total Saldo Lu</p>
                    <h2 class="text-4xl font-bold text-blue-400">Rp <span x-text="formatRupiah(totalSaldo())"></span>
                    </h2>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-zinc-900/50 p-6 rounded-3xl border border-zinc-800">
                        <p class="text-zinc-500 text-xs mb-1 uppercase">Pemasukan</p>
                        <p class="text-xl font-bold text-green-400">Rp <span
                                x-text="formatRupiah(totalIncome())"></span></p>
                    </div>
                    <div class="bg-zinc-900/50 p-6 rounded-3xl border border-zinc-800">
                        <p class="text-zinc-500 text-xs mb-1 uppercase">Pengeluaran</p>
                        <p class="text-xl font-bold text-red-400">Rp <span x-text="formatRupiah(totalExpense())"></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-zinc-900/50 p-6 rounded-3xl border border-zinc-800 flex items-center justify-center">
                <div class="w-40 h-40">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-zinc-900 p-1 rounded-3xl border border-blue-900/20 mb-10 overflow-hidden shadow-2xl">
            <div class="p-6">
                <h3 class="text-sm font-bold text-blue-500 uppercase mb-4"
                    x-text="isEdit ? 'Ubah Catatan' : 'Tambah Catatan Baru'"></h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" x-model="formData.title" placeholder="Makan siang, Gaji, dll"
                        class="bg-black border border-zinc-800 p-4 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                    <input type="number" x-model="formData.amount" placeholder="Nominal"
                        class="bg-black border border-zinc-800 p-4 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm text-blue-400 font-bold">
                    <select x-model="formData.type"
                        class="bg-black border border-zinc-800 p-4 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none text-zinc-400 text-sm appearance-none">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                    <div class="flex gap-2">
                        <button @click="saveData()"
                            class="flex-1 p-4 rounded-2xl font-bold transition text-sm shadow-lg"
                            :class="isEdit ? 'bg-orange-600 hover:bg-orange-700 shadow-orange-900/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-900/20'"
                            x-text="isEdit ? 'Update' : 'Simpan'">
                        </button>
                        <template x-if="isEdit">
                            <button @click="cancelEdit()"
                                class="bg-zinc-800 hover:bg-zinc-700 p-4 rounded-2xl transition">✕</button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-zinc-900/30 rounded-3xl border border-zinc-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-zinc-900/80 text-zinc-500 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="p-5 font-semibold">Transaksi</th>
                        <th class="p-5 text-right font-semibold">Nominal</th>
                        <th class="p-5 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    <template x-for="item in transactions" :key="item.id">
                        <tr class="hover:bg-zinc-800/20 transition group">
                            <td class="p-5">
                                <div class="font-medium text-sm text-zinc-200" x-text="item.title"></div>
                                <div class="text-[10px] text-zinc-600"
                                    x-text="new Date(item.created_at).toLocaleDateString('id-ID')"></div>
                            </td>
                            <td class="p-5 text-right font-bold text-sm"
                                :class="item.type == 'income' ? 'text-green-400' : 'text-red-400'">
                                <span x-text="item.type == 'income' ? '+' : '-'"></span> Rp <span
                                    x-text="formatRupiah(item.amount)"></span>
                            </td>
                            <td class="p-5 text-center">
                                <div class="flex justify-center gap-4 opacity-0 group-hover:opacity-100 transition">
                                    <button @click="editData(item)"
                                        class="text-blue-400 hover:text-blue-300 text-xs font-semibold">Edit</button>
                                    <button @click="deleteData(item.id)"
                                        class="text-zinc-700 hover:text-red-500 text-xs">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function financeApp() {
            return {
                transactions: [],
                formData: { title: '', amount: '', type: 'income' },
                isEdit: false,
                editId: null,
                chart: null,

                async init() {
                    const res = await fetch('/api/transactions');
                    this.transactions = await res.json();
                    this.renderChart();
                },

                editData(item) {
                    this.isEdit = true;
                    this.editId = item.id;
                    this.formData = { title: item.title, amount: item.amount, type: item.type };
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async saveData() {
                    if (!this.formData.title || !this.formData.amount) return alert('Lengkapi datanya bos!');
                    const url = this.isEdit ? `/api/transactions/${this.editId}` : '/api/transactions';
                    const method = this.isEdit ? 'PUT' : 'POST';

                    const res = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        await this.init();
                        this.cancelEdit();
                    }
                },

                cancelEdit() {
                    this.isEdit = false;
                    this.editId = null;
                    this.formData = { title: '', amount: '', type: 'income' };
                },

                async deleteData(id) {
                    if (!confirm('Hapus transaksi ini?')) return;
                    await fetch(`/api/transactions/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    this.init();
                },

                renderChart() {
                    const ctx = document.getElementById('myChart').getContext('2d');
                    if (this.chart) this.chart.destroy();
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [this.totalIncome(), this.totalExpense()],
                                backgroundColor: ['#3b82f6', '#18181b'],
                                borderColor: ['#1e3a8a', '#27272a'],
                                borderWidth: 2
                            }]
                        },
                        options: { cutout: '80%', plugins: { legend: { display: false } } }
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