# Queries SQL Útiles

Colección de queries SQL helpers organizadas por base de datos.

---

## Sistema Local (cuaderno_prod)

### Users

#### Listar usuarios con idVendedor

```sql
SELECT id, name, email, idVendedor
FROM users
ORDER BY name;
```

---

## Sistema Jazz

### Users

#### Vincular idVendedor a usuarios del sistema

```sql
-- =====================================================
-- SCRIPT: Actualizar idVendedor en users desde Jazz
-- Fecha: 2026-04-26
-- =====================================================

UPDATE users u
INNER JOIN (
    SELECT 5 AS idVendedor, 'allende@allenderepuestos.com.ar' AS email UNION
    SELECT 5, 'allende@allenderepuestos.com.ar_' UNION
    SELECT 8, 'ignaciopaglieri@allenderepuestos.com.ar' UNION
    SELECT 4, 'ivanescobar@allenderepuestos.com.ar' UNION
    SELECT 17, 'joaquinberrios@allenderepuestos.com.ar' UNION
    SELECT 375, 'leandroalmazabal@allenderepuestos.com.ar' UNION
    SELECT 3, 'maximoperello@allenderepuestos.com.ar' UNION
    SELECT 1, 'oscarbarigelli@allenderepuestos.com.ar' UNION
    SELECT 10, 'santiagofuentes@allenderepuestos.com.ar' UNION
    SELECT 15, 'sebastiandeharo@allenderepuestos.com.ar' UNION
    SELECT 6, 'tomasbarros@allenderepuestos.com.ar' UNION
    SELECT 16, 'tomasschawb@allenderepuestos.com.ar'
) AS jazz_vendedores ON u.email = jazz_vendedores.email
SET u.idVendedor = jazz_vendedores.idVendedor;
```

| Usuario (cuaderno_prod) | email | Vendedor Jazz | idVendedor |
|------------------------|-------|----------------|------------|
| Allende | allende@allenderepuestos.com.ar_ | Allende Sergio | 5 |
| Allende Repuestos | allende@allenderepuestos.com.ar | Allende Sergio | 5 |
| Ignacio Paglieri | ignaciopaglieri@allenderepuestos.com.ar | Paglieri Ignacio | 8 |
| Ivan Escobar | ivanescobar@allenderepuestos.com.ar | Escobar Ivan | 4 |
| Joaquin Berrios | joaquinberrios@allenderepuestos.com.ar | Joaquin Berrios | 17 |
| Leandro Almazabal | Leandroalmazabal@allenderepuestos.com.ar | Leandro Almazabal | 375 |
| Maximo Perello | maximoperello@allenderepuestos.com.ar | Perello Maximo | 3 |
| Oscar Barigelli | oscarbarigelli@allenderepuestos.com.ar | Barigelli Oscar | 1 |
| Santiago Fuentes | santiagofuentes@allenderepuestos.com.ar | Fuentes Santiago | 10 |
| Sebastian De Haro | sebastiandeharo@allenderepuestos.com.ar | De Haro Sebastian | 15 |
| Tomas Barros | tomasbarros@allenderepuestos.com.ar | Barros Tomas | 6 |
| Tomas Schawb | tomasschawb@allenderepuestos.com.ar | Tomas Schwab | 16 |

---

### Vendedores

#### Listar vendedores activos

```sql
SELECT IdVendedor, Nombre, Numero, Activo
FROM vendedores
WHERE Activo = 'S'
ORDER BY Nombre;
```

---

### Productos

#### Detalle completo de un producto

```sql
SELECT
    p.IdProducto,
    p.numero,
    p.Nombre,
    p.FechaMOD AS fecha_mod,
    p.FechaALTA AS fecha_alta,
    m.Codigo as codigo_marca,
    (
    SELECT SUM(
    fa.Cantidad *
    CASE 
        WHEN f.CodigoPerfil = 6 THEN 0
        WHEN f.Tipo IN (3, 4) THEN 1
        ELSE -1
    END
)
FROM facturas_articulos fa
JOIN facturas f ON f.NroInterno = fa.NroInterno
WHERE fa.IdProducto = p.IdProducto) AS stock,
    (
    SELECT CodigoProducto
    FROM productosproveedores pp
    WHERE pp.IdProducto = p.IdProducto
    AND pp.CostoEstandar = (
        SELECT preciocostoestandar
        FROM productoscombinacionescabecera p2
        WHERE p2.IdProducto = p.IdProducto
    )
    LIMIT 1
    ) AS provider_code,
    MAX(CASE WHEN pv.idLista = 2 THEN pv.Precio END) AS precio_lista_2,
    MAX(CASE WHEN pv.idLista = 3 THEN pv.Precio END) AS precio_lista_3,
    MAX(CASE WHEN pv.idLista = 5 THEN pv.Precio END) AS precio_lista_5,
    MAX(CASE WHEN pv.idLista = 6 THEN pv.Precio END) AS precio_lista_6,
    MAX(CASE WHEN c.Nombre = 'Comodín' THEN cv.Valor END) AS `Comodín`,
    MAX(CASE WHEN c.Nombre = 'F.AVISO' THEN cv.Valor END) AS `F_AVISO`,
    MAX(CASE WHEN c.Nombre = 'UBICACION' THEN cv.Valor END) AS `UBICACION`,
    MAX(CASE WHEN c.Nombre = 'CODIGO ORIGINAL' THEN cv.Valor END) AS `CODIGO_ORIGINAL`,
    MAX(CASE WHEN c.Nombre = 'EQUIVALENCIA' THEN cv.Valor END) AS `EQUIVALENCIA`,
    MAX(CASE WHEN c.Nombre = 'CODIGO PROVEEDOR' THEN cv.Valor END) AS `CODIGO_PROVEEDOR`,
    MAX(CASE WHEN c.Nombre = 'OBSERVACIONES' THEN cv.Valor END) AS `OBSERVACIONES`,
    MAX(CASE WHEN c.Nombre = 'ID' THEN cv.Valor END) AS `ID`,
    MAX(CASE WHEN c.Nombre = 'borrar' THEN cv.Valor END) AS `borrar`
                FROM productos p
                LEFT JOIN precios_venta pv ON p.IdProducto = pv.IdProducto
                LEFT JOIN comodinesvalores cv ON p.IdProducto = cv.IdCampo
                LEFT JOIN comodines c ON cv.IdComodin = c.IdComodin
                LEFT JOIN productoscombinacionescabecera pcc on pcc.IdProducto  = p.IdProducto
                LEFT JOIN marcas m on m.IdMarca = pcc	.Marca
                where p.IdProducto = 17489
                GROUP BY p.IdProducto, p.numero, p.Nombre, p.FechaMOD, p.FechaALTA, codigo_marca
```