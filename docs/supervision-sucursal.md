# Autorización obligatoria de mercancía

El empleado captura y pulsa **Enviar a autorización**. La solicitud se guarda en `receiving_requests`, separada de los recibos que mueven inventario. No agrega existencias, series, costos, precios, deuda al proveedor ni facturas. No aparece como recepción completada en reportes.

El supervisor recibe el aviso en Home, abre **Revisar mercancía** y compara productos, cantidades, equivalencias por empaque, series, caducidad y costos con la mercancía física. Autorizar aplica el recibo nativo de SAMYPOS una sola vez dentro de la misma transacción que guarda supervisor, fecha y vínculo al recibo. Rechazar exige motivo y no modifica inventario. El panel se actualiza cada 30 segundos mientras Home está visible.

## Activación

Respaldar la base y aplicar `20261007053000_receiving_supervisor` desde `index.php/migrate/start`. Asignar acceso a Recepciones y el permiso **Autorizar recepciones de mercancía** únicamente a los supervisores. El permiso no se concede automáticamente. Todos los empleados, incluidos supervisores, envían primero una solicitud; la autorización es una acción aparte.

El bloqueo se aplica en `Receiving::save`, por lo que la confirmación directa, API/importaciones y recepciones parciales no pueden aplicar mercancía sin pasar por el servicio de autorización. Si la migración falta, el flujo falla cerrado. Los borradores y órdenes suspendidas con cantidad recibida cero siguen guardándose, sin actualizar inventario, precios ni series. Las APIs de confirmación devuelven 403 e indican enviar la solicitud desde Recepciones. Los pagos genuinos a cuenta del proveedor conservan su flujo y no permiten camuflar líneas de mercancía.

Las solicitudes enviadas son inmutables. Un borrador guardado solo puede originar una solicitud. Para corregir una rechazada, capturar una nueva solicitud. No se permite usar este flujo para editar recibos previamente aplicados: deben corregirse con una devolución documentada, también sujeta a autorización. Anular mercancía ya aplicada exige permiso de supervisor. Los recibos históricos permanecen intactos.

El inventario se aplica con actualizaciones locales síncronas. Se omiten los webhooks externos del recibo durante esta transacción, evitando enviar eventos de una operación que después pudiera revertirse; una sincronización externa posterior requerirá una cola de eventos confirmados.

## Verificación

`php tests/supervisor_receiving.php` valida bloqueos en el servidor, intento de inyectar autorización, recepción parcial, cambio fraudulento de modo, permisos, sucursal, versión desactualizada, reversión ante fallo y aprobación duplicada. La prueba de aplicación usa un doble del servicio de inventario y no sustituye una prueba con la base real. Se verifica sintaxis PHP de todos los archivos modificados y se incluyen comprobaciones en GitHub Actions.

Antes de producción, probar con una copia de la base: enviar una solicitud y confirmar que cantidades/costos/saldo/series no cambien; revisar y autorizar con otro usuario; comparar el inventario y el saldo resultantes con el recibo; repetir el POST de autorización sin duplicar movimientos; rechazar una solicitud; intentar confirmar por API o recibir parcialmente; probar productos con variantes, empaques, series, moneda extranjera y transferencias entre sucursales autorizadas. Confirmar que las tablas afectadas usen InnoDB para permitir reversión transaccional.
