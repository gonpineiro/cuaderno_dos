# Documentación de Productos

## Producto de Ajuste (`AJUSTE`)

### Propósito

El producto con código `AJUSTE` se utiliza para registrar ajustes en el precio final de un pedido, específicamente cuando el cliente utiliza un medio de pago que aplica un recargo.

### Implementación

#### Método: `Product::productoAjuste()`

```php
public static function productoAjuste()
{
    return self::where('code', 'AJUSTE')->first();
}
```

**Ubicación**: `app/Models/Product.php:100`

**Comportamiento**:
- Busca y retorna el producto cuya columna `code` sea igual a `'AJUSTE'`
- Retorna `null` si no existe ningún producto con ese código

#### Uso en OrderController

```php
if (isset($request->recargo) && $request->recargo) {
    $recargoProducto = Product::productoAjuste();
    $data = [
        'product_id' => $recargoProducto->id,
        'order_id' => $order_id,
        'state_id' => $detail[0]['state']['id'],
        'unit_price' => $request->recargo,
        'description' => 'AJUSTE POR MEDIO DE PAGO',
        'amount' => 1,
    ];
    OrderProduct::create($data);
}
```

**Ubicación**: `app/Http/Controllers/OrderController.php:165-176`

**Flujo**:
1. Se verifica si la request contiene un parámetro `recargo` con valor truthy
2. Se obtiene el producto de ajuste mediante `Product::productoAjuste()`
3. Se crea un registro en `order_products` con:
   - Producto de ajuste
   - Precio unitario igual al valor del recargo
   - Cantidad de 1 unidad
   - Descripción `'AJUSTE POR MEDIO DE PAGO'`

### Requisitos

- Debe existir un producto en la tabla `products` con `code = 'AJUSTE'`
- El producto debe estar activo y asociado a un estado válido

### Casos de Uso

| Medio de Pago | Recargo | Resultado |
|---------------|---------|-----------|
| Tarjeta de crédito/débito | Sí | Se agrega producto de ajuste al pedido |
| Transferencia bancaria | No | No se agrega ajuste |
| Efectivo | No | No se agrega ajuste |

### Consideraciones

- El ajuste se agrega como un item más del pedido, permitiendo trazabilidad completa
- El valor del recargo se refleja en el total del pedido
- La descripción `'AJUSTE POR MEDIO DE PAGO'` facilita la identificación en reportes y exportaciones
