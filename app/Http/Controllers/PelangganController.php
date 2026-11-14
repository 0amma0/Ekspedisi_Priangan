<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\Telepon;
use Illuminate\Http\Request;
 
class PelangganController extends Controller
{
    public function index()
    {
        return view('pelanggan.index', ['pelanggans' => Customer::orderBy('name')->get()]);
    }
 
    public function create() { return view('pelanggan.form', ['pelanggan' => new Customer()]); }
 
    public function store(Request $r)
    {
        Customer::create($this->data($r));
        return redirect('/pelanggan')->with('ok', 'Pelanggan tersimpan.');
    }
 
    public function edit(Customer $pelanggan) { return view('pelanggan.form', compact('pelanggan')); }
 
    public function update(Request $r, Customer $pelanggan)
    {
        $pelanggan->update($this->data($r));
        return redirect('/pelanggan')->with('ok', 'Pelanggan diperbarui.');
    }
 
    private function data(Request $r): array
    {
        $v = $r->validate([
            'name' => 'required',
            'phone' => ['required', Telepon::aturan()],
            'address' => 'required',
        ]);
        $v['phone'] = Telepon::rapikan($v['phone']);   
        return $v;
    }
}
