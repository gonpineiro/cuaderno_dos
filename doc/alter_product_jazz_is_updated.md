# Alter table product_jazz - Add is_updated column

## Nueva columna

```sql
ALTER TABLE product_jazz
ADD COLUMN is_updated TINYINT(1) NOT NULL DEFAULT 0
AFTER punto_pedido;
```

## CREATE TABLE completo (incluyendo columnas existentes faltantes en migration)

```sql
CREATE TABLE `product_jazz` (
    `id`             bigint(20) unsigned NOT NULL,
    `nombre`         varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `code`           varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `provider_code`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `equivalence`    varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `observation`    varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ubicacion`      varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `stock`          int(11) NOT NULL,
    `precio_lista_2` double(12,2) DEFAULT NULL,
    `precio_lista_3` double(12,2) DEFAULT NULL,
    `precio_lista_6` double(12,2) DEFAULT NULL,
    `stock_min`      double(12,2) DEFAULT NULL,
    `stock_max`      double(12,2) DEFAULT NULL,
    `punto_pedido`   double(12,2) DEFAULT NULL,
    `is_updated`     tinyint(1) NOT NULL DEFAULT 0,
    `fecha_alta`     datetime DEFAULT NULL,
    `fecha_mod`      datetime DEFAULT NULL,
    `created_at`     timestamp NULL DEFAULT NULL,
    `updated_at`     timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
