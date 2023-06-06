<?php

namespace App\Http\Controllers;

use App\Http\Resources\PriceQuoteResource;
use App\Models\PriceQuote;
use Illuminate\Http\Request;

class PriceQuoteController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $priceQuote = PriceQuoteResource::collection(PriceQuote::all());
        return sendResponse($priceQuote);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Api\Order  $priceQuote
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $priceQuote = PriceQuote::findOrFail($id);
        return sendResponse(new PriceQuoteResource($priceQuote));
    }
}
