<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\ComboProduct;
use Illuminate\Http\Request;

class ComboController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->id) {
                $combos = Combo::where('id', $request->id)                    
                    ->with('products')
                    ->withCount(['products as cantidad'])
                    ->first();
            } else {
                $combos = Combo::with('products')->withCount(['products as cantidad'])->get();
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

            $detail = $request->detail;

            foreach ($detail as $comboProduct) {
                ComboProduct::create([
                    'combo_id' => $combo->id,
                    'product_id' => $comboProduct['id']
                ]);
            }
            
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

            foreach ($combo->detail as $comboProduct) {
                $comboProduct->delete();
            }

            foreach ($request->detail as $comboProduct) {
                ComboProduct::create([
                    'combo_id' => $combo->id,
                    'product_id' => $comboProduct['id']
                ]);
            }

            $combo = Combo::where('id', $request->id)                    
                    ->with('products')
                    ->withCount(['products as cantidad'])
                    ->first();

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
