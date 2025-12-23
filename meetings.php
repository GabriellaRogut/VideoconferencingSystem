<?php
// Example data (will fetch from database)
$meetings = [
    [
        'title' => 'Среща с Мария',
        'date' => '2025-12-22',
        'start_time' => '16:32',
        'duration' => '12 мин',
        'participants' => ['Иван Петров', 'Мария Стоянова']
    ],
    [
        'title' => 'Проектна дискусия',
        'date' => '2025-12-21',
        'start_time' => '14:00',
        'duration' => '45 мин',
        'participants' => ['Иван Петров', 'Мариан Станев']
    ],
];
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect | Вашите срещи</title>

    <?php include("includes/links.php"); ?>
    <link rel="stylesheet" href="assets/css/meetings-style.css?v=2">
</head>

<body>
    <header class="main-header">
        <div class="logo">SignConnect</div>
        <nav class="account-nav">
            <a href="index.php">Начало</a>
            <a class="active" href="meetings.php">Срещи</a>
            <a href="account.php">Акаунт</a>
        </nav>
    </header>

    <main class="main" style="height: 100vh;">
        <div class="page-top">
            <div>   
                <h2>Вашите срещи</h2>
            </div> 
            <button class="new-meeting-btn" onclick="startMeeting()">Стартирай нова среща</button>
        </div>

        <div class="meetings-list">
            <?php foreach($meetings as $meeting): ?>
                <div class="meeting-card">
                    <div class="meeting-info">
                        <span class="title"><?php echo $meeting['title']; ?></span>
                        <span class="datetime"><?php echo $meeting['date'] . ' · ' . $meeting['start_time'] . ' · ' . $meeting['duration']; ?></span>
                    </div>
                    <div class="participants-list">
                        <?php foreach($meeting['participants'] as $participant): ?>
                            <div class="participant">
                                <img src="assets/images/default-pfp.png" alt="user">
                                <?php echo $participant; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        © 2025 SignConnect. Всички права запазени.
    </footer>

    <script>
    function startMeeting() {
        const code = Math.random().toString(36).substring(2,8).toUpperCase();
        window.location.href = `meeting_wait.php?code=${code}`;
    }
    </script>

    <script>
        // START MEETING BUTTON
        const startBtn = document.querySelector(".new-meeting-btn");
        startBtn.addEventListener("click", () => {
            // Generate a simple meeting code
            const meetingCode = Math.random().toString(36).substring(2, 8).toUpperCase();

            // Open new page for host
            window.location.href = `waiting.php?code=${meetingCode}&host=1`;
        });
    </script>
</body>
</html>
