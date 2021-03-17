var cacheName = 'cacheName';
var domaine = 'https://domaineName.com/';

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(cacheName).then((cache) => cache.addAll([
      '/offline.html'
    ])),
  );
});

self.addEventListener("fetch", event => {
    if (event.request.url === domaine) {
        // or whatever your app's URL is
        event.respondWith(
            fetch(event.request).catch(err =>
                self.caches.open(cacheName).then(cache => cache.match("/offline.html"))
            )
        );
    } else {
        event.respondWith(
            fetch(event.request).catch(err =>
                caches.match(event.request).then(response => response)
            )
        );
    }
});