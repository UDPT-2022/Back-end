<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return product::all();
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
            'TEN_SP' => 'required',
            'MA_CUA_HANG' => 'required',
            'GIA_SP' => 'required|numeric|gte:0',
            'SL_CON_LAI' => 'required|numeric|gte:0',
        ]);
        // if ($request->SL_CON_LAI < 0 || $request->GIA_SP < 0)
        // {
        //     return response()->json([
        //         'error' => 'Invalid input (SL_CON_LAI or GIA_SP)'
        //     ]);
        // }
        // $check = product::where([['TEN_SP','like','%'.$request->TEN_SP.'%'],['MA_CUA_HANG','=',$request->MA_CUA_HANG]])->exists();
        // if ($check){
        //     return response()->json([
        //         'error' => 'product already in store'
        //     ]);
        // }
        return product::create($request->all());
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
       return product::find($id);
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
        $request->validate([
            'GIA_SP' => 'numeric|gte:0',
            'SL_CON_LAI' => 'numeric|gte:0',
        ]);

        $product = product::find($id);
        $product->update($request->all());
        return $product ;
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
        return product::destroy($id);
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
        $builder = product::query();
        $term = $request->all();
        if (!empty($term['TEN_SP'])){
            $builder->where('TEN_SP','like','%'.$term['TEN_SP'].'%');
        }
        if (!empty($term['MA_CUA_HANG'])){
            $builder->where('MA_CUA_HANG','=',$term['MA_CUA_HANG']);
        }
        if (!empty($term['GIA_SP'])){
            $builder->where('GIA_SP','=',$term['GIA_SP']);
        }
        if (!empty($term['SL_CON_LAI'])) {
            $builder->where('SL_CON_LAI', '=', $term['SL_CON_LAI']);
        }
        return $builder->get();
    }
}
