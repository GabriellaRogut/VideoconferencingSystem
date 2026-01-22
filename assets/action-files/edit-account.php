<?php
    include_once("../../includes/connection.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../index.php");
        exit;
    }


    if (isset($_POST['update_account'])) {

        $userID = $_SESSION['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $_SESSION['errors_update'] = [];

        // validation
        if (!$username || !$email) {
            $_SESSION['errors_update'][] = "Име и имейл са задължителни полета.";
        }

        // current password hash
        $stmt = $connection->prepare("
            SELECT password_hash, profile_photo 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userID]);
        $user = $stmt->fetch();

        // Check if changing password
        if ($new_password) {
            if (!$current_password) {
                $_SESSION['errors_update'][] = "Въведете текуща парола.";
            } elseif (!password_verify($current_password, $user['password_hash'])) {
                $_SESSION['errors_update'][] = "Текущата парола е грешна.";
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['errors_update'][] = "Паролата не съвпада.";
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            }
        }


        // profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
            $fileName = $_FILES['profile_photo']['name'];
            $fileSize = $_FILES['profile_photo']['size'];
            $fileType = $_FILES['profile_photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                // rename file to avoid collisions
                $newFileName = $userID . '_' . time() . '.' . $fileExtension;
                $uploadFileDir = '../../assets/images/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $profile_photo_to_save = $newFileName;
                } else {
                    $_SESSION['errors_update'][] = "Грешка при качване на снимката.";
                }
            } else {
                $_SESSION['errors_update'][] = "Невалиден формат на снимката. Разрешени: jpg, png, gif.";
            }
        }


        // update database
        if (!$_SESSION['errors_update']) {
            $sql = "UPDATE users SET username = ?, email = ?";

            $params = [$username, $email];

            if ($new_password) {
                $sql .= ", password_hash = ?";
                $params[] = $new_password_hash;
            }

            if (isset($profile_photo_to_save)) {
                $sql .= ", profile_photo = ?";
                $params[] = $profile_photo_to_save;
            }

            $sql .= " WHERE id = ?";
            $params[] = $userID;

            $stmt = $connection->prepare($sql);
            $stmt->execute($params);

            // Update session
            $_SESSION['username'] = $username;

            header("Location: ../../account.php?success=1");
            exit;
        } else {
            $_SESSION['edit_errors'] = $_SESSION['errors_update'];
            header("Location: ../../account.php");
            exit;
        }

    }
?>
