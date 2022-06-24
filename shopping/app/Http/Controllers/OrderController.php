<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'MA_NGUOI_DUNG' => 'required',
            'MA_CUA_HANG' => 'required',
            'DIA_CHI' => 'required',
            'TRANG_THAI' => 'required',
        ]);
        return order::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return order::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $term = [
            'MA_SHIPPER' => $request->MA_SHIPPER,
            'DIA_CHI' => $request->DIA_CHI,
            'TRANG_THAI' => $request->TRANG_THAI
        ];
        $order = order::find($id);
        $order->update($term);
        return $order;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        return order::destroy($id);
    }

    /**
     * Search the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        //
        $builder = order::query();
        $term = $request->all();
        if (!empty($term['MA_NGUOI_DUNG'])) {
            $builder->where('MA_NGUOI_DUNG', '=', $term['MA_NGUOI_DUNG']);
        }
        if (!empty($term['MA_CUA_HANG'])) {
            $builder->where('MA_CUA_HANG', '=', $term['MA_CUA_HANG']);
        }
        if (!empty($term['MA_SHIPPER'])) {
            $builder->where('MA_SHIPPER', '=', $term['MA_SHIPPER']);
        }
        if (!empty($term['DIA_CHI'])) {
            $builder->where('DIA_CHI', 'like', '%' . $term['DIA_CHI'] . '%');
        }
        if (!empty($term['TONG_TIEN'])) {
            $builder->where('TONG_TIEN', '=', $term['TONG_TIEN']);
        }
        if (!empty($term['TRANG_THAI'])) {
            $builder->where('TRANG_THAI', '=', $term['TRANG_THAI']);
        }
        return $builder->get();
    }
}
