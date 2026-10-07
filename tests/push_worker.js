'use strict';
const vm = require('node:vm');
const fs = require('node:fs');
const assert = require('node:assert/strict');
const events = {}; const shown = []; const opened = [];
const context = {URL, self: {registration: {scope: 'https://example.test/pos/', showNotification: async (title, options) => shown.push({title, options})}, addEventListener: (name, callback) => events[name] = callback}, clients: {openWindow: async url => opened.push(url)}};
vm.runInNewContext(fs.readFileSync('samypos-push-events.js', 'utf8'), context);
(async () => {
 let promise;
 events.push({data: {json: () => ({title: 'Mercancía', url: 'https://example.test/pos/index.php/home/receiving_request/8', tag: 'receiving-8'})}, waitUntil: p => promise=p});
 await promise; assert.equal(shown.length, 1); assert.equal(shown[0].options.tag, 'receiving-8');
 events.notificationclick({notification: {data: shown[0].options.data, close() {}}, waitUntil: p=>promise=p});
 await promise; assert.equal(opened[0], 'https://example.test/pos/index.php/home/receiving_request/8');
 events.notificationclick({notification: {data: {url: 'https://evil.test/'}, close() {}}, waitUntil: p=>promise=p});
 await promise; assert.equal(opened[1], 'https://example.test/pos/index.php/home');
 events.push({data: {json() { throw new Error('bad payload'); }}, waitUntil: p=>promise=p});
 await promise; assert.equal(shown[1].title, 'SAMYPOS');
 console.log('Push worker display, click and safe fallback tests passed');
})().catch(e => {console.error(e); process.exitCode=1;});
