<?php
// == app/Http/Controllers/KasirController.php ==

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;



class KasirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman kasir
     */
    public function index()
    {
        return view('kasir.index');
    }

    /**
     * AJAX: Cari buku berdasarkan kode
     * GET /kasir/cari?kode=NV-01
     */
    public function cariBuku(Request $request)
    {
        $kode = strtoupper(trim($request->get('kode', '')));

        if (empty($kode)) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Kode buku tidak boleh kosong.',
                'data'    => null,
            ], 400);
        }

        $buku = Buku::with('kategori')->where('kode', $kode)->first();

        if (!$buku) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Buku dengan kode "' . $kode . '" tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Buku ditemukan.',
            'data'    => [
                'idbuku'    => $buku->idbuku,
                'kode'      => $buku->kode,
                'judul'     => $buku->judul,
                'pengarang' => $buku->pengarang,
                'harga'     => $buku->harga,
                'harga_fmt' => 'Rp ' . number_format($buku->harga, 0, ',', '.'),
                'kategori'  => $buku->kategori->nama_kategori ?? '-',
            ],
        ]);
    }

    /**
     * AJAX: Simpan transaksi pembayaran
     * POST /kasir/bayar
     */
    public function bayar(Request $request)
{
    $request->validate([
        'items'          => ['required', 'array', 'min:1'],
        'items.*.idbuku' => ['required', 'exists:buku,idbuku'],
        'items.*.jumlah' => ['required', 'integer', 'min:1'],
        'total'          => ['required', 'integer', 'min:1'],
    ]);
 
    // Hitung ulang total di server
    $total = 0;
    $items = [];
    foreach ($request->items as $item) {
        $buku     = Buku::find($item['idbuku']);
        if (!$buku) continue;
        $jumlah   = (int) $item['jumlah'];
        $subtotal = $buku->harga * $jumlah;
        $total   += $subtotal;
        $items[]  = [
            'idbuku'   => $buku->idbuku,
            'jumlah'   => $jumlah,
            'subtotal' => $subtotal,
        ];
    }
 
    if (empty($items)) {
        return response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'Tidak ada item valid.',
            'data'    => null,
        ], 422);
    }
 
    // Simpan header penjualan
    $penjualan = Penjualan::create([
        'id_user' => auth()->id(),
        'total'   => $total,
    ]);
 
    foreach ($items as $item) {
        PenjualanDetail::create([
            'id_penjualan' => $penjualan->id_penjualan,
            'idbuku'       => $item['idbuku'],
            'jumlah'       => $item['jumlah'],
            'subtotal'     => $item['subtotal'],
        ]);
    }
 
    // ===== GENERATE QR CODE =====
    // Isi QR: id_penjualan (bisa diubah ke URL struk jika perlu)
    $qrContent = 'PENJUALAN:' . $penjualan->id_penjualan
        . '|TOTAL:' . $total
        . '|TGL:' . now()->format('Y-m-d H:i');
 
    $qrCode = QrCode::create($qrContent)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
        ->setSize(200)
        ->setMargin(5)
        ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));
 
    $writer   = new PngWriter();
    $result   = $writer->write($qrCode);
 
    // Encode ke base64 untuk dikirim ke frontend
    $qrBase64 = base64_encode($result->getString());
 
    return response()->json([
        'status'         => 'success',
        'code'           => 200,
        'message'        => 'Transaksi berhasil disimpan.',
        'data'           => [
            'id_penjualan' => $penjualan->id_penjualan,
            'total'        => $total,
            'total_fmt'    => 'Rp ' . number_format($total, 0, ',', '.'),
            'jumlah_item'  => count($items),
            'qr_base64'    => $qrBase64,   // ← kirim ke frontend
            'qr_content'   => $qrContent,
        ],
    ]);
}
}