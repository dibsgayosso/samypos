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

## Panel del propietario

Aplicar `20261007140000_owner_dashboard`. En Empleados → permisos de Reportes, asignar **Ver panel del propietario** únicamente al propietario o responsable autorizado, junto con acceso al módulo Reportes y a las sucursales correspondientes. No se concede acceso automáticamente a supervisores ni empleados. Para mostrar diferencias en cada sucursal también debe tener **Ver diferencia en cortes de caja**; sin ese permiso el panel no consulta ni envía importes de diferencias.

Home muestra el consolidado de las sucursales autorizadas y una tabla comparativa: ventas de hoy, entradas aplicadas, pendientes de autorización de todas las fechas, último corte con faltante/sobrante, minutos desde la última venta, créditos por cobrar y gastos de hoy. Cada sucursal permite desplegar sus ventas por método de pago y gastos por categoría. Se actualiza cada 30 segundos con la página visible, conserva los detalles abiertos y avisa si falla la actualización. La ruta home/owner_panel verifica nuevamente los permisos y no acepta IDs de sucursales proporcionados por el cliente.

Los métodos conservan los nombres configurados y los pagos históricos. Se reutiliza la distribución nativa de los reportes para pagos combinados y cambio. Las devoluciones reducen los montos; se excluyen ventas suspendidas, eliminadas y abonos a cuentas de crédito de las ventas de mercancía. Los créditos por cobrar son el saldo positivo actual de clientes asignados a la sucursal (como en Reportes → cuentas de clientes), no la venta a crédito únicamente de hoy ni la deuda con proveedores. Saldos a favor se presentan aparte. Clientes sin sucursal asignada no se adjudican a una sucursal automáticamente. Los gastos incluyen impuestos y excluyen registros eliminados.

Las entradas son recepciones aplicadas con cantidad recibida positiva y fecha del recibo de hoy; excluyen órdenes/borradores suspendidos, pagos a proveedores y devoluciones de mercancía. Una solicitud pendiente no se suma como entrada. El último corte se selecciona por fecha real de cierre, con ID como desempate; se reutiliza la fórmula nativa y se muestra su caja, empleado, fecha y hora. No se suman diferencias de cortes de fechas distintas en un falso total global.

Cada sucursal calcula el día con su zona horaria configurada usando las convenciones de fechas existentes del POS. Las pruebas automatizadas no reemplazan una comparación con la base real: verificar cifras contra Reportes de ventas, recepciones, gastos, cuentas de clientes y cortes; incluir pago combinado, cambio, devolución, abono a crédito, clientes con saldo a favor, cierre de caja antiguo actualizado, sucursal sin actividad y permisos revocados. `php tests/owner_dashboard.php` valida consolidación, filtros, saldos, alcance de permisos, selección de cortes y escape de contenido.

## Informe periódico de créditos por correo

Aplicar `20261007180000_credit_report_schedule`. En Home del propietario abrir **Programar informe de créditos por correo**. Configurar el correo personal del propietario en Empleados, habilitar envío, elegir día, hora, zona horaria y frecuencia (cada 1, 2, 3 o 4 semanas). El primer envío es el próximo día seleccionado. Puede deshabilitarse desde la misma pantalla. **Descargar muestra en PDF** permite revisar el informe sin enviar correo.

Configurar SMTP y un remitente válido en smtp_user. Añadir un cron cada minuto con la ruta real del sistema:

```sh
/usr/local/bin/php /RUTA/DEL/SISTEMA/index.php creditreportmailer cron
```

El cron solo admite CLI. Envía un PDF con secciones por sucursal autorizada: cartera actual, cartera hace 7 días, cambio absoluto y porcentual, saldos de clientes (incluidas cuentas liquidadas esta semana), y documentos abiertos con más de 30 días desde su vencimiento. Aunque se programe cada 2–4 semanas, la comparación siempre es contra 7 días antes. No compara contra cero si falta historial, ni divide entre cero. La referencia semanal se obtiene del último saldo en store_accounts anterior al corte de referencia, con sno para desempates. La atribución usa los clientes asignados actualmente a cada sucursal; reasignaciones o eliminaciones pueden cambiar el comparativo. Es una comparación del historial disponible, no un reconstruido de afiliaciones históricas.

Los créditos sin fecha de vencimiento documentada no se clasifican como atraso. La sección vencida utiliza customer_invoices.due_date y su saldo pendiente, excluye documentos eliminados y distingue exactamente 30 de más de 30 días. Los abonos generales que no se hayan aplicado al documento pueden dejar su saldo pendiente; deben conciliarse antes de usar el reporte para cobranza. Un documento pertenece a su sucursal emisora, mientras el saldo de cliente sigue su sucursal asignada. Estos importes no se suman dos veces.

Los informes se capturan con lectura consistente y se congelan en la cola antes de enviar. Se revalidan usuario activo, correo, permisos y sucursales justo antes de SMTP. Cambiar programación, deshabilitar, modificar correo o retirar acceso cancela entregas pendientes incompatibles. Una conexión obtiene el bloqueo de trabajador; el par propietario/ocurrencia es único. Los fallos reintentan hasta 10 veces con espera creciente. Una interrupción entre la aceptación SMTP y guardar sent puede repetir un correo; no modifica créditos. Si el servidor estuvo detenido, se genera un informe de recuperación, sin bombardear con todos los envíos omitidos. La hora efectiva depende de la ejecución del cron.

No se han configurado SMTP ni envíos reales desde este entorno. Verificar PDF de muestra contra cuentas de clientes y documentos, comparar abonos parciales/liquidaciones, falta de historial, cero de referencia, plazos futuros y 30/31 días vencidos. Probar programación con una copia de la base y un correo autorizado antes de habilitar en producción. `php tests/credit_reports.php` comprueba calendario y cambios de horario, porcentajes, fronteras de atraso, escape y generación real del PDF.

### Pruebas de conexión y reporte

En la pantalla **Programar informe de créditos por correo** aparecen **Probar conexión y correo** y **Enviar reporte PDF de prueba**. La primera verifica el envío completo enviando un mensaje sencillo al correo del propietario; la segunda genera y envía el PDF actual de las sucursales autorizadas. Ambas requieren un POST autenticado con token de sesión, se limitan a una prueba cada 30 segundos y no cambian fechas, frecuencia ni estado del envío automático. El destinatario se toma de Empleados, nunca del navegador. No muestran contraseñas, tokens ni contenido de depuración SMTP. “Aceptado por el proveedor” no garantiza llegada a la bandeja: revisar entrada y spam. Las pruebas necesitan configuración previamente guardada, y no activan el cron.

Se soporta el SMTP configurado y Gmail API cuando ya está conectado en el POS; para API se requiere el correo remitente de la sucursal si no hay smtp_user. Se limpia la colección de adjuntos de Gmail API antes de cada mensaje para impedir que se agreguen PDFs de un envío anterior. No se ejecutaron pruebas con cuentas o correos reales desde este entorno.


### Reparar instalaciones con migración registrada pero tablas faltantes

Actualizar el código completo de `main`, incluida `application/config/migration.php`, y ejecutar desde la raíz del sitio correcto:

```bash
php index.php migrate version 20261008063000
```

La nueva migración vuelve a aplicar exclusivamente las cinco migraciones aditivas de supervisión, push y reportes de crédito. Usa `CREATE TABLE IF NOT EXISTS` e `INSERT IGNORE`: conserva datos, suscripciones y permisos concedidos, y no concede acceso automáticamente. No cambia estructuras existentes incompletas ni reconstruye datos. No reducir manualmente `phppos_migrations.version`. Una falla SQL debe detener la migración; CLI devuelve código 1. Verificar la versión impresa y revisar logs si falla.

El panel del propietario registra fallos y presenta un aviso sin bloquear todo Home. Se corrigió el prefijo de categorías de gastos. Las pruebas incluyen el compilador SQL real de CodeIgniter, pero no sustituyen una prueba con la base MySQL del servidor ni una entrega push o correo real.


### Panel visual del supervisor

Home muestra el panel al inicio a quienes tienen acceso a Recepciones y autorización en al menos una sucursal accesible. Incluye tarjetas por sucursal, métodos de pago con sus nombres configurados, operaciones, gráfica de distribución positiva, gastos con impuestos, última venta de mercancía y solicitudes pendientes. Las devoluciones se conservan en los importes; abonos a crédito se excluyen de las ventas. Cada sucursal utiliza su fecha local. Se refresca cada 30 segundos. Revisar una solicitud de otra sucursal utiliza el flujo de cambio de sucursal ya existente y vuelve a validar los permisos antes de autorizar.


### Panel ilustrativo del propietario

Incluye tarjetas de totales, gráfica de métodos de pago, comparación por sucursal, tarjetas de entradas/créditos/gastos y diferencias del último corte sujetas al permiso existente. El detalle de gastos y pagos se despliega por sucursal. Las tarjetas históricas, bienvenida, accesos rápidos y gráficos antiguos de Home se ocultan para quien ve el panel del propietario; los controles push quedan plegados y siguen disponibles. No necesita otra migración.


### Activar panel del supervisor desde Empleados

Actualizar main y ejecutar `php index.php migrate version 20261008131000`. En Empleados → Editar usuario → Permisos → Reportes, marcar Ver panel del supervisor y las sucursales que puede consultar. Guardar. El permiso no se concede automáticamente ni depende de Autorizar recepciones. Para aprobar mercancía y recibir push, conservar Recepciones → Autorizar recepciones de mercancía y acceso al módulo. La autorización siempre se valida en el servidor.


### Importes visibles al supervisor

El panel del supervisor entrega únicamente filas, montos y operaciones de métodos cuyo nombre comienza con Transferencia(s), Transfer, Bank transfer o SPEI. Se conservan los nombres configurados. No entrega total de ventas ni filas de tarjeta, efectivo o créditos en el fragmento HTML del supervisor. Continúan gastos, última venta y mercancía pendiente. Se ocultan también los bloques antiguos de Home para usuarios con este panel, para evitar mostrar ventas por esa vía. El propietario conserva sus importes completos.
