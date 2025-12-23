<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SignConnect | Обаждане</title>

  <?php include("includes/links.php"); ?>

  <link rel="stylesheet" href="assets/css/call-style.css?v=2">
</head>
<body>

<div class="app">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">
      <img src="assets/images/favicon.ico" alt="SignConnect">
    </div>

    <img class="profile-picture" src="assets/images/default-pfp.png" alt="user">
  </aside>

  <!-- Main Content -->
  <main class="main">

    <header class="header">
      <div class="meeting-info">
        <span class="meeting-details">Начало: 16:32 · Продължителност: 12 мин</span>
      </div>
    </header>

    <!-- Video Grid -->
    <section class="video-area">
      <div class="video-grid">

        <!-- LOCAL VIDEO -->
        <div class="video-card" id="localVideoCard">
          <video id="localVideo" autoplay muted playsinline></video>
          <div class="username-bubble">
            <i class="fa-solid fa-user"></i> Вие
          </div>
          <div class="subtitle">Това са примерни субтитри.</div>
        </div>

        <!-- REMOTE VIDEO -->
        <div class="video-card">
          <video id="remoteVideo" autoplay playsinline></video>
          <div class="username-bubble">
            <i class="fa-solid fa-user"></i> Друг участник
          </div>
        </div>

      </div>
    </section>

    <!-- Bottom Controls -->
    <footer class="controls">
      <div class="controls-container">
        <button id="muteBtn"><i class="fa-solid fa-microphone-lines"></i></button>
        <button id="camBtn"><i class="fa-solid fa-video"></i></button>
        <button class="end-btn leave-btn"><i class="fa-solid fa-right-from-bracket"></i></button>
        <button class="end-btn leave-btn"><i class="fa-solid fa-phone-slash"></i></button>
        <button><i class="fa-solid fa-hands-asl-interpreting"></i></button>
        <button><i class="fa-solid fa-closed-captioning"></i></button>
      </div>
    </footer>
  </main>

  <!-- Right Sidebar -->
  <aside class="right-sidebar">

    <section class="participants card">
      <div class="card-header">
        <h3>Участници <span>(2)</span></h3>
        <i class="fa-solid fa-user-plus participants-options"></i>
      </div>
      <ul>
        <li>
          <img class="avatar" src="assets/images/default-pfp.png"> Мария Стоянова (Host)
        </li>
        <li>
          <img class="avatar" src="assets/images/default-pfp.png"> Иван Петров
        </li>
      </ul>
    </section>

    <section class="messages card">
      <div class="card-header">
        <h3>Чат</h3>
        <div class="menu-wrapper">
          <i class="fa-solid fa-ellipsis-vertical chat-options"></i>
          <div class="dropdown-menu" id="chatMenu">
            <div class="menu-item" id="clearChat">Clear Chat</div>
            <div class="menu-item" id="stopChat">Stop Chat</div>
          </div>
        </div>
      </div>

      <div class="msg">
        <p class="name">Мария Стоянова</p>
        <p>Добър ден!</p>
      </div>

      <div class="msg me">
        <p class="name">Вие</p>
        <p>Чувате ли ме?</p>
      </div>

      <div class="input-wrapper">
        <input type="text" class="msg-input" placeholder="Съобщение...">
        <button class="send-btn"><i class="fa-solid fa-paper-plane"></i></button>
      </div>
    </section>

  </aside>
</div>

<!-- CHAT MENU JS -->
<script>
const icon = document.querySelector(".chat-options");
const menu = document.getElementById("chatMenu");

icon.addEventListener("click", () => {
  menu.style.display = menu.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", e => {
  if (!e.target.closest(".menu-wrapper")) menu.style.display = "none";
});
</script>

<script src="assets/js/webrtc.js"></script>



<script>
  // =========================
// MIC & CAMERA CONTROLS
// =========================

const muteBtn = document.getElementById("muteBtn");
const camBtn  = document.getElementById("camBtn");

let micEnabled = true;
let camEnabled = true;

// Toggle microphone
muteBtn.addEventListener("click", () => {
  if (!localStream) return;

  localStream.getAudioTracks().forEach(track => {
    track.enabled = !track.enabled;
    micEnabled = track.enabled;
  });

  muteBtn.innerHTML = micEnabled
    ? '<i class="fa-solid fa-microphone-lines"></i>'
    : '<i class="fa-solid fa-microphone-slash"></i>';

  muteBtn.classList.toggle("off", !micEnabled);
});

// Toggle camera
camBtn.addEventListener("click", () => {
  if (!localStream) return;

  localStream.getVideoTracks().forEach(track => {
    track.enabled = !track.enabled;
    camEnabled = track.enabled;
  });

  camBtn.innerHTML = camEnabled
    ? '<i class="fa-solid fa-video"></i>'
    : '<i class="fa-solid fa-video-slash"></i>';

  camBtn.classList.toggle("off", !camEnabled);
});

</script>

</body>
</html>
