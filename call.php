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
    <div class="logo">
      <img src="assets/images/favicon.ico" alt="SignConnect">
    </div>

    <img class="profile-picture" src="assets\images\default-pfp.png" alt="user"> <!-- change to real photo if there is one -->
  </aside>

  <!-- Main Content -->
  <main class="main">

    <header class="header">
      <div class="meeting-info">
        <!-- <h2>Онлайн среща</h2> -->
        <span class="meeting-details">Начало: 16:32 · Продължителност: 12 мин</span>
      </div>
    </header>

    <!-- Video Grid -->
    <section class="video-area">
      <div class="video-grid">
          <div class="video-card video-card-speaking"> <!-- speaking class ON SPEAKING PERSON JS -->
            <img src="assets/images/default-vid.png" alt="participant">
            <div class="username-bubble"><i class="fa-solid fa-user" style="margin-right: 2px"></i>  Иван Петров</div>
            <div class="subtitle show">Това са примерни субтитри.</div> <!-- add class show in js when subtitles are on -->
          </div>

          <div class="video-card">
            <img src="assets/images/default-vid.png" alt="participant">
            <div class="username-bubble"><i class="fa-solid fa-user" style="margin-right: 2px"></i>  Мария Стоянова</div>
          </div>
      </div>
    </section>


    <!-- Bottom Controls -->
    <footer class="controls">
      <div class="controls-container">
        <button title="Микрофон"><i class="fa-solid fa-microphone-lines"></i></button>
        <button title="Камера"><i class="fa-solid fa-video"></i></button>
        <button class="end-btn leave-btn" title="Напускане"><i class="fa-solid fa-right-from-bracket"></i></button>
        <button class="end-btn leave-btn" title="Прекратяване"><i class="fa-solid fa-phone-slash"></i></button> <!-- for hosts -->
        <button title="Жестомимичен превод"><i class="fa-solid fa-hands-asl-interpreting"></i></button>
        <button title="Субтитри"><i class="fa-solid fa-closed-captioning"></i></button>
      </div>
    </footer>
  </main>

  <!-- Right Sidebar -->
  <aside class="right-sidebar">

    <!-- Participant List -->
    <section class="participants card">
      <div class="card-header">
        <h3>Участници <span>(2)</span></h3>
        <i class="fa-solid fa-user-plus participants-options"></i>
      </div>
      <ul>
        <li>
          <img class="avatar" src="assets\images\default-pfp.png" alt="user"> Мария Стоянова (Host)
        </li>

        <li>
          <img class="avatar" src="assets\images\default-pfp.png" alt="user"> Иван Петров
        </li>

        <!-- <li>
          <img class="avatar" src="assets\images\default-pfp.png" alt="user"> Даная Иванова
        </li>
        <li>
          <img class="avatar" src="assets\images\default-pfp.png" alt="user"> Мариан Станев
        </li> -->
      </ul>
    </section>

    <!-- Messages -->
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

      <div class="msg">
        <p class="name">Мария Стоянова</p>
        <p>Да :)</p>
      </div>

      <div class="input-wrapper">
        <input type="text" class="msg-input" placeholder="Съобщение...">
        <button class="send-btn"><i class="fa-solid fa-paper-plane"></i></button>
      </div>

    </section>

  </aside>

</div>

</body>
</html>


<script>const icon = document.querySelector(".chat-options");
const menu = document.getElementById("chatMenu");

icon.addEventListener("click", () => {
  menu.style.display = menu.style.display === "block" ? "none" : "block";
});

// Optional: close menu when clicking outside
document.addEventListener("click", (e) => {
  if (!e.target.closest(".menu-wrapper")) {
    menu.style.display = "none";
  }
});

document.getElementById("clearChat").addEventListener("click", () => {
  console.log("Clear Chat clicked");
  // Add your clear chat function here
});

document.getElementById("stopChat").addEventListener("click", () => {
  console.log("Stop Chat clicked");
  // Add your stop chat function here
});
</script>