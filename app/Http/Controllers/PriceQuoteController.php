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
}
