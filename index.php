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


<body>
    <header class="main-header">
        <div class="logo">SignConnect</div>
        <div class="menu-toggle">&#9776;</div>
        
        <nav>
            <a href="#features">Характеристики</a>
            <a href="#demo">Демо</a>
            <a href="#testimonials">Отзиви</a>
            <a href="#join">Присъединете се</a>

            <!-- if logged in ?? -->
            <a href="meetings.php">Срещи</a>
            <a href="account.php">Акаунт</a> 
        </nav>
    </header>

    <section class="hero">
        <h1>Премахваме бариерите в комуникацията</h1>
        <p>SignConnect дава възможност на потребители с увреден слух и чуващи да комуникират безпроблемно чрез превод в реално време от жестомимичен език и глас към текст.</p>
        <button class="open-modal">Започнете</button>
    </section>

    <section class="features" id="features">
        <div class="feature-card">
            <img src="https://img.icons8.com/?size=100&id=16557&format=png&color=2D5BE3" />
            <h3>Жестове → Субтитри</h3>
            <p>Автоматичен превод на жестомимичен език в четим текст в реално време.</p>
        </div>

        <div class="feature-card">
            <img src="https://img.icons8.com/?size=100&id=10482&format=png&color=2D5BE3" />
            <h3>Глас → Субтитри</h3>
            <p>Превръща говор в текст за по-ясни и достъпни разговори.</p>
        </div>

        <div class="feature-card">
            <img src="https://img.icons8.com/ios-filled/50/2D5BE3/video-call.png"/>
            <h3>Кристално Ясно Видео</h3>
            <p>Оптимизирано качество на видеото за точно улавяне на всеки жест и движение.</p>
        </div>

        <div class="feature-card">
            <img src="https://img.icons8.com/ios-filled/50/2D5BE3/lock.png"/>
            <h3>Сигурност & Поверителност</h3>
            <p>Криптиране от край до край гарантира, че разговорите остават конфиденциални.</p>
        </div>
    </section>

    <section class="demo" id="demo">
        <video controls autoplay muted loop>
            <source src="https://filesamples.com/samples/video/mp4/sample_1280x720.mp4" type="video/mp4">
        </video>
    </section>

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

    <section class="join-section" id="join">
        <h2>Готови ли сте да издигнете видеоконференциите си на следващо ниво?</h2>
        <button class="open-modal">Присъединете се</button>
    </section>

    <footer>
        © 2025 SignConnect. Всички права запазени.
    </footer>

    <!-- Modal -->
    <div class="modal-overlay" id="modal">
        <div class="modal">
            <span class="close" id="closeModal">&times;</span>
            <div class="modal-tabs">
                <button id="signupTab" class="active-tab">Регистрация</button>
                <button id="loginTab">Вход</button>
            </div>

            <!-- Sign Up Form -->
            <form class="modal-form active-form" method="POST" action="assets/action-files/signup.php">
                <input type="text" name="username" placeholder="Потребителско име" required>
                <input type="email" name="email" placeholder="Имейл" required>
                <input type="password" name="password" placeholder="Парола" required>
                <button type="submit" name="signup">Регистрация</button>

                <p class="toggle-text">Вече имате акаунт? 
                    <span class="toggle-link" id="switchToLogin">Вход</span>
                </p>

                <?php
                    if ( isset( $errors_signup) ) {

                        foreach( $errors_signup as $error ) {
                            echo "<div class='error'>". $error . "</div>";
                        }
                    }
                ?>
            </form>

            <!-- Login Form -->
            <form class="modal-form" method="POST" action="assets/action-files/login.php">
                <input type="email" name="email" placeholder="Имейл" required>
                <input type="password" name="password" placeholder="Парола" required>
                <button type="submit" name="login">Вход</button>
                
                <p class="toggle-text">Все още нямате акаунт? 
                    <span class="toggle-link" id="switchToSignup">Регистрация</span>
                </p>

                <?php
                    if ( isset( $errors_login ) ) {

                        foreach( $errors_login as $error ) {
                            echo "<div class='error'>". $error . "</div>";
                        }
                    }
                ?>
            </form>
        </div>
    </div>

    <script>
        // Feature fade-in on scroll
        const cards = document.querySelectorAll('.feature-card');
        const observer = new IntersectionObserver(entries=>{
            entries.forEach(entry=>{
                if(entry.isIntersecting){entry.target.classList.add('visible');}
            });
        },{threshold:0.2});
        cards.forEach(card=>observer.observe(card));

        // Modal open/close
        const modal = document.getElementById('modal');
        document.querySelectorAll('.open-modal').forEach(btn=>{
            btn.addEventListener('click',()=> modal.classList.add('active'));
        });
        document.getElementById('closeModal').addEventListener('click',()=> modal.classList.remove('active'));
        window.addEventListener('click', e=> {if(e.target===modal) modal.classList.remove('active');});

        // Tabs & toggle logic
        const signupTab = document.getElementById('signupTab');
        const loginTab = document.getElementById('loginTab');
        const signupForm = document.getElementById('signupForm');
        const loginForm = document.getElementById('loginForm');
        const switchToLogin = document.getElementById('switchToLogin');
        const switchToSignup = document.getElementById('switchToSignup');

        signupTab.addEventListener('click',()=>{
            signupTab.classList.add('active-tab'); loginTab.classList.remove('active-tab');
            signupForm.classList.add('active-form'); loginForm.classList.remove('active-form');
        });
        loginTab.addEventListener('click',()=>{
            loginTab.classList.add('active-tab'); signupTab.classList.remove('active-tab');
            loginForm.classList.add('active-form'); signupForm.classList.remove('active-form');
        });
        switchToLogin.addEventListener('click',()=> loginTab.click());
        switchToSignup.addEventListener('click',()=> signupTab.click());
    </script>



</body>
</html>


