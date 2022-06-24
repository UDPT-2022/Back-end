<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cart_detail;
use App\Models\product;
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
        $DON_GIA = $product->GIA_SP;
        $GIA = $DON_GIA * $request->SO_LUONG;
        
        $term = [
            'MA_GIO_HANG' => $request->MA_GIO_HANG,
            'MA_SP' => $request->MA_SP,
            'SO_LUONG' => $request->SO_LUONG,
            'DON_GIA' => $DON_GIA,
            'GIA' => $GIA
        ];

        return cart_detail::create( $term );

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

        $DON_GIA = $detail->DON_GIA;
        $GIA = $DON_GIA * $request->SO_LUONG;
        $term = [
            'SO_LUONG' => $request->SO_LUONG,
            'GIA' => $GIA
        ];
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
        //
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
