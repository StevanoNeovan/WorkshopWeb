<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; }

    @page {
      size: A4 portrait;
      margin: 2.3mm 3.3mm 2.3mm 3.3mm;
    }
    .page { page-break-after: always; }
    .page:last-child { page-break-after: avoid; }

    .row-table {
      width: 202.2mm;
      border-collapse: collapse;
      margin-bottom: 2.3mm;
    }
    .row-table:last-child { margin-bottom: 0; }

    .label-cell {
      width: 38mm;
      height: 18mm;
      border: 0.5px solid #999;
      vertical-align: middle;
      text-align: center;
      padding: 0.5mm 1mm;
      overflow: hidden;
    }
    .gap-cell {
      width: 3.3mm;
      height: 18mm;
      border: none;
      padding: 0;
    }

    .barcode-img {
      display: block;
      margin: 0 auto 0.3mm;
      width: 34mm;
      height: 6mm;
    }

    /* Kode buku (001, 002, dst) di bawah barcode */
    .label-kode {
      font-size: 5pt;
      color: #444;
      line-height: 1;
      margin-bottom: 0.5mm;
      letter-spacing: 0.5px;
    }

    /* Judul dipotong max ~20 karakter lalu ... */
    .label-nama {
      font-size: 5.5pt;
      line-height: 1.2;
      color: #222;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      max-width: 34mm;
      margin: 0 auto 0.3mm;
    }

    .label-harga {
      font-size: 8pt;
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
            {{-- Barcode PNG dari kode buku --}}
            <img class="barcode-img"
              src="data:image/png;base64,{{ $label['barcode_png'] }}"
              alt="{{ $label['kode'] }}">

            {{-- Kode buku (001, 002, dst) --}}
            <div class="label-kode">{{ $label['kode'] }}</div>

            {{-- Judul dipotong max 22 karakter --}}
            <div class="label-nama">{{ mb_strlen($label['judul']) > 22 ? mb_substr($label['judul'], 0, 22).'...' : $label['judul'] }}</div>

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