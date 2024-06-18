<form id="depositForm">
  <!-- Label dan input untuk jumlah deposito -->
  <label for="jumlah">Jumlah Deposito:</label><br>
  <input type="text" id="jumlah" name="jumlah" placeholder="Rp 0" required oninput="formatJumlah()"><br><br>
  
  <!-- Label dan dropdown untuk jangka waktu deposito -->
  <label for="jangkaWaktu">Jangka Waktu:</label><br>
  <select id="jangkaWaktu" name="jangkaWaktu" onchange="updateSukuBunga()">
    <option value="1">1 Bulan</option>
    <option value="3">3 Bulan</option>
    <option value="6">6 Bulan</option>
    <option value="12">12 Bulan</option>
    <option value="24">24 Bulan</option>
  </select><br><br>
  
  <!-- Label dan input untuk suku bunga -->
  <label for="sukuBunga">Suku Bunga:</label><br>
  <input type="text" id="sukuBunga" name="sukuBunga" readonly><br><br>
  
  <!-- Label dan input untuk tanggal awal deposito -->
  <label for="awalDeposito">Awal Deposito:</label><br>
  <input type="month" id="awalDeposito" name="awalDeposito" required><br><br>

  <!-- Tombol untuk menghitung hasil deposito -->
  <input type="button" value="Hitung" onclick="calculateDeposit()" id="hitungButton">
</form>

<!-- Tabel untuk menampilkan hasil kalkulasi deposito -->
<h3>Hasil Kalkulasi</h3>
<table id="depositTable" border="1">
  <thead>
    <tr>
      <th>No.</th>
      <th>Periode</th>
      <th>Bunga Kotor</th>
      <th>PPH</th>
      <th>Bunga Bersih</th>
    </tr>
  </thead>
  <tbody>
    <!-- Hasil akan dimasukkan di sini -->
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="highlight"><b>Jumlah</b></td>
      <td id="totalBungaKotor">Rp 0</td>
      <td id="totalPPH">Rp 0</td>
      <td id="totalBungaBersih"><b>Rp 0</b></td>
    </tr>
    <tr>
      <td colspan="4" class="highlight"><b>Nominal Penempatan</b></td>
      <td id="nominalPenempatan"><b>Rp 0</b></td>
    </tr>
    <tr>
      <td colspan="4" class="highlight"><b>Total Pengembalian</b></td>
      <td id="totalPengembalian"><b>Rp 0</b></td>
    </tr>
  </tfoot>
</table>

<style>
  /* Gaya untuk tombol hitung */
  #hitungButton {
    background-color: green;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    float: right;
  }

  /* Gaya untuk tombol hitung saat di-hover */
  #hitungButton:hover {
    background-color: darkgreen;
  }

  /* Gaya untuk header dan sel tabel */
  #depositTable th, #depositTable td {
    text-align: center;
  }

  /* Gaya untuk sel yang ditandai */
  .highlight {
    background-color: #f2f2f2; /* Menyamakan warna dengan header tabel */
  }
</style>

<script>
/* Fungsi untuk memformat angka ke dalam format rupiah */
function formatRupiah(value) {
  return 'Rp ' + value.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

/* Fungsi untuk memformat input jumlah deposito */
function formatJumlah() {
  const jumlahField = document.getElementById('jumlah');
  const value = jumlahField.value.replace(/\D/g, '');
  jumlahField.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
}

/* Mapping suku bunga berdasarkan jangka waktu */
const sukuBungaMapping = {
  "1": 4.50,
  "3": 4.75,
  "6": 5.00,
  "12": 5.25,
  "24": 5.50
};

/* Mengatur nilai default untuk awal deposito ke bulan dan tahun saat ini */
document.getElementById('awalDeposito').value = new Date().toISOString().slice(0, 7);

/* Fungsi untuk memperbarui suku bunga berdasarkan jangka waktu yang dipilih */
function updateSukuBunga() {
  const selectedValue = document.getElementById('jangkaWaktu').value;
  document.getElementById('sukuBunga').value = sukuBungaMapping[selectedValue] + "%";
}

/* Fungsi untuk menghitung deposito dan menampilkan hasilnya dalam tabel */
function calculateDeposit() {
  const jumlah = Math.round(parseFloat(document.getElementById('jumlah').value.replace(/Rp|\./g, '').trim()));
  const jangkaWaktu = parseInt(document.getElementById('jangkaWaktu').value);
  const sukuBunga = sukuBungaMapping[jangkaWaktu] / 100;
  const awalDeposito = new Date(document.getElementById('awalDeposito').value);

  let totalBungaKotor = 0;
  let totalPPH = 0;
  let totalBungaBersih = 0;

  let tableBody = document.getElementById('depositTable').getElementsByTagName('tbody')[0];
  tableBody.innerHTML = ''; // Hapus hasil sebelumnya

  for (let i = 1; i <= jangkaWaktu; i++) {
    const currentMonth = new Date(awalDeposito.getFullYear(), awalDeposito.getMonth() + i, 1);
    const daysInMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0).getDate();
    const daysInYear = (currentMonth.getFullYear() % 4 === 0 ? 366 : 365);

    const bungaKotor = Math.round((jumlah * sukuBunga * daysInMonth) / daysInYear);
    const pph = Math.round(bungaKotor * 0.20);
    const bungaBersih = Math.round(bungaKotor - pph);

    totalBungaKotor += bungaKotor;
    totalPPH += pph;
    totalBungaBersih += bungaBersih;

    const row = tableBody.insertRow();
    row.insertCell(0).textContent = i;
    row.insertCell(1).textContent = `${String(currentMonth.getMonth() + 1).padStart(2, '0')}/${currentMonth.getFullYear()}`;
    row.insertCell(2).textContent = formatRupiah(bungaKotor);
    row.insertCell(3).textContent = formatRupiah(pph);
    row.insertCell(4).innerHTML = '<b>' + formatRupiah(bungaBersih) + '</b>';
  }

  document.getElementById('totalBungaKotor').textContent = formatRupiah(totalBungaKotor);
  document.getElementById('totalPPH').textContent = formatRupiah(totalPPH);
  document.getElementById('totalBungaBersih').innerHTML = '<b>' + formatRupiah(totalBungaBersih) + '</b>';
  document.getElementById('nominalPenempatan').innerHTML = '<b>' + formatRupiah(jumlah) + '</b>';
  document.getElementById('totalPengembalian').innerHTML = '<b>' + formatRupiah(jumlah + totalBungaBersih) + '</b>';
}

// Inisialisasi nilai default pada saat halaman dimuat
updateSukuBunga();
</script>
