<?php
    $code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect | Изчакване</title>

    <?php include("includes/links.php"); ?>
    <link rel="stylesheet" href="assets/css/waiting-style.css?v=2">
</head>

<body>
    <div class="waiting-container">
        <div class="waiting-card">
            <h1>Изчакват се участници</h1>
            <p>Код за присъединяване:</p>
            <div class="meeting-code"><?php echo $code; ?></div>
            <button class="copy-btn" id="copyBtn">Копирай кода</button>

            <video id="localVideo" autoplay muted playsinline></video>
            <p style="color:#555; margin-top:10px;">Вашата камера се вижда тук</p>
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
