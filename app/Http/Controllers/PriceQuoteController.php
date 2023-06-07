<?php

namespace App\Http\Controllers;

use App\Http\Resources\PriceQuote\PriceQuoteResource;
use App\Models\PriceQuote;

class PriceQuoteController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $priceQuote = PriceQuoteResource::collection(PriceQuote::all());
        return sendResponse($priceQuote);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $priceQuote
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $priceQuote = PriceQuote::findOrFail($id);
        return sendResponse(new PriceQuoteResource($priceQuote));
    }
}
