# Herramientas de Comandos

Comandos útiles para administrar el proyecto Laravel.

> **Nota:** Este documento asume que el proyecto corre dentro de Docker. Los nombres de containers asumen que el proyecto se llama `cuaderno_dos`.

---

## Acceso a Docker

### Contenedor PHP
```bash
docker exec -it cuaderno_dos-app-1 bash
```

### Contenedor MySQL

**Credenciales:**
- Host: `db`
- Usuario: `root`
- Contraseña: `root`
- Base de datos: `cuaderno_prod`

**Comandos:**

```bash
# Opción 1: Conectar directamente al contenedor MySQL
docker exec -it cuaderno_dos-db-1 mysql -u root -p

# Opción 2: Conectar desde el contenedor PHP (usando host de Docker)
docker exec -it cuaderno_dos-app-1 mysql -h db -u root -p
```

Una vez conectado, seleccionar la base de datos:
```sql
USE cuaderno_prod;
SHOW TABLES;
SELECT COUNT(*) FROM products;
```

**Consultas útiles:**
```sql
-- Ver estructura de una tabla
DESCRIBE products;

-- Contar registros
SELECT COUNT(*) FROM price_quotes;
SELECT COUNT(*) FROM orders;
SELECT COUNT(*) FROM clients;

-- Ver productos con stock bajo
SELECT code, description, stock FROM product_jazz WHERE stock < 5;
```

### phpMyAdmin
- Acceder desde navegador: `http://localhost:8080`
- Servidor: `db`
- Usuario: `root`
- Contraseña: `root`

---

## Comandos Laravel

Hay dos formas de ejecutar comandos artisan:

### Opción A: Entrar al contenedor y ejecutar
```bash
docker exec -it cuaderno_dos-app-1 bash
```
Luego dentro del contenedor:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate
php artisan tinker
php artisan route:list
```

### Opción B: Ejecutar directamente con docker exec
```bash
# Limpiar cache
docker exec -it cuaderno_dos-app-1 php artisan cache:clear
docker exec -it cuaderno_dos-app-1 php artisan config:clear
docker exec -it cuaderno_dos-app-1 php artisan route:clear
docker exec -it cuaderno_dos-app-1 php artisan view:clear

# Ejecutar migraciones
docker exec -it cuaderno_dos-app-1 php artisan migrate

# Ejecutar tinker (sesión interactiva)
docker exec -it cuaderno_dos-app-1 php artisan tinker

# Listar rutas
docker exec -it cuaderno_dos-app-1 php artisan route:list
```

---

## Comandos de Sincronización

### Opción A: Entrar al contenedor y ejecutar
```bash
docker exec -it cuaderno_dos-app-1 bash
php artisan sync:product-jazz
php artisan update:stock-prices
php artisan shipments:recalculate
php artisan orders:recalculate
```

### Opción B: Ejecutar directamente
```bash
# Sincronizar productos desde Jazz API
docker exec -it cuaderno_dos-app-1 php artisan sync:product-jazz

# Actualizar stock y precios
docker exec -it cuaderno_dos-app-1 php artisan update:stock-prices

# Recalcular envíos
docker exec -it cuaderno_dos-app-1 php artisan shipments:recalculate

# Recalcular pedidos
docker exec -it cuaderno_dos-app-1 php artisan orders:recalculate
```

---

## Tinker (Consultas Interactivas)

Para ejecutar consultas interactivas con Tinker:

```bash
# Entrar al contenedor
docker exec -it cuaderno_dos-app-1 bash

# Ejecutar tinker
php artisan tinker
```

Una vez dentro de Tinker:
```php
// Obtener una cotización con productos
$quote = App\Models\PriceQuote::with('products', 'client')->find(1);

// Obtener un pedido con detalles
$order = App\Models\Order::with('detail.product', 'client')->find(1);

// Buscar productos en Jazz
$products = App\Models\ProductJazz::where('code', 'like', '%ABC%')->get();

// Actualizar coeficientes
App\Models\Coeficiente::where('description', 'Contado')->update(['value' => 1.0]);
```

---

## Puertos Útiles

| Servicio | Puerto |
|----------|--------|
| Nginx (web) | 8000 |
| PHP (xdebug) | 9006 |
| MySQL | 3307 |
| phpMyAdmin | 8080 |
