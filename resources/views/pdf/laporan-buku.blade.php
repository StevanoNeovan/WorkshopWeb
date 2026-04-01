<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page { size: A4 landscape; margin: 15mm 15mm 15mm 15mm; }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 9.5pt;
      color: #1a1a2e;
    }

    /* ===== HEADER ===== */
    .header {
      border-bottom: 3px solid #6c3483;
      padding-bottom: 6px;
      margin-bottom: 12px;
    }
    .header-title {
      font-size: 16pt;
      font-weight: bold;
      color: #4a235a;
      letter-spacing: 1px;
    }
    .header-sub {
      font-size: 9pt;
      color: #666;
      margin-top: 2px;
    }
    .header-meta {
      font-size: 8pt;
      color: #888;
      margin-top: 4px;
    }

    /* ===== SUMMARY BOXES ===== */
    .summary {
      display: table;
      width: 100%;
      margin-bottom: 12px;
    }
    .summary-box {
      display: table-cell;
      width: 33.33%;
      padding: 6px 10px;
      border-radius: 4px;
      text-align: center;
    }
    .box-purple { background-color: #f5eefa; border-left: 4px solid #7b5ea7; }
    .box-blue   { background-color: #eaf4fb; border-left: 4px solid #2471a3; }
    .box-green  { background-color: #eafaf1; border-left: 4px solid #1e8449; }
    .summary-num  { font-size: 18pt; font-weight: bold; color: #4a235a; }
    .summary-label { font-size: 8pt; color: #555; }
    .summary-gap { display: table-cell; width: 10px; }

    /* ===== TABLE ===== */
    table.data {
      width: 100%;
      border-collapse: collapse;
    }
    table.data thead tr {
      background-color: #6c3483;
      color: #ffffff;
    }
    table.data thead th {
      padding: 7px 8px;
      text-align: left;
      font-size: 9pt;
      font-weight: bold;
    }
    table.data tbody tr:nth-child(even) {
      background-color: #f9f5ff;
    }
    table.data tbody tr:nth-child(odd) {
      background-color: #ffffff;
    }
    table.data tbody td {
      padding: 6px 8px;
      border-bottom: 1px solid #e8dff5;
      font-size: 9pt;
      vertical-align: top;
    }
    .badge {
      display: inline-block;
      padding: 1px 7px;
      border-radius: 10px;
      font-size: 8pt;
      font-weight: bold;
    }
    .badge-purple { background-color: #7b5ea7; color: white; }
    .badge-green  { background-color: #1e8449; color: white; }

    /* ===== FOOTER ===== */
    .footer {
      margin-top: 14px;
      padding-top: 6px;
      border-top: 1px solid #d7bde2;
      display: table;
      width: 100%;
    }
    .footer-left  { display: table-cell; text-align: left;  font-size: 8pt; color: #888; }
    .footer-right { display: table-cell; text-align: right; font-size: 8pt; color: #888; }
  </style>
</head>
<body>

  {{-- ===== HEADER ===== --}}
  <div class="header">
    <div class="header-title">&#128218; LAPORAN KOLEKSI BUKU</div>
    <div class="header-sub">Sistem Manajemen Koleksi Buku &mdash; Data seluruh buku yang tersedia</div>
    <div class="header-meta">
      Tanggal Cetak: {{ $tanggal }} &nbsp;|&nbsp; Dicetak oleh: {{ $dicetak }}
    </div>
  </div>

  {{-- ===== SUMMARY ===== --}}
  <div class="summary">
    <div class="summary-box box-purple">
      <div class="summary-num">{{ $bukus->count() }}</div>
      <div class="summary-label">Total Buku</div>
    </div>
    <div class="summary-gap"></div>
    <div class="summary-box box-blue">
      <div class="summary-num">{{ $bukus->pluck('idkategori')->unique()->count() }}</div>
      <div class="summary-label">Kategori Terpakai</div>
    </div>
    <div class="summary-gap"></div>
    <div class="summary-box box-green">
      <div class="summary-num">{{ $bukus->pluck('pengarang')->unique()->count() }}</div>
      <div class="summary-label">Pengarang Unik</div>
    </div>
  </div>


  {{-- ===== TABEL BUKU ===== --}}
  <table class="data">
    <thead>
      <tr>
        <th style="width:30px">No</th>
        <th style="width:70px">Kode</th>
        <th>Judul Buku</th>
        <th style="width:140px">Pengarang</th>
        <th style="width:80px">Harga</th>
        <th style="width:90px">Kategori</th>
      </tr>
    </thead>
    <tbody>
      @forelse($bukus as $i => $buku)
      <tr>
        <td style="text-align:center">{{ $i + 1 }}</td>
        <td><span class="badge badge-purple">{{ $buku->kode }}</span></td>
        <td>{{ $buku->judul }}</td>
        <td>{{ $buku->pengarang }}</td>
        <td style="text-align:right">{{ number_format($buku->harga, 0, ',', '.') }}</td>
        <td><span class="badge badge-green">{{ $buku->kategori->nama_kategori ?? '-' }}</span></td>
      </tr>
      @empty
      <tr>
        <td colspan="5" style="text-align:center; padding:20px; color:#999;">
          Belum ada data buku.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- ===== FOOTER ===== --}}
  <div class="footer">
    <div class="footer-left">Koleksi Buku &copy; {{ now()->year }}</div>
    <div class="footer-right">Total: {{ $bukus->count() }} buku &nbsp;|&nbsp; {{ $tanggal }}</div>
  </div>

</body>
</html>