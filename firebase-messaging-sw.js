importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyCAuZZ8qc47KRyDzveFT_3BshY063X7T40",
  authDomain: "govtjobs-ai-prod.firebaseapp.com",
  projectId: "govtjobs-ai-prod",
  messagingSenderId: "424723239749",
  appId: "1:424723239749:web:a3005d9f3d7173f2a8242c"
});

const messaging = firebase.messaging();

// ===============================
// 🔥 BACKGROUND HANDLER (FIXED)
// ===============================
messaging.onBackgroundMessage(async function(payload) {

  const allClients = await clients.matchAll({
    type: "window",
    includeUncontrolled: true
  });

  // 👉 check if any tab is visible
  const isAppOpen = allClients.some(client => client.visibilityState === "visible");

  // ❌ अगर site open है → notification मत दिखाओ
  if (isAppOpen) {
    console.log("🚫 App open → skip background notification");
    return;
  }

  const title = payload.data?.title || "New Job Alert";
  const body  = payload.data?.body || "";
  const icon  = payload.data?.icon || "/assets/favicon.png";
  const url   = payload.data?.link || "/";

  // ✅ show only when app closed
  self.registration.showNotification(title, {
    body: body,
    icon: icon,
    data: { url: url },
    tag: "job-notification",   // prevent duplicate stacking
    renotify: true
  });

});

// ===============================
// 🔗 CLICK HANDLER
// ===============================
self.addEventListener('notificationclick', function(event) {
  event.notification.close();

  const url = event.notification.data?.url || "/";

  event.waitUntil(
    clients.matchAll({ type: "window", includeUncontrolled: true })
      .then(function(clientList) {

        for (let i = 0; i < clientList.length; i++) {
          let client = clientList[i];

          if (client.url.includes(url) && 'focus' in client) {
            return client.focus();
          }
        }

        return clients.openWindow(url);
      })
  );
});