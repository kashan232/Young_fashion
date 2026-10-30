<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('id', 'desc')->get();
        return view('admin_panel.size.index', compact('sizes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:sizes,name,' . $request->edit_id . ',id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                    'message' => $validator->errors()->first()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', $validator->errors()->first());
        }

        // UPDATE
        if ($request->filled('edit_id')) {
            $size = Size::find($request->edit_id);

            if (!$size) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => 'Size not found',
                    'message' => 'Size not found'
                ], 404);
            }

            $message = 'Size Updated Successfully';
        }
        // CREATE
        else {
            $size = new Size();
            $message = 'Size Created Successfully';
        }

        $size->name = $request->name;
        $size->size_code = $request->size_code ?? null;
        $size->save();

        // PRODUCT PAGE MODAL RESPONSE
        if ($request->page === 'product_page') {
            return response()->json([
                'status'  => 'success',
                'success' => true,
                'id'      => $size->id,
                'name'    => $size->name,
                'message' => $message
            ]);
        }

        // NORMAL AJAX RESPONSE
        return response()->json([
            'status'  => 'success',
            'success' => $message,
            'message' => $message,
            'reload'  => true
        ]);
    }

    public function delete($id)
    {
        $size = Size::find($id);
        if ($size) {
            $size->delete();
            $msg = [
                'status'  => 'success',
                'success' => 'Size Deleted Successfully',
                'message' => 'Size Deleted Successfully',
                'reload'  => route('Size.home'),
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('Size.home')->with('success', 'Size Deleted Successfully');
            }
        } else {
            $msg = [
                'status'  => 'error',
                'error'   => 'Size Not Found',
                'message' => 'Size Not Found'
            ];
            if (!request()->ajax() && !request()->wantsJson()) {
                return redirect()->route('Size.home')->with('error', 'Size Not Found');
            }
        }
        return response()->json($msg);
    }

    public function getSizesJson()
    {
        $sizes = Size::select('id', 'name', 'size_code')->orderBy('name', 'asc')->get();
        return response()->json($sizes);
    }
}
