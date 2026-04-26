# Integración con Jazz

## Índice
1. [Visión General](#visión-general)
2. [Productos](#productos)
   - [Sincronización por Consola (Proceso Diario)](#sincronización-por-consola-proceso-diario)
   - [Sincronización por API (Bajo Demanda)](#sincronización-por-api-bajo-demanda)
3. [Pedidos](#pedidos)
   - [Sincronización de Pedido](#sincronización-de-pedido)
   - [Envío de Pedido a Jazz](#envío-de-pedido-a-jazz)
4. [Clientes](#clientes)
   - [Búsqueda en Jazz](#búsqueda-en-jazz)
   - [Relación Cliente-App con Jazz](#relación-cliente-app-con-jazz)
5. [Usuarios](#usuarios)
   - [Relación con Vendedor Jazz](#relación-con-vendedor-jazz)
6. [Servicios y Configuración](#servicios-y-configuración)

---

## Visión General

El sistema se integra con **Jazz** (sistema ERP externo) para sincronizar información de productos, gestionar pedidos y buscar clientes. La aplicación funciona como intermediario entre el usuario y el sistema Jazz.

### Arquitectura de la Integración

```
┌─────────────────────────────────────────────────────────────────┐
│                     Aplicación Laravel                          │
├─────────────────────────────────────────────────────────────────┤
│  Controllers                                                     │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────────┐ │
│  │JazzController│  │ProductJazzCtrl │  │  OrderController     │ │
│  └──────┬───────┘  └───────┬───────┘  └──────────┬───────────┘ │
│         │                  │                     │              │
│  ┌──────┴──────────────────┴─────────────────────┴───────────┐ │
│  │                 Services Layer                              │ │
│  │  ┌─────────────┐  ┌──────────────┐  ┌─────────────────┐    │ │
│  │  │ApiService   │  │ProductService│  │  PedidoService  │    │ │
│  │  └─────────────┘  └──────────────┘  └─────────────────┘    │ │
│  └──────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
            │                      │                    │
    ┌───────┴───────┐        ┌─────┴──────┐        ┌──────┴──────┐
    │   MySQL       │        │  MySQL     │        │  Jazz API   │
    │  (Local)      │        │(product_   │        │  (REST)     │
    │               │        │ jazz)       │        │             │
    └───────────────┘        └─────────────┘        └─────────────┘
            │
    ┌───────┴──────────────────┐
    │  Conexión Directa MySQL │
    │    (DB::connection)     │
    └─────────────────────────┘
```

### Conexiones de Base de Datos

| Conexión | Propósito |
|----------|-----------|
| `default` (mysql) | Tablas propias de la aplicación |
| `jazz` | Acceso directo a la base de datos de Jazz (MySQL) |

---

## Productos

La integración de productos permite mantener sincronizado el catálogo entre la aplicación y Jazz, incluyendo precios, stock y datos complementarios.

### Tablas Involucradas

| Tabla | Propósito |
|-------|-----------|
| `product_jazz` | Catálogo principal de productos sincronizados |
| `product_jazz_temp` | Tabla temporal para proceso de sincronización |
| `product_jazz_history` | Historial de sincronizaciones |
| `product_brands_jazz` | Marcas de productos en Jazz |
| `products` | Productos propios de la aplicación (relacionados con Jazz) |

### Campo de Relación

Los productos locales (`products`) tienen un campo `idProducto` que los vincula con el registro correspondiente en `product_jazz` (y Jazz).

---

### Sincronización por Consola (Proceso Diario)

La sincronización por consola está diseñada para ejecutarse periódicamente (diariamente) para mantener actualizados los precios y stocks de todos los productos.

#### Comando: `sync:product-jazz`

**Ubicación:** `app/Console/Commands/SyncProductJazz.php`

```bash
php artisan sync:product-jazz
```

**Proceso:**
1. Obtiene productos locales que tienen `idProducto` pero no existen en `product_jazz`
2. Para cada producto, consulta la API de Jazz (`ProductService::getProduct()`)
3. Actualiza el registro en `product_jazz` con los datos obtenidos
4. Registra errores y genera estadísticas de éxito

**Código relevante:**
```php
// Obtiene productos que necesitan sincronización
$productos = Product::whereNotNull('idProducto')
    ->whereNotIn('idProducto', function ($query) {
        $query->select('id')->from('product_jazz');
    })
    ->select('idProducto')
    ->get();

// Por cada producto: consulta API y actualiza
$data = $ps->getProduct($producto->idProducto);
$controller->updateProductJazz($data);
```

#### Comando: `update:stock-prices`

**Ubicación:** `app/Console/Commands/UpdateStockPrices.php`

```bash
php artisan update:stock-prices
```

**Proceso:**
1. **Actualización de stock y precios:** Consulta Jazz directamente (MySQL) y actualiza todos los registros en `product_jazz`
2. **Actualización de marcas:** Sincroniza las marcas desde Jazz

**SQL utilizado (conexión directa a Jazz):**
```sql
SELECT 
    p.IdProducto,
    p.numero,
    p.Nombre,
    pcc.StockMin, pcc.StockMax, pcc.PuntoPedido,
    (SELECT SUM(fa.Cantidad * 
        CASE WHEN f.Tipo IN (3, 4) THEN 1 ELSE -1 END)
     FROM facturas_articulos fa
     JOIN facturas f ON f.NroInterno = fa.NroInterno
     WHERE fa.IdProducto = p.IdProducto) AS stock
FROM productos p
LEFT JOIN precios_venta pv ON p.IdProducto = pv.IdProducto
LEFT JOIN productoscombinacionescabecera pcc ON pcc.IdProducto = p.IdProducto
```

---

### Sincronización por API (Bajo Demanda)

La sincronización por API permite solicitar productos específicos bajo demanda, ideal para actualizaciones inmediatas o procesos de análisis comparativo.

#### Flujo de Sincronización

```
┌─────────────┐    ┌──────────────┐    ┌─────────────┐    ┌────────────┐
│   Jazz DB   │───▶│Temp Table    │───▶│  Análisis   │───▶│ Sync       │
│  (SQL)      │    │(product_jazz │    │(processTemp)│    │(upsert)    │
│             │    │  _temp)      │    │             │    │            │
└─────────────┘    └──────────────┘    └─────────────┘    └────────────┘
```

#### Paso 1: Descarga a tabla temporal

**Endpoint:** `GET /api/producto/jazz/inicio-sync`

**Ubicación:** `JazzController::syncProductTemp()`

Proceso:
1. Limpia la tabla temporal: `DELETE FROM product_jazz_temp`
2. Consulta Jazz (conexión MySQL directa):
   - Tabla `productos`: datos básicos
   - Tabla `precios_venta`: precios por lista
   - Tabla `comodines`/`comodinesvalores`: campos adicionales (código original, equivalencia, etc.)
   - Tabla `facturas_articulos` + `facturas`: cálculo de stock
3. Inserta los datos en `product_jazz_temp` con estado `en_proceso`

#### Paso 2: Análisis de diferencias

**Endpoint:** `GET /api/producto/jazz/analizar`

**Ubicación:** `ProductJazz::processTemp()`

Clasifica los productos en tres estados:

| Estado | Descripción | SQL |
|--------|-------------|-----|
| `no_requiere` | Productos que existen y no sufrieron cambios | Comparación de todos los campos |
| `requiere` | Productos que existen pero tienen cambios |JOIN exitoso pero con diferencias |
| `nuevo` | Productos que no existen en la tabla principal |LEFT JOIN donde no hay match |

**Retorna:**
```php
[
    'total' => 5000,
    'no_requiere' => 4500,
    'requiere' => 300,
    'nuevo' => 200
]
```

#### Paso 3: Sincronización

**Endpoint:** `POST /api/producto/jazz/sincronizar`

**Ubicación:** `JazzController::sync()`

Parámetros:
- `ids`: Array de IDs de productos a sincronizar
- `type`: Tipo de sincronización (`requiere` o `nuevo`)

Proceso:
1. Genera un `sinc_id` para auditoría
2. Guarda el estado actual en `product_jazz_history`
3. Ejecuta UPSERT en `product_jazz`
4. Según el tipo:
   - **requiere:** Relaciona productos locales (actualiza `idProducto`, `product_brand_id`, etc.)
   - **nuevo:** Crea nuevos registros en `products`

#### Relacionar productos existentes

**Ubicación:** `JazzController::relacionarProductosPorCode()`

```php
// Por cada producto local, busca coincidencia por código
$productJazz = DB::table('product_jazz as pj')
    ->leftJoin('product_brands as pb', 'pj.codigo_marca', '=', 'pb.code')
    ->whereIn('pj.code', $codes)
    ->get();

// Actualiza campos de relación
DB::table('products')->where('id', $update['id'])->update([
    'idProducto' => $jazzMap[$product->code]->idProducto,
    'product_brand_id' => $jazzMap[$product->code]->product_brand_id,
    'provider_code' => $jazzMap[$product->code]->provider_code,
    'equivalence' => $jazzMap[$product->code]->equivalence,
    'factory_code' => $jazzMap[$product->code]->factory_code,
    // ubicación parseada
]);
```

#### Crear productos nuevos

**Ubicación:** `JazzController::crearProductosNuevos()`

```php
// Crea productos en tabla local
DB::table('products')->upsert($data, ['code'], [
    'idProducto', 'description', 'provider_code', 'equivalence', 
    'factory_code', 'ship', 'module', 'side', 'column', 'row'
]);
```

#### Endpoint de consulta

**Endpoint:** `GET /api/products_jazz`

Lista productos por estado (no_requiere, requiere, nuevo) para que el usuario seleccione qué sincronizar.

---

## Pedidos

La integración de pedidos permite enviar los pedidos creados en la aplicación al sistema Jazz, creando el pedido formalmente en Jazz.

### Campo de Relación

Los pedidos locales (`orders`) tienen los campos:
- `ref_jazz_id`: ID del pedido en Jazz (asignado al enviar)
- `numero_jazz`: Número de referencia en Jazz

---

### Sincronización de Pedido

La sincronización de pedido implica obtener datos de un pedido en Jazz (consulta).

**Ubicación:** `PedidoService` en `app/Services/JazzServices/PedidoService.php`

```php
// Formato de datos para crear pedido en Jazz
public function getFormatData($cliente_jazz_id)
{
    return [
        "empresa" => 1,
        "sucursal" => 2,
        "letra" => "P",
        "boca" => 0,
        "idCliente" => $cliente_jazz_id,
        "ivaTipo" => 3,
        "idVendedor" => 1,
        "idLista" => 6,
        "condicion" => 0,
        "moneda" => 1,
        "enMostrador" => "S",
        "fecha" => Carbon::now('UTC')->format('Y-m-d\TH:i:s.v\Z'),
        "idEstado" => 5
    ];
}
```

---

### Envío de Pedido a Jazz

El envío de pedido crea el pedido en Jazz y relaciona ambos sistemas.

**Endpoint:** `POST /api/pedido/generar_factura_jazz`

**Ubicación:** `OrderController::generar_factura_jazz()`

**Flujo:**

```
┌──────────────┐    ┌─────────────┐    ┌──────────────┐    ┌───────────┐
│ Order Local  │───▶│getFormatData│───▶│agregarPedido  │───▶│ get refID  │
└──────────────┘    └─────────────┘    └──────────────┘    └─────┬─────┘
                                                               │
                    ┌───────────────────────────────────────────┘
                    ▼
┌──────────────┐    ┌─────────────┐    ┌──────────────┐    ┌───────────┐
│ OrderProduct │───▶│getJazzData  │───▶│agregarArticulo│───▶│ ref_jazz  │
└──────────────┘    └─────────────┘    └──────────────┘    └─────┬─────┘
                                                               │
                                    ┌───────────────────────────┘
                                    ▼
                            ┌──────────────┐
                            │finalizarPedido│
                            └──────────────┘
```

**Proceso detallado:**

1. **Verifica que el pedido no esté relacionado:**
   ```php
   if ($order->ref_jazz_id) {
       return error("Este pedido ya tiene relación con Jazz");
   }
   ```

2. **Prepara datos del cliente (requiere jazz_id):**
   ```php
   $data = $service->getFormatData($order->client->jazz_id);
   ```

3. **Crea el pedido base en Jazz:**
   ```php
   $pedido = $this->agregarPedido($data);
   $id_pedido_jazz = $pedido['refID'];
   ```

4. **Agrega los artículos (productos):**
   ```php
   foreach ($order->detail as $detail) {
       $data = [
           "nroInterno" => $id_pedido_jazz,
           "idProducto" => $detail->product->idProducto,
           "cantidad" => $detail->amount,
           "precio" => $detail->unit_price,
       ];
       $pedido_producto = $this->agregarArticulo($data, $id_pedido_jazz);
       
       // Guarda la referencia en OrderProduct
       $order_product->ref_jazz_id = $pedido_producto["refID"];
       $order_product->save();
   }
   ```

5. **Finaliza el pedido:**
   ```php
   $this->finalizarPedido($id_pedido_jazz);
   ```

6. **Guarda la referencia en el pedido local:**
   ```php
   $order->ref_jazz_id = $id_pedido_jazz;
   $order->save();
   ```

**Pre-requisitos:**
- El cliente debe tener `jazz_id` asociado
- Los productos deben tener `idProducto` vinculado

---

## Clientes

La integración de clientes permite buscar clientes en Jazz y relacionarlos con los clientes de la aplicación.

### Campo de Relación

Los clientes locales (`clients`) tienen el campo `jazz_id` que los vincula con el cliente correspondiente en Jazz.

---

### Búsqueda en Jazz

**Endpoint:** `POST /api/cliente/buscar-jazz`

**Ubicación:** `ClientController::searchJazz()`

Parámetros:
- `search`: Texto a buscar

Proceso:
1. Conecta a la tabla `clientes` en Jazz (vía `ClientJazz` modelo con conexión directa)
2. Busca en los campos: Nombre, Domicilio, CP, Localidad, Mail, CUIT, Telefono, etc.
3. Retorna los resultados

**Modelo utilizado:**
```php
class ClientJazz extends Model
{
    protected $connection = 'jazz';
    protected $table = 'clientes';
    
    // Campos disponibles: IdCliente, Numero, Tipo, Empresa, 
    // Nombre, Domicilio, Mail, Telefono, CUIT, etc.
}
```

---

### Relación Cliente-App con Jazz

**Endpoint:** `POST /api/cliente/relacionar-cliente-jazz`

**Ubicación:** `ClientController::relacionarClienteJazz()`

Parámetros:
- `cliente_id`: ID del cliente en la aplicación
- `jazz_id`: ID del cliente en Jazz

Proceso:
1. Verifica que el cliente no tenga relación previa
2. Asigna el `jazz_id` al cliente local
3. Permite que el cliente use los servicios de Jazz (crear pedidos, etc.)

```php
$cliente = Client::find($request->cliente_id);

if ($cliente->jazz_id) {
    return error('El cliente ya se encuentra relacionado');
}

$cliente->jazz_id = $request->jazz_id;
$cliente->save();
```

---

## Usuarios

### Relación con Vendedor Jazz

Cada usuario del sistema puede estar vinculado a un **vendedor** en Jazz mediante el campo `idVendedor`. Esto permite identificar qué vendedor crearassociated un pedido en Jazz.

**Tabla:** `users`

**Campo:** `idVendedor` (INT, NULL)

**Ubicación del campo:** Después de `id` (primary key)

**Query SQL:*** `ALTER TABLE users ADD COLUMN idVendedor INT NULL DEFAULT NULL AFTER id;`

**Propósito:**
- Vincular el usuario de la aplicación con el vendedor en Jazz
- Se usa al crear pedidos en Jazz para asignar el vendedor

**Ejemplo de uso en pedido:**

```php
// Al crear un pedido en Jazz, se usa el idVendedor del usuario creador
$pedidoData = [
    // ... otros campos
    "idVendedor" => $user->idVendedor,  // NULL si no está vinculado
];
```

---

## Servicios y Configuración

### Servicios

| Servicio | Propósito |
|----------|-----------|
| `ApiService` | Clase base para comunicación con API REST de Jazz |
| `ProductService` | Endpoints de productos (consulta, stock, precios) |
| `PedidoService` | Endpoints de pedidos (crear, agregar artículos, finalizar) |

### ApiService

**Ubicación:** `app/Services/JazzServices/ApiService.php`

Características:
- **Autenticación:** Token cacheado por 1 hora (Cache::remember)
- **Logging:** Todas las peticiones se registran en `log_jazz_api`
- **Métodos:** `get()`, `post()`

```php
class ApiService
{
    protected $baseUrl;
    protected $token;
    
    // Obtiene token (cacheado)
    public function authenticate(): string
    
    // GET request
    public function get(string $endpoint, array $queryParams = [])
    
    // POST request
    public function post(string $endpoint, array $data = [])
}
```

### ProductService

**Ubicación:** `app/Services/JazzServices/ProductService.php`

```php
class ProductService extends ApiService
{
    public function sayHello()                                    // Prueba de conexión
    public function listProducts(string $empresa)                 // Lista productos
    public function getProduct(int $id)                           // Consulta producto
    public function getStock(int $id)                             // Consulta stock
    public function updatePrice(int $id, int $lista, float $precio)  // Actualiza precio
    public function listDiscounts()                               // Lista descuentos
    public function listSuppliers()                               // Lista proveedores
}
```

### Configuración

**Variables de entorno (.env):**
```env
# API REST Jazz
API_BASE_URL=https://api.jazz.com.ar
API_AUTH_URL=https://auth.jazz.com.ar
API_USERNAME=usuario
API_PASSWORD=password

# Conexión MySQL Jazz
JAZZ_HOST=192.168.1.100
JAZZ_DATABASE=jazz_db
JAZZ_USERNAME=user
JAZZ_PASSWORD=pass
```

**Configuración (config/services.php):**
```php
'jazz_api' => [
    'base_url' => env('API_BASE_URL'),
    'auth_url' => env('API_AUTH_URL'),
    'username' => env('API_USERNAME'),
    'password' => env('API_PASSWORD'),
],
```

**Configuración (config/database.php):**
```php
'connections' => [
    'jazz' => [
        'driver' => 'mysql',
        'host' => env('JAZZ_HOST', '127.0.0.1'),
        'database' => env('JAZZ_DATABASE', 'jazz'),
        'username' => env('JAZZ_USERNAME', 'root'),
        'password' => env('JAZZ_PASSWORD', ''),
    ],
]
```

### Logging

Todas las llamadas a la API se registran en la tabla `log_jazz_api`:

| Campo | Descripción |
|-------|-------------|
| endpoint | Endpoint llamado |
| metod | Método HTTP |
| user_id | Usuario que realizó la llamada |
| request | Datos enviados (JSON) |
| response | Respuesta recibida (JSON) |
| time_ms | Tiempo de ejecución |
| error | Error si ocurrió |

---

## Resumen de Endpoints

| Modelo | Endpoint | Método | Descripción |
|--------|----------|--------|-------------|
| **Productos** | `/api/producto/jazz/inicio-sync` | GET | Descarga productos a tabla temporal |
| | `/api/producto/jazz/analizar` | GET | Analiza diferencias |
| | `/api/producto/jazz/sincronizar` | POST | Sincroniza productos seleccionados |
| | `/api/products_jazz` | GET | Lista productos por estado |
| | `/api/producto/detalle_jazz` | GET | Consulta producto específico |
| **Pedidos** | `/api/pedido/generar_factura_jazz` | POST | Envía pedido a Jazz |
| **Clientes** | `/api/cliente/buscar-jazz` | POST | Busca clientes en Jazz |
| | `/api/cliente/relacionar-cliente-jazz` | POST | Relaciona cliente con Jazz |
| **Consola** | `sync:product-jazz` | - | Sincroniza productos por API |
| | `update:stock-prices` | - | Actualiza stock y precios desde Jazz |

---

## Notas Importantes

1. **Stock en Jazz:** Se calcula desde las facturas (ventas - devoluciones), no es un campo directo.

2. **Precios por lista:** La aplicación usa listas 2, 3 y 6. La lista 6 es típicamente la de precios finales.

3. **Ubicación de productos:** El campo `ubicacion` en Jazz tiene formato específico:
   - Formato: `NAVE: A - MOD: 1 - COL: 02 - FILA: 05 - LADO: I`
   - Se convierte a: ship, module, side, column, row

4. **Campos adicionales (Comodines):** Jazz usa un sistema de comodines para campos adicionales como CODIGO_ORIGINAL, EQUIVALENCIA, OBSERVACIONES, UBICACION.

5. **Pedidos:** Al crear un pedido en Jazz se requiere que el cliente tenga `jazz_id` y los productos tengan `idProducto`.

6. **Proceso diario:** Se recomienda configurar los comandos de consola en el scheduler de Laravel para ejecución automática diaria.
