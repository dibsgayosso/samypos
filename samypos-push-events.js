/* Shared with the existing offline worker; no additional caching. */
self.addEventListener('install', function (event) { event.waitUntil(self.skipWaiting()); });
self.addEventListener('activate', function (event) { event.waitUntil(clients.claim()); });
self.addEventListener('push', function (event) {
  var data = {};
  try { data = event.data ? event.data.json() : {}; } catch (_) {}
  event.waitUntil(self.registration.showNotification(data.title || 'SAMYPOS', {
    body: data.body || 'Hay una recepción por revisar.', tag: data.tag || 'samypos',
    icon: new URL('assets/img/push-192.png', self.registration.scope).href,
    data: {url: data.url || new URL('index.php/home', self.registration.scope).href}
  }));
});
self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  var fallback = new URL('index.php/home', self.registration.scope);
  var target;
  try { target = new URL(event.notification.data.url); } catch (_) { target = fallback; }
  if (target.origin !== fallback.origin || !target.pathname.startsWith(new URL(self.registration.scope).pathname)) target = fallback;
  event.waitUntil(clients.openWindow(target.href));
});
