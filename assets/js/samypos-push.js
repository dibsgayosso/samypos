(function () {
  'use strict';
  var box = document.getElementById('samypos-push');
  if (!box) return;
  var status = box.querySelector('[role="status"]');
  var activate = box.querySelector('[data-action="subscribe"]');
  var deactivate = box.querySelector('[data-action="unsubscribe"]');
  var test = box.querySelector('[data-action="test"]');
  var setup = box.querySelector('[data-action="setup"]');
  var config, registration, subscription;
  function message(text) { status.textContent = text; }
  function post(action, sub) {
    return fetch(box.dataset.api + '/' + action, {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({csrf: config.csrf, subscription: sub ? sub.toJSON() : null})})
      .then(function (r) { return r.json(); }).then(function (data) { if (!data.ok) throw new Error(data.error || 'No se pudo guardar.'); return data; });
  }
  function controls(active) { activate.disabled = false; deactivate.disabled = !active; test.disabled = !active; }
  function key(value) {
    var raw = atob(value.replace(/-/g, '+').replace(/_/g, '/') + '='.repeat((4-value.length%4)%4));
    return Uint8Array.from(raw, function (c) { return c.charCodeAt(0); });
  }
  fetch(box.dataset.config, {credentials: 'same-origin', cache: 'no-store'}).then(function (r) { return r.json(); }).then(function (data) {
    if (data.error) throw new Error(data.error);
    config = data;
    if (!data.publicKey) {
      setup.hidden = !data.admin; setup.style.display = data.admin ? 'inline-block' : 'none'; setup.disabled = false;
      message('El administrador debe activar el servicio push.'); return;
    }
    if (!data.supervisor) { message('Asigna el permiso de autorización para activar este teléfono.'); return; }
    if (!window.isSecureContext || !('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
      message('Abre SAMYPOS por HTTPS en un navegador compatible. En iPhone, agrégalo a la pantalla de inicio y ábrelo desde allí.'); return;
    }
    return navigator.serviceWorker.register(box.dataset.worker, {scope: box.dataset.scope})
      .then(function () { return navigator.serviceWorker.ready; })
      .then(function (reg) { registration = reg; return reg.pushManager.getSubscription(); })
      .then(function (sub) { subscription = sub; controls(false); message(sub ? 'Pulsa Activar para vincular los avisos con tu cuenta.' : 'Activa los avisos en este teléfono.'); });
  }).catch(function (e) { message(e.message); });
  activate.addEventListener('click', function () {
    // Subscribe directly in the user gesture: required for iOS permission prompts.
    if (!registration || !config.publicKey) return;
    activate.disabled = true;
    var operation;
    try { operation = subscription ? Promise.resolve(subscription) : registration.pushManager.subscribe({userVisibleOnly: true, applicationServerKey: key(config.publicKey)}); }
    catch (e) { activate.disabled = false; message(e.message); return; }
    operation.then(function (sub) { subscription = sub; return post('subscribe', sub); })
      .then(function () { controls(true); message('Avisos activados para tu cuenta en este teléfono.'); })
      .catch(function () { controls(false); message('No se activaron los avisos. Revisa los permisos del navegador; en iPhone abre SAMYPOS desde la pantalla de inicio.'); });
  });
  deactivate.addEventListener('click', function () {
    deactivate.disabled = true;
    post('unsubscribe', subscription).then(function () { return subscription.unsubscribe(); })
      .then(function () { subscription = null; controls(false); message('Avisos desactivados en este teléfono.'); })
      .catch(function (e) { controls(true); message(e.message); });
  });
  test.addEventListener('click', function () {
    test.disabled = true;
    post('test', subscription).then(function () { message('Prueba aceptada por el servicio push. Revisa las notificaciones de tu teléfono.'); })
      .catch(function (e) { message(e.message); }).finally(function () { test.disabled = false; });
  });
  setup.addEventListener('click', function () {
    setup.disabled = true;
    post('setup').then(function () { location.reload(); }).catch(function (e) { setup.disabled = false; message(e.message); });
  });
}());
