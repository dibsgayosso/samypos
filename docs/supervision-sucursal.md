# Supervisión de sucursal

Aplicar la migración `20261007053000_receiving_supervisor` desde el actualizador del sistema (`index.php/migrate`). Respaldar la base antes de actualizar. Asignar en Empleados → permisos de Recepciones → Autorizar recepciones de mercancía únicamente a supervisores. El permiso no se concede automáticamente.

El inicio muestra pagos de hoy por cada método configurado (incluye transferencias y tarjetas), operaciones e importes; última venta completada de la sucursal; gastos del día con impuestos; y todas las recepciones pendientes. La cola se refresca cada 30 segundos mientras el inicio está visible. La tabla existente de pagos se actualiza al cargar la página.

Solo nuevos recibos completados o recibos editados con cantidad neta positiva requieren autorización. Pedidos suspendidos, órdenes de compra y pagos de cuenta quedan excluidos. Los recibos históricos no se ponen pendientes al migrar. El inventario conserva el movimiento al recibir; esta función es una revisión posterior, no una reserva de inventario.

Cada edición reinicia la revisión y aumenta la versión. La autorización es POST, valida permiso y token de sesión, limita la sucursal y usa una actualización condicional en transacción para evitar autorizaciones duplicadas o de una versión desactualizada. `receiving_authorizations` guarda recibo, versión, supervisor y fecha. No se elimina este historial al revertir la migración.

## Validación en un entorno de prueba

1. Aplicar la migración dos veces y confirmar que no falla.
2. Recibir mercancía: aparece pendiente en la sucursal correcta. Confirmar inventario una sola vez.
3. Autorizar con supervisor: desaparece de pendientes y aparece auditoría en SQL.
4. Editar el recibo: vuelve a pendientes; una autorización con la versión anterior se rechaza.
5. Intentar autorizar con usuario sin permiso, otra sucursal, GET o sin token: se rechaza.
6. Comparar pagos combinados y gastos con los reportes del mismo día.

No se dispone de la base de datos del servidor para verificar la integración en producción.
