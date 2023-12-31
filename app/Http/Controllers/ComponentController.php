<?php

namespace App\Http\Controllers;

use Mauricius\LaravelHtmx\Http\HtmxRequest;
use Mauricius\LaravelHtmx\Http\HtmxResponseClientRedirect;

use App\Http\Requests\UpdateComponentRequest;
use App\Http\Requests\StoreComponentRequest;
use App\Http\Requests\ConnectVendorToComponentRequest;
use App\Models\Component;
use App\Models\Vendor;
use App\Models\Stockroom;

class ComponentController extends Controller
{
    public function get(HtmxRequest $request) {

        if($request->isHtmxRequest()) {
            return view('curatech_components.partials.components', [
                'components' => Component::where('component_id', 'like', "%$request->search%")
                ->orWhere('description', 'like', "%$request->search%")
                ->get()
            ]);
        }

        return view('curatech_components.index', [
            'components' => Component::all(),
        ]);
    }

    // Request to the edit page for a specific component
    // Edit page displays a form to enter how much new stock there is and which vendor is was purchased from
    public function editPage($id, HtmxRequest $rq) {
        if ($rq->isHtmxRequest()) {
            return new HtmxResponseClientRedirect(route('components.edit', $id));
        }

        return view('curatech_components.edit', [
            'comp' => Component::where('component_id', $id)->First(),
            'vendors' => Component::find($id)->vendors()->withPivot('component_unit_price')->get(),
            'all_vendors' => Vendor::all()->whereNotIn('id', Component::find($id)->vendors()->pluck('vendors_components.vendor_id')->toArray()),
            'disabled' => false,
            'all_stockrooms' => Stockroom::all(),
            'linked_shelves' => Component::find($id)->shelves()->get(),
        ]);
    }

    // API call to update component data in the database
    // Add a new Restock item to the restocks table to keep a history of when stock was bought
    public function update($id, UpdateComponentRequest $request) {
        $request->validated();

        $comp = $request->except('_token');
        $comp['courant'] = $comp['courant'] == 'Y' ? 1 : 0;
        Component::find($id)->update($comp);

        return redirect()->back()->with('success', 'Component succesvol opgeslagen!');
    }

    public function details($id, HtmxRequest $rq) {
        if($rq->isHtmxRequest()) {
            return new HtmxResponseClientRedirect(route('components.details', $id));
        }
        $comp = Component::find($id);
        $restocks = $comp->restocks()->get()->keyBy('created_at')->toArray();
        $writeoffs = $comp->writeoffs()->with('curatech_product')->get()->keyBy('created_at')->toArray();
        $ownWriteoffs = $comp->ownWriteoffs()->get()->keyBy('created_at')->toArray();
        $purchase_history = array_merge($restocks, $writeoffs, $ownWriteoffs);
        krsort($purchase_history);

        return view('curatech_components.details', [
            'comp' => Component::find($id),
            'vendors' => Component::find($id)->vendors()->withPivot('component_unit_price')->get(),
            'all_vendors' => Vendor::all()->whereNotIn('id', Component::find($id)->vendors()->pluck('vendors_components.vendor_id')->toArray()),
            'disabled' => true,
            'linked_shelves' => Component::find($id)->shelves()->get(),
            'purchase_history' => $purchase_history,
        ]);
    }

    public function createPage(HtmxRequest $request) {

        return $request->isHtmxRequest() ? new HtmxResponseClientRedirect(route('components_create')) : view('curatech_components.create', [
            'disabled' => false,
        ]);
    }

    public function create(StoreComponentRequest $request) {
        $valid = $request->validated();

        if ($valid) {
            $valid['courant'] = $valid['courant'] == 'Y' ? 1 : 0;
            Component::create($valid);
        }

        return redirect()->back()->with(['success' => 'Component aangemaakt']);

        return view('curatech_products.partials.components')->with([

        ]);
    }

    public function addVendor($id, ConnectVendorToComponentRequest $request) {
        $request->validated();

        Component::find($id)->vendors()->attach($request->vendor_id, [
            'vendor_product_nr' => $request->vendor_product_nr,
            'component_unit_price' => $request->component_unit_price,
        ]);

        return redirect()->back();
    }

    public function removeVendor($id, $vendor_id, HtmxRequest $rq) {
        try {
            Component::find($id)->vendors()->detach($vendor_id);
            return '';
        } catch (Exeption $e) {
            return redirect()->back()->with(['error' => $e]);
        }
    }

    public function addShelf($id, HtmxRequest $rq) {
        if (!isset($rq->shelf_id)) {
            return '*Verplicht';
        }

        $shelves = Component::find($id)->shelves()->pluck('shelf_id')->toArray();

        if(in_array($rq->shelf_id, $shelves)) {
            return '*Dit component ligt al op deze plank';
        }

        Component::find($id)->shelves()->attach($rq->shelf_id);

        return new HtmxResponseClientRedirect(route('components.edit', $id));
    }

    public function removeShelf($id, $shelf_id, HtmxRequest $rq) {
        Component::find($id)->shelves()->detach($shelf_id);
        return '';
    }
}



