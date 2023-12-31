<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuratechProduct;
use App\Models\Component;

use Mauricius\LaravelHtmx\Http\HtmxRequest;
use Mauricius\LaravelHtmx\Http\HtmxResponseClientRedirect;

class PurchasesController extends Controller
{
    // Return view only with products and components linked to one another
    public function index(HtmxRequest $rq) {
        if ($rq->isHtmxRequest()) {
            return new HtmxResponseClientRedirect(route('purchases'));
        }


        return view('purchases.index', [
            'curatech_products' => CuratechProduct::with('components')->whereHas('components')->orderBy('name', 'ASC')->get(),
            'components' => Component::with('curatech_products')
                ->whereHas('curatech_products')
                ->orderBy('component_id', 'ASC')
                ->get()
                ->filter(function($comp) {
                    return $comp->required_stock() > 0;
                })->chunk(100),
        ]);
    }

    public function update(HtmxRequest $request) {
        foreach ($request->except('_token') as $curatech_product_id=>$stock) {
            CuratechProduct::find(str_replace('_', '.', $curatech_product_id))->update(['stock_desired' => $stock ?? 0]);
        }

        return view('purchases.partials.components-table', [
            'components' => Component::with('curatech_products')
            ->whereHas('curatech_products')
            ->orderBy('component_id', 'ASC')
            ->get()
            ->filter(function($comp) {
                return $comp->required_stock() > 0;
            }),
        ]);
    }
}
