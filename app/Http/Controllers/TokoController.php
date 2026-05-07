<?php
// app/Http/Controllers/TokoController.php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Guest;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TokoController extends Controller
{
    // ============================================================
    // HALAMAN TOKO (PUBLIC - tanpa auth)
    // ============================================================

    /** GET /toko - halaman belanja guest */
    public function index()
    {
        $kategoris = Kategori::withCount('buku')->orderBy('nama_kategori')->get();
        $bukus     = Buku::with('kategori')->orderBy('judul')->get();
        return view('toko.index', compact('kategoris', 'bukus'));
    }

    /** AJAX GET /toko/buku?kode=NV-01 - cari buku by kode */
    public function cariBuku(Request $request)
    {
        $kode = strtoupper(trim($request->get('kode', '')));
        $buku = Buku::with('kategori')->where('kode', $kode)->first();

        if (!$buku) {
            return response()->json([
                'status' => 'error', 'code' => 404,
                'message' => "Buku \"{$kode}\" tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success', 'code' => 200,
            'data'   => [
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
     * AJAX GET /toko/buku-by-kategori?idkategori=1
     * Untuk select berjenjang: pilih kategori → tampil buku
     */
    public function bukuByKategori(Request $request)
    {
        $bukus = Buku::where('idkategori', $request->idkategori)
            ->orderBy('judul')->get(['idbuku','kode','judul','harga']);

        return response()->json(['status' => 'success', 'code' => 200, 'data' => $bukus]);
    }

    /**
     * POST /toko/checkout
     * Buat pesanan + guest otomatis + Snap Token Midtrans
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'nama'           => ['nullable', 'string', 'max:100'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.idbuku' => ['required', 'exists:buku,idbuku'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'total'          => ['required', 'integer', 'min:1'],
        ]);

        // Buat guest otomatis
        $guest = Guest::create([
            'kode_guest' => Guest::generateKode(),
            'nama'       => $request->nama ?: null,
            'phone'      => $request->phone ?: null,
        ]);

        // Hitung ulang total di server
        $total = 0;
        $items = [];
        $itemDetails = [];

        foreach ($request->items as $item) {
            $buku     = Buku::findOrFail($item['idbuku']);
            $jumlah   = (int) $item['jumlah'];
            $subtotal = $buku->harga * $jumlah;
            $total   += $subtotal;

            $items[] = [
                'idbuku'   => $buku->idbuku,
                'harga'    => $buku->harga,
                'jumlah'   => $jumlah,
                'subtotal' => $subtotal,
            ];

            // Format item detail untuk Midtrans
            $itemDetails[] = [
                'id'       => (string) $buku->idbuku,
                'price'    => $buku->harga,
                'quantity' => $jumlah,
                'name'     => mb_substr($buku->judul, 0, 50), // max 50 char
            ];
        }

        // Buat pesanan
        $kode = Pesanan::generateKode();
        $pesanan = Pesanan::create([
            'guest_id'          => $guest->id,
            'kode_pesanan'      => $kode,
            'total'             => $total,
            'midtrans_order_id' => $kode,
        ]);

        foreach ($items as $item) {
            PesananDetail::create([
                'pesanan_id' => $pesanan->id,
                'idbuku'     => $item['idbuku'],
                'harga'      => $item['harga'],
                'jumlah'     => $item['jumlah'],
                'subtotal'   => $item['subtotal'],
            ]);
        }

        // Request Snap Token ke Midtrans
        $snapToken = $this->getMidtransSnapToken($pesanan, $guest, $itemDetails, $total);

        if (!$snapToken) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal terhubung ke payment gateway. Coba lagi.',
            ], 500);
        }

        // Simpan snap token
        $pesanan->update(['snap_token' => $snapToken]);

        return response()->json([
            'status'     => 'success',
            'code'       => 200,
            'snap_token' => $snapToken,
            'kode_pesanan' => $kode,
            'guest_kode' => $guest->kode_guest,
            'total_fmt'  => 'Rp ' . number_format($total, 0, ',', '.'),
        ]);
    }

    /**
     * POST /toko/webhook
     * Midtrans mengirim notifikasi ke endpoint ini saat status bayar berubah
     */
    public function webhook(Request $request)
    {
        $serverKey  = config('midtrans.server_key');
        $orderId    = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount= $request->gross_amount;

        // Verifikasi signature key
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pesanan = Pesanan::where('midtrans_order_id', $orderId)->first();
        if (!$pesanan) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $paymentType       = $request->payment_type;

        // Update status berdasarkan notifikasi Midtrans
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $vaNumber = null;
            if (isset($request->va_numbers[0]['va_number'])) {
                $vaNumber = $request->va_numbers[0]['bank'] . ' - ' . $request->va_numbers[0]['va_number'];
            }

            $pesanan->update([
                'status_bayar' => 'lunas',
                'payment_type' => $paymentType,
                'va_number'    => $vaNumber,
                'paid_at'      => now(),
            ]);
        } elseif (in_array($transactionStatus, ['expire', 'expired'])) {
            $pesanan->update(['status_bayar' => 'expired']);
        } elseif ($transactionStatus === 'cancel' || $transactionStatus === 'deny') {
            $pesanan->update(['status_bayar' => 'failed']);
        }

        return response()->json(['message' => 'OK']);
    }

    /** GET /toko/status/{kode} - cek status pembayaran (polling dari frontend) */
    public function statusPesanan($kode)
    {
        $pesanan = Pesanan::where('kode_pesanan', $kode)->with('guest')->first();
        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'status'       => 'success',
            'status_bayar' => $pesanan->status_bayar,
            'kode_pesanan' => $pesanan->kode_pesanan,
            'total_fmt'    => 'Rp ' . number_format($pesanan->total, 0, ',', '.'),
        ]);
    }

    // ============================================================
    // HALAMAN ADMIN — lihat pesanan lunas
    // ============================================================

    /** GET /admin/pesanan - khusus user yang login (admin/vendor) */
    public function pesananAdmin()
    {
        $pesanans = Pesanan::with(['guest', 'detail.buku'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('toko.pesanan-admin', compact('pesanans'));
    }

    // ============================================================
    // PRIVATE: Midtrans API
    // ============================================================

    private function getMidtransSnapToken(Pesanan $pesanan, Guest $guest, array $itemDetails, int $total): ?string
    {
        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);
        $baseUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $payload = [
            'transaction_details' => [
                'order_id'     => $pesanan->midtrans_order_id,
                'gross_amount' => $total,
            ],
            'item_details'    => $itemDetails,
            'customer_details' => [
                'first_name' => $guest->nama ?? $guest->kode_guest,
                'phone'      => $guest->phone ?? '',
            ],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(15)
                ->post($baseUrl, $payload);

            if ($response->successful()) {
                return $response->json('token');
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans error: ' . $e->getMessage());
        }

        return null;
    }
}