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

## Notificaciones push y recibo autorizado

Aplicar la migración `20261007123000_receiving_push` además de la migración de autorización. Usar HTTPS y PHP 8.2 o posterior con curl, mbstring, openssl, phar y zlib. Permitir conexiones salientes HTTPS a los servicios push de los navegadores. La librería mantenida minishlink/web-push está fijada en composer.lock y empaquetada con sus licencias; no hace falta ejecutar Composer en cPanel. Para reconstruirla: dentro de application/libraries/webpush ejecutar `composer install --no-dev`, seguido de `php -d phar.readonly=0 build.php`.

Con una cuenta con acceso a Configuración, abrir Home y pulsar **Activar servicio push**. Esto genera claves VAPID una sola vez en el servidor. No publicar ni copiar la clave privada; respaldar la tabla webpush_settings junto con la base y conservarla al desplegar actualizaciones para mantener las suscripciones.

Cada supervisor entra desde su teléfono y pulsa **Activar en este teléfono**, acepta el permiso del navegador y usa **Enviar prueba**. En iPhone/iPad se requiere iOS/iPadOS 16.4 o posterior, agregar SAMYPOS a la pantalla de inicio y abrirlo desde ese icono. No hace falta publicar una app en las tiendas. Cada teléfono debe activarse por separado. En equipos compartidos desactivar los avisos antes de cambiar de usuario; activar con otra cuenta vincula ese navegador a la nueva cuenta. Las notificaciones muestran texto genérico, sin importes ni nombres en la pantalla bloqueada.

Configurar un cron de cPanel cada minuto con PHP 8.2 o posterior:

```sh
/usr/local/bin/php /RUTA/DEL/SISTEMA/index.php receivingnotifications cron https://TU-DOMINIO/
```

El trabajador solo admite CLI. La captura encola el aviso sin esperar una conexión externa; el cron recupera solicitudes pendientes e incluye dispositivos activados posteriormente. Revalida usuario activo, permisos de Recepciones/autorización y acceso a la sucursal antes de enviar. Excluye al capturista. Reintenta hasta 10 veces con espera creciente, recupera trabajos interrumpidos y desactiva suscripciones vencidas (HTTP 404/410). No envía correos automáticamente. El panel de Home sigue disponible aunque el teléfono no tenga permiso o el servicio push falle.

La entrega depende de conexión, permisos y configuración del teléfono. El estado “aceptado” significa que el servicio push aceptó el aviso, no que el supervisor lo leyó. Una interrupción tras el envío puede repetir el aviso; el tag agrupa avisos de la misma solicitud y la autorización sigue siendo única. La notificación abre la recepción para revisión y exige sesión y permisos; abrirla no autoriza. No se almacenan páginas privadas adicionales en caché.

El empleado ve sus 30 solicitudes más recientes en Home, con estado y motivo de rechazo. La lista se actualiza cada 30 segundos. Al autorizar se habilita **Imprimir recibo autorizado**. Las rutas de impresión, PDF y correo comprueban autorización y acceso; el recibo muestra al empleado que capturó, al supervisor y la fecha de autorización.

Pruebas automatizadas: `php tests/receiving_push.php`, `node tests/push_worker.js`, más las comprobaciones anteriores de autorización. Validan el runtime empaquetado y claves VAPID, formato de suscripciones y destinos seguros, exclusión del capturista, permisos/sucursal, reintentos y cancelación, y visualización/apertura del aviso. Antes de producción aplicar migraciones en una copia de la base y probar la entrega real con Android e iPhone, Chrome/Edge/Safari, permiso denegado, teléfono sin conexión, recepción ya autorizada y cambio de sucursal. No se han configurado ni probado dispositivos reales desde este entorno.

La separación de cuentas aporta trazabilidad, pero no acredita por sí sola la entrega física. El supervisor debe contrastar mercancía, cantidades, costo y documento del proveedor antes de aprobar.
