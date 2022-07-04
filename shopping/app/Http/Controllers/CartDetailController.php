<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cart_detail;
use App\Models\product;
use App\Models\order;
use Error;

class CartDetailController extends Controller
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
        $product = product::find($request->MA_SP);
        if (empty($product) || $product == null)
            throw new Error('Sản phẩm không tồn tại');

        $product = product::find($request->MA_SP);
        if ($product->SL_CON_LAI - $request->SO_LUONG < 0)
            throw new Error('Không đủ hàng hóa');

        $updateProduct = [
            'SL_CON_LAI' => $product->SL_CON_LAI - $request->SO_LUONG
        ];
        $product->update($updateProduct);

        $DON_GIA = $product->GIA_SP;
        $GIA = $DON_GIA * $request->SO_LUONG;

        $term = [
            'MA_GIO_HANG' => $request->MA_GIO_HANG,
            'MA_SP' => $request->MA_SP,
            'SO_LUONG' => $request->SO_LUONG,
            'DON_GIA' => $DON_GIA,
            'GIA' => $GIA
        ];

        return cart_detail::create($term);
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
        return cart_detail::find($id);
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
        $detail = cart_detail::find($id);
        // Đơn hàng đã đặt
        if (!empty($detail->MA_DON_HANG) && $detail->MA_DON_HANG !=null)
            throw new Error('Đơn hàng đã tồn tại');

        $term = [];
        if (!empty($request->DAT_HANG) && $request->DAT_HANG!=null) {
            switch ($request->DAT_HANG) {
                case 0:
                    $term['DAT_HANG'] = false;
                    break;
                case 1:
                    $term['DAT_HANG'] = true;
                    break;
                default:
                    throw new Error('Lỗi tham số nhập');
            }
        }
        if (!empty($request->MA_DON_HANG) && $request->MA_DON_HANG !=null) {
            $order = order::find($request->MA_DON_HANG);
            if (empty($order) || $order == null)
                throw new Error('Hóa đơn không tồn tại');
            if ($detail->DAT_HANG == 0)
                throw new Error('Hàng không đặt');
            $term['MA_DON_HANG'] = $request->MA_DON_HANG;
        }
        $product = product::find($detail->MA_SP);
        if ($product->SL_CON_LAI + $detail->SO_LUONG - $request->SO_LUONG < 0)
            throw new Error('Không đủ hàng hóa');

        $updateProduct = [
            'SL_CON_LAI' => $product->SL_CON_LAI + $detail->SO_LUONG - $request->SO_LUONG
        ];
        $product->update($updateProduct);

        $term['DON_GIA'] = $detail->DON_GIA;
        $term['GIA'] = $detail->DON_GIA * $request->SO_LUONG;
        $term['SO_LUONG'] = $request->SO_LUONG;
        // $DON_GIA = $detail->DON_GIA;
        // $GIA = $DON_GIA * $request->SO_LUONG;
        // $term = [
        //     'SO_LUONG' => $request->SO_LUONG,
        //     'GIA' => $GIA,
        //     'MA_DON_HANG'=> $request->MA_DON_HANG
        // ];

        $detail->update($term);
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
        // Đơn hàng đã đặt
        $detail = cart_detail::find($id);
        
        if (!empty($detail->MA_DON_HANG) && $detail->MA_DON_HANG !=null)
            throw new Error('Đơn hàng đã tồn tại');

        $product = product::find($detail->MA_SP);
        $updateProduct = [
            'SL_CON_LAI' => $product->SL_CON_LAI + $detail->SO_LUONG
        ];
        $product->update($updateProduct);

        return cart_detail::destroy($id);
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
        $builder = cart_detail::query();
        $term = $request->all();
        if (!empty($term['MA_GIO_HANG'])) {
            $builder->where('MA_GIO_HANG', '=', $term['MA_GIO_HANG']);
        }
        if (!empty($term['MA_SP'])) {
            $builder->where('MA_SP', '=', $term['MA_SP']);
        }
        if (!empty($term['MA_DON_HANG'])) {
            $builder->where('MA_DON_HANG', '=', $term['MA_DON_HANG']);
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
