/**
 * Service Worker for Push Notifications
 * Generates a sine wave notification sound using AudioContext
 */

self.addEventListener('push', function (event) {
    let data = {
        title: 'New Order',
        body: 'You have a new order!',
        icon: '/frontEnd/images/no_image.png',
        data: {}
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body || 'You have a new order!',
        icon: data.icon || '/frontEnd/images/no_image.png',
        badge: '/frontEnd/images/no_image.png',
        vibrate: [200, 100, 200, 100, 200],
        tag: 'new-order-' + (data.data && data.data.order_id ? data.data.order_id : Date.now()),
        renotify: true,
        requireInteraction: true,
        data: data.data || {},
        actions: [
            { action: 'view_order', title: 'View Order' },
            { action: 'dismiss', title: 'Dismiss' }
        ]
    };

    event.waitUntil(
        // Check permission before showing notification to avoid TypeError
        (Notification.permission === 'granted'
            ? self.registration.showNotification(data.title || 'New Order', options)
            : Promise.resolve()
        )
            .then(function () {
                // Notify all clients to play the notification sound
                return self.clients.matchAll({ type: 'window', includeUncontrolled: true });
            })
            .then(function (clients) {
                clients.forEach(function (client) {
                    client.postMessage({
                        type: 'NEW_ORDER_NOTIFICATION',
                        data: data.data || {}
                    });
                });
            })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    const orderData = event.notification.data;
    let targetUrl = '/admin-orders';

    if (orderData && orderData.order_id) {
        targetUrl = '/admin-orders/' + orderData.order_id + '/edit';
    }

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function (clientList) {
                // Try to focus an existing window
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url.includes('/admin') || client.url.includes('/manager') || client.url.includes('/employee')) {
                        return client.navigate(targetUrl).then(function (c) {
                            return c.focus();
                        });
                    }
                }
                // Open a new window if none found
                return self.clients.openWindow(targetUrl);
            })
    );
});

self.addEventListener('notificationclose', function (event) {
    // Notification was closed without interaction
});
