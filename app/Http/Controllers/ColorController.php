<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('id', 'desc')->get();
        return view('admin_panel.color.index', compact('colors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120|unique:colors,name,' . $request->edit_id . ',id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', $validator->errors()->first());
        }

        if ($request->filled('edit_id')) {
            $color = Color::find($request->edit_id);
            if (! $color) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => 'Variant/Color not found',
                    'message' => 'Variant/Color not found',
                ], 404);
            }
            $message = 'Variant & Color Updated Successfully';
        } else {
            $color = new Color();
            $message = 'Variant & Color Created Successfully';
        }

        $color->name = trim($request->name);
        $color->color_code = $request->filled('color_code') ? trim($request->color_code) : null;
        $color->save();

        if ($request->page === 'product_page') {
            return response()->json([
                'status'     => 'success',
                'success'    => true,
                'id'         => $color->id,
                'name'       => $color->name,
                'color_code' => $color->color_code,
                'message'    => $message,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'success' => $message,
            'message' => $message,
            'reload'  => true,
        ]);
    }

    public function delete($id)
    {
        $color = Color::find($id);
        if ($color) {
            $color->delete();
            if (! request()->ajax() && ! request()->wantsJson()) {
                return redirect()->route('Color.home')->with('success', 'Variant & Color Deleted Successfully');
            }
            return response()->json([
                'status'  => 'success',
                'success' => 'Variant & Color Deleted Successfully',
                'message' => 'Variant & Color Deleted Successfully',
                'reload'  => route('Color.home'),
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'error'   => 'Variant & Color not found',
            'message' => 'Variant & Color not found',
        ], 404);
    }

    public function getColorsJson()
    {
        return response()->json(
            Color::orderBy('name', 'asc')->get(['id', 'name', 'color_code'])
        );
    }
}
