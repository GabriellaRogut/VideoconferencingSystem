<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect | Профил</title>

    <?php include("includes/links.php"); ?>

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/account-style.css?v=2">
</head>

<body>

<header class="main-header">
    <div class="logo">SignConnect</div>

    <nav class="account-nav">
        <a href="index.php">Начало</a>
        <a href="meetings.php">Срещи</a>
        <a class="active" href="account.php">Акаунт</a>
    </nav>
</header>

<section class="account-container">

    <!-- LEFT SIDE: Profile -->
    <div class="profile-card acc-card">
        <img src="assets/images/default-pfp.png" class="profile-photo">

        <h2>Иван Петров</h2>
        <p class="email">ivan.petrov@example.com</p>

        <button class="edit-btn">Редактирай профил</button>
        <hr>

        <div class="stats">
            <div class="stat-box">
                <h3>34</h3>
                <p>Проведени срещи</p>
            </div>

            <div class="stat-box">
                <h3>12</h3>
                <p>Часове видео</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Account Settings -->
    <div class="settings-card acc-card" id="view-account">
        <h2 class="section-title">Настройки</h2>

        <form class="settings-form">
            
            <div class="input-row">
                <div class="field">
                    <label>Име:</label>
                    <p class="view-fields">Иван</p>
                </div>

                <div class="field">
                    <label>Фамилия:</label>
                    <p class="view-fields">Петров</p>
                </div>
            </div>

            <div class="field">
                <label>Имейл:</label>
                <p class="view-fields">ivan.petrov@example.com</p>
            </div>

            <div class="field">
                <label>Парола:</label>
                <p class="view-fields">•••••••</p>
            </div>
        </form>

        <hr>
        <div class="danger-container">
            <button class="danger-btn">Изтриване на акаунта</button>
        </div>

    </div>


    <div class="settings-card acc-card" id="edit-account"  style="display:none;">

        <h2 class="section-title">Настройки на Акаунта</h2>

        <form class="settings-form">
            
            <div class="input-row">
                <div class="field">
                    <label>Име:</label>
                    <input type="text" value="Иван">
                </div>

                <div class="field">
                    <label>Фамилия:</label>
                    <input type="text" value="Петров">
                </div>
            </div>

            <div class="field">
                <label>Имейл:</label>
                <input type="email" value="ivan.petrov@example.com">
            </div>

            <div class="field">
                <label>Парола:</label>
                <input type="password" placeholder="•••••••">
            </div>

            <div class="field">
                <label>Нова парола:</label>
                <input type="password" placeholder="•••••••">
            </div>

            <div class="field">
                <label>Потвърди паролата:</label>
                <input type="password" placeholder="•••••••">
            </div>

            <div class="save-btn-div">
                <button class="save-btn" type="submit">Запази промените</button>
            </div>
        </form>

        <hr>

        <button class="danger-btn">Изтриване на акаунта</button>
    </div>

</section>

<footer>
    © 2025 SignConnect. Всички права запазени.
</footer>

</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.querySelector(".edit-btn");
    const saveBtn = document.querySelector(".save-btn");
    const viewDiv = document.getElementById("view-account");
    const editDiv = document.getElementById("edit-account");

    // Open edit mode
    editBtn.addEventListener("click", () => {
        viewDiv.style.display = "none";
        editDiv.style.display = "block";
    });

    // Save and return to view mode
    saveBtn.addEventListener("click", (e) => {
        e.preventDefault(); // stop page reload
        editDiv.style.display = "none";
        viewDiv.style.display = "block";
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.querySelector(".edit-btn");
    const saveBtn = document.querySelector(".save-btn");
    const viewDiv = document.getElementById("view-account");
    const editDiv = document.getElementById("edit-account");

    // Open edit mode
    editBtn.addEventListener("click", () => {
        viewDiv.style.display = "none";
        editDiv.style.display = "block";
    });

    // Save and return to view mode
    saveBtn.addEventListener("click", (e) => {
        e.preventDefault();
        editDiv.style.display = "none";
        viewDiv.style.display = "block";
    });
});
</script>

