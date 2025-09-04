<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DsInput;
use App\Models\Record;
use App\Models\M_Model_Part;

class DsInputController extends Controller
{
    public function index(Request $request)
    {
        $pics = ['Iqbal', 'Nauval', 'Dandi', 'Bayu F', 'Eko', 'Putut'];
        $ds_number = $request->get('ds_number', '');
        $ds_data = null;
        $type_delivery_display = ''; 
        $model_display = ''; 

        if ($ds_number && !session()->has('success')) {
            // Cari data dengan flag_record = 0
            $ds_data = DsInput::where('ds_number', $ds_number)
                ->where('flag_record', 0)
                ->first();

            if ($ds_data) {
                session()->flash('found', 'Data DS Number ditemukan.');

                // Tentukan Type Delivery untuk tampilan
                if (stripos($ds_data->di_type, 'export') !== false) {
                    $type_delivery_display = 'Export';
                } else {
                    $type_delivery_display = 'Local';
                }

                // Ambil model dari master_model_part
                $modelData = M_Model_Part::where('part_number', $ds_data->supplier_part_number)->first();
                $model_display = $modelData ? $modelData->model : '';
            } else {
                // Cek apakah data sebenarnya ada tapi flag_record = 1
                $alreadyExists = DsInput::where('ds_number', $ds_number)
                    ->where('flag_record', 1)
                    ->exists();

                if ($alreadyExists) {
                    session()->flash('exists', 'Data sudah ada di database.');
                } else {
                    session()->flash('notfound', 'Data DS Number tidak ditemukan.');
                }
            }
        }

        return view('dsinput.index', compact('pics', 'ds_data', 'ds_number', 'type_delivery_display', 'model_display'));
    }


    public function autocomplete(Request $request)
    {
        $search = $request->get('query', '');
        $results = DsInput::select('ds_number')
            ->where('flag_record', 0) 
            ->where('ds_number', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($results);
    }


    public function tambah(Request $request)
    {
        $request->validate([
            'pic' => 'required',
            'model' => 'required',
            'plant_destination' => 'required',
            'tgl_preparation' => 'required',
            'tgl_delivery' => 'required',
            'quantity' => 'required|numeric',
            'ds_number' => 'required' 
        ]);

        // Ambil tanggal preparation
        $tgl = $request->tgl_preparation; 
        $dateObj = \Carbon\Carbon::parse($tgl);
        $tglFormat = $dateObj->format('dmy'); 

        // Cari no_transaksi terakhir hari ini
        $last = Record::where('no_transaksi', 'like', 'AV'.$tglFormat.'%')
            ->orderBy('no_transaksi', 'desc')
            ->first();

        $newIncrement = $last ? str_pad(intval(substr($last->no_transaksi, -2)) + 1, 2, '0', STR_PAD_LEFT) : '01';
        $no_transaksi = 'AV'.$tglFormat.$newIncrement;

        // Cek type_delivery
        $type_delivery_final = stripos($request->type_delivery, 'export') !== false ? 'Export' : 'Local';

        // Ambil semua part_number yang sesuai dengan model
        $partNumbers = M_Model_Part::where('model', $request->model)
                                    ->distinct()
                                    ->pluck('part_number')
                                    ->toArray();

        // Simpan data ke tabel Record
        $record = Record::create([
            'no_transaksi'    => $no_transaksi,
            'tgl_bln_thn'     => $request->tgl_preparation,
            'tgl_bln_thn_dlv' => $request->tgl_delivery,
            'model'           => $request->model,
            'plant_dest'      => $request->plant_destination,
            'tipe_delv'       => $type_delivery_final,
            'pic'             => $request->pic,
            'qty'             => $request->quantity,
            'flag'            => '1'
        ]);

        // Update flag_record jadi 1 di ds_input
        DsInput::where('ds_number', $request->ds_number)
            ->update(['flag_record' => 1]);

        // Simpan data baru ke session
        $request->session()->put([
            'no_transaksi' => $record->no_transaksi,
            'tgl_bln_thn'  => $record->tgl_bln_thn,
            'plant_dest'   => $record->plant_dest,
            'tipe_delv'    => $record->tipe_delv,
            'model'        => $record->model,
            'qty'          => $record->qty,
            'pic'          => $record->pic,
            'part_numbers' => $partNumbers,
        ]);

        return redirect()->route('delivery.create')
                        ->with('success', 'Data Quantity Full berhasil disimpan. No Transaksi: '.$no_transaksi);
    }
}