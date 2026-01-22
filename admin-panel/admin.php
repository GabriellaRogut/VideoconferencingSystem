<?php include_once("../includes/connection.php") ?>

<?php
    // Fetch users
    $stmt = $connection->prepare("
        SELECT id, username, email, created_at, role
        FROM users
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
?>


<!-- WELCOME ADMIN MESSAGE HERE -->

<!DOCTYPE html>
<html lang="bg" theme="dark-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect — Администраторски панел</title>


    <?php include("../includes/links.php") ?>
    <link rel="stylesheet" href="../assets/css/general.css"> <!-- !!? -->
    <link rel="stylesheet" href="../assets/css/landing-style.css">

    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
</head>


<body>
    <header class="main-header">
        <div class="logo-wrap">
            <span class="logo">SignConnect</span>
            <span class="admin-badge">ADMIN</span>
        </div>

        <nav>
            <a href="" class="account-btn leave-btn"><i class="fa-solid fa-right-from-bracket"></i></a> <!-- logout -->
            <a href="admin-account.php"><i class="fa-solid fa-user account-btn"></i></a>
        </nav>
    </header>


    
    <section class="features" id="features">
        <div class="feature-card">
            <h3>Населени места с най-висока употреба на SignConnect</h3>

            <!-- data -->
        </div>

        <div class="feature-card">
            <h3>Процент потребители с увреждания на слуха</h3>

            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
        </div>
    </section>




    <section class="admin-users">
        <div class="feature-card">
            <h3><i class="fa-solid fa-users"></i> Потребителски профили</h3>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Потребител</th>
                            <th>Email</th>
                            <th>Роля</th>
                            <th>Регистриран</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users){ ?>
                            <?php foreach ($users as $user){ ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="role <?= $user['role'] ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                    <td class="actions-td">
                                        <button class="action-btn del-btn"><i class="fa-solid fa-trash"></i>  Изтрий</button>
                                        <button class="action-btn upd-btn"><i class="fa-solid fa-pen-to-square"></i> Редактирай</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5" class="empty">Няма потребители</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    

    <footer>
        © 2025 SignConnect • Всички права запазени 
    </footer>


    <!-- EDIT USER MODAL -->
    <div class="admin-modal-overlay" id="editModal">
        <div class="admin-modal">
            <h2>Редактиране</h2>

            <form id="editUserForm">
                <input type="hidden" id="editUserId">

                <div class="modal-field">
                    <label>Потребителско име</label>
                    <input type="text" id="editUsername" required>
                </div>

                <div class="modal-field">
                    <label>Имейл</label>
                    <input type="email" id="editEmail" required>
                </div>

                <div class="modal-field">
                    <label>Роля</label>
                    <select id="editRole">
                        <option value="user">Потребител</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="modal-cancel">Откажи промените</button>
                    <button type="submit" class="modal-save">Запази промените</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div class="admin-modal-overlay" id="deleteModal">
        <div class="admin-modal delete-modal">
            <h2>Потвърждение</h2>

            <p class="delete-warning">
                Сигурни ли сте, че искате да изтриете този потребител?<br>
                <strong>Всички данни ще бъдат изтрити завинаги и не могат да бъдат възстановени.</strong>
            </p>

            <div class="modal-actions">
                <button class="modal-cancel" id="cancelDelete">Отказ</button>
                <button class="modal-delete" id="confirmDelete">Изтрий</button>
            </div>
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
    </script>

    
<script>
    window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        backgroundColor: 'transparent',
        data: [{
            type: "doughnut",
            startAngle: 60,
            indexLabelFontSize: 17,
            indexLabel: "{label} - #percent%",
            toolTipContent: "<b>{label}:</b> {y} (#percent%)",
            dataPoints: [
                { y: 67, label: "Hearing problems" },
                { y: 28, label: "No hearing problems" },
            ]
        }]
    });
    chart.render();}
</script>


<script>
    const modal = document.getElementById("editModal");
    const cancelBtn = document.querySelector(".modal-cancel");

    document.querySelectorAll(".upd-btn").forEach(btn => {
        btn.addEventListener("click", e => {
            const row = e.target.closest("tr");

            document.getElementById("editUserId").value = row.children[0].innerText;
            document.getElementById("editUsername").value = row.children[1].innerText;
            document.getElementById("editEmail").value = row.children[2].innerText;
            document.getElementById("editRole").value = row.querySelector(".role").classList.contains("admin") ? "admin" : "user";

            modal.classList.add("active");
        });
    });

    cancelBtn.addEventListener("click", () => {
        modal.classList.remove("active");
    });

    modal.addEventListener("click", e => {
        if (e.target === modal) modal.classList.remove("active");
    });
</script>


<script>
    const deleteModal = document.getElementById("deleteModal");
    let deleteUserId = null;

    document.querySelectorAll(".del-btn").forEach(btn => {
        btn.addEventListener("click", e => {
            const row = e.target.closest("tr");
            deleteUserId = row.children[0].innerText;
            deleteModal.classList.add("active");
        });
    });

    document.getElementById("cancelDelete").addEventListener("click", () => {
        deleteModal.classList.remove("active");
        deleteUserId = null;
    });

    document.getElementById("confirmDelete").addEventListener("click", () => {
        // later you will connect this to PHP / AJAX
        alert("Потребител с ID " + deleteUserId + " ще бъде изтрит.");
        deleteModal.classList.remove("active");
    });
</script>



</body>
</html>


