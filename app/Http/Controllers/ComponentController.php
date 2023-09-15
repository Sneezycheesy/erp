<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateComponentRequest;
use App\Http\Requests\StoreComponentRequest;
use App\Models\Component;
use App\Models\Vendor;

class ComponentController extends Controller
{
    public function get(Request $request) {
        $search = '';
        $comps = [];
        if (!empty($request->query()['search'])) {
            $search = $request->query()['search'];
            $comps = Component::where('component_id', 'like', "%$search%")
                        ->orWhere('description', 'like', $search)
                        ->get();
        }

        return view('curatech_components.Index', [
            'components' => empty($comps) ? Component::all() : $comps,
            'search' => $search,
        ]);
    }

    // Request to the edit page for a specific component
    // Edit page displays a form to enter how much new stock there is and which vendor is was purchased from
    public function editPage($id) {
        return view('components/component_edit', [
            'comp' => Component::where('component_id', $id)->First(),
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

    public function details($id) {
        return view('curatech_components.Details', [
            'comp' => Component::find($id),
            'vendors' => Component::find($id)->vendors()->get(),
            'all_vendors' => Vendor::all()->whereNotIn('id', Component::find($id)->vendors()->pluck('vendors_components.vendor_id')->toArray()),
        ]);
    }

    public function createPage() {
        return view('curatech_components.Create');
    }

    public function create(StoreComponentRequest $request) {
        $valid = $request->validated(true);

        if ($valid) {
            $valid['courant'] = $valid['courant'] == 'Y' ? 1 : 0;
            Component::create($valid);
        }

        // TODO: Add suppliers based on name
        // Also add the price of the component for each supplier
        // Create a new supplier when name is unique
        // Create link to existing suppier when name is exists

        return redirect()->back();
    }

    public function addVendor($id, Request $request) {
        Component::find($id)->vendors()->attach($request->vendor_id, ['vendor_product_nr' => $request->vendor_product_nr]);

        return redirect()->back();
    }
}



