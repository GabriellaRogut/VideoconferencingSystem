<?php include_once("includes/connection.php"); 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// user info
$userID = $_SESSION['user_id'];
$stmt = $connection->prepare("
    SELECT * 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php");
    exit;
}
?>


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
    <div class="menu-toggle">&#9776;</div>

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

        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <p class="email"><?= htmlspecialchars($user['email']) ?></p>


        <button class="edit-btn">Редактирай профил</button>
        <hr>

        <div class="stats">
            <div class="stat-box">
                <h3><?= htmlspecialchars($user['total_meetings']) ?></h3>
                <p>Проведени срещи</p>
            </div>

            <div class="stat-box">
                <h3><?= htmlspecialchars($user['total_video_hours']) ?></h3>
                <p>Часове видео</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Account Settings -->
    <div class="settings-card acc-card" id="view-account">
        <h2 class="section-title">Настройки</h2>

        <form class="settings-form">
            
            <div class="field">
                <label>Име:</label>
                <p class="view-fields"><?= htmlspecialchars($user['username']) ?></p>
            </div>

            <div class="field">
                <label>Имейл:</label>
                <p class="view-fields"><?= htmlspecialchars($user['email']) ?></p>
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

        <form class="settings-form" method="POST" action="assets/action-files/edit-account.php">
            <div class="field">
                <label>Име:</label>
                <input type="text" name="username" value="<?= $user['username'] ?>">
            </div>

            <div class="field">
                <label>Имейл:</label>
                <input type="email" name="email" value="<?= $user['email'] ?>">
            </div>

            <div class="field">
                <label>Парола:</label>
                <input type="password" name="current_password" placeholder="•••••••">
            </div>

            <div class="field">
                <label>Нова парола:</label>
                <input type="password" name="new_password" placeholder="•••••••">
            </div>

            <div class="field">
                <label>Потвърди паролата:</label>
                <input type="password" name="confirm_password" placeholder="•••••••">
            </div>

            <div class="btn-div">
                <button class="save-btn" type="submit" name="update_account">Запази промените</button>
                <button class="cancel-btn" type="button">Назад</button>
            </div>
        </form>
        <?php
            if ( isset( $edit_errors) ) {

                foreach( $edit_errors as $error ) {
                    echo "<div class='error'>". $error . "</div>";
                }
            }
        ?>

        <hr style="margin: 20px;">

        <button class="danger-btn danger-btn-edit">Изтриване на акаунта</button>
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
    const cancelBtn = document.querySelector(".cancel-btn");
    const viewDiv = document.getElementById("view-account");
    const editDiv = document.getElementById("edit-account");

    // Open edit mode
    editBtn.addEventListener("click", () => {
        viewDiv.style.display = "none";
        editDiv.style.display = "block";
    });

    // Cancel and return to view mode without submitting
    cancelBtn.addEventListener("click", () => {
        editDiv.style.display = "none";
        viewDiv.style.display = "block";
    });
});
</script>
