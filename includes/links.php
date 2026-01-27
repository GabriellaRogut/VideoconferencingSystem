<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,100..900;1,100..900&family=WDXL+Lubrifont+SC&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<!-- General Styles -->
<link rel="stylesheet" href="assets/css/general.css?v=2">

<!-- General scripts -->
<script src="assets/js/main.js" defer></script>

<!-- Favicon -->
<link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">


<!-- ADMIN MODAL -->
     <div class="modal-overlay <?php if (isset( $_SESSION['errors_admin']) ) echo " active" ?>" id="adminModal">
        <div class="modal">
            <span class="close" id="closeAdminModal">&times;</span>
            <h3 class="admin-title">Оторизиран достъп</h3>
            <p style="color: var(--color-muted); margin-bottom:1rem;">
                Само администратори могат да влязат в системния панел.
            </p>

            <form method="POST" action="admin-panel/admin-login.php" class="modal-form <?php if (isset( $_SESSION['errors_admin']) ) echo " active-form" ?>" id="modalForm">
                <input type="email" name="admin_email" placeholder="Имейл" required>
                <input type="password" name="admin_password" placeholder="Парола" required>
                <button type="submit" name="admin_login">Вход в системата</button>

                <?php
                    if (isset( $_SESSION['errors_admin'] ) ) {

                        foreach( $_SESSION['errors_admin'] as $error ) {
                            echo "<div class='error'>". $error . "</div>";

                            unset( $_SESSION['errors_admin'] );
                        }
                    }
                ?>
            </form>
        </div>
    </div>