<?php
// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** GET /customer - Data Customer */
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('customer.index', compact('customers'));
    }

    /** GET /customer/tambah1 - Form foto BLOB */
    public function tambah1Form()
    {
        return view('customer.tambah1');
    }

    /**
     * POST /customer/tambah1
     * Simpan foto sebagai BLOB (binary) ke database
     */
    public function tambah1Store(Request $request)
    {
        $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'email'     => ['nullable', 'email', 'max:100'],
            'no_hp'     => ['nullable', 'string', 'max:20'],
            'foto_blob' => ['required', 'string'], // base64 dari kamera
        ]);

        // Decode base64 → binary
        $base64 = $request->foto_blob;

        // Hapus prefix "data:image/jpeg;base64," jika ada
        if (str_contains($base64, ',')) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $binaryData = base64_decode($base64);

        Customer::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'no_hp'     => $request->no_hp,
            'foto_blob' => $binaryData,
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Customer berhasil ditambahkan (foto BLOB).');
    }

    /** GET /customer/tambah2 - Form foto FILE */
    public function tambah2Form()
    {
        return view('customer.tambah2');
    }

    /**
     * POST /customer/tambah2
     * Simpan foto sebagai FILE, simpan path ke database
     */
    public function tambah2Store(Request $request)
    {
        $request->validate([
            'nama'       => ['required', 'string', 'max:100'],
            'email'      => ['nullable', 'email', 'max:100'],
            'no_hp'      => ['nullable', 'string', 'max:20'],
            'foto_file'  => ['required', 'string'], // base64 dari kamera
        ]);

        // Decode base64 → simpan sebagai file
        $base64 = $request->foto_file;
        if (str_contains($base64, ',')) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $binaryData = base64_decode($base64);
        $filename   = 'customer_' . time() . '_' . uniqid() . '.jpg';
        $path       = 'customers/' . $filename;

        // Simpan ke storage/app/public/customers/
        Storage::disk('public')->put($path, $binaryData);

        Customer::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'no_hp'      => $request->no_hp,
            'foto_path'  => $path,
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Customer berhasil ditambahkan (foto file).');
    }

    /** DELETE /customer/{id} */
    public function destroy(Customer $customer)
    {
        // Hapus file jika ada
        if ($customer->foto_path) {
            Storage::disk('public')->delete($customer->foto_path);
        }
        $customer->delete();

        return back()->with('success', 'Customer dihapus.');
    }
}