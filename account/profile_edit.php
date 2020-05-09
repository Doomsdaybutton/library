<?php
    require('../config/config.php');
    require('../config/db.php');

    $firstname = FALSE;
    $lastname = FALSE;
    $email = FALSE;
    $password = FALSE;
    $gender = FALSE;
    $profile_picture = FALSE;
    $param = '';
    $msg = '';
    $id = $_SESSION['current_user']['id'];
    $msg_class = 'success';


    if(isset($_GET['firstname'])){
        $firstname = TRUE;
        $param = 'firstname';
        $placeholder = 'Vorname';
    }
    if(isset($_GET['lastname'])){
        $lastname = TRUE;
        $param = 'lastname';
        $placeholder = 'Nachname';
    }
    if(isset($_GET['email'])){
        $email = TRUE;
        $param = 'email';
        $placeholder = 'Email';
    }
    if(isset($_GET['password'])){
        $password = TRUE;
        $param = 'password';
        $placeholder = 'Passwort';
    }
    if(isset($_GET['gender'])){
        $gender = TRUE;
        $param = 'gender';
        $placeholder = 'Geschlecht';
    }
    if(isset($_GET['profile_picture'])){
        $profile_picture = TRUE;
        $param = 'profile_picture';
        $placeholder = 'Profilbild';
    }

    if(isset($_POST['submit'])){
        if(array_key_exists('value', $_POST)){
            $value = htmlentities($_POST['value']);
        }
        if(array_key_exists('password_repeat', $_POST)){
            $password_repeat = htmlentities($_POST['password_repeat']);
        }

        if(!$profile_picture){
        //no files to handle
            if(!empty($value)){
                //all filled
                if($value == $_SESSION['current_user'][$param]){
                    $msg = 'Es wurden keine Änderungen gemacht!';
                    $msg_class = 'warning';
                }
                if($email){
                    if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                        //unvalid email
                        $msg = "Tragen Sie bitte eine gültige Email ein!";
                        $msg_class= 'danger';
                        $value_error = TRUE;
                    } else {
                        $low_email = strtolower($value);
                        $query = "SELECT LOWER(email) FROM users WHERE email = '$low_email'";
                        $sql_result = mysqli_query($conn, $query);
                        $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

                        if(count($sql_fetch) != 0){
                            $msg = "Diese Email ist schon belegt!";
                            $msg_class = 'danger';
                            $value_error = TRUE;
                        } elseif(count($sql_fetch) > 1){
                            $msg = "Es gab mehrere solche Emails!";
                            $msg_class = 'danger';
                            $value_error = TRUE;

                            $query = "DELETE FROM users WHERE LOWER(email) = '$low_email'";
                            mysqli_query($conn, $query);
                        }

                    }
                } elseif($password){
                    if($value != $password_repeat){
                        $msg = 'Die Passwörter stimmen nicht überein!';
                        $msg_class = 'danger';
                        $value_error = TRUE;
                    }
                } 
                
            

            } else {
                //some fields are missing
                $empty_error = TRUE;
                $msg = "Bitte füllen Sie alle Felder aus!";
                $msg_class = 'danger';
            }

            if($msg_class == 'success'){
                $query = "UPDATE users SET $param = '$value' WHERE id = '$id'";
                if(mysqli_query($conn, $query)){
                    new_log("Profile Edit: $param was changed to \"$value\"!", array("profile_before" => $_SESSION['current_user'], "changed_parameter" => $param, "to_value" => $value));
                    $_SESSION['current_user'][$param] = $value;
                } else {
                    $msg = 'Die Änderungen konnten nicht gespeichert werden!';
                    $msg_class = 'danger';
                }
            }
        } else {
            //files to handle

            $target_dir = "../images/users/";
            $target_file = $target_dir . $_SESSION['current_user']['id'] . '.png';
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if(getimagesize($_FILES["profile_picture"]["tmp_name"])){
                //picture
                if(file_exists($target_file)){
                    //image already exists -> delete it
                    unlink($target_file);
                }
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $msg = "Die Datei ". basename( $_FILES["profile_picture"]["name"]). " wurde hochgeladen.";
                    $msg_class = 'success';
                    $_SESSION['current_user']['profile_picture'] = '';
                    $query = "UPDATE users SET profile_picture = '' WHERE id = '$id'";
                    if(!mysqli_query($conn, $query)){
                        $msg .= "Die Datei " . basename( $_FILES["profile_picture"]["name"]) . "konnte nicht in die Datenbank gespeichert werden." ;
                        $msg_class = 'danger';
                    }
                } else {
                    $msg = "Die Datei konnte nicht hochgeladen werden.";
                    $msg_class = 'danger';
                }
            } else {
                //no picture
                $value_error = TRUE;
                $msg = 'Die Datei ist kein Bild!';
                $msg_class = 'danger';
            }
        }
    }

    


?>

    <?php include('../inc/header.php'); ?>
    <?php include('../inc/navbar.php'); ?>

    <div class="container d-flex justify-content-center">
        <div class="card" style="width:auto; padding: 20px;">
            <div class="card-body text-center">
                <p class="card-header bg-white border-0 font-weight-bold">Bibliothek Proenca</p>
                <p class="card-subtitle mb-2"><small>Profil - <?php echo $placeholder; ?></small></p>
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?' . $param; ?>" method="post" novalidate <?php echo $profile_picture ? 'enctype="multipart/form-data"' : ''; ?>

                    <?php if(!$gender && !$profile_picture && !$password): ?>
                        <div class="form-group">
                            <input value="<?php echo isset($_POST['value']) ? $value : (isset($_SESSION['current_user']["$param"]) ? $_SESSION['current_user']["$param"] : ''); ?>" type="text" class="form-control <?php if(($value_error) or ($empty_error && (!isset($value) or ($value == '')))){echo 'alert-danger border border-danger';} ?>" placeholder="<?php echo $placeholder; ?>" name="value">
                        </div>
                    <?php elseif($password): ?>
                        <div class="form-group mx-0">
                            <div class="input-group">
                                <input value="<?php echo isset($_POST['value']) ? '' : $_SESSION['current_user']['password']; ?>" type="password" class="password-toggle form-control mb-4 <?php if(($value_error) or ($empty_error && (!isset($value) or ($value == '')))){echo 'alert-danger border border-danger';} ?>" placeholder="Passwort" name="value">
                                <div class="input-group-append">
                                    <button onclick="passwordToggle()" class="btn btn-outline-secondary px-1" style="height: 45px;" type="button" id="button-addon2"><img class="px-2 password-toggle-image" style="height: 21px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTUuNTU2IDUxNS41NTYiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCA1MTUuNTU2IDUxNS41NTYiIHdpZHRoPSI1MTJweCI+PHBhdGggZD0ibTI1Ny43NzggNjQuNDQ0Yy0xMTkuMTEyIDAtMjIwLjE2OSA4MC43NzQtMjU3Ljc3OCAxOTMuMzM0IDM3LjYwOSAxMTIuNTYgMTM4LjY2NiAxOTMuMzMzIDI1Ny43NzggMTkzLjMzM3MyMjAuMTY5LTgwLjc3NCAyNTcuNzc4LTE5My4zMzNjLTM3LjYwOS0xMTIuNTYtMTM4LjY2Ni0xOTMuMzM0LTI1Ny43NzgtMTkzLjMzNHptMCAzMjIuMjIzYy03MS4xODQgMC0xMjguODg5LTU3LjcwNi0xMjguODg5LTEyOC44ODkgMC03MS4xODQgNTcuNzA1LTEyOC44ODkgMTI4Ljg4OS0xMjguODg5czEyOC44ODkgNTcuNzA1IDEyOC44ODkgMTI4Ljg4OWMwIDcxLjE4Mi01Ny43MDUgMTI4Ljg4OS0xMjguODg5IDEyOC44ODl6IiBmaWxsPSIjMDAwMDAwIi8+PHBhdGggZD0ibTMwMy4zNDcgMjEyLjIwOWMyNS4xNjcgMjUuMTY3IDI1LjE2NyA2NS45NzEgMCA5MS4xMzhzLTY1Ljk3MSAyNS4xNjctOTEuMTM4IDAtMjUuMTY3LTY1Ljk3MSAwLTkxLjEzOCA2NS45NzEtMjUuMTY3IDkxLjEzOCAwIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+Cg==" /></button>
                                </div>
                            </div>
                            
                        
                            <input value="" type="password" class="password-toggle form-control <?php if(($value_error) or ($empty_error && (!isset($value) or ($value == '')))){echo 'alert-danger border border-danger';} ?>" placeholder="Passwort wiederholen" name="password_repeat">
                        </div>
                    <?php elseif($gender): ?>
                        <div class="form-group">
                            <select class="form-control <?php if($value_error or $empty_error){echo 'alert-danger border border-danger';} ?>" name="value">
                                <option value="" disabled hidden <?php if(!isset($value)){echo 'selected';} ?>>Geschlecht</option>
                                <option value="2" <?php if(isset($value) && $value == 2){echo 'selected';} ?>>Männlich</option>
                                <option value="1" <?php if(isset($value) && $value == 1){echo 'selected';} ?>>Weiblich</option>
                                <option value="3" <?php if(isset($value) && $value == 3){echo 'selected';} ?>>Anderes</option>
                            </select>
                        </div>
                    <?php elseif($profile_picture): ?>
                        <div class="form-group">
                            <div class="input-group mt-4">
                                <div class="custom-file">
                                    <input class="custom-file-input" type="file" id="profile_picture" name="profile_picture" value="">
                                    <label class="custom-file-label mr-2" for="profile_picture"><span class="pr-5 ml-n5">Bild auswählen...</span></label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>



                    <div class="form-group">
                        <label class="mb-0 mt-0 pt-0 <?php echo $msg_class == 'success' ? 'text-success' : ($msg_class == 'warning' ? 'text-warning' : 'text-danger'); ?>"><small><?php if($msg != ''){echo $msg; } ?></small></label>
                    </div>
                    <div class="form-group d-none d-sm-block">
                        <div class="row">
                            <div class="col-sm">
                                <a class="btn btn-dark btn-block py-n1" href="profile.php">Zurück</a>
                            </div>
                            <div class="col-sm">
                                <input type="submit" value="Speichern" name="submit" class="btn btn-success btn-block">
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-block d-sm-none">
                        <div class="row">
                            <div class="col-sm">
                                <input type="submit" value="Speichern" name="submit" class="btn btn-success btn-block mb-4">
                            </div>
                            <div class="col-sm">
                                <a class="btn btn-dark btn-block py-n1" href="profile.php">Zurück</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="d-block d-sm-none" style="margin-bottom: 45px;"></div>
    <div class="d-none d-sm-block" style="margin-bottom: 115px;"></div>
    <div class="d-none d-lg-block d-xl-none" style="margin-bottom: 130px;"></div>
    <div class="d-none d-xl-block" style="margin-bottom: 210px;"></div>

<?php include('../inc/footer.php'); ?>