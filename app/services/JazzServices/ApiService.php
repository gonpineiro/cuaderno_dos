<?php

namespace App\Services\JazzServices;

use App\Models\Jazz\LogJazzApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.jazz_api.base_url');
        $this->token = $this->authenticate();
    }

    /**
     * Autenticación en la API
     */
    public function authenticate(): string
    {
        return  Cache::remember('api_token_jazz', 3600, function () {

            $params = [
                'user' => config('services.jazz_api.username'),
                'password' => config('services.jazz_api.password'),
            ];

            $endpoint = 'Auth/Login';
            $path = config('services.jazz_api.base_url') . "/$endpoint";

            $logJazzApi = LogJazzApi::create([
                'endpoint' => $endpoint,
                'user_id' => auth()->user()->id,
                'metod' => 'authenticate',
                'request' => $params,
            ]);

            try {
                $start = microtime(true);
                $response = Http::post($path, $params);

                $logJazzApi->update([
                    'response' => [
                        'status' => $response->status(),
                        'headers' => $response->headers(),
                        'body' => $response->json() ?? $response->body(),
                    ],
                    'time_ms' => round((microtime(true) - $start) * 1000),
                ]);

                if ($response->failed()) {
                    $msg = 'Error al autenticar con la API Jazz';
                    throw new \Exception($msg, $response->status());
                }

                return $response->json('token');
            } catch (\Throwable $e) {
                $logJazzApi->update(['error' => get_excep_array($e)]);
                throw $e;
            }
        });
    }

    /**
     * Realizar una solicitud GET a la API
     */
    public function get(string $endpoint, array $queryParams = [])
    {
        $logJazzApi = LogJazzApi::create([
            'endpoint' => $endpoint,
            'user_id'  => auth()->id(),
            'metod'    => 'get',
            'request'  => [
                'query' => $queryParams,
            ],
        ]);

        try {
            $start = microtime(true);

            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/{$endpoint}", $queryParams);

            $logJazzApi->update([
                'response' => [
                    'status'  => $response->status(),
                    'headers' => $response->headers(),
                    'body'    => $response->json() ?? $response->body(),
                ],
                'time_ms' => round((microtime(true) - $start) * 1000),
            ]);

            if ($response->failed()) {
                throw new \Exception(
                    'Error en la consulta GET a Jazz',
                    $response->status()
                );
            }

            return $response->json();
        } catch (\Throwable $e) {

            $logJazzApi->update([
                'error' => get_excep_array($e),
            ]);

            throw $e;
        }
    }

    /**
     * Realizar una solicitud POST a la API
     */
    public function post(string $endpoint, array $data = [])
    {
        $logJazzApi = LogJazzApi::create([
            'endpoint' => $endpoint,
            'user_id'  => auth()->id(),
            'metod'    => 'POST',
            'request'  => [
                'body' => $data,
            ],
        ]);

        try {
            $start = microtime(true);

            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/{$endpoint}", $data);

            $logJazzApi->update([
                'response' => [
                    'status'  => $response->status(),
                    'headers' => $response->headers(),
                    'body'    => $response->json() ?? $response->body(),
                ],
                'time_ms' => round((microtime(true) - $start) * 1000),
            ]);

            if ($response->failed()) {
                throw new \Exception(
                    'Error en la consulta POST a Jazz',
                    $response->status()
                );
            }

            return $response->json();
        } catch (\Throwable $e) {

            $logJazzApi->update([
                'error' => get_excep_array($e),
            ]);

            throw $e;
        }
    }
}
