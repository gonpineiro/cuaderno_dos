# Proceso de Consistencia de Ubicación entre `product` y `product_jazz`

## Objetivo

Mantener la consistencia entre el atributo `ubicacion` de la tabla `product_jazz` y los campos desglosados (`ship`, `module`, `column`, `row`, `side`) en la tabla `products`.

## Estructura del atributo `ubicacion`

El campo `ubicacion` en `product_jazz` es una cadena de texto que contiene la ubicación física del producto, codificada con prefijos:

```
ubicacion = "NAVE:{ship}-MOD:{module}-COL:{column}-FILA:{row}-LADO:{side}"
```

### Desglose de componentes

| Campo en `products` | Prefijo en `ubicacion` | Descripción |
|---------------------|------------------------|-------------|
| `ship`              | `NAVE:`                | Nave o galpón donde se ubica el producto |
| `module`            | `MOD:`                 | Módulo dentro de la nave |
| `column`            | `COL:`                 | Columna dentro del módulo |
| `row`               | `FILA:`                | Fila dentro de la columna |
| `side`              | `LADO:`                | Lado (último componente, sin delimitador posterior) |

## Scripts SQL

### 1. Query de Verificación (SELECT)

Permite visualizar las diferencias entre ambos conjuntos de datos antes de aplicar cambios.

```sql
SELECT
  p.ship,
  TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1)) AS ship_j,
  p.module,
  TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'MOD:', -1), '-', 1)) AS module_j,
  p.`column`,
  TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'COL:', -1), '-', 1)) AS `column_j`,
  p.`row`,
  TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'FILA:', -1), '-', 1)) AS `row_j`,
  p.side,
  TRIM(SUBSTRING_INDEX(pj.ubicacion, 'LADO:', -1)) AS side_j,
  pj.ubicacion
FROM products p
JOIN product_jazz pj ON pj.id = p.idProducto AND pj.code = p.code
WHERE p.idProducto IS NOT NULL
  AND p.ship IS NULL
  AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1)) IS NOT NULL;
```

**Condiciones del WHERE:**
- `p.idProducto IS NOT NULL`: Solo productos con referencia a `product_jazz`
- `p.ship IS NULL`: Solo productos donde el campo `ship` aún no está poblado
- `TRIM(...) IS NOT NULL`: Solo registros donde `ubicacion` contenga un valor válido para `NAVE:`

### 2. Query de Actualización (UPDATE)

Aplica los valores extraídos de `ubicacion` a los campos correspondientes en `products`.

```sql
UPDATE products p
JOIN product_jazz pj
  ON pj.id = p.idProducto
  AND pj.code = p.code
SET
  p.ship = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1)),
  p.module = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'MOD:', -1), '-', 1)),
  p.`column` = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'COL:', -1), '-', 1)),
  p.`row` = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'FILA:', -1), '-', 1)),
  p.side = TRIM(SUBSTRING_INDEX(pj.ubicacion, 'LADO:', -1))
WHERE
  p.idProducto IS NOT NULL
  AND p.ship IS NULL
  AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1)) IS NOT NULL;
```

## Lógica de Extracción

Se utiliza `SUBSTRING_INDEX` anidado para extraer valores entre prefijos y delimitadores:

1. **Para campos con delimitador `-`:**
   ```sql
   TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1))
   ```
   - `SUBSTRING_INDEX(..., 'NAVE:', -1)`: Obtiene todo lo que está después de `NAVE:`
   - `SUBSTRING_INDEX(..., '-', 1)`: Obtiene todo hasta el primer `-`

2. **Para el último campo (`side`):**
   ```sql
   TRIM(SUBSTRING_INDEX(pj.ubicacion, 'LADO:', -1))
   ```
   - No requiere segundo delimitador porque `LADO:` es el último componente

3. **TRIM()**: Elimina espacios en blanco al inicio y final del valor extraído.

## Uso Recomendado

1. **Ejecutar primero el SELECT** para verificar qué registros serán afectados
2. **Revisar los resultados** y confirmar que la extracción es correcta
3. **Ejecutar el UPDATE** para aplicar los cambios
4. **Ejecutar nuevamente el SELECT** para verificar que la consistencia se logró

## Archivos Relacionados

- `../sql/` - Scripts SQL de verificación y actualización
