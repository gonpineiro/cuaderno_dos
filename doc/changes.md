# Registro de Cambios

## Cambios sin commit

### [Fecha: Por definir]

#### Model: Product (`app/Models/Product.php`)

- **Agregado**: Método estático `productoAjuste()`
  - Retorna el producto con código `'AJUSTE'`
  - Utilizado para generar ajustes de precio por medio de pago

#### Controller: OrderController (`app/Http/Controllers/OrderController.php`)

- **Modificado**: Método `storeOrderProduct()`
  - Agregada lógica para detectar si viene un recargo por medio de pago (`$request->recargo`)
  - Cuando existe recargo, se crea un `OrderProduct` asociado al producto de ajuste
  - El ajuste se registra con:
    - `description`: `'AJUSTE POR MEDIO DE PAGO'`
    - `amount`: `1`
    - `unit_price`: valor del recargo
  - Agregado import de `Product`

#### Controller: PriceQuoteController (`app/Http/Controllers/PriceQuoteController.php`)

- **Temporal**: Se agregaron excepciones temporales en `asignarSiniestro()` y `asignarCliente()` para pruebas

---

## Historial

*(Agregar entradas aquí conforme se realizan cambios)*
