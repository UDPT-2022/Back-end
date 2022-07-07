<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order;
use App\Models\product;
use App\Models\order_detail;
use Illuminate\Support\Facades\Http;
use Error;

class OrderDetailController extends Controller
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
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        $rules = [
            'MA_DON_HANG' => 'required|numeric',
            'MA_SP' => 'required|numeric',
            'SO_LUONG' => 'required|numeric|gt:0'
        ];
        $fields = $request->validate($rules);

        $order = order::find($fields['MA_DON_HANG']);
        if (empty($order) || $order == null)
            throw new Error('order không tồn tại');

        if ($order['TRANG_THAI'] != 'đặt')
            throw new Error('order đang được xử lý');

        $order = order::find($fields['MA_DON_HANG']);
        if (empty($order) || $order == null)
            throw new Error('order không tồn tại');

        $product = product::find($fields['MA_SP']);
        if (empty($product) || $product == null)
            throw new Error('product không tồn tại');

        if ($CurUser != null) {
            if ($CurUser['id'] != $order['MA_NGUOI_DUNG'])
                throw new Error('không có quyền điều chỉnh');
        }
        if ($order['MA_CUA_HANG'] != $product['MA_CUA_HANG']) {
            throw new Error('product không nằm trong cửa hàng của order');
        }

        if ($product['SL_CON_LAI'] - $fields['SO_LUONG'] < 0)
            throw new Error('Không đủ hàng hóa');

        $product->update([
            'SL_CON_LAI' => $product['SL_CON_LAI'] - $fields['SO_LUONG']
        ]);
        $fields['DON_GIA'] = $product['GIA_SP'];
        $fields['GIA'] = $fields['DON_GIA'] * $fields['SO_LUONG'];


        $detail = order_detail::create($fields);
        return $detail;
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
        return order_detail::find($id);
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
        $detail = order_detail::find($id);
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        if ($CurUser != null) {
            $order = order::find($detail['MA_DON_HANG']);
            if ($CurUser['id'] != $order['MA_NGUOI_DUNG'])
                throw new Error('không có quyền điều chỉnh');
        }

        $rules = [
            'SO_LUONG' => 'numeric|gt:0'
        ];
        $fields = $request->validate($rules);
        $product = product::find($detail['MA_SP']);

        $slct = $product['SL_CON_LAI'] + $detail['SO_LUONG'] - $fields['SO_LUONG'];

        if ($slct < 0)
            throw new Error('Không đủ hàng hóa');
        $product->update(['SL_CON_LAI' => $slct]);

        $detail->update($fields);
        return $detail;
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
        $detail = order_detail::find($id);
        $order = order::find($detail['MA_DON_HANG']);
        if ($order['TRANG_THAI'] != 'đặt' && $order['TRANG_THAI'] != 'chuẩn bị') {
            throw new Error('Order đã được xử lý');
        }
        return order_detail::destroy($id);
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

        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }

        if (count($request->all())  <= 0 && $CurUser != null)
            return [];

        $builder = order_detail::query();
        $term = $request->all();
        if (!empty($term['MA_DON_HANG'])) {
            $order = order::find($term['MA_DON_HANG']);
            $find = '-1';
            if (!empty($order) && $order != null && $CurUser != null && $order['MA_NGUOI_DUNG'] == $CurUser['id'])
                $find = $term['MA_DON_HANG'];

            $builder->where('MA_DON_HANG', '=', $find);
        }
        if (!empty($term['MA_SP'])) {
            $builder->where('MA_SP', '=', $term['MA_SP']);
        }
        if (!empty($term['SO_LUONG'])) {
            $builder->where('SO_LUONG', '=', $term['SO_LUONG']);
        }
        if (!empty($term['DON_GIA'])) {
            $builder->where('DON_GIA', '=', $term['DON_GIA']);
        }
        if (!empty($term['GIA'])) {
            $builder->where('GIA', '=', $term['GIA']);
        }

        return $builder->get();
    }
}
