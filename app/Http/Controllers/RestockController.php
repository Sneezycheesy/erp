<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restock;
use App\Models\Component;
use App\Models\CuratechProduct;

use Mauricius\LaravelHtmx\Http\HtmxRequest;
use Mauricius\LaravelHtmx\Http\HtmxResponseClientRedirect;

class RestockController extends Controller
{
    // Return view only with products and components linked to one another
    public function index (HtmxRequest $rq) {
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
                })
        ]);
    }

    public function updateDesiredStock(HtmxRequest $request) {
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

    //
    public function store(string $id, HtmxRequest $rq) {
        $component = Component::find($id);
        $component_id = $component->id;
        $invoice = $rq->invoice ?? '';
        $vendor_id = $rq->vendor_id;
        $amount = $rq->amount;

        $valid = $this->validateRequest($id, $component, $vendor_id, $amount);

        if (!empty($valid)) {
                return view('curatech_components.partials.restock-form', [
                    'id' => $id,
                    'vendors' => $component->vendors()->get(),
                    'amount_error' => $valid['amount_error'] ?? null,
                    'vendor_error' => $valid['vendor_error'] ?? null,
                    'amount' => $amount,
                    'vendor_id' => $vendor_id,
                ]);
        }

        try {
            Restock::create([
                'component_id' => $component_id,
                'vendor_id' => $vendor_id,
                'amount' => $amount,
                'invoice' => $invoice,
            ]);
    
            $component->update([
                'stock' => $component->stock + (int) $rq->amount,
            ]);
    
            return view('curatech_components.partials.restock-form', [
                'id' => $id,
                'vendors' => $component->vendors()->get(),
                'success' => true,
            ]);
        } catch (Exception $e) {
            return "Something went horribly wrong!!! Use this error code to explain to a dev what you managed to break: $e";
        }
    }

    private function validateRequest(&$id, &$component, &$vendor_id, &$amount) {
        $returnVal = [];

        if (!isset($vendor_id)) {
            $returnVal['vendor_error'] = '*Verplicht veld';
        }

        if($amount == 0) {
            $returnVal['amount_error'] = '*Aantal moet groter zijn dan 0';
        }

        if (!isset($amount)) {
            $returnVal['amount_error'] = '*Verplicht veld';
        }

        $components_vendors = $component->vendors()->pluck('vendor_id')->toArray();
        if (!in_array($vendor_id, $components_vendors)) {
            $returnVal['vendor_id'] = '*Leverancier moet aan component gekoppeld zijn';
        }

        return $returnVal;
    }
}