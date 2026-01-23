<?php
  include_once("includes/connection.php");

  // Check if user is logged in
  if (!isset($_SESSION['user_id'])) {
      header("Location: index.php");
      exit;
  }

  $userID = $_SESSION['user_id'];

  // Fetch user info
  $stmt = $connection->prepare("
      SELECT username, profile_photo 
      FROM users 
      WHERE id = ?
  ");
  $stmt->execute([$userID]);
  $localUser = $stmt->fetch(PDO::FETCH_ASSOC);

  // Fallback to default if no photo
  $localPhoto = $localUser['profile_photo'] ?: 'default-pfp.png';
  $local_username = $localUser['username'];

  // Get meeting code from URL
  $meeting_code = $_GET['code'] ?? null;


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

  // Fetch participants
  $stmt = $connection->prepare("
      SELECT u.username, p.role, u.profile_photo
      FROM participants p
      JOIN users u ON p.user_id = u.id
      WHERE p.meeting_id = ?
      ORDER BY p.joined_at ASC
  ");
  $stmt->execute([$meeting['id']]);
  $participants = $stmt->fetchAll();

  // Local user name
  // $local_username = $_SESSION['username'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SignConnect | Обаждане</title>
 
  <?php include("includes/links.php"); ?>

  <!-- Styles -->
  <link rel="stylesheet" href="assets/css/call-style.css?v=2">
</head>
<body>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo text-logo">
        <span class="sign">Sign</span><span class="connect">Connect</span>
      </div>

      <img class="profile-picture" src="assets/images/<?= htmlspecialchars($localPhoto) ?>" alt="<?= htmlspecialchars($local_username) ?>">
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

        <?php foreach ($participants as $p){ ?>

          <!-- LOCAL VIDEO -->
          <?php 
            if($p['role'] == "host"){ 
          ?>
              <div class="video-card" id="localVideoCard">
                <video id="localVideo" autoplay muted playsinline></video>
                <div class="username-bubble">
                  <i class="fa-solid fa-user"></i> Вие
                </div>
              </div>

          <?php 
            } else { 
          ?>

          <!-- REMOTE VIDEO -->
          <?php 
            if ($p['username'] !== $local_username){ 
          ?>
              <div class="video-card">
                <video autoplay playsinline></video>
                <div class="username-bubble">
                  <i class="fa-solid fa-user"></i> <?= htmlspecialchars($p['username']) ?>
                </div>

                <div class="subtitle speech-subtitle active">Това са примерни субтитри за говор.</div>
                <div class="subtitle sign-subtitle">Това са примерни субтитри за жестомимичен език.</div>
              </div>
          <?php 
            } 
          ?>
        <?php 
          } 
        ?>

          <?php } ?>

        </div>
      </section>


      <!-- Bottom Controls -->
      <footer class="controls">
        <div class="controls-container">
          <button id="muteBtn"><i class="fa-solid fa-microphone-lines"></i></button>
          <button id="camBtn"><i class="fa-solid fa-video"></i></button>
          <button class="end-btn leave-btn" onclick="leaveMeeting()"><i class="fa-solid fa-right-from-bracket"></i></button>

          <!-- end call accessible only for host -->
          <?php
            foreach($participants as $p){
                if($p['username'] == $local_username && $p['role'] == 'host'){
          ?>
                  <a class="end-btn leave-btn" href="assets/action-files/end-meeting.php?code=<?= htmlspecialchars($meeting['code']) ?>"><i class="fa-solid fa-phone-slash"></i></a>
          <?php
                  break; 
                }
            }
          ?>
          <button id="signSubBtn"><i class="fa-solid fa-hands-asl-interpreting"></i></button>
          <button id="speechSubBtn"><i class="fa-solid fa-closed-captioning"></i></button>
        </div>
      </footer>
    </main>


    <!-- Right Sidebar -->
    <aside class="right-sidebar">

      <section class="participants card">
        <div class="card-header">
          <h3>Участници <span>(<?= count($participants) ?>)</span></h3>
          <i class="fa-solid fa-user-plus participants-options copy-code-btn" data-code="<?= htmlspecialchars($meeting['code']) ?>"></i>
        </div>
        <ul>
          <?php foreach ($participants as $index => $participant){ ?>
              <li>
                <img class="avatar" src="assets/images/<?= htmlspecialchars($participant['profile_photo']) ?>" alt="<?= htmlspecialchars($participant['username']) ?>">
                <?= htmlspecialchars($participant['username']) ?> <?php if($participant['role'] == "host"){ echo("(Host)");} ?>
              </li>
          <?php } ?>
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


  <!-- CHAT MENU -->
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


  <!-- MIC & CAMERA CONTROLS -->
  <script>
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


  <!-- END MEETING REDIRECT (CHECK STATUS) -->
  <script>
    setInterval(() => {
        fetch(`assets/action-files/check-meeting-status.php?code=<?= $meeting['code'] ?>`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ended') {
                    window.location.href = 'meetings.php?meeting_ended=1';
                }
            });
    }, 5000); 
  </script>

  <!-- LEAVE MEETING -->
  <script>
    function leaveMeeting() {
        // Redirect to meetings.php with a GET parameter for flash message
        window.location.href = 'meetings.php?left_meeting=1';
    }
  </script>

  <!-- Copy Code (add participant) -->
  <script>
  document.querySelector(".copy-code-btn").addEventListener("click", function () {
      const code = this.dataset.code;

      navigator.clipboard.writeText(code).then(() => {
          // Change tooltip text temporarily
          const el = this;
          el.setAttribute("data-original", el.getAttribute("data-code"));

          el.style.setProperty('--tooltip-text', '"Кодът е копиран!"');

          // Quick visual feedback
          el.style.transform = "scale(1.2)";
          setTimeout(() => el.style.transform = "scale(1)", 200);

          // Temporary tooltip change
          el.classList.add("copied");
          setTimeout(() => el.classList.remove("copied"), 1500);
      });
  });
  </script>


  <!-- SUBTITLES -->
  <script>
    const signBtn = document.getElementById("signSubBtn");
    const speechBtn = document.getElementById("speechSubBtn");

    let signOn = false;
    let speechOn = false;

    // SIGN LANGUAGE → TEXT
    signBtn.addEventListener("click", () => {
        signOn = !signOn;

        document.querySelectorAll(".sign-subtitle").forEach(el => {
            el.style.display = signOn ? "block" : "none";
        });

        signBtn.classList.toggle("off", !signOn);
    });

    // SPEECH → TEXT
    speechBtn.addEventListener("click", () => {
        speechOn = !speechOn;

        document.querySelectorAll(".speech-subtitle").forEach(el => {
            el.style.display = speechOn ? "block" : "none";
        });

        speechBtn.classList.toggle("off", !speechOn);
    });
  </script>


  <script src="assets/js/webrtc.js"></script>

</body>
</html>
