document.addEventListener("DOMContentLoaded", function () {

  // ===============================
  // 🔥 CREATE HTML DYNAMICALLY (same UI as original)
  // ===============================
  const html = `
<style>
#floatingPush {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 320px;
  max-width: 90%;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  color: #fff;
  border-radius: 16px;
  padding: 14px 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  display: none;
  z-index: 9999;
  font-family: 'Segoe UI', sans-serif;
  animation: slideIn 0.4s ease;
}

#floatingPush .title {
  font-size: 15px;
  font-weight: 600;
}

#floatingPush .body {
  font-size: 13px;
  opacity: 0.9;
  margin-top: 4px;
}

#floatingPush .close {
  background: rgba(255,255,255,0.15);
  border: none;
  color: #fff;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  cursor: pointer;
}

#floatingPush .close:hover {
  background: rgba(255,255,255,0.3);
}

#notifyPrompt {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 340px;
  max-width: 90%;
  background: #fff;
  border-radius: 18px;
  padding: 18px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  display: none;
  z-index: 9999;
  font-family: 'Segoe UI', sans-serif;
  animation: fadeUp 0.4s ease;
}

#notifyPrompt h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1e3a8a;
}

#notifyPrompt p {
  font-size: 13px;
  color: #555;
  margin: 8px 0 14px;
}

#notifyPrompt .btns {
  display: flex;
  gap: 10px;
}

#notifyPrompt button {
  flex: 1;
  padding: 8px;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
}

#enableNotifyBtn {
  background: #2563eb;
  color: #fff;
  border: none;
}

#enableNotifyBtn:hover {
  background: #1d4ed8;
}

#closePromptBtn {
  background: #f3f4f6;
  border: none;
}

@keyframes slideIn {
  from { transform: translateY(40px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes fadeUp {
  from { transform: translate(-50%, 40px); opacity: 0; }
  to { transform: translate(-50%, 0); opacity: 1; }
}
</style>

<!-- FLOATING NOTIFICATION -->
<div id="floatingPush">
  <div style="display:flex; justify-content:space-between; align-items:start;">
    <img id="floatIcon" src="" alt="icon" style="width:36px; height:36px; border-radius:8px; margin-right:10px; display:none;" />
    <div id="floatingContent" style="flex:1; cursor:pointer;">
      <div class="title" id="floatTitle"></div>
      <div class="body" id="floatBody"></div>
    </div>
    <button class="close" id="closeFloating">×</button>
  </div>
</div>

<!-- PERMISSION PROMPT -->
<div id="notifyPrompt">
  <h4>🔔 Get Job Alerts</h4>
  <p>Stay updated with latest govt jobs, results & admit cards instantly.</p>
  <div class="btns">
    <button id="enableNotifyBtn">Enable</button>
    <button id="closePromptBtn">Not Now</button>
  </div>
</div>
`;
  document.body.insertAdjacentHTML("beforeend", html);

  // ===============================
  // DOM ELEMENTS
  // ===============================
  const promptDiv = document.getElementById('notifyPrompt');
  const enableBtn = document.getElementById('enableNotifyBtn');
  const closePrompt = document.getElementById('closePromptBtn');

  // ===============================
  // SHOW PROMPT (only once, if permission not yet decided)
  // ===============================
  if (Notification.permission !== "granted" && Notification.permission !== "denied") {
    if (!sessionStorage.getItem('notifyPromptShown')) {
      setTimeout(() => {
        if (promptDiv) promptDiv.style.display = 'block';
        sessionStorage.setItem('notifyPromptShown', 'true');
      }, 3000);
    }
  }

  // ===============================
  // ENABLE NOTIFICATIONS
  // ===============================
  if (enableBtn) {
    enableBtn.onclick = async () => {
      if (promptDiv) promptDiv.style.display = 'none';

      if (!('Notification' in window)) {
        alert("Browser not supported");
        return;
      }

      let permission = Notification.permission;
      if (permission !== "granted") {
        permission = await Notification.requestPermission();
      }

      if (permission === "granted") {
        startFirebase();
      } else {
        alert("Please allow notifications");
      }
    };
  }

  if (closePrompt) {
    closePrompt.onclick = () => {
      if (promptDiv) promptDiv.style.display = 'none';
    };
  }

  // ===============================
  // FIREBASE INITIALIZATION
  // ===============================
  async function startFirebase() {
    try {
      // Ensure Firebase is initialized
      if (!firebase.apps || !firebase.apps.length) {
        firebase.initializeApp({
          apiKey: "AIzaSyCAuZZ8qc47KRyDzveFT_3BshY063X7T40",
          authDomain: "govtjobs-ai-prod.firebaseapp.com",
          projectId: "govtjobs-ai-prod",
          messagingSenderId: "424723239749",
          appId: "1:424723239749:web:a3005d9f3d7173f2a8242c"
        });
      }

      const messaging = firebase.messaging();

      // Register service worker with cache-busting
      const registration = await navigator.serviceWorker.register(
        '/firebase-messaging-sw.js?v=' + Date.now()
      );
      const activeSW = await navigator.serviceWorker.ready;

      // Generate or retrieve device ID
      let deviceId = localStorage.getItem('device_id');
      if (!deviceId) {
        deviceId = 'dev_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('device_id', deviceId);
      }

      // Get FCM token
      const token = await messaging.getToken({
        vapidKey: "BNnUhWyOf4uHiCjWww2Kq-09-PXfuy8-zqSPGIQZ7UBOEdVLijBGFzZARZyD5Obst4fxjNmqF3IVxERFCKc2mQ4",
        serviceWorkerRegistration: activeSW
      });

      if (!token) {
        console.warn("❌ No FCM token received");
        return;
      }

      // Save token only if changed
      const oldToken = localStorage.getItem('fcm_token');
      if (oldToken !== token) {
        localStorage.setItem('fcm_token', token);
        await saveTokenWithRetry(token, deviceId);
      }

      // Foreground message handler
      messaging.onMessage((payload) => {
       const title = payload.data?.title || "Notification";
       const body  = payload.data?.body || "";
       const link  = payload.data?.link || "/";
       const icon  = payload.data?.icon || "";       // ← new
       showFloatingNotification(title, body, link, icon); // ← pass icon
    });

    } catch (err) {
      console.error("🔥 Firebase Error:", err);
    }
  }

  // ===============================
  // SAVE TOKEN WITH RETRY + CSRF
  // ===============================
  async function saveTokenWithRetry(token, deviceId) {
    // Use global CSRF functions (assumed to be defined in <head>)
    const csrfName = getCSRFName();
    const csrfToken = getCSRFToken();
    const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/';

    try {
      const formData = new FormData();
      formData.append("token", token);
      formData.append("device_id", deviceId);
      formData.append(csrfName, csrfToken);

      const response = await fetch(baseUrl + "push/save_token", {
        method: "POST",
        body: formData
      });

      const data = await response.json();

      // Update CSRF if returned
      if (data.csrf && typeof updateCSRFToken === 'function') {
        updateCSRFToken(data.csrf.token, data.csrf.name);
      }

      // Retry once if failed
      if (!data.status) {
        console.warn("⚠ First save failed, retrying...");
        const retryForm = new FormData();
        retryForm.append("token", token);
        retryForm.append("device_id", deviceId);
        retryForm.append(getCSRFName(), getCSRFToken());

        await fetch(baseUrl + "push/save_token", {
          method: "POST",
          body: retryForm
        });
      } else {
        console.log("✅ Token saved successfully");
      }
    } catch (err) {
      console.error("🔥 Save token error:", err);
    }
  }

  // ===============================
  // FLOATING NOTIFICATION UI
  // ===============================
  function showFloatingNotification(title, body, link, icon) {
    const floatDiv = document.getElementById('floatingPush');
    const floatTitle = document.getElementById('floatTitle');
    const floatBody = document.getElementById('floatBody');
    const floatIcon = document.getElementById('floatIcon');
    const contentDiv = document.getElementById('floatingContent');
    const closeFloat = document.getElementById('closeFloating');

    if (!floatDiv) return;

    floatTitle.innerText = title;
    floatBody.innerText = body;

    // Show icon if provided
    if (icon && floatIcon) {
        floatIcon.src = icon;
        floatIcon.style.display = 'block';
    } else if (floatIcon) {
        floatIcon.style.display = 'none';
    }

    floatDiv.style.display = 'block';

    if (contentDiv) {
        contentDiv.onclick = () => {
            window.location.href = link;
        };
    }
    if (closeFloat) {
        closeFloat.onclick = () => {
            floatDiv.style.display = 'none';
        };
    }

    // Auto-hide after 3 minutes
    setTimeout(() => {
        if (floatDiv) floatDiv.style.display = 'none';
    }, 3 * 60000);
  }

  // ===============================
  // AUTO-START IF ALREADY GRANTED
  // ===============================
  if (Notification.permission === "granted") {
    startFirebase();
  }
});