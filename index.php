<?php
include_once("includes/connection.php");
?>

<?php
// Log out message
    if (isset($_SESSION['logout_success'])){ ?>
        <div id="logoutMessage" class="logout-message">
            <?= htmlspecialchars($_SESSION['logout_success']) ?>
        </div>
        <?php unset($_SESSION['logout_success']); ?>
    <?php } 
?>

<?php
// Account deleted message
    if (isset($_SESSION['account_deleted'])){ ?>
        <div id="accountDeletedMessage" class="logout-message">
            <?= htmlspecialchars($_SESSION['account_deleted']) ?>
        </div>
        <?php unset($_SESSION['account_deleted']); ?>
    <?php } 
?>



<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect — Видео Конференции за Всеки</title>

    <?php include("includes/links.php"); ?>

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/landing-style.css">
</head>



<script>
    // log out / delete acct mssgs
    document.addEventListener("DOMContentLoaded", () => {
        const messages = ["logoutMessage", "accountDeletedMessage"];

        messages.forEach(id => {
            const msg = document.getElementById(id);
            if (msg) {
                setTimeout(() => {
                    msg.style.opacity = '0';
                    msg.style.transform = 'translateY(-20px)';
                    setTimeout(() => msg.remove(), 1000);
                }, 1200);
            }
        });
    });
</script>


</script>

<body>
    <header class="main-header">
        <div class="logo">SignConnect</div>
        <div class="menu-toggle">&#9776;</div>
        
        <nav>
            <a href="#features">Характеристики</a>
            <a href="#demo">Демо</a>
            <a href="#testimonials">Отзиви</a>
            <a href="#join">Присъединете се</a>

            <!-- if logged in -->
            <?php
                if (isset($_SESSION['user_id'])) {
            ?>
                    <a href="meetings.php">Срещи</a>
                    <a href="account.php">Акаунт</a> 
            <?php 
                }
            ?>
        </nav>
    </header>

    <section class="hero">
        <h1>Премахваме бариерите в комуникацията</h1>
        <p>SignConnect дава възможност на потребители с увреден слух и чуващи да комуникират безпроблемно чрез превод в реално време от жестомимичен език и глас към текст.</p>
        <button class="open-modal">Започнете</button>
    </section>

    <section class="features" id="features">
        <div class="feature-card">
            <img class="theme-icon" data-icon="16557" data-type="png" />
            <h3>Жестове → Субтитри</h3>
            <p>Автоматичен превод на жестомимичен език в четим текст в реално време.</p>
        </div>

        <div class="feature-card">
            <img class="theme-icon" data-icon="10482" data-type="png" />
            <h3>Глас → Субтитри</h3>
            <p>Превръща говор в текст за по-ясни и достъпни разговори.</p>
        </div>

        <div class="feature-card">
            <img class="theme-icon" data-icon="video-call" data-type="ios-filled" />
            <h3>Кристално Ясно Видео</h3>
            <p>Оптимизирано качество на видеото за точно улавяне на всеки жест.</p>
        </div>

        <div class="feature-card">
            <img class="theme-icon" data-icon="lock" data-type="ios-filled" />
            <h3>Сигурност & Поверителност</h3>
            <p>Криптиране от край до край гарантира, че разговорите остават конфиденциални.</p>
        </div>
 
        <script>
            const isDark = localStorage.getItem("theme") === "dark";
            const color = isDark ? "8ea0d2" : "2D5BE3";

            document.querySelectorAll(".theme-icon").forEach(icon => {
                const type = icon.dataset.type;
                const name = icon.dataset.icon;

                if (type === "png") {
                    icon.src = `https://img.icons8.com/?size=100&id=${name}&format=png&color=${color}`;
                } else {
                    icon.src = `https://img.icons8.com/ios-filled/50/${color}/${name}.png`;
                }
            });
        </script>

    </section>

    <!-- DEMO SECTION -->
    <section class="demo" id="demo">
        <video controls autoplay muted loop>
            <source src="https://filesamples.com/samples/video/mp4/sample_1280x720.mp4" type="video/mp4">
        </video>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section class="testimonials" id="testimonials">
        <h2>Какво казват потребителите ни</h2>
        <div class="testimonial-card">
            <p>"SignConnect напълно промени начина, по който провеждам срещите си. Безпроблемно и точно!"</p>
            <h4>- Симеон В.</h4>
        </div>

        <div class="testimonial-card">
            <p>"Това е стъпка към свят без езикови бариери, където всички могат да участват в комуникацията свободно."</p>
            <h4>- Валентина К.</h4>
        </div>
    </section>

    <!-- JOIN SECTION -->
    <section class="join-section" id="join">
        <h2>Готови ли сте да издигнете видеоконференциите си на следващо ниво?</h2>
        <button class="open-modal">Присъединете се</button>
    </section>

    <footer>
        © 2025 SignConnect. Всички права запазени.
        <a href="admin-panel/admin.php" class="admin-entry">System</a>
    </footer>


    <!-- MODALS -->
    <div class="modal-overlay<?php if ( isset( $_SESSION['errors_login']  ) || isset( $_SESSION['errors_signup'] ) ) echo " active" ?>" id="modal">
        <div class="modal">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-tabs">
                <button id="signupTab" class="<?php if( isset( $_SESSION['errors_signup'] ) ) echo " active-tab"  ?>">Регистрация</button>
                <button id="loginTab" class="<?php if( isset( $_SESSION['errors_login'] ) ) echo " active-tab"  ?>">Вход</button>
            </div>

            <!-- SIGN UP FORM -->
            <form class="modal-form <?php if( isset( $_SESSION['errors_signup'] ) ) echo " active-form"  ?>""  method="POST" id="signupForm" action="assets/action-files/signup.php">
                <input type="text" name="username" placeholder="Потребителско име" required>
                <input type="email" name="email" placeholder="Имейл" required>
                <input type="password" name="password" placeholder="Парола" required>
                <button type="submit" name="signup">Регистрация</button>

                <p class="toggle-text">Вече имате акаунт? 
                    <span class="toggle-link" id="switchToLogin">Вход</span>
                </p>

                <?php
                    if ( isset( $_SESSION['errors_signup'] ) ) {

                        foreach( $_SESSION['errors_signup'] as $error ) {
                            echo "<div class='error'>". $error . "</div>";

                            unset( $_SESSION['errors_signup'] );
                        }
                    }
                ?>
            </form>


            <!-- LOG IN FORM -->
            <form class="modal-form<?php if( isset( $_SESSION['errors_login'] ) ) echo " active-form"  ?>" method="POST" id="loginForm" action="assets/action-files/login.php">
                <input type="email" name="email" placeholder="Имейл" required>
                <input type="password" name="password" placeholder="Парола" required>
                <button type="submit" name="login" value="login">Вход</button>
                
                <p class="toggle-text">Все още нямате акаунт? 
                    <span class="toggle-link" id="switchToSignup">Регистрация</span>
                </p>

                <?php
                    if (isset( $_SESSION['errors_login'] ) ) {

                        foreach( $_SESSION['errors_login'] as $error ) {
                            echo "<div class='error'>". $error . "</div>";

                            unset( $_SESSION['errors_login'] );
                        }
                    }
                ?>
            </form>
        </div>
    </div>

    <!-- MODALS -->
    <script>
        // Tabs & toggle logic
        const signupTab = document.getElementById('signupTab');
        const loginTab = document.getElementById('loginTab');
        const signupForm = document.getElementById('signupForm');
        const loginForm = document.getElementById('loginForm');
        const switchToLogin = document.getElementById('switchToLogin');
        const switchToSignup = document.getElementById('switchToSignup');

        // open modal
        const modal = document.getElementById('modal');
        document.querySelectorAll('.open-modal').forEach(btn=>{
            btn.addEventListener('click',()=> { 
                modal.classList.add('active') ;
                signupTab.classList.add('active-tab'); loginTab.classList.remove('active-tab');
                signupForm.classList.add('active-form'); loginForm.classList.remove('active-form');          
            } );
 
        });

        // close modal
        document.getElementById('closeModal')
            .addEventListener('click', () => modal.classList.remove('active'));

 
        // Switch by tabs
        signupTab.addEventListener('click',()=>{
            signupTab.classList.add('active-tab'); loginTab.classList.remove('active-tab');
            signupForm.classList.add('active-form'); loginForm.classList.remove('active-form');
        });
        loginTab.addEventListener('click',()=>{
            loginTab.classList.add('active-tab'); signupTab.classList.remove('active-tab');
            loginForm.classList.add('active-form'); signupForm.classList.remove('active-form');
        });

        // Switch login <-> signup
        switchToLogin.addEventListener('click',()=> loginTab.click());
        switchToSignup.addEventListener('click',()=> signupTab.click());
    </script>


</body>
</html>

