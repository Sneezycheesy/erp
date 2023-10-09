<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\DesiredStock;
use Illuminate\Http\Request;

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
        $curatech_components = Component::whereHas('desired_stocks')->with('vendors')->paginate(15);

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DesiredStock $desiredStock)
    {
        //
        return view('desired_stocks.details');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DesiredStock $desiredStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DesiredStock $desiredStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DesiredStock $desiredStock)
    {
        //
    }
}