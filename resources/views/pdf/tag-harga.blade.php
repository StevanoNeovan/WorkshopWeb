<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'DejaVu Sans', sans-serif;
    }

    /*
      Ukuran dari temanmu (presisi kertas TnJ 108):
      Label   : 38mm x 18mm
      Gap LR  : 3.3mm (column-gap)
      Gap TB  : 2.3mm (row-gap)
      Margin  : 2.3mm atas/bawah, 3.3mm kiri/kanan
      Grid    : 5 kolom x 8 baris = 40 label
    */
    
    @page {
      size: A4 portrait;
      margin: 2.3mm 3.3mm 2.3mm 3.3mm;
    }

    .page {
      page-break-after: always;
    }
    .page:last-child {
      page-break-after: avoid;
    }

    /*
      DomPDF tidak support CSS Grid, pakai table.
      Simulasikan column-gap: 3.3mm dengan sel spacer.
      Simulasikan row-gap: 2.3mm dengan margin-bottom pada tiap row-table.
    */
    .row-table {
      width: 202.2mm; /* 5*38 + 4*3.3 = 203.2mm, sedikit adjust untuk DomPDF */
      border-collapse: collapse;
      margin-bottom: 2.3mm;
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
      padding: 1mm;
      overflow: hidden;
    }

    /* Sel spacer simulasi column-gap 3.3mm */
    .gap-cell {
      width: 3.3mm;
      height: 18mm;
      border: none;
      padding: 0;
    }

    .label-nama {
      font-size: 7pt;
      line-height: 1.2;
      margin-bottom: 1px;
      overflow: hidden;
      max-height: 8mm;
      word-break: break-word;
      color: #222;
    }

    .label-harga {
      font-size: 9pt;
      font-weight: bold;
      color: #8500be;
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
            <div class="label-nama">{{ $label['judul'] }}</div>
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