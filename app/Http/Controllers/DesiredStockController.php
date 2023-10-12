<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\CuratechProduct;
use App\Models\DesiredStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Mauritius\LaravelHtmx\Http\HtmxResponseClientRedirect;

class DesiredStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $desired_stocks = DesiredStock::where('expiration_date', '>=', now())
            ->with('curatechProduct')
            ->paginate(50);
        $curatech_components = Component::whereHas('desired_stocks')->with('vendors')->paginate(50);

        return view('desired_stocks.index', [
            'desired_stocks' => $desired_stocks,
            'curatech_components' => $curatech_components,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CuratechProduct $curatech_product)
    {
        //
        $expiration_date = null;
        if ($curatech_product->activeDesiredStock()) {
            $expiration_date = $curatech_product->activeDesiredStock()->expiration_date;
        }

        $validator = Validator::make($request->all(), [
            'amount_initial' => 'required|numeric|integer|min:1',
            'start_date' => 'required|after:' . $expiration_date ?? now(),
            'expiration_date' => 'required|after:start_date',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        DesiredStock::create([
            'curatech_product_id' => $curatech_product->id,
            'amount_initial' => $request->amount_initial,
            'amount_made' => 0,
            'amount_to_make' => $request->amount_initial,
            'start_date' => $request->start_date,
            'expiration_date' => $request->expiration_date,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(DesiredStock $desiredStock)
    {
        //
        return view('desired_stocks.details', [
            'desired_stock' => $desiredStock,
            'curatech_product' => $desiredStock->curatechProduct()->first(),
            'curatech_components' => $desiredStock->curatechComponents()->paginate(50),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DesiredStock $desiredStock)
    {
        //
        return view('desired_stocks.edit', [
            'desired_stock' => $desiredStock,
            'curatech_product' => $desiredStock->curatechProduct()->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DesiredStock $desiredStock)
    {
        $validator = Validator::make($request->all(), [
            'amount_initial' => 'min:1',
            'start_date' => 'before:expiration_date',
            'expiration_date' => 'required|after:start_date',
        ]);

        // When validation fails, return the form with errors
        if($validator->fails()) {
            return redirect(route('desired_stocks.edit', $desiredStock))
                ->withErrors($validator);
        }

        $desiredStock->update($request->except('_token'));

        //
        return view('desired_stocks.partials.information-container', [
            'desired_stock' => $desiredStock,
            'curatech_product' => $desiredStock->curatechProduct()->first(),
            'curatech_components' => $desiredStock->curatechComponents()->paginate(50),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DesiredStock $desiredStock)
    {
        //
    }
}
