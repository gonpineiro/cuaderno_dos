<?php

namespace App\Http\Controllers;

use App\Http\Resources\CoeficienteResource;
use App\Models\Coeficiente;
use App\Models\ProductJazz;
use App\Models\Provider;
use App\Models\Province;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends \App\Http\Controllers\Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (!$token = \Tymon\JWTAuth\Facades\JWTAuth::attempt($credentials)) {
            return sendResponse(null, 'Credenciales invalidas', 400);
        }

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        \Tymon\JWTAuth\Facades\JWTAuth::invalidate($request->token);

        return sendResponse('Afuera!!');
    }

    public function get_user()
    {
        return response()->json(['user' => auth()->user()]);
    }

    public function refresh()
    {
        return $this->respondWithToken(\Tymon\JWTAuth\Facades\JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $JWTAuth = \Tymon\JWTAuth\Facades\JWTAuth::class;
        $user = User::find(auth()->user()->id);
        $user->roles->map(function ($r) {
            $r->permissions;
        });

        $coeficientes  = Coeficiente::orderBy('position', 'asc')->get();
        $data = [
            'product_jazz_report' => $this->getProductJazzReport(),
            'user' => $user,
            'coeficientes' => CoeficienteResource::collection($coeficientes),
            'provinces' => Province::all(),
            'providers' => Provider::all(),
            'access_token' => $token,
            'tables' => Table::all(),
        ];

        return sendResponse($data);
    }

    private function getProductJazzReport(): array
    {
        $productJazzUpdated = ProductJazz::where('is_updated', 1)->count();
        $productJazzNotUpdated = ProductJazz::where('is_updated', 0)->count();
        $productJazzTotal = ProductJazz::count();

        $inicio = \Spatie\Activitylog\Models\Activity::where('log_name', 'success.updateStockPrices')
            ->where('description', 'Inicio')
            ->latest('created_at')
            ->first();

        $fin = \Spatie\Activitylog\Models\Activity::where('log_name', 'success.updateStockPrices')
            ->where('description', 'Proceso finalizado')
            ->latest('created_at')
            ->first();

        $finProceso = $fin->created_at;
        if ($finProceso && $inicio && $finProceso <= $inicio->created_at) {
            $finProceso = null;
        }

        return [
            'is_updated_1' => $productJazzUpdated,
            'is_updated_0' => $productJazzNotUpdated,
            'total' => $productJazzTotal,
            'inicio_proceso' => $inicio->created_at,
            'fin_proceso' => $finProceso,
        ];
    }
}
