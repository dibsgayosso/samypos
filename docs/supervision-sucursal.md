# Autorización obligatoria de mercancía

El empleado captura y pulsa **Enviar a autorización**. La solicitud se guarda en `receiving_requests`, separada de los recibos que mueven inventario. No agrega existencias, series, costos, precios, deuda al proveedor ni facturas. No aparece como recepción completada en reportes.

El supervisor recibe el aviso en Home, abre **Revisar mercancía** y compara productos, cantidades, equivalencias por empaque, series, caducidad y costos con la mercancía física. Autorizar aplica el recibo nativo de SAMYPOS una sola vez dentro de la misma transacción que guarda supervisor, fecha y vínculo al recibo. Rechazar exige motivo y no modifica inventario. El panel se actualiza cada 30 segundos mientras Home está visible.

## Activación

Respaldar la base y aplicar `20261007053000_receiving_supervisor` desde `index.php/migrate/start`. Asignar acceso a Recepciones y el permiso **Autorizar recepciones de mercancía** únicamente a los supervisores. El permiso no se concede automáticamente. Todos los empleados, incluidos supervisores, envían primero una solicitud. Quien captura no puede autorizar su propia solicitud: debe intervenir otra cuenta con permiso de supervisor.

El bloqueo se aplica en `Receiving::save`, por lo que la confirmación directa, API/importaciones y recepciones parciales no pueden aplicar mercancía sin pasar por el servicio de autorización. Si la migración falta, el flujo falla cerrado. Los borradores y órdenes suspendidas con cantidad recibida cero siguen guardándose, sin actualizar inventario, precios ni series. Las APIs de confirmación devuelven 403 e indican enviar la solicitud desde Recepciones. Los pagos genuinos a cuenta del proveedor conservan su flujo y no permiten camuflar líneas de mercancía.

Las solicitudes enviadas son inmutables. Un borrador guardado solo puede originar una solicitud. Para corregir una rechazada, capturar una nueva solicitud. No se permite usar este flujo para editar recibos previamente aplicados: deben corregirse con una devolución documentada, también sujeta a autorización. Anular mercancía ya aplicada exige permiso de supervisor. Los recibos históricos permanecen intactos.

El inventario se aplica con actualizaciones locales síncronas. Se omiten los webhooks externos del recibo durante esta transacción, evitando enviar eventos de una operación que después pudiera revertirse; una sincronización externa posterior requerirá una cola de eventos confirmados.

## Verificación

`php tests/supervisor_receiving.php` valida bloqueos en el servidor, intento de inyectar autorización, recepción parcial, cambio fraudulento de modo, permisos, sucursal, versión desactualizada, reversión ante fallo y aprobación duplicada. La prueba de aplicación usa un doble del servicio de inventario y no sustituye una prueba con la base real. Se verifica sintaxis PHP de todos los archivos modificados y se incluyen comprobaciones en GitHub Actions.

Antes de producción, probar con una copia de la base: enviar una solicitud y confirmar que cantidades/costos/saldo/series no cambien; revisar y autorizar con otro usuario; comparar el inventario y el saldo resultantes con el recibo; repetir el POST de autorización sin duplicar movimientos; rechazar una solicitud; intentar confirmar por API o recibir parcialmente; probar productos con variantes, empaques, series, moneda extranjera y transferencias entre sucursales autorizadas. Confirmar que las tablas afectadas usen InnoDB para permitir reversión transaccional.

## Aviso en el teléfono y recibo autorizado

Aplicar también la migración `20261007060000_receiving_notifications`. Completar el correo de cada supervisor en Empleados y configurar SMTP en Configuración del sistema. El aviso se envía a cuentas activas con acceso a Recepciones, permiso de autorización y acceso a la sucursal de la solicitud. Se excluye la cuenta que capturó la mercancía. La elegibilidad y el correo se verifican nuevamente al intentar el envío. El enlace exige login y nunca autoriza por abrirlo.

El empleado ve sus 30 solicitudes más recientes en Home, con estado y motivo de rechazo. La lista se actualiza cada 30 segundos. Al autorizar se habilita **Imprimir recibo autorizado**. Las rutas de impresión, PDF y correo comprueban autorización y acceso; el recibo muestra el empleado que capturó más el supervisor y fecha de autorización. El supervisor puede revisar desde el celular; la notificación del correo en pantalla depende de tener activadas las notificaciones de su app de correo.

La solicitud se conserva aunque SMTP falle. El envío se procesa fuera de la captura para evitar que SMTP retrase al empleado. Configurar un cron de cPanel cada minuto para avisos y reintentos:

```sh
/usr/local/bin/php /RUTA/DEL/SISTEMA/index.php receivingnotifications cron https://TU-DOMINIO/
```

Usar la ruta real de PHP y del sistema, y su URL pública real. El trabajador solo admite CLI y no requiere exponer una URL sin login. Recupera envíos interrumpidos, bloquea mensajes en proceso y reintenta hasta 10 veces con espera creciente. Una interrupción después de aceptar SMTP y antes de guardar `sent` puede producir un correo repetido; no duplica recepción ni inventario. La pantalla de la solicitud muestra si el aviso fue enviado, sigue en cola o no hay destinatarios con correo válido. No se han enviado correos de prueba ni configurado el servidor real.

La separación de cuentas aporta trazabilidad, pero no acredita por sí sola la entrega física. El supervisor debe contrastar mercancía, cantidades, costo y documento del proveedor antes de aprobar.
