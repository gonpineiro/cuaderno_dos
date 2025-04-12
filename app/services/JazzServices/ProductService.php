<?php

namespace App\Services\JazzServices;

class ProductService extends ApiService
{
    public function sayHello()
    {
        return $this->get('Producto/SayHello');
    }

    public function listProducts(string $empresa)
    {
        return $this->get("Producto/ListadoProductos/{$empresa}");
    }

    public function getProduct(int $id)
    {
        return $this->get("Producto/ConsultarProducto/{$id}");
    }

    public function getStock(int $id)
    {
        return $this->get("Producto/ConsultarStockDeProducto/{$id}");
    }

    public function updatePrice(int $id, int $lista, float $precio)
    {
        return $this->post("Producto/ActualizarPrecioDeUnProducto/{$id},{$lista},{$precio}", []);
    }

    public function listDiscounts()
    {
        return $this->post("Producto/ListarDescuentosPorCantidad", []);
    }

    public function listSuppliers()
    {
        return $this->get("Producto/ListarProveedoresDeProductos");
    }
}
