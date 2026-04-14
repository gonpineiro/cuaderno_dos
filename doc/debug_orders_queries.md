# Debug Queries - Orders / Order Products / Products

## 1. Query base

``` sql
SELECT 
    o.id AS order_id,
    op.id AS order_product_id,
    op.product_id,
    p.code AS product_code,
    op.description AS order_product_description,
    p.description AS product_description,
    op.amount,
    op.unit_price
FROM orders o
JOIN order_product op ON op.order_id = o.id
JOIN products p ON p.id = op.product_id
WHERE o.id = :order_id;
```

------------------------------------------------------------------------

## 2. Detectar inconsistencias de descripción

``` sql
SELECT 
    op.id,
    p.code,
    op.description AS order_desc,
    p.description AS product_desc
FROM order_product op
JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id
AND (
    op.description IS NULL 
    OR op.description != p.description
);
```

------------------------------------------------------------------------

## 3. Totales del pedido

``` sql
SELECT 
    o.id,
    SUM(op.amount * op.unit_price) AS total_calculado,
    COUNT(op.id) AS cantidad_items
FROM orders o
JOIN order_product op ON op.order_id = o.id
WHERE o.id = :order_id
GROUP BY o.id;
```

------------------------------------------------------------------------

## 4. Productos duplicados

``` sql
SELECT 
    op.product_id,
    p.code,
    COUNT(*) AS veces,
    SUM(op.amount) AS total_cantidad
FROM order_product op
JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id
GROUP BY op.product_id, p.code
HAVING COUNT(*) > 1;
```

------------------------------------------------------------------------

## 5. Productos huérfanos

``` sql
SELECT 
    op.id,
    op.product_id
FROM order_product op
LEFT JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id
AND p.id IS NULL;
```

------------------------------------------------------------------------

## 6. Estados de cada item

``` sql
SELECT 
    op.id,
    p.code,
    op.state_id
FROM order_product op
JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id;
```

------------------------------------------------------------------------

## 7. Debug completo

``` sql
SELECT 
    o.id AS order_id,
    o.created_at,

    op.id AS order_product_id,
    op.product_id,
    p.code,

    op.description AS op_desc,
    p.description AS prod_desc,

    op.amount,
    op.unit_price,
    (op.amount * op.unit_price) AS subtotal,

    op.provider_id,
    op.state_id,

    op.deleted_at AS op_deleted,
    p.deleted_at AS product_deleted

FROM orders o
JOIN order_product op ON op.order_id = o.id
LEFT JOIN products p ON p.id = op.product_id

WHERE o.id = :order_id;
```

------------------------------------------------------------------------

## 8. Soft deletes

``` sql
SELECT 
    op.id,
    p.code,
    op.deleted_at,
    p.deleted_at
FROM order_product op
JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id
AND (
    op.deleted_at IS NOT NULL
    OR p.deleted_at IS NOT NULL
);
```

------------------------------------------------------------------------

## 9. Validar provider

``` sql
SELECT 
    op.id,
    op.provider_id AS op_provider,
    p.provider_id AS product_provider
FROM order_product op
JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id
AND op.provider_id != p.provider_id;
```

------------------------------------------------------------------------

## 10. Orden por impacto económico

``` sql
SELECT 
    p.code,
    op.amount,
    op.unit_price,
    (op.amount * op.unit_price) AS subtotal
FROM order_product op
JOIN products p ON p.id = op.product_id
WHERE op.order_id = :order_id
ORDER BY subtotal DESC;
```

------------------------------------------------------------------------

## Bonus: View de debug

``` sql
CREATE VIEW vw_order_debug AS
SELECT 
    o.id AS order_id,
    op.id AS order_product_id,
    p.code,
    op.description,
    p.description AS product_description,
    op.amount,
    op.unit_price
FROM orders o
JOIN order_product op ON op.order_id = o.id
JOIN products p ON p.id = op.product_id;
```

``` sql
SELECT * FROM vw_order_debug WHERE order_id = :order_id;
```
