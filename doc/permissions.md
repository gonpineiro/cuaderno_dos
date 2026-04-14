# Permisos y Roles (Spatie Laravel Permission)

Documentación sobre el sistema de permisos y roles implementado con **spatie/laravel-permission**.

---

## Introducción

El proyecto utiliza `spatie/laravel-permission` para gestionar la autorización de usuarios mediante roles y permisos. Este sistema permite:

- Asignar múltiples roles a un usuario
- Asignar múltiples permisos a un usuario
- Asignar permisos a roles
- Verificar permisos en middleware o directamente en código

---

## Estructura de la Base de Datos

### Tablas principales

| Tabla | Descripción |
|-------|-------------|
| `roles` | Define los roles disponibles |
| `permissions` | Define los permisos disponibles |
| `model_has_roles` | Relación muchos a muchos entre modelos (usuarios) y roles |
| `model_has_permissions` | Relación directa entre modelos y permisos |
| `role_has_permissions` | Relación muchos a muchos entre roles y permisos |

---

## Roles Definidos

| ID | Nombre | Descripción |
|----|--------|-------------|
| 1 | sudo | Acceso total (super usuario) |
| 2 | admin | Administrador con todos los permisos |
| 3 | vendedor_mostrador | Vendedor de mostrador |
| 4 | vendedor_whatsapp | Vendedor por WhatsApp |
| 5 | siniestro | Manejo de siniestros |
| 6 | compras | Gestión de compras |
| 7 | deposito | Personal de depósito |

---

## Permisos Definidos

| ID | Nombre | Descripción |
|----|--------|-------------|
| 1 | audit.product.view | Puede ver auditoría de productos |
| 2 | pedido.delete | Eliminar un pedido |
| 3 | cotizacion.pedido.pendiente.edit | Modificar cotización con pedido pendiente |
| 4 | pedido.estado.entregado | Cambiar estado a entregado |
| 5 | pedido.estado.cancelado | Cambiar estado a cancelado |
| 7 | product.delete.view | Recuperar producto eliminado |
| 8 | cotizacion.delete | Borrar Cotización |
| 9 | product.delete | Puede borrar un producto |

---

## Relación Roles - Permisos

| Rol | Permisos Asignados |
|-----|-------------------|
| **sudo** | Acceso total (hereda todos los permisos implícitamente) |
| **admin** | cotizacion.delete, product.delete, audit.product.view, pedido.delete, cotizacion.pedido.pendiente.edit, pedido.estado.entregado, pedido.estado.cancelado, product.delete.view |

---

## Relación Usuarios - Roles

| Usuario | Email | Rol(es) |
|---------|-------|---------|
| Allende Repuestos | allende@allenderepuestos.com.ar | admin |
| Matias Fernandez | matiasfernandez@allenderepuestos.com.ar | admin |
| Maria Garcia | mariagarcia@allenderepuestos.com.ar | admin |
| Oscar Barigelli | oscarbarigelli@allenderepuestos.com.ar | vendedor_mostrador |
| Maximo Perello | maximoperello@allenderepuestos.com.ar | vendedor_mostrador |
| Jorge Riquelme | jorgeriquelme@allenderepuestos.com.ar | vendedor_mostrador |
| Santiago Fuentes | santiagofuentes@allenderepuestos.com.ar | vendedor_mostrador |
| Tomas Schawb | tomasschawb@allenderepuestos.com.ar | vendedor_mostrador |
| Sebastian Fernandez | sebastianfernandez@allenderepuestos.com.ar | vendedor_mostrador, vendedor_whatsapp, siniestro |
| Ivan Escobar | ivanescobar@allenderepuestos.com.ar | vendedor_mostrador, compras |
| Sebastian De Haro | sebastiandeharo@allenderepuestos.com.ar | vendedor_whatsapp |
| Leandro Almazabal | leandroalmazabal@allenderepuestos.com.ar | vendedor_whatsapp |
| Joaquin Berrios | joaquinberrios@allenderepuestos.com.ar | vendedor_whatsapp |
| Tomas Barros | tomasbarros@allenderepuestos.com.ar | vendedor_whatsapp, deposito |
| Ignacio Paglieri | ignaciopaglieri@allenderepuestos.com.ar | siniestro |
| Luciano Lucero | lucianolucero@allenderepuestos.com.ar | deposito |
| Brian Toledo | briantoledo@allenderepuestos.com.ar | deposito |
| Julian Castro | juliancastro@allenderepuestos.com.ar | deposito |
| Matias Marin | matiasmarin@allenderepuestos.com.ar | deposito |
| Franco Novile | franconovile@allenderepuestos.com.ar | deposito |

---

## Uso en Código

### Asignar rol a usuario
```php
$user->assignRole('admin');
```

### Revocar rol
```php
$user->removeRole('admin');
```

### Asignar permiso directo
```php
$user->givePermissionTo('pedido.delete');
```

### Verificar permiso
```php
// En controlador o middleware
$user->hasPermissionTo('pedido.delete');

// En rutas (middleware)
Route::put('/pedido/{pedido}', [OrderController::class, 'update'])
    ->middleware('permission:pedido.delete');
```

### Verificar rol
```php
$user->hasRole('admin');
```

### Verificar si tiene alguno de los roles
```php
$user->hasAnyRole(['admin', 'vendedor_mostrador']);
```

---

## Middleware

### Verificación de permisos en rutas

El proyecto usa el middleware `permission` de Spatie:

```php
// Requiere permiso específico
Route::get('/producto/audit', [ProductController::class, 'audit'])
    ->middleware('permission:audit.product.view');

// Requiere uno de varios permisos
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('permission:pedido.delete|pedido.estado.entregado');
```

### Verificación de roles en rutas

```php
// Requiere rol específico
Route::get('/siniestro', [SiniestroController::class, 'index'])
    ->middleware('role:siniestro');
```

---

## Controladores de Permisos

El proyecto incluye `PermissionController` para gestionar roles y permisos:

| Endpoint | Método | Acción |
|----------|--------|--------|
| `/api/permissions` | GET | Listar todos los permisos y roles |
| `/api/permissions/change_user_role` | POST | Cambiar rol de usuario |
| `/api/permissions/change_user_permissions` | POST | Asignar permisos directos a usuario |
| `/api/permissions/change_role_permission` | POST | Asignar/quitar permiso a rol |
| `/api/permissions/save_element` | POST | Guardar configuración |

---

## Notas

- El rol `sudo` tiene acceso total sin necesidad de permisos explícitos
- Los permisos directo a usuarios (`model_has_permissions`) están vacíos actualmente
- Los permisos se asignan principalmente a través de roles
- Algunos usuarios tienen múltiples roles (ej: Sebastian Fernandez tiene 3 roles)