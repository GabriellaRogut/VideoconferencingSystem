<?php
include_once("includes/connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Fetch meetings with host info
$sql = "
    SELECT DISTINCT m.id, m.code, m.status, m.start_time, m.duration_minutes, m.end_time,
           u.username AS host_name
    FROM meetings m
    JOIN users u ON m.host_id = u.id
    LEFT JOIN participants p ON m.id = p.meeting_id
    WHERE m.host_id = ? OR p.user_id = ?
    ORDER BY m.start_time DESC
";

$stmt = $connection->prepare($sql);
$stmt->execute([$userID, $userID]);
$meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch participants per meeting (host included, no duplicates)
$meeting_participants = [];
foreach ($meetings as $meeting) {
    $stmt = $connection->prepare("
        SELECT u.username, u.profile_photo, p.role
        FROM participants p
        JOIN users u ON u.id = p.user_id
        WHERE p.meeting_id = ?
        ORDER BY CASE WHEN p.role='host' THEN 0 ELSE 1 END, p.joined_at ASC
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
                    <button class="new-meeting-btn">Стартирай нова среща</button>
                </div>
            </div>
        </div>

        <div class="meetings-list">
            <?php if ($meetings): ?>
                <?php foreach ($meetings as $meeting):
                    $start = strtotime($meeting['start_time']);
                    $end   = $meeting['end_time'] ? strtotime($meeting['end_time']) : null;
                    $status = $end ? round(($end - $start)/60) . ' мин' : 'Ongoing';
                    $participants = $meeting_participants[$meeting['id']];
                ?>
                    <div class="meeting-card">
                        <div class="left">
                            <div class="meeting-info">
                                <span class="title">Код:</span>
                                <span class="code"><?= htmlspecialchars($meeting['code']) ?></span>
                            </div>
                            <div class="participants-list">
                                <?php foreach ($participants as $participant): ?>
                                    <div class="participant">
                                        <img src="assets/images/<?= htmlspecialchars($participant['profile_photo'] ?: 'default-pfp.png') ?>" 
                                            alt="<?= htmlspecialchars($participant['username']) ?>">
                                        <span class="participant-name">
                                            <?= htmlspecialchars($participant['username']) ?>
                                            <?= $participant['role'] === 'host' ? " (Host)" : "" ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="right">
                            <span class="datetime"><?= date('d.m.Y · H:i', $start) ?></span>
                            <span class="duration"><?= $status ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-meetings">
                    <p>Няма проведени видеоконференции за показване.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        © 2025 SignConnect. Всички права запазени.
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
