<?php
    $code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect | Изчакване на участници</title>

    <?php include("includes/links.php"); ?>
    <link rel="stylesheet" href="assets/css/waiting-style.css?v=2">
</head>

<body>
    <div class="waiting-container">
        <div class="waiting-card">

            <div class="waiting-header">
                <h1>Изчакване на участници...</h1>
                <p class="waiting-subtitle">
                    Срещата ще започне, когато друг участник се присъедини
                </p>
            </div>

            <div class="code-box">
                <span class="code-label">Код за среща</span>
                <div class="meeting-code"><?php echo $code; ?></div>
                <button class="copy-btn" id="copyBtn">
                    Копирай кода
                </button>
            </div>

            <!-- VIDEO PREVIEW -->
            <div class="preview-wrapper">
                <video id="localVideo" autoplay muted playsinline></video>
                <span class="preview-label">Вие</span>
            </div>

            <!-- CONTROLS -->
            <div class="controls">
                <button id="micBtn"><i class="fa-solid fa-microphone-lines"></i></button>
                <button class="end-btn leave-btn"><i class="fa-solid fa-phone-slash"></i></button>
                <button id="camBtn"><i class="fa-solid fa-video"></i></button>
            </div>

        </div>
    </div>


    <script>
    // Copy meeting code
    const copyBtn = document.getElementById('copyBtn');
    copyBtn.addEventListener('click', () => {
        const codeText = document.querySelector('.meeting-code').textContent;
        navigator.clipboard.writeText(codeText).then(() => {
            alert('Кодът е копиран в клипборда!');
        });
    });

    // WebRTC local preview
    const localVideo = document.getElementById('localVideo');

    async function initLocalVideo() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            localVideo.srcObject = stream;
            window.localStream = stream; // save globally for later use
        } catch(e) {
            console.error('Грешка при достъп до камерата и микрофона', e);
            alert('Не може да се достъпи камерата или микрофона');
        }
    }

    initLocalVideo();
    </script>



</body>
</html>
