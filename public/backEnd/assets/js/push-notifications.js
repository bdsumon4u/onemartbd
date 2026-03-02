/**
 * Push Notification Manager
 * Handles service worker registration, push subscription, sine wave audio playback,
 * and notification/sound toggle controls with localStorage persistence.
 */
(function () {
    "use strict";

    var PushNotificationManager = {
        vapidPublicKey: null,
        subscribeUrl: null,
        unsubscribeUrl: null,
        csrfToken: null,
        userRole: "guest",
        isAdmin: false,
        audioContext: null,
        audioUnlocked: false,
        notificationEnabled: true,
        soundEnabled: true,
        swRegistration: null,
        pendingSoundOnVisible: false,

        init: function (config) {
            this.vapidPublicKey = config.vapidPublicKey;
            this.subscribeUrl = config.subscribeUrl;
            this.unsubscribeUrl = config.unsubscribeUrl;
            this.csrfToken = config.csrfToken;
            this.userRole = config.userRole || "guest";
            this.isAdmin = this.userRole === "admin";

            // Restore toggle states from localStorage
            this.loadToggleStates();

            if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
                console.warn(
                    "[Push] Push notifications are not supported in this browser.",
                );
                this.updateToggleUI();
                return;
            }

            this.registerServiceWorker();
            this.setupAudioContext();
            this.listenForMessages();
            this.setupVisibilityHandler();
            this.updateToggleUI();
        },

        /**
         * When tab becomes visible: resume AudioContext (often suspended in background)
         * and play any notification sound that was deferred while tab was hidden.
         */
        setupVisibilityHandler: function () {
            var self = this;

            document.addEventListener("visibilitychange", function () {
                if (document.visibilityState !== "visible") {
                    return;
                }

                if (
                    self.audioContext &&
                    self.audioContext.state === "suspended"
                ) {
                    self.audioContext.resume().then(function () {
                        if (self.pendingSoundOnVisible && self.soundEnabled) {
                            self.pendingSoundOnVisible = false;
                            self.playNotificationSound();
                        }
                    });
                } else if (
                    self.audioContext &&
                    self.pendingSoundOnVisible &&
                    self.soundEnabled
                ) {
                    self.pendingSoundOnVisible = false;
                    self.playNotificationSound();
                }
            });
        },

        /**
         * Load toggle states from localStorage
         */
        loadToggleStates: function () {
            var notifState = localStorage.getItem("push_notification_enabled");
            var soundState = localStorage.getItem("push_sound_enabled");

            this.notificationEnabled =
                notifState === null ? true : notifState === "true";
            this.soundEnabled =
                soundState === null ? true : soundState === "true";
        },

        /**
         * Save toggle states to localStorage
         */
        saveToggleStates: function () {
            localStorage.setItem(
                "push_notification_enabled",
                this.notificationEnabled,
            );
            localStorage.setItem("push_sound_enabled", this.soundEnabled);
        },

        /**
         * Toggle notification on/off
         */
        toggleNotification: function () {
            var self = this;

            // Managers/employees are not allowed to turn OFF notifications
            if (!this.isAdmin && this.notificationEnabled) {
                if (typeof toastr !== "undefined") {
                    toastr.options = { positionClass: "toast-bottom-right" };
                    toastr.info(
                        "Only admins can disable notifications.",
                        "Action not allowed",
                    );
                }
                return;
            }

            // If user is trying to enable but browser permission is denied
            if (
                !this.notificationEnabled &&
                "Notification" in window &&
                Notification.permission === "denied"
            ) {
                if (typeof toastr !== "undefined") {
                    toastr.options = {
                        positionClass: "toast-bottom-right",
                        timeOut: 8000,
                        closeButton: true,
                    };
                    toastr.error(
                        "Notification permission was blocked by your browser. " +
                            "Click the lock/site-settings icon in the address bar, " +
                            'set Notifications to "Allow", then reload the page.',
                        "Permission Blocked",
                    );
                }
                return;
            }

            // If enabling and permission is 'default' (not yet asked), request it first
            if (
                !this.notificationEnabled &&
                "Notification" in window &&
                Notification.permission === "default"
            ) {
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        self.notificationEnabled = true;
                        self.saveToggleStates();
                        self.updateToggleUI();
                        if (self.swRegistration) {
                            self.subscribe(self.swRegistration);
                        }
                        if (typeof toastr !== "undefined") {
                            toastr.options = {
                                positionClass: "toast-bottom-right",
                            };
                            toastr.success("Notifications enabled");
                        }
                    } else if (permission === "denied") {
                        if (typeof toastr !== "undefined") {
                            toastr.options = {
                                positionClass: "toast-bottom-right",
                                timeOut: 8000,
                                closeButton: true,
                            };
                            toastr.error(
                                "You blocked notification permission. " +
                                    "Click the lock icon in the address bar to allow notifications.",
                                "Permission Denied",
                            );
                        }
                    }
                });
                return;
            }

            this.notificationEnabled = !this.notificationEnabled;
            this.saveToggleStates();
            this.updateToggleUI();

            if (this.notificationEnabled) {
                // Re-subscribe when enabling
                if (this.swRegistration) {
                    this.requestPermissionAndSubscribe(this.swRegistration);
                }
                if (typeof toastr !== "undefined") {
                    toastr.options = { positionClass: "toast-bottom-right" };
                    toastr.success("Notifications enabled");
                }
            } else {
                // Unsubscribe when disabling
                this.unsubscribeFromPush();
                if (typeof toastr !== "undefined") {
                    toastr.options = { positionClass: "toast-bottom-right" };
                    toastr.warning("Notifications disabled");
                }
            }
        },

        /**
         * Toggle sound on/off
         */
        toggleSound: function () {
            // Managers/employees are not allowed to turn OFF sound
            if (!this.isAdmin && this.soundEnabled) {
                if (typeof toastr !== "undefined") {
                    toastr.options = { positionClass: "toast-bottom-right" };
                    toastr.info(
                        "Only admins can mute notification sound.",
                        "Action not allowed",
                    );
                }
                return;
            }

            this.soundEnabled = !this.soundEnabled;
            this.saveToggleStates();
            this.updateToggleUI();

            if (typeof toastr !== "undefined") {
                toastr.options = { positionClass: "toast-bottom-right" };
                toastr.info(
                    this.soundEnabled ? "Sound enabled" : "Sound muted",
                );
            }
        },

        /**
         * Update the toggle button UI to reflect current state
         */
        updateToggleUI: function () {
            // Desktop buttons
            this.updateButtonState(
                "btn-toggle-notification",
                "icon-notification",
                this.notificationEnabled,
                "fa-bell",
                "fa-bell-slash",
            );
            this.updateButtonState(
                "btn-toggle-sound",
                "icon-sound",
                this.soundEnabled,
                "fa-volume-up",
                "fa-volume-off",
            );
            // Mobile buttons
            this.updateButtonState(
                "btn-toggle-notification-mobile",
                "icon-notification-mobile",
                this.notificationEnabled,
                "fa-bell",
                "fa-bell-slash",
            );
            this.updateButtonState(
                "btn-toggle-sound-mobile",
                "icon-sound-mobile",
                this.soundEnabled,
                "fa-volume-up",
                "fa-volume-off",
            );

            // Indicate when audio is not yet unlocked/ready
            var soundBtn = document.getElementById("btn-toggle-sound");
            var soundBtnMobile = document.getElementById(
                "btn-toggle-sound-mobile",
            );

            if (!this.audioUnlocked) {
                if (soundBtn) {
                    soundBtn.classList.add("audio-locked");
                }
                if (soundBtnMobile) {
                    soundBtnMobile.classList.add("audio-locked");
                }
            } else {
                if (soundBtn) {
                    soundBtn.classList.remove("audio-locked");
                }
                if (soundBtnMobile) {
                    soundBtnMobile.classList.remove("audio-locked");
                }
            }
        },

        /**
         * Update a single toggle button's appearance
         */
        updateButtonState: function (
            btnId,
            iconId,
            isActive,
            activeIcon,
            inactiveIcon,
        ) {
            var btn = document.getElementById(btnId);
            var icon = document.getElementById(iconId);

            if (!btn || !icon) {
                return;
            }

            if (isActive) {
                btn.classList.add("active");
                btn.classList.remove("muted");
                icon.classList.remove(inactiveIcon);
                icon.classList.add(activeIcon);
            } else {
                btn.classList.remove("active");
                btn.classList.add("muted");
                icon.classList.remove(activeIcon);
                icon.classList.add(inactiveIcon);
            }
        },

        /**
         * Unsubscribe from push notifications
         */
        unsubscribeFromPush: function () {
            var self = this;

            if (!this.swRegistration) {
                return;
            }

            this.swRegistration.pushManager
                .getSubscription()
                .then(function (subscription) {
                    if (!subscription) {
                        return;
                    }

                    var endpoint = subscription.endpoint;

                    subscription.unsubscribe().then(function () {
                        // Also remove from server
                        fetch(self.unsubscribeUrl, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": self.csrfToken,
                                Accept: "application/json",
                            },
                            body: JSON.stringify({ endpoint: endpoint }),
                        })
                            .then(function () {
                                console.log(
                                    "[Push] Unsubscribed successfully.",
                                );
                            })
                            .catch(function (error) {
                                console.error(
                                    "[Push] Error removing subscription from server:",
                                    error,
                                );
                            });
                    });
                });
        },

        /**
         * Register the service worker
         */
        registerServiceWorker: function () {
            var self = this;

            navigator.serviceWorker
                .register("/sw.js")
                .then(function (registration) {
                    console.log(
                        "[Push] Service Worker registered with scope:",
                        registration.scope,
                    );
                    self.swRegistration = registration;

                    // Only subscribe if notifications are enabled
                    if (self.notificationEnabled) {
                        self.requestPermissionAndSubscribe(registration);
                    }
                })
                .catch(function (error) {
                    console.error(
                        "[Push] Service Worker registration failed:",
                        error,
                    );
                });
        },

        /**
         * Request notification permission and subscribe
         */
        requestPermissionAndSubscribe: function (registration) {
            var self = this;

            if (Notification.permission === "granted") {
                self.subscribe(registration);
                return;
            }

            if (Notification.permission === "denied") {
                console.warn(
                    "[Push] Notification permission was denied by the user. Toggle turned off.",
                );
                self.notificationEnabled = false;
                self.saveToggleStates();
                self.updateToggleUI();
                return;
            }

            // Ask for permission (permission === 'default')
            Notification.requestPermission().then(function (permission) {
                if (permission === "granted") {
                    self.subscribe(registration);
                } else {
                    console.warn(
                        "[Push] Notification permission was not granted.",
                    );
                    self.notificationEnabled = false;
                    self.saveToggleStates();
                    self.updateToggleUI();
                }
            });
        },

        /**
         * Subscribe to push notifications
         */
        subscribe: function (registration) {
            var self = this;
            var applicationServerKey = this.urlBase64ToUint8Array(
                this.vapidPublicKey,
            );

            registration.pushManager
                .getSubscription()
                .then(function (existingSubscription) {
                    if (existingSubscription) {
                        // Already subscribed, send to server to ensure it's saved
                        self.sendSubscriptionToServer(existingSubscription);
                        return;
                    }

                    registration.pushManager
                        .subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: applicationServerKey,
                        })
                        .then(function (subscription) {
                            self.sendSubscriptionToServer(subscription);
                        })
                        .catch(function (error) {
                            console.error("[Push] Failed to subscribe:", error);
                        });
                });
        },

        /**
         * Send subscription details to the server
         */
        sendSubscriptionToServer: function (subscription) {
            var key = subscription.getKey("p256dh");
            var auth = subscription.getKey("auth");

            var data = {
                endpoint: subscription.endpoint,
                keys: {
                    p256dh: key ? this.arrayBufferToBase64Url(key) : "",
                    auth: auth ? this.arrayBufferToBase64Url(auth) : "",
                },
                content_encoding: (PushManager.supportedContentEncodings || [
                    "aesgcm",
                ])[0],
            };

            fetch(this.subscribeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify(data),
            })
                .then(function (response) {
                    if (response.ok) {
                        console.log("[Push] Subscription saved to server.");
                    } else {
                        console.error(
                            "[Push] Failed to save subscription:",
                            response.status,
                        );
                    }
                })
                .catch(function (error) {
                    console.error("[Push] Error saving subscription:", error);
                });
        },

        /**
         * Setup AudioContext for sine wave notification sound.
         * Creates context on first user interaction, and keeps it alive
         * by resuming on every subsequent interaction (browsers re-suspend
         * after idle periods or when the tab goes to background).
         */
        setupAudioContext: function () {
            var self = this;
            this.AudioCtx = window.AudioContext || window.webkitAudioContext;

            if (!this.AudioCtx) {
                console.warn("[Push] AudioContext not supported.");
                return;
            }

            var onUserInteraction = function () {
                // First interaction: create the context
                if (!self.audioUnlocked) {
                    self.ensureAudioContext();
                    self.audioUnlocked = true;
                    console.log("[Push] Audio context unlocked.");

                    if (self.pendingSoundOnVisible && self.soundEnabled) {
                        self.pendingSoundOnVisible = false;
                        self.playNotificationSound();
                    }
                } else if (
                    self.audioContext &&
                    self.audioContext.state === "suspended"
                ) {
                    // Subsequent interactions: keep context alive by resuming if suspended
                    self.audioContext.resume();
                }

                // Update UI to remove the locked state once audio is ready
                self.updateToggleUI();
            };

            // Keep listeners permanently — they act as a keepalive
            document.addEventListener("click", onUserInteraction);
            document.addEventListener("keydown", onUserInteraction);
            document.addEventListener("touchstart", onUserInteraction);
            document.addEventListener("scroll", onUserInteraction);
        },

        /**
         * Ensure a usable AudioContext exists. Creates a new one if the
         * current context is missing or has been closed by the browser.
         */
        ensureAudioContext: function () {
            if (this.audioContext && this.audioContext.state !== "closed") {
                return;
            }

            this.audioContext = new this.AudioCtx();

            if (this.audioContext.state === "suspended") {
                this.audioContext.resume();
            }
        },

        /**
         * Play a unique notification tone using sine wave synthesis.
         * Creates a pleasant ascending three-tone chime (C5→E5→G5).
         *
         * Handles all AudioContext lifecycle issues:
         *  - Recreates a closed context
         *  - Awaits resume() on a suspended context before scheduling tones
         *  - Falls back gracefully if resume is blocked by the browser
         */
        playNotificationSound: function () {
            if (!this.audioUnlocked) {
                console.warn("[Push] Audio context not ready. Sound deferred.");
                this.pendingSoundOnVisible = true;
                return;
            }

            // Recreate if the browser closed the context (happens on some mobile browsers)
            this.ensureAudioContext();

            var self = this;
            var ctx = this.audioContext;

            // ctx.resume() returns a promise — we MUST wait for it before
            // scheduling oscillators, otherwise they fire on a suspended
            // context and produce no audible output.
            var ready =
                ctx.state === "suspended" ? ctx.resume() : Promise.resolve();

            ready
                .then(function () {
                    var now = ctx.currentTime;

                    // First tone: ascending note (C5 = 523 Hz)
                    self.playTone(ctx, 523.25, now, 0.15, 0.4);
                    // Second tone: higher note (E5 = 659 Hz)
                    self.playTone(ctx, 659.25, now + 0.15, 0.15, 0.4);
                    // Third tone: even higher (G5 = 783 Hz)
                    self.playTone(ctx, 783.99, now + 0.3, 0.25, 0.35);
                })
                .catch(function (err) {
                    console.warn("[Push] Could not resume AudioContext:", err);
                });
        },

        /**
         * Play a single sine wave tone
         */
        playTone: function (ctx, frequency, startTime, duration, volume) {
            var oscillator = ctx.createOscillator();
            var gainNode = ctx.createGain();

            oscillator.type = "sine";
            oscillator.frequency.setValueAtTime(frequency, startTime);

            // Envelope: quick attack, sustain, smooth decay
            gainNode.gain.setValueAtTime(0, startTime);
            gainNode.gain.linearRampToValueAtTime(volume, startTime + 0.02);
            gainNode.gain.setValueAtTime(volume, startTime + duration * 0.6);
            gainNode.gain.exponentialRampToValueAtTime(
                0.001,
                startTime + duration,
            );

            oscillator.connect(gainNode);
            gainNode.connect(ctx.destination);

            oscillator.start(startTime);
            oscillator.stop(startTime + duration);
        },

        /**
         * Listen for messages from the service worker
         */
        listenForMessages: function () {
            var self = this;

            navigator.serviceWorker.addEventListener(
                "message",
                function (event) {
                    if (
                        event.data &&
                        event.data.type === "NEW_ORDER_NOTIFICATION"
                    ) {
                        if (self.soundEnabled) {
                            if (document.visibilityState === "visible") {
                                self.playNotificationSound();
                            } else {
                                self.pendingSoundOnVisible = true;
                            }
                        }

                        // Show toastr only if notifications are enabled
                        if (
                            self.notificationEnabled &&
                            typeof toastr !== "undefined"
                        ) {
                            toastr.options = {
                                positionClass: "toast-bottom-right",
                            };
                            var orderData = event.data.data || {};
                            var invoiceId = orderData.invoice_id || "";
                            toastr.info(
                                "New order received! #" + invoiceId,
                                "New Order",
                            );
                        }
                    }
                },
            );
        },

        /**
         * Convert a base64 VAPID key to Uint8Array
         */
        urlBase64ToUint8Array: function (base64String) {
            var padding = "=".repeat((4 - (base64String.length % 4)) % 4);
            var base64 = (base64String + padding)
                .replace(/-/g, "+")
                .replace(/_/g, "/");
            var rawData = window.atob(base64);
            var outputArray = new Uint8Array(rawData.length);

            for (var i = 0; i < rawData.length; i++) {
                outputArray[i] = rawData.charCodeAt(i);
            }

            return outputArray;
        },

        /**
         * Convert an ArrayBuffer to Base64URL-safe string (RFC 4648 §5)
         */
        arrayBufferToBase64Url: function (buffer) {
            var bytes = new Uint8Array(buffer);
            var binary = "";

            for (var i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }

            return btoa(binary)
                .replace(/\+/g, "-")
                .replace(/\//g, "_")
                .replace(/=+$/, "");
        },
    };

    // Expose globally
    window.PushNotificationManager = PushNotificationManager;
})();
