<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\Product\ComboProductResource;
use App\Models\Combo;
use App\Models\ComboProduct;

class ComboController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->id) {
                $combos = Combo::where('id', $request->id)->first();
                $combos = new ComboProductResource($combos);
            } else {
                $combos = Combo::orderBy('id', 'desc')->get();
                $combos = ComboProductResource::collection($combos);
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

            return sendResponse(new ComboProductResource($combo));
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

            $combo = Combo::find( $request->id);

            return sendResponse(new ComboProductResource($combo));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $combo = Combo::findOrFail($request->id);
        $combo->delete();
        return sendResponse(new ComboProductResource($combo));
    }
}