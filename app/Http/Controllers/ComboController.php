<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Http\Request;

class ComboController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->id) {
                $combos = Combo::where('id', $request->id)
                    ->with('detail.product')->first();
            } else {
                $combos = Combo::with('detail.product')->get();
            }
            return sendResponse($combos);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $combo = Combo::create($request->all());
            return sendResponse($combo);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $combo = Combo::findOrFail($request->id);
            $combo->fill($request->all())->save();
            return sendResponse($combo);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function destroy($request)
    {
        $combo = Combo::findOrFail($request->id);
        $combo->delete();
        return sendResponse($combo);
    }
}
