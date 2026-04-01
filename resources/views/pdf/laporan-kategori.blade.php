<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page { size: A4 portrait; margin: 18mm 20mm 18mm 20mm; }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 10pt;
      color: #1a1a2e;
    }

    /* ===== HEADER ===== */
    .header {
      border-bottom: 3px solid #6c3483;
      padding-bottom: 8px;
      margin-bottom: 16px;
    }
    .header-title {
      font-size: 15pt;
      font-weight: bold;
      color: #4a235a;
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

    /* ===== SUMMARY ===== */
    .summary {
      display: table;
      width: 100%;
      margin-bottom: 16px;
    }
    .sum-box {
      display: table-cell;
      width: 48%;
      background-color: #f5eefa;
      border-left: 4px solid #7b5ea7;
      border-radius: 4px;
      padding: 8px 12px;
      text-align: center;
    }
    .sum-gap { display: table-cell; width: 4%; }
    .sum-box2 {
      display: table-cell;
      width: 48%;
      background-color: #eaf4fb;
      border-left: 4px solid #2471a3;
      border-radius: 4px;
      padding: 8px 12px;
      text-align: center;
    }
    .sum-num   { font-size: 22pt; font-weight: bold; color: #4a235a; }
    .sum-label { font-size: 8pt; color: #555; }

    /* ===== TABLE ===== */
    table.data {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }
    table.data thead tr {
      background-color: #6c3483;
      color: white;
    }
    table.data thead th {
      padding: 8px 10px;
      font-size: 9.5pt;
      text-align: left;
    }
    table.data tbody tr:nth-child(even) { background-color: #f9f5ff; }
    table.data tbody tr:nth-child(odd)  { background-color: #ffffff; }
    table.data tbody td {
      padding: 8px 10px;
      border-bottom: 1px solid #e8dff5;
      font-size: 10pt;
      vertical-align: middle;
    }

    .badge {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 10px;
      font-size: 9pt;
      font-weight: bold;
    }
    .badge-info   { background-color: #2471a3; color: white; }
    .badge-zero   { background-color: #aaa;    color: white; }

    /* Progress bar per kategori */
    .bar-wrap {
      background-color: #e8dff5;
      border-radius: 4px;
      height: 8px;
      width: 100%;
      margin-top: 4px;
    }
    .bar-fill {
      background-color: #7b5ea7;
      border-radius: 4px;
      height: 8px;
    }

    /* ===== CATATAN ===== */
    .note {
      background-color: #fef9e7;
      border-left: 4px solid #d4ac0d;
      padding: 8px 12px;
      font-size: 8.5pt;
      color: #7d6608;
      border-radius: 3px;
      margin-bottom: 14px;
    }

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

    /* Tanda tangan */
    .ttd-section { margin-top: 24px; text-align: right; }
    .ttd-kota    { font-size: 10pt; margin-bottom: 18mm; }
    .ttd-nama    { font-weight: bold; font-size: 10pt; border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 120px; }
    .ttd-jabatan { font-size: 9pt; color: #555; }
  </style>
</head>
<body>

  {{-- ===== HEADER ===== --}}
  <div class="header">
    <div class="header-title">&#127991; LAPORAN KATEGORI BUKU</div>
    <div class="header-sub">Sistem Manajemen Koleksi Buku &mdash; Ringkasan data per kategori</div>
    <div class="header-meta">
      Tanggal Cetak: {{ $tanggal }} &nbsp;|&nbsp; Dicetak oleh: {{ $dicetak }}
    </div>
  </div>

  {{-- ===== SUMMARY ===== --}}
  <div class="summary">
    <div class="sum-box">
      <div class="sum-num">{{ $kategoris->count() }}</div>
      <div class="sum-label">Total Kategori</div>
    </div>
    <div class="sum-gap"></div>
    <div class="sum-box2">
      <div class="sum-num">{{ $totalBuku }}</div>
      <div class="sum-label">Total Buku</div>
    </div>
  </div>

  {{-- ===== CATATAN ===== --}}
  @if($kategoris->where('buku_count', 0)->count() > 0)
  <div class="note">
    &#9888; Terdapat {{ $kategoris->where('buku_count', 0)->count() }} kategori yang belum memiliki buku.
  </div>
  @endif

  {{-- ===== TABEL KATEGORI ===== --}}
  <table class="data">
    <thead>
      <tr>
        <th style="width:35px">No</th>
        <th>Nama Kategori</th>
        <th style="width:90px; text-align:center">Jumlah Buku</th>
        <th style="width:160px">Proporsi Koleksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($kategoris as $i => $kat)
      <tr>
        <td style="text-align:center">{{ $i + 1 }}</td>
        <td><strong>{{ $kat->nama_kategori }}</strong></td>
        <td style="text-align:center">
          <span class="badge {{ $kat->buku_count > 0 ? 'badge-info' : 'badge-zero' }}">
            {{ $kat->buku_count }}
          </span>
        </td>
        <td>
          @if($totalBuku > 0)
            @php $pct = round(($kat->buku_count / $totalBuku) * 100, 1); @endphp
            <div style="font-size:8pt; color:#555; margin-bottom:3px;">{{ $pct }}%</div>
            <div class="bar-wrap">
              <div class="bar-fill" style="width:{{ $pct }}%"></div>
            </div>
          @else
            <span style="color:#aaa; font-size:8pt;">-</span>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" style="text-align:center; padding:20px; color:#999;">
          Belum ada data kategori.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- ===== TANDA TANGAN ===== --}}
  <div class="ttd-section">
    <div class="ttd-kota">Surabaya, {{ $tanggal }}</div>
    <div class="ttd-nama">{{ $dicetak }}</div>
    <div class="ttd-jabatan">Petugas Perpustakaan</div>
  </div>

  {{-- ===== FOOTER ===== --}}
  <div class="footer">
    <div class="footer-left">Koleksi Buku &copy; {{ now()->year }}</div>
    <div class="footer-right">{{ $kategoris->count() }} kategori &nbsp;|&nbsp; {{ $totalBuku }} buku total</div>
  </div>

</body>
</html>