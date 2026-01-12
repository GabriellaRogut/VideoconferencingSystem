<?php
session_start();
include("includes/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Fetch all meetings where user is host or participant
$sql = "
    SELECT m.id, m.code, m.status, m.start_time, m.duration_minutes, m.host_id, u.username AS host_name
    FROM meetings m
    JOIN users u ON m.host_id = u.id
    LEFT JOIN participants p ON m.id = p.meeting_id
    WHERE m.host_id = ? OR p.user_id = ?
    GROUP BY m.id
    ORDER BY m.start_time DESC
";

$stmt = $connection->prepare($sql);
$stmt->execute([$userID, $userID]);
$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch participants usernames
$meeting_participants = [];

if ($meetings) {
    foreach ($meetings as $meeting) {
        $meeting_id = $meeting['id'];

        $stmt = $connection->prepare("
            SELECT u.username
            FROM participants p
            JOIN users u ON p.user_id = u.id
            WHERE p.meeting_id = ?
            ORDER BY p.joined_at ASC
        ");
        $stmt->execute([$meeting_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_COLUMN); // get only usernames

        $meeting_participants[$meeting_id] = $participants;
    }
}
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
        <div class="top-meetings-card">
            <h2>Вашите срещи</h2>
            <div class="meeting-actions">
                <!-- ENTER MEETING LEFT -->
                <div class="enter-meeting">
                    <input type="text" id="meetingCodeInput" placeholder="Въведете код за присъединяване">
                    <button onclick="joinMeeting()">Влез</button>
                </div>

                <!-- CREATE NEW MEETING RIGHT -->
                <div class="create-meeting">
                    <button class="new-meeting-btn" onclick="startMeeting()">
                        <span>Стартирай нова среща</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>


        <div class="meetings-list">
            <?php if ($meetings){ ?>
                <?php foreach($meetings as $meeting){ ?>
                    <div class="meeting-card">
                        <div class="meeting-info">
                            <span class="title"><?= htmlspecialchars($meeting['code']) ?></span>
                            <span class="datetime"> <?= date('d.m.Y · H:i', strtotime($meeting['start_time'])) ?>
                                · <?= $meeting['duration_minutes'] ?> мин</span>
                        </div>
                        <div class="participants-list">
                            <?php
                                $participants = $meeting_participants[$meeting['id']];
                                foreach ($participants as $participant_name):
                            ?>
                                <div class="participant">
                                    <img src="assets/images/default-pfp.png" alt="user">
                                    <span class="participant-name"><?= htmlspecialchars($participant_name) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else {?>
                <div>
                    <p>Няма проведени видеоконференции за показване.</p>
                </div>

            <?php } ?>
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
