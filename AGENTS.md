# AGENTS.md

## Alcance

Este repositorio contiene la API Laravel 8.75 del sistema Allende: cotizaciones, pedidos, envíos, catálogo, compras, autenticación, PDF/correo e integración con Jazz.

La documentación canónica está fuera de este repositorio, en [`../docs/README.md`](../docs/README.md). Léela primero y continúa por la página del dominio afectado. En particular:

- [flujo comercial](../docs/modules/commercial/overview.md);
- [catálogo](../docs/modules/catalog/overview.md);
- [integración Jazz](../docs/integrations/jazz/overview.md);
- [API, datos y autorización](../docs/shared/api/overview.md), [datos](../docs/shared/data/overview.md) y [autorización](../docs/shared/authorization/overview.md);
- [operación y riesgos](../docs/operations/overview.md) y [riesgos conocidos](../docs/quality/risks-and-gaps.md).

No mantengas documentación dentro de este repositorio. Las rutas, controladores, servicios, modelos, migraciones y configuración actual son la evidencia primaria; la documentación raíz describe el comportamiento verificado.

## Reglas de implementación

- Mantén validación de entrada en `FormRequest` cuando exista, reglas en la capa ya utilizada y respuestas mediante Resources/helpers existentes.
- Revisa `routes/api.php` antes de cambiar un contrato HTTP y conserva la autorización del servidor: los controles de la interfaz no bastan.
- Usa transacciones para escrituras locales que abarquen varias entidades. Jazz, correo y otros efectos externos no participan de una transacción distribuida; ante un timeout, concilia antes de reintentar.
- Los detalles de cotización, pedido y envío son históricos e independientes después de la copia. No recalcules precios del catálogo de forma implícita.
- Estados, tipos y formas de pago se resuelven por `tables.name` + `tables.value`; evita depender de IDs, excepto las dependencias explícitas documentadas.
- El esquema versionado no reproduce necesariamente producción: contrasta migraciones, modelos y base objetivo antes de cambios de datos.

## Verificación y operación segura

- El desarrollo del backend se ejecuta con Docker Compose definido en `docker-compose.yml`; no se espera PHP ni Composer instalados en el host. Confirma el servicio con `docker compose config --services` y, con el contenedor activo, ejecuta los comandos dentro de `app`: `docker compose exec app php artisan test` o el test enfocado correspondiente. Cuando afectes rutas, revisa `docker compose exec app php artisan route:list`. Si Docker o el contenedor no están disponibles, informa ese bloqueo concreto; no lo sustituyas por una comprobación en el host.
- No ejecutes migraciones destructivas, seeders, sincronizaciones masivas, correos reales ni llamadas Jazz como prueba.
- No edites `.env` ni copies valores sensibles. Documenta solamente nombres y propósito de variables.
- Para cambios de comportamiento, contrato, operación, integración o configuración, actualiza la página canónica en `../docs/` y ejecuta `python ../.agents/skills/document-system/scripts/audit_docs.py ../docs`.
