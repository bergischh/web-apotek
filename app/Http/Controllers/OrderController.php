<?php

namespace App\Http\Controllers;

use PDF;
use Excel;
use App\Models\Order;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $orders = Order::with('user')->simplePaginate(10);
        

        // $filterDate = $request->input('filter');

        // $orders = Order::whereDate('created_at', '=', $filterDate)->simplePaginate(3);

        $query = Order::query()->with('user');

        $filterDate = $request->input('filter');
        
        if ($filterDate) {
            $query->whereDate('created_at', '=', $filterDate);
        }
    
        $orders = $query->simplePaginate(10);
    
        return view("order.kasir.index", compact('orders'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = Medicine::all();
        return view("order.kasir.create", compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required',
            'medicines' => 'required',
        ],
        [
            'nama_customer.required' => 'Nama Pembeli harus diisi',
            'medicines.required' => 'Obat Harus diisi',
        ]);

        //dd($request->medicines); //ambil semua value dari input name=medicines

        $arrayDistinct = array_count_values($request->medicines); //ambil value dan hitung ada berapa
        //id 1 => 2, obat dengan id 

        $arrayAssocMedicines = [];

        //dd(arrayDistinc)
        foreach ($arrayDistinct as $id => $count) {
            $medicine = Medicine::where('id', $id)->first();
            $subPrice = $medicine->price  * $count;
            $arrayItem = [
                'id' => $id,
                'name_medicine' => $medicine->name,
                'qty' => $count,
                'price' => $medicine->price,
                'sub_price' => $subPrice,
            ];

            array_push($arrayAssocMedicines, $arrayItem);
        }

        //dd($arrayAssocMedicines)
        $totalPrice = 0;

        foreach ($arrayAssocMedicines as $item) {
            $totalPrice += (int)$item['sub_price'];
        }

        $priceWithPPN = $totalPrice + ($totalPrice * 0.01);

        $proses = Order::create([
            'user_id' => Auth::user()->id,
            'medicines' => $arrayAssocMedicines,
            'nama_customer' => $request->nama_customer,
            'total_price' => $priceWithPPN,
        ]);

        if ($proses) {
            $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();

            return redirect()->route('kasir.order.print', $order->id);
        }else {
            return redirect()->back()->with('failed', 'Gagal membuat data pembelian, silahkan coba kembali dengan data yang sama');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order, $id)
    {
        $order = Order::where('id', $id)->first();

        return view('order.kasir.print', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function downloadPDF($id)
    {
        //ambil data yang diperlukan, dan pastikan data berformat array 
        $order = Order::find($id)->toArray();
        //mengirim inisial variabel dari data yang akan digunakan pada layout PDF 
        view()->share('order', $order);
        //panggil blade yang akan di download 
        $pdf = PDF::loadView('order.kasir.download-pdf', $order);
        //kembalikan atau hasilkan bentuk pdf dengan nama file tertentu 
        return $pdf->download('receipt.pdf');


        //$order = Order::find($id);
        // $pdf = PDF::loadview9('order.kasir.download-pdf', compact('order'));
        //return $pdf->stream('receipt.pdf'); //kalo pake ini pdf nya ngga ke download cuman kebuka aja

        
    }

    public function data() {
        $orders = Order::with('user')->simplePaginate(5);

        return view("order.admin.index", compact('orders'));
    }

    public function exportExcel() {
        $fileName = 'data_pembelian.xlsx';
        return Excel::download(new OrdersExport, $fileName);
    }
}
