

// Service Worker untuk SiLaporan PWA
const CACHE_NAME = 'silaporan-v3.0.0';
const urlsToCache = [
    'https://silaporan.puskesmasbalaraja.com/',
    'https://silaporan.puskesmasbalaraja.com/download',
    'https://silaporan.puskesmasbalaraja.com/dashboard',
    'https://silaporan.puskesmasbalaraja.com/css/app.css',
    'https://silaporan.puskesmasbalaraja.com/js/app.js',
    'https://silaporan.puskesmasbalaraja.com/logo.png',
    'https://silaporan.puskesmasbalaraja.com/favicon.ico'
];

// Install Service Worker
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Service Worker: Caching files for PWA');
                return cache.addAll(urlsToCache);
            })
            .then(function() {
                console.log('Service Worker: Installation complete');
                return self.skipWaiting();
            })
    );
});

// Activate Service Worker
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Clearing old cache');
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch Event - Network first, cache fallback
self.addEventListener('fetch', function(event) {
    // Skip caching for API calls and dynamic content
    if (event.request.url.includes('/api/') || 
        event.request.url.includes('/login') || 
        event.request.url.includes('/dashboard')) {
        event.respondWith(fetch(event.request));
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Return cached version or fetch from network
                if (response) {
                    return response;
                }
                
                return fetch(event.request).then(function(response) {
                    // Check if valid response
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }
                    
                    // Clone the response
                    var responseToCache = response.clone();
                    
                    caches.open(CACHE_NAME)
                        .then(function(cache) {
                            cache.put(event.request, responseToCache);
                        });
                    
                    return response;
                });
            })
    );
});

// Background sync untuk presensi offline
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync-presensi') {
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    // Implementasi sync data presensi jika diperlukan
    console.log('Background sync triggered');
}

// Push Notifications
self.addEventListener('push', function(event) {
    const options = {
        body: event.data ? event.data.text() : 'SiLaporan - Notifikasi baru',
        icon: '/logo.png',
        badge: '/favicon.ico',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Buka Aplikasi'
            },
            {
                action: 'close',
                title: 'Tutup'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('SiLaporan', options)
    );
});

// Notification Click Handler
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handler untuk komunikasi dengan main thread
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: '3.0.0-pwa' });
    }
});

console.log('Service Worker: SiLaporan PWA Mode initialized');
