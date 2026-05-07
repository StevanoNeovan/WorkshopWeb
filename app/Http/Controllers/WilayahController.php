<?php
// app/Http/Controllers/WilayahController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('wilayah.index');
    }

    /** GET /wilayah/provinsi */
    public function provinsi()
    {
        $data = DB::table('provinces')->orderBy('name')->get(['id', 'name']);
        return response()->json(['status' => 'success', 'code' => 200, 'data' => $data]);
    }

    /** GET /wilayah/kota?province_id=31 */
    public function kota(Request $request)
    {
        $data = DB::table('regencies')
            ->where('province_id', $request->province_id)
            ->orderBy('name')->get(['id', 'name']);
        return response()->json(['status' => 'success', 'code' => 200, 'data' => $data]);
    }

    /** GET /wilayah/kecamatan?regency_id=3578 */
    public function kecamatan(Request $request)
    {
        $data = DB::table('districts')
            ->where('regency_id', $request->regency_id)
            ->orderBy('name')->get(['id', 'name']);
        return response()->json(['status' => 'success', 'code' => 200, 'data' => $data]);
    }

    /** GET /wilayah/kelurahan?district_id=357801 */
    public function kelurahan(Request $request)
    {
        $data = DB::table('villages')
            ->where('district_id', $request->district_id)
            ->orderBy('name')->get(['id', 'name']);
        return response()->json(['status' => 'success', 'code' => 200, 'data' => $data]);
    }
}