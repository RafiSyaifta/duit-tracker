<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DuitTracker | Personal Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-black text-white font-sans" x-data="financeApp()">

    <div class="max-w-4xl mx-auto p-6">
        <header class="flex justify-between items-center py-8 border-b border-blue-900/30">
            <h1 class="text-3xl font-bold tracking-tighter text-blue-500">Duit<span class="text-white">Tracker</span>
            </h1>
            <button @click="exportExcel()"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm transition">Ekspor Excel</button>
        </header>
        <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 mb-10 flex flex-col items-center">
            <h3 class="text-zinc-400 text-sm mb-4">Proporsi Keuangan</h3>
            <div class="w-64 h-64">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-10 text-center">
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
                <p class="text-zinc-400 text-sm">Total Saldo</p>
                <h2 class="text-2xl font-bold text-blue-400">Rp <span x-text="formatRupiah(totalSaldo())"></span></h2>
            </div>
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
                <p class="text-zinc-400 text-sm">Pemasukan</p>
                <h2 class="text-2xl font-bold text-green-400">Rp <span x-text="formatRupiah(totalIncome())"></span></h2>
            </div>
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
                <p class="text-zinc-400 text-sm">Pengeluaran</p>
                <h2 class="text-2xl font-bold text-red-400">Rp <span x-text="formatRupiah(totalExpense())"></span></h2>
            </div>
        </div>

        <div class="bg-zinc-900 p-6 rounded-2xl border border-blue-900/20 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" x-model="formData.title" placeholder="Nama Transaksi"
                    class="bg-black border border-zinc-700 p-3 rounded-xl focus:border-blue-500 outline-none">
                <input type="number" x-model="formData.amount" placeholder="Nominal"
                    class="bg-black border border-zinc-700 p-3 rounded-xl focus:border-blue-500 outline-none">
                <select x-model="formData.type"
                    class="bg-black border border-zinc-700 p-3 rounded-xl focus:border-blue-500 outline-none text-zinc-400">
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
                <button @click="saveData()"
                    class="bg-blue-600 hover:bg-blue-700 p-3 rounded-xl font-bold transition">Tambah</button>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900/50">
            <table class="w-full text-left">
                <thead class="bg-zinc-900 text-zinc-500 text-sm uppercase">
                    <tr>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4 text-right">Nominal</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in transactions" :key="item.id">
                        <tr class="border-t border-zinc-800 hover:bg-zinc-800/30 transition">
                            <td class="p-4 font-medium" x-text="item.title"></td>
                            <td class="p-4 text-right font-bold"
                                :class="item.type == 'income' ? 'text-green-400' : 'text-red-400'">
                                <span x-text="item.type == 'income' ? '+' : '-'"></span> Rp <span
                                    x-text="formatRupiah(item.amount)"></span>
                            </td>
                            <td class="p-4 text-center">
                                <button @click="deleteData(item.id)"
                                    class="text-zinc-600 hover:text-red-500">Hapus</button>
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
                chart: null, // Variabel buat nyimpen grafik [cite: 175]

                async init() {
                    const res = await fetch('/api/transactions');
                    this.transactions = await res.json();
                    this.renderChart(); // Langsung bikin grafik pas buka web [cite: 175, 176]
                },

                async saveData() {
                    if (!this.formData.title || !this.formData.amount) return alert('Isi dulu bos!');

                    const res = await fetch('/api/transactions', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.formData)
                    });

                    if (res.ok) {
                        await this.init(); // Refresh data & grafik [cite: 175]
                        this.formData = { title: '', amount: '', type: 'income' };
                    }
                },

                async deleteData(id) {
                    if (!confirm('Yakin mau hapus?')) return;
                    await fetch(`/api/transactions/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    this.init(); // Refresh data & grafik [cite: 175]
                },

                renderChart() {
                    const ctx = document.getElementById('myChart').getContext('2d');
                    if (this.chart) { this.chart.destroy(); } // Hapus chart lama biar gak tumpang tindih [cite: 175]

                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Masuk', 'Keluar'],
                            datasets: [{
                                data: [this.totalIncome(), this.totalExpense()],
                                backgroundColor: ['#1E40AF', '#18181B'],
                                borderColor: ['#3b82f6', '#3f3f46'],
                                borderWidth: 2
                            }]
                        },
                        options: { cutout: '80%', plugins: { legend: { display: false } } }
                    });
                },

                totalIncome() {
                    return this.transactions.filter(t => t.type === 'income').reduce((a, b) => a + parseFloat(b.amount), 0);
                },

                totalExpense() {
                    return this.transactions.filter(t => t.type === 'expense').reduce((a, b) => a + parseFloat(b.amount), 0);
                },

                totalSaldo() {
                    return this.totalIncome() - this.totalExpense();
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                },

                exportExcel() {
                    window.location.href = '/api/export'; // Link export yang lu buat tadi [cite: 137]
                }
            }
        }
    </script>
</body>

</html>