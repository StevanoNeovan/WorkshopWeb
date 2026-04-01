<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    /*
      TnJ No. 108 - ukuran terukur:
      Label   : 38mm lebar x 18mm tinggi
      Gap LR  : 3mm antar kolom
      Gap TB  : 2mm antar baris
      Margin  : 4mm semua sisi
      Grid    : 5 kolom x 8 baris = 40 label

      Lebar total: 5*38 + 4*3 = 202mm  (+4+4 margin = 210mm A4 ✓)
      Tinggi label: 8*18 + 7*2 = 158mm (+4+4 margin = 166mm dari 297mm A4)
    */
    @page {
      size: A4 portrait;
      margin: 4mm 4mm 4mm 4mm;
    }

    body {
      font-family: 'DejaVu Sans', sans-serif;
    }

    .page {
      page-break-after: always;
    }
    .page:last-child {
      page-break-after: avoid;
    }

    /* Satu baris = tabel lebar penuh */
    .row-table {
      width: 202mm;
      border-collapse: collapse;
      margin-bottom: 2mm;
    }
    .row-table:last-child {
      margin-bottom: 0;
    }

    .label-cell {
      width: 38mm;
      height: 18mm;
      border: 0.5px solid #999;
      vertical-align: middle;
      text-align: center;
      padding: 1mm 1mm;
      overflow: hidden;
    }

    /* Gap 3mm antar kolom */
    .gap-cell {
      width: 3mm;
      height: 18mm;
      border: none;
      padding: 0;
    }

    .label-judul {
      font-size: 5pt;
      color: #222;
      line-height: 1.4;
      margin-bottom: 1mm;
      overflow: hidden;
      /* Batasi 2 baris: 2 x (5pt * 1.4) = ~14pt = ~5mm, sisakan 1mm untuk harga */
      max-height: 7mm;
      word-break: break-word;
    }

    .label-harga {
      font-size: 8pt;
      font-weight: bold;
      color: #000;
      line-height: 1;
    }
  </style>
</head>
<body>

@foreach($pages as $pageIndex => $labels)
<div class="page">

  @for($row = 0; $row < 8; $row++)
  <table class="row-table">
    <tr>
      @for($col = 0; $col < 5; $col++)
        @php $label = $labels[$row * 5 + $col]; @endphp

        <td class="label-cell">
          @if(!is_null($label))
            <div class="label-judul">{{ $label['judul'] }}</div>
            <div class="label-harga">Rp {{ number_format($label['harga'], 0, ',', '.') }}</div>
          @endif
        </td>

        @if($col < 4)
          <td class="gap-cell"></td>
        @endif
      @endfor
    </tr>
  </table>
  @endfor

</div>
@endforeach

</body>
</html>