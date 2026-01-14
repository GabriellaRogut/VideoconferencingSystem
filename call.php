<?php
include_once("includes/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Get meeting code from URL
// $meeting_code = $_GET['code'] ?? null;
$meeting_code = "TEST1234";


if (!$meeting_code) {
    die("Няма предоставен код за срещата.");
}

// Fetch meeting info
$stmt = $connection->prepare("
    SELECT m.id, m.code, m.start_time, m.duration_minutes, u.username AS host_name
    FROM meetings m
    JOIN users u ON m.host_id = u.id
    WHERE m.code = ?
");
$stmt->execute([$meeting_code]);
$meeting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meeting) {
    die("Срещата не съществува.");
}

// Fetch participants (without host)
$stmt = $connection->prepare("
    SELECT u.username, p.role
    FROM participants p
    JOIN users u ON p.user_id = u.id
    WHERE p.meeting_id = ? AND p.role != 'host'
    ORDER BY p.joined_at ASC
");
$stmt->execute([$meeting['id']]);
$participants = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Add host at the beginning of the participants list
// array_unshift($participants, $meeting['host_name']);

// Local user name
$local_username = $_SESSION['username'];
?>


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
        <span class="meeting-details">
          Начало: <?= date('H:i', strtotime($meeting['start_time'])) ?> ·
          Продължителност: <?= $meeting['duration_minutes'] ?> мин
        </span>
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
          <div class="subtitle show">Това са примерни субтитри.</div> <!-- .show on active subtitles !!! -->
        </div>

        <!-- REMOTE VIDEO -->
        <?php foreach ($participants as $username){ ?>
          <?php if ($username !== $local_username){ ?>
            <div class="video-card">
              <video autoplay playsinline></video>
              <div class="username-bubble">
                <i class="fa-solid fa-user"></i> <?= htmlspecialchars($username) ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>

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
        <h3>Участници <span>(<?= count($participants) ?>)</span></h3>
        <i class="fa-solid fa-user-plus participants-options"></i>
      </div>
      <ul>
        <?php foreach ($participants as $index => $username): ?>
          <li>
            <img class="avatar" src="assets/images/default-pfp.png">
            <?= htmlspecialchars($username) ?><?= $index === 0 ? " (Host)" : "" ?>
          </li>
        <?php endforeach; ?>
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


<!-- SIDEBAR CONTROL  -->
<script>
  const toggleBtn = document.createElement("div");
  toggleBtn.classList.add("sidebar-toggle");
  toggleBtn.innerHTML = "&#9654;"; // right arrow
  document.body.appendChild(toggleBtn);

  const rightSidebar = document.querySelector(".right-sidebar");

  // hidden on small screens
  if (window.innerWidth <= 1200) {
    rightSidebar.classList.add("hidden");
  }

  toggleBtn.addEventListener("click", () => {
    if (rightSidebar.classList.contains("hidden")) {
      rightSidebar.classList.remove("hidden");
      rightSidebar.classList.add("visible");
      toggleBtn.innerHTML = "&#10005;"; // X
    } else {
      rightSidebar.classList.remove("visible");
      rightSidebar.classList.add("hidden");
      toggleBtn.innerHTML = "&#9654;"; // arrow
    }
  });


  window.addEventListener("resize", () => {
    if (window.innerWidth > 1200) {
      rightSidebar.classList.remove("hidden", "visible");
      toggleBtn.style.display = "none";
    } else {
      toggleBtn.style.display = "block";
      rightSidebar.classList.add("hidden");
      rightSidebar.classList.remove("visible");
      toggleBtn.innerHTML = "&#9654;";
    }
  });
</script>

<script src="assets/js/webrtc.js"></script>

</body>
</html>
