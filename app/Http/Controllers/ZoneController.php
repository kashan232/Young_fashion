<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::orderByDesc('id')->get();

        $topZoneName = '—';
        $maxCount = 0;

        foreach ($zones as $z) {
            $count = Customer::where('zone', $z->zone)
                ->orWhere('zone', (string) $z->id)
                ->count();
            $z->customer_count = $count;

            if ($count > $maxCount) {
                $maxCount = $count;
                $topZoneName = $z->zone;
            }
        }

        $totalZones = $zones->count();
        $totalMappedCustomers = Customer::whereNotNull('zone')->where('zone', '!=', '')->count();

        return view('admin_panel.zone.index', compact('zones', 'totalZones', 'totalMappedCustomers', 'topZoneName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone' => 'required|string|max:255'
        ]);

        $zone = Zone::updateOrCreate(
            ['id' => $request->edit_id],
            ['zone' => trim($request->zone)]
        );

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => $request->edit_id ? 'Zone updated successfully.' : 'Zone created successfully.',
                'zone'    => $zone
            ]);
        }

        return redirect()->route('zone.index')->with('success', 'Zone saved successfully.');
    }

    public function edit($id)
    {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        if (request()->ajax()) {
            return response()->json([
                'status'  => 'deleted',
                'message' => 'Zone deleted successfully.'
            ]);
        }

        return redirect()->route('zone.index')->with('success', 'Zone deleted successfully.');
    }
}
