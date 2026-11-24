<?php

namespace App\Http\Controllers;

use App\Models\VendorCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorCategoryController extends Controller
{
    // List all vendor categories
    public function index()
    {
        $categories = VendorCategory::withCount('vendors')->orderBy('id', 'desc')->get();
        return view('admin_panel.vendors.categories', compact('categories'));
    }

    // Store or update vendor category
    public function store(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'name' => 'required|string|max:150|unique:vendor_categories,name,' . ($id ?? 'NULL') . ',id',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:0,1',
        ]);

        $data = [
            'name' => trim($request->name),
            'description' => $request->description,
            'status' => $request->has('status') ? (int) $request->status : 1,
        ];

        if ($id) {
            $category = VendorCategory::findOrFail($id);
            $oldName = $category->name;
            $category->update($data);

            if ($oldName !== $category->name) {
                Vendor::where('category', $oldName)->update(['category' => $category->name]);
            }

            $message = 'Vendor Category updated successfully.';
        } else {
            VendorCategory::create($data);
            $message = 'Vendor Category created successfully.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    // Delete vendor category
    public function destroy($id)
    {
        $category = VendorCategory::findOrFail($id);
        $categoryName = $category->name;

        // Optionally detach category from vendors
        Vendor::where('category', $categoryName)->update(['category' => null]);

        $category->delete();

        return redirect()->back()->with('success', 'Vendor Category deleted successfully.');
    }
}
