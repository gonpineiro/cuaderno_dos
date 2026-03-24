# Configuración del Agente

Este archivo contiene guidelines e información útil para agentes IA que trabajan en este proyecto Laravel.

## Descripción del Proyecto

Backend API Laravel para gestionar cotizaciones, pedidos y envíos. Se integra con el sistema ERP externo **Jazz**.

### Flujo de Negocio
```
PriceQuote (Cotización) → Order (Pedido: online/cliente/siniestro) → Shipment (Envío)
```

### Tecnologías Principales
- Laravel 9+
- Autenticación JWT (tymon/jwt-auth)
- Permisos: Spatie
- Base de datos: MySQL + conexión MySQL a Jazz
- PDF: DomPDF

---

## Archivos de Documentación

Toda la documentación del proyecto está en el directorio `doc/`:

| Archivo | Descripción |
|---------|-------------|
| `doc/documentacion_completa.md` | Documentación principal (modelos, endpoints, flujo de negocio) |
| `doc/integracion_jazz.md` | Detalles de integración con Jazz (Productos, Pedidos, Clientes, API) |
| `doc/commands_tools.md` | Comandos útiles (Docker, Laravel, Sincronización) |
| `doc/productos.md` | Información de productos |
| `doc/consistencia_ubicacion.md` | Notas de consistencia de ubicación |
| `doc/changes.md` | Registro de cambios |

---

## Comandos Útiles

Consulta `doc/commands_tools.md` para comandos completos de Docker, Laravel y sincronización.

---

## Tareas Comunes

### Crear una Cotización
1. Crear `PriceQuote` con client_id y datos del vehículo
2. Agregar productos mediante el pivot `PriceQuoteProduct`
3. Calcular total usando coeficientes

### Convertir Cotización a Pedido
- Llamar al endpoint: `POST /api/cotizacion/asignar/{type}`
- Types: `online`, `cliente`, `siniestro`
- Auto-determina el tipo según el cliente (is_insurance) y productos (is_special)

### Sincronizar Productos desde Jazz
1. Ejecutar: `php artisan sync:product-jazz`
2. O API: `POST /api/producto/jazz/sincronizar`
3. Los productos van a la tabla `product_jazz`
4. Vincular a productos internos mediante `products.idProducto`

### Enviar Pedido a Jazz
1. Verificar que el pedido tenga cliente Jazz (`client.jazz_id`)
2. Llamar: `POST /api/pedido/generar_factura_jazz`
3. Usa `PedidoService` para crear el pedido en Jazz

---

## Integración con Jazz

### Tablas
- `product_jazz` - Productos sincronizados
- `product_jazz_temp` - Datos temporales de sincronización
- `product_jazz_history` - Historial de sincronización
- `product_brands_jazz` - Mapeo de marcas
- `log_jazz_api` - Logs de llamadas API

### Servicios
- `App\Services\JazzServices\ApiService` - Autenticación base
- `App\Services\JazzServices\ProductService` - API de productos
- `App\Services\JazzServices\PedidoService` - API de pedidos

### Configuración
- El archivo `.env` contiene credenciales de Jazz
- Conexión a base de datos en `config/database.php`

---

## Modelos Principales

| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| PriceQuote | price_quotes | Cotizaciones con productos |
| Order | orders | Pedidos (online/cliente/siniestro) |
| Shipment | shipments | Envíos |
| Product | products | Productos internos |
| ProductJazz | product_jazz | Productos de Jazz |
| Client | clients | Clientes |
| Coeficiente | coeficientes | Coeficientes de precio |
| Ticket | tickets | Reclamos internos |
| PurchaseOrder | purchase_orders | Órdenes de compra a proveedores |

---

## Permisos

Usando Spatie. Permisos principales:
- `cotizacion.delete` - Eliminar cotizaciones
- `pedido.delete` - Eliminar pedidos
- `pedido.estado.entregado` - Modificar pedidos entregados
- `audit.product.view` - Ver auditoría de productos

---

## URL Base de la API

Todos los endpoints comienzan con `/api/` y requieren autenticación JWT excepto `/api/login` y `/api/register`.
