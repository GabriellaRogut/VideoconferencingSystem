<?php
    include_once("includes/connection.php");

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    $userID = $_SESSION['user_id'];

    // Fetch all meetings where user takes part
    $sql = "
        SELECT DISTINCT m.id, m.code, m.status, m.start_time, m.end_time, m.duration_minutes
        FROM meetings m
        LEFT JOIN participants p 
        ON m.id = p.meeting_id
        WHERE m.host_id = ? OR p.user_id = ?
        ORDER BY m.start_time DESC
    ";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$userID, $userID]);
    // ** return each row as an associative array, using only column names as keys **
    $meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch participants
    $meeting_participants = [];

    foreach ($meetings as $meeting) {
        $stmt = $connection->prepare("
            SELECT u.username, u.profile_photo, p.role
            FROM participants p
            JOIN users u 
            ON u.id = p.user_id
            WHERE p.meeting_id = ?
        ");
        $stmt->execute([$meeting['id']]);

        $meeting_participants[$meeting['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect | Вашите срещи</title>

    <?php include("includes/links.php"); ?>

    <!-- Styles -->
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

    <main class="main">
        <div class="top-meetings-card">
            <h2>Вашите срещи</h2>

            <div class="meeting-actions">
                <div class="enter-meeting">
                    <input type="text" id="meetingCodeInput" placeholder="Въведете код за присъединяване">
                    <button onclick="joinMeeting()">Влез</button>
                </div>

                <div class="create-meeting">
                    <button class="new-meeting-btn"> 
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
                <?php foreach ($meetings as $meeting){ ?>
                    <div class="meeting-card">

                        <div class="left">
                            <div class="meeting-info">
                                <span class="title">Код:</span>
                                <span class="code"><?= htmlspecialchars($meeting['code']) ?></span>
                            </div>

                            <div class="participants-list">
                                <?php foreach ($meeting_participants[$meeting['id']] as $p){ ?>
                                    <div class="participant">
                                        <img src="assets/images/<?= htmlspecialchars($p['profile_photo'] ?: 'default-pfp.png') ?>">
                                        <span class="participant-name">
                                            <?= htmlspecialchars($p['username']) ?>
                                            <?= $p['role'] === 'host' ? ' (Host)' : '' ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="right">
                            <span class="datetime">
                                <?= date('d.m.Y · H:i', strtotime($meeting['start_time'])) ?>
                            </span>
                            <span class="duration">
                                <?php
                                    $start = new DateTime($meeting['start_time']);
                                    if (!empty($meeting['end_time'])) {
                                        $end = new DateTime($meeting['end_time']);
                                        $interval = $start->diff($end);
                                        $hours = $interval->h;
                                        $minutes = $interval->i;
                                        if ($hours > 0) {
                                            echo "{$hours} ч {$minutes} мин";
                                        } else {
                                            echo "{$minutes} мин";
                                        }
                                    } else {
                                        echo "В процес";
                                    }
                                ?>
                            </span>

                        </div>

                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="no-meetings">
                    <p>Няма проведени видеоконференции за показване.</p>
                </div>
            <?php } ?>
        </div>
    </main>

    <footer>
        © 2025 SignConnect. Всички права запазени.
        <a href="admin-panel/admin.php" class="admin-entry">System</a>
    </footer>


    <script>
        document.querySelector(".new-meeting-btn").addEventListener("click", () => {
            window.location.href = "assets/action-files/create-meeting.php";
        });

        function joinMeeting() {
            const code = document.getElementById("meetingCodeInput").value.trim();
            if (!code) return alert("Въведете код");
            window.location.href = "assets/action-files/join-meeting.php?code=" + code;
        }
    </script>

</body>
</html>
