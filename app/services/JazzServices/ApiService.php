<?php

namespace App\Services\JazzServices;

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
    private function authenticate(): string
    {
        return Cache::remember('api_token', 3600, function () {
            $response = Http::post(config('services.jazz_api.base_url') . "/Auth/Login", [
                'user' => config('services.jazz_api.username'),
                'password' => config('services.jazz_api.password'),
            ]);

            if ($response->failed()) {
                throw new \Exception('Error al autenticar con la API.');
            }

            return $response->json('token'); // Ajusta según la estructura de respuesta
        });
    }

    /**
     * Realizar una solicitud GET a la API
     */
    public function get(string $endpoint, array $queryParams = [])
    {
        $response = Http::withToken($this->token)->get("{$this->baseUrl}/{$endpoint}", $queryParams);

        if ($response->failed()) {
            throw new \Exception('Error en la consulta GET: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Realizar una solicitud POST a la API
     */
    public function post(string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->token)->post("{$this->baseUrl}/{$endpoint}", $data);

        if ($response->failed()) {
            throw new \Exception('Error en la consulta POST: ' . $response->body());
        }

        return $response->json();
    }
}
