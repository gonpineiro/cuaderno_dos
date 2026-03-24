# Documentación Técnica del Proyecto

## Índice
1. [Arquitectura General](#arquitectura-general)
2. [Modelos y Entidades](#modelos-y-entidades)
3. [Flujo de Negocio](#flujo-de-negocio)
4. [API Endpoints](#api-endpoints)
5. [Tablas de Configuración](#tablas-de-configuración)
6. [Permisos y Roles](#permisos-y-roles)

> **Nota:** La documentación de la integración con Jazz se encuentra en el archivo separado `doc/integracion_jazz.md`

---

## Arquitectura General

El proyecto es una aplicación Laravel que funciona como backend API con autenticación JWT. El flujo de negocio principal es:

```
Cotización (PriceQuote) → Pedido (Order) → Envío (Shipment)
```

Cada entidad puede tener productos asociados. La aplicación se integra con un sistema externo llamado **Jazz** para gestión de productos, clientes y pedidos.

### Tecnologías
- **Framework:** Laravel 9+
- **Autenticación:** JWT (tymon/jwt-auth)
- **Permisos:** Spatie
- **PDF:** DomPDF
- **Base de datos:** MySQL + conexión a Jazz (segunda BD)

---

## Modelos y Entidades

### 1. Cotización (PriceQuote)
**Tabla:** `price_quotes`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| user_id | bigint | Usuario que crea la cotización |
| client_id | bigint | Cliente asociado |
| year | varchar | Año del vehículo |
| chasis | varchar | Número de chasis |
| version | varchar | Versión del vehículo |
| patente | varchar | Patente del vehículo |
| contacto | varchar | Datos de contacto |
| vehiculo_id | bigint | Relación con vehículo |
| information_source_id | int | Fuente de información |
| type_price_id | int | Tipo de precio (contado/lista) |
| observation | text | Observaciones |
| order_id | bigint | Pedido generado (nullable) |

**Relaciones:**
- `products()` - Muchos a muchos con Product (PriceQuoteProduct)
- `client()` - belongsTo Client
- `user()` - belongsTo User
- `vehiculo()` - belongsTo Vehiculo
- `order()` - belongsTo Order
- `shipment()` - belongsTo Shipment (vía order)
- `tickets()` - morphMany Ticket

**Estados:**
- Pendiente: Sin pedido generado
- Pedido: Con pedido asignado (online/cliente/siniestro)
- Envío: Con envío asignado

---

### 2. Pedido (Order)
**Tabla:** `orders`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| user_id | bigint | Usuario que crea el pedido |
| client_id | bigint | Cliente asociado |
| price_quote_id | bigint | Cotización origen |
| shipment_id | bigint | Envío asociado |
| type_id | int | Tipo de pedido (online/cliente/siniestro) |
| state_id | int | Estado del pedido |
| year, chasis, contacto, vehiculo_id | mixed | Datos del vehículo |
| payment_method_id | int | Método de pago |
| invoice_number | varchar | Número de factura |
| deposit | decimal | Depósito recibido |
| estimated_date | date | Fecha estimada (cliente) |
| remito | varchar | Remito (siniestro) |
| workshop | varchar | Taller (siniestro) |
| ref_jazz_id | int | Referencia en Jazz |
| numero_jazz | varchar | Número de Jazz |
| observation | text | Observaciones |

**Tipos de Pedido:**
- **Online:** Pedido web/compras en línea
- **Cliente:** Pedido de cliente directo
- **Siniestro:** Pedido de aseguradora

**Estados por Tipo:**

| Tipo | Estados |
|------|---------|
| Online | pendiente, retirar, entregado, cancelado |
| Cliente | incompleto, pendiente, retirar, entregado, cancelado |
| Siniestro | incompleto, completo, entregado, cancelado |

**Relaciones:**
- `detail()` - hasMany OrderProduct
- `detailPending()` - productos no entregados/cancelados
- `client()` - belongsTo Client
- `vehiculo()` - belongsTo Vehiculo
- `type()` - belongsTo Table (order_type)
- `payment_method()` - belongsTo Table
- `shipment()` - belongsTo Shipment
- `price_quote()` - belongsTo PriceQuote
- `state()` - belongsTo Table
- `activities()` - morphMany Activity

---

### 3. Envío (Shipment)
**Tabla:** `shipments`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| user_id | bigint | Usuario que crea el envío |
| order_id | bigint | Pedido asociado |
| client_id | bigint | Cliente destino |
| year, chasis, contacto, vehiculo_id | mixed | Datos del vehículo |
| version, patente | varchar | Versión/patente |
| payment_method_id | int | Método de pago |
| state_id | int | Estado del envío |
| transport | varchar | Transportista |
| invoice_number | varchar | Número de factura |
| nro_guia | varchar | Número de guía |
| bultos | int | Cantidad de bultos |
| send_adress | text | Dirección de envío |
| observation | text | Observaciones |

**Estados de Envío:**
- pendiente, listo_enviar, despachado, contrareemboldo, cancelado

**Relaciones:**
- `detail()` - hasMany ShipmentProduct
- `products()` - belongsToMany Product
- `order()` - belongsTo Order
- `client()` - belongsTo Client
- `state()` - belongsTo Table

---

### 4. Producto (Product)
**Tabla:** `products`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| code | varchar | Código interno |
| provider_code | varchar | Código del proveedor |
| factory_code | varchar | Código de fábrica |
| equivalence | varchar | Código equivalente |
| description | text | Descripción |
| model | varchar | Modelo |
| engine | varchar | Motor |
| observation | text | Observaciones |
| ship, module, side, column, row | varchar | Ubicación en almacén |
| verified | boolean | Producto verificado |
| is_special | boolean | Producto especial |
| brand_id | bigint | Marca de vehículo |
| product_brand_id | int | Marca de producto |
| rubro, subrubro | varchar | Clasificación |
| provider_id | bigint | Proveedor principal |
| state_id | int | Estado |
| idProducto | int | ID en Jazz |

**Relaciones:**
- `provider()` - belongsTo Provider
- `providers()` - belongsToMany Provider
- `product_providers()` - hasMany ProductProvider
- `state()` - belongsTo Table
- `orders()` - belongsToMany Order
- `orderProduct()` - hasMany OrderProduct
- `shipmentProduct()` - hasMany ShipmentProduct
- `priceQuoteProduct()` - hasMany PriceQuoteProduct
- `brand()` - belongsTo Brand
- `product_brand()` - belongsTo ProductBrand
- `jazz()` - belongsTo ProductJazz
- `toAsk()` - hasMany ToAsk
- `tickets()` - morphMany Ticket

**Métodos:**
- `productoAjuste()` - Retorna el producto "AJUSTE" para recargos
- `getUbicationAttribute()` - Formatea la ubicación (ship+module+side+column+row)

---

### 5. Producto Jazz (ProductJazz)
**Tabla:** `product_jazz`

Tabla de sincronización con el sistema externo Jazz. Contiene:
- id, code, nombre
- stock, stock_min, stock_max, punto_pedido
- precio_lista_2, precio_lista_3, precio_lista_6
- provider_code, equivalence, observation, ubicacion
- fecha_alta, fecha_mod
- codigo_marca

**Relaciones:**
- `brand()` - belongsTo ProductBrand (vía codigo_marca)

---

### 6. Cliente (Client)
**Tabla:** `clients`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| dni | varchar | DNI |
| name | varchar | Nombre/Razón social |
| phone | varchar | Teléfono |
| email | varchar | Email |
| city_id | bigint | Ciudad |
| adress | varchar | Dirección |
| cuit | varchar | CUIT |
| is_insurance | boolean | ¿Es aseguradora? |
| is_company | boolean | ¿Es empresa? |
| reference_id | int | Referencia externa |
| jazz_id | int | ID en Jazz |
| year, chasis, vehiculo_id | mixed | Vehículo asociado |
| condicion_iva_id | int | Condición IVA |
| is_sin_vehiculo | boolean | Sin vehículo registrado |
| observation | text | Observaciones |

**Relaciones:**
- `orders()` - hasMany Order
- `config()` - hasMany ClientConfig
- `condicion_iva()` - belongsTo Table
- `city()` - belongsTo City
- `vehiculos()` - hasMany ClientChasis

**Validaciones:**
- No se puede eliminar un cliente con pedidos asociados

---

### 7. Vehículo (Vehiculo)
**Tabla:** `vehiculos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| brand_id | bigint | Marca |
| name | varchar | Modelo/Nombre |

**Relaciones:**
- `brand()` - belongsTo Brand
- `cotizaciones()` - hasMany PriceQuote

---

### 8. Proveedor (Provider)
**Tabla:** `providers`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| name | varchar | Nombre |
| email | varchar | Email |

**Relaciones:**
- `products()` - hasMany Product

---

### 9. Pedido de Compra (PurchaseOrder)
**Tabla:** `purchase_orders`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| observation | text | Observaciones |
| provider_id | bigint | Proveedor |
| state_id | int | Estado |

**Estados:**
- pendiente, enviado, entregado

**Relaciones:**
- `provider()` - belongsTo Provider
- `products()` - belongsToMany Product
- `state()` - belongsTo Table
- `detail()` - hasMany PurchaseOrderProduct

---

### 10. Coeficiente (Coeficiente)
**Tabla:** `coeficientes`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| description | varchar | Descripción |
| value | double | Valor multiplicador |
| coeficiente | double | Coeficiente de cálculo |
| cuotas | int | Cantidad de cuotas |
| decimals | int | Decimales |
| position | int | Posición en lista |
| show | boolean | ¿Mostrar? |
| has_recargo | boolean | ¿Tiene recargo? |

Usado para calcular precios en cotizaciones y pedidos.

---

### 11. Ticket (Reclamos Internos)
**Tabla:** `tickets`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| user_id | bigint | Usuario creador |
| responsable_id | bigint | Usuario responsable |
| titulo | varchar | Título |
| descripcion | text | Descripción |
| resolucion | text | Resolución |
| estado_id | int | Estado |
| prioridad_id | int | Prioridad |
| ticketable_type, ticketable_id | mixed | Entidad relacionada |

**Origenes posibles:**
- PriceQuote (cotizacion)
- Product (producto)
- Genérico (sin origen)

**Relaciones:**
- `user()` - belongsTo User
- `responsable()` - belongsTo User
- `ticketable()` - morphTo
- `estado()` - belongsTo Table
- `prioridad()` - belongsTo Table

---

### 12. Tablas Auxiliares (Table)
**Tabla:** `tables`

Tabla genérica para estados y configuraciones. Se organiza por el campo `name`:

| name | values | Descripción |
|------|--------|-------------|
| order_type | online, cliente, siniestro | Tipos de pedido |
| order_online_state | pendiente, retirar, entregado, cancelado | Estados pedido online |
| order_cliente_state | incompleto, pendiente, retirar, entregado, cancelado | Estados pedido cliente |
| order_siniestro_state | incompleto, completo, entregado, cancelado | Estados siniestro |
| order_envio_state | pendiente, listo_enviar, despachado, contrareemboldo, cancelado | Estados envío |
| price_quote_state | cotizar, no cotizar | Estado producto cotización |
| type_price | contado, lista | Tipo de precio |
| information_source | wap-clientes, wap-mecanicos, facebook, Mostrador, email, google, pagina-web | Fuente de contacto |
| payment_method | mostrador, online, corriente | Método de pago |
| payment_method_send | contrareembolso, online, corriente | Método de pago envío |
| purchase_order | pendiente, enviado, entregado | Estados orden compra |
| product_state | sin_control_stock, control_stock | Control de stock |
| client_condicion_iva | resp_incripto, monotributista, etc. | Condición IVA |

---

### 13. Modelos Adicionales

**Brand** - Marcas de vehículos
- Tabla: `brands`
- Campos: name

**ProductBrand** - Marcas de productos (relación con Jazz)
- Tabla: `product_brands`
- Campos: code, name, jazz_id

**City** - Ciudades
- Tabla: `cities`
- Campos: name, province_id, zip_code

**Province** - Provincias
- Tabla: `provinces`
- Campos: name

**Combo** - Combos de productos
- Tabla: `combos`
- Campos: name
- `products()` - belongsToMany Product

**ToAsk** - Productos por solicitar al proveedor
- Tabla: `to_ask`
- Campos: order_product_id, product_id, provider_id, purchase_order, user_id, amount

**ClientChasis** - Chasis asociados a cliente
- Tabla: `client_chasis`
- Campos: client_id, vehiculo_id, year, chasis

**ClientConfig** - Configuraciones por cliente
- Tabla: `client_config`
- Campos: client_id, type, value

---

## Flujo de Negocio

### 1. Creación de Cotización

```
1. Se crea una PriceQuote con datos del cliente y vehículo
2. Se agregan productos (PriceQuoteProduct)
3. Cada producto tiene estado: cotizar / no cotizar
4. Se calcula el total usando coeficientes
5. Se genera PDF de cotización
```

### 2. Conversión a Pedido

Una cotización puede convertirse en:
- **Pedido Online:** Si el cliente no es aseguradora y no hay productos especiales
- **Pedido Cliente:** Si hay productos especiales
- **Pedido Siniestro:** Si el cliente es aseguradora (is_insurance = true)

El método `getToAsignAttribute()` en PriceQuote determina automáticamente el tipo de pedido.

### 3. Estados del Pedido

El estado general del pedido se calcula en base a los estados de sus productos:
- **Online:** pendiente → retirar → entregado
- **Cliente:** incompleto → pendiente → retirar → entregado
- **Siniestro:** incompleto → completo → entregado

### 4. Generación de Envío

Al crear un pedido se puede generar automáticamente un envío:
- Se crea la entidad Shipment
- Se copian los productos como ShipmentProduct
- El envío tiene estados independientes

### 5. Productos

Cada modelo (PriceQuote, Order, Shipment) tiene sus productos asociados en tablas pivot:
- `price_quote_product`
- `order_product`
- `shipment_product`

Cada registro de producto incluye:
- product_id
- state_id (estado del item)
- provider_id
- amount (cantidad)
- unit_price (precio unitario)
- description (descripción opcional)

---

## API Endpoints

### Autenticación
```
POST /api/login
POST /api/register
POST /api/logout (requiere JWT)
POST /api/refresh (requiere JWT)
GET  /api/get_user (requiere JWT)
```

### Usuarios
```
GET    /api/user (listar)
POST   /api/user (crear)
GET    /api/user/{id}
PUT    /api/user/{id}
DELETE /api/user/{id}
GET    /api/user/obtener
```

### Productos
```
GET    /api/producto (listar)
POST   /api/producto (crear)
GET    /api/producto/{id}
PUT    /api/producto/{id}
GET    /api/producto/buscar?q=...
GET    /api/producto/buscar_fusionar
GET    /api/producto/buscar_relaciones
POST   /api/producto/fusionar
GET    /api/producto/detalle_jazz
GET    /api/producto/jazz
POST   /api/producto/borrar
POST   /api/producto/recuperar
GET    /api/producto/relacion
GET    /api/producto/cotizaciones
GET    /api/producto/relacion/sin-stock
GET    /api/producto/{id}/pedidos
GET    /api/producto/{id}/cotizaciones
GET    /api/producto/audit (requiere permiso)
```

### Productos Jazz
```
GET /api/products_jazz (sincronización)
GET /api/producto/jazz/inicio-sync
GET /api/producto/jazz/analizar
POST /api/producto/jazz/sincronizar
```

### Cotizaciones
```
GET    /api/cotizacion
POST   /api/cotizacion
GET    /api/cotizacion/{id}
PUT    /api/cotizacion/{id}
POST   /api/cotizacion/borrar
POST   /api/cotizacion/search
PUT    /api/cotizacion/{id}/update-productos

POST   /api/cotizacion/asignar/siniestro
POST   /api/cotizacion/asignar/online
POST   /api/cotizacion/asignar/cliente
POST   /api/cotizacion/asignar/envio
POST   /api/update_price_quote_product

GET    /api/cotizacion/pdf/{id}
```

### Pedidos
```
GET    /api/pedidos
GET    /api/pedidos/{id}
PUT    /api/pedidos/{id}
POST   /api/pedido/borrar
GET    /api/pedido/productos
GET    /api/pedido/pdf/{id}
POST   /api/pedido/search
POST   /api/pedido/generar_factura_jazz
POST   /api/pedido/productos/search
POST   /api/pedidos/cambiar-estado
POST   /api/update_pedido_product
GET    /api/pedido/reporte-online
```

### Pedidos Online
```
GET /api/online
GET /api/online/{id}
POST /api/online/cambiar-estado/{id}
```

### Siniestros
```
GET /api/siniestro
GET /api/siniestro/{id}
POST /api/siniestro/cambiar-estado/{id}
POST /api/update_siniestro_product
```

### Envíos
```
GET    /api/envio
POST   /api/envio
GET    /api/envio/{id}
PUT    /api/envio/{id}
DELETE /api/envio/{id}
PUT    /api/envio/{id}/update-productos
POST   /api/envio/cambiar-estado
GET    /api/envio/pdf/{id}
POST   /api/update_envio_product
```

### Clientes
```
GET    /api/cliente
POST   /api/cliente
GET    /api/cliente/{id}
DELETE /api/cliente/{id}
GET    /api/cliente/referencia
POST   /api/cliente/buscar-jazz
POST   /api/cliente/buscar
POST   /api/cliente/relacionar-cliente-jazz
POST   /api/cliente/update
POST   /api/cliente/config/save
```

### Cliente Chasis
```
GET /api/chasis
POST /api/cliente_chasis/update
```

### Vehículos
```
GET /api/vehiculo
POST /api/vehiculo
GET /api/vehiculo/{id}
PUT /api/vehiculo/{id}
DELETE /api/vehiculo/{id}
```

### Ciudades
```
GET /api/ciudad
POST /api/ciudad
GET /api/ciudad/{id}
PUT /api/ciudad/{id}
DELETE /api/ciudad/{id}
POST /api/ciudad/buscar
```

### Marcas
```
GET /api/marca
POST /api/marca
GET /api/marca/{id}
PUT /api/marca/{id}
DELETE /api/marca/{id}
```

### Proveedores
```
GET /api/proveedor
POST /api/proveedor
GET /api/proveedor/{id}
PUT /api/proveedor/{id}
DELETE /api/proveedor/{id}
```

### Órdenes de Compra
```
GET    /api/ordenes_compra
POST   /api/ordenes_compra
GET    /api/ordenes_compra/{id}
PUT    /api/ordenes_compra/{id}
DELETE /api/ordenes_compra/{id}
POST   /api/ordenes_compra/generar_pedir
POST   /api/ordenes_compra/producto_generar_pedir
POST   /api/ordenes_compra/producto_modificar_pedir
GET    /api/ordenes_compra/pedir
GET    /api/ordenes_compra/evaluar_pedir
POST   /api/ordenes_compra/listado_producto_generar_pedir
POST   /api/ordenes_compra/borrar
POST   /generar_orden/generar
POST   /api/ordenes_compra/cambiar-estado/{id}
```

### Coeficientes
```
POST /api/coeficientes/update
```

### Combos
```
GET /api/combos
POST /api/combos
POST /api/combos/update
POST /api/combos/borrar
```

### Permisos
```
GET /api/permissions
POST /api/permissions/change_user_role
POST /api/permissions/change_user_permissions
POST /api/permissions/save_element
POST /api/permissions/change_role_permission
```

### Tickets
```
GET /api/tickets
POST /api/generar_ticket
POST /api/tickets/resolver
POST /api/tickets/borrar
```

### Marcas de Productos
```
GET /api/product_marca
POST /api/product_marca
GET /api/product_marca/{id}
POST /api/product_marca/update
POST /api/product_marca/borrar
```

---

## Permisos y Roles

El sistema usa Spatie para gestión de permisos:

### Roles
- **sudo:** Acceso total (solo para testing)
- Usuarios con permisos específicos según rol

### Permisos
- `cotizacion.delete` - Eliminar cotizaciones
- `pedido.delete` - Eliminar pedidos
- `pedido.estado.entregado` - Modificar pedidos entregados
- `audit.product.view` - Ver auditoría de productos

Los permisos se gestionan mediante:
- `PermissionController::change_user_role()`
- `PermissionController::change_user_permissions()`
- `PermissionController::change_role_permission()`

---

## Notas Adicionales

### Campos de Ubicación de Producto
- ship: Nave
- module: Módulo
- side: Lado (I/D)
- column: Columna
- row: Fila

### Trait LogsActivity
Todos los modelos principales usan `LogsActivity` para registrar cambios en la tabla `activity_log`.

### Productos Especiales
Los productos marcados como `is_special = true` cambian el tipo de pedido automático de Online a Cliente.

### Clientes Aseguradoras
Los clientes con `is_insurance = true` generan automáticamente pedidos de tipo Siniestro.