<?php
    require('../config/config.php');
    require('../config/db.php');
    $msg = '';
    $empty_errror = FALSE;
    $password_error = FALSE;
    $email_error = FALSE;
    if(filter_has_var(INPUT_POST, 'submit')){
        $firstname = htmlentities($_POST['firstname']);
        $lastname = htmlentities($_POST['lastname']);
        if(array_key_exists('gender', $_POST)){
            $gender = $_POST['gender'];
        }
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);
        $password_repeat = htmlentities($_POST['password_repeat']);
        
        if(!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password) && !empty($password_repeat)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE){
                $msg = 'Tragen Sie bitte ein gültige Email ein!';
                $email_error = TRUE;
            } else if($password != $password_repeat){
                $msg = 'Die Passwörter stimmen nicht überein!';
                $password_error = TRUE;
            } else {
                $low_email = strtolower($email);
                // $query = "SELECT LOWER(email) FROM users WHERE email = '$low_email'";

                // $sql_result = mysqli_query($conn, $query);

                // $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);


                $query = "SELECT LOWER(email) FROM users WHERE email = ?";

                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $query)){
                    $msg = 'Database failed!';
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $low_email);
                    mysqli_stmt_execute($stmt);
                    $sql_result = mysqli_stmt_get_result($stmt);

                    $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

                    if(count($sql_fetch) != 0){
                        $msg = 'Diese Email ist schon belegt!';
                        $email_error = TRUE;

                    } else {
                        if(isset($gender)){
                            $query = "INSERT INTO users(firstname, lastname, email, password, gender) VALUES(?, ?, ?, ?, ?)";
                        } else {
                            $query = "INSERT INTO users(firstname, lastname, email, password) VALUES(?, ?, ?, ?)";
                        }
                        
                        $stmt = mysqli_stmt_init($conn);

                        if(!mysqli_stmt_prepare($stmt, $query)){
                            $msg = 'Database failed!';
                        } else {
                            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                            if(isset($gender)){
                                mysqli_stmt_bind_param($stmt, "sssss", $firstname, $lastname, $email, $password_hashed, $gender);
                                mysqli_stmt_execute($stmt);
                            } else {
                                mysqli_stmt_bind_param($stmt, "ssss", $firstname, $lastname, $email, $password_hashed);
                                mysqli_stmt_execute($stmt);
                            }
                        }
                        

                        $query = "SELECT LAST_INSERT_ID() AS 'current_id'";

                        $sql_result = mysqli_query($conn, $query);
                        $sql_fetch = mysqli_fetch_assoc($sql_result);
                        
                        mysqli_close($conn);
                        
                        if(isset($gender)){
                            $current_user = array(
                                "id"=>(int)$sql_fetch["current_id"],
                                "firstname"=>$firstname,
                                "lastname"=>$lastname,
                                "name"=>$firstname . ' ' . $lastname,
                                "email"=>$email,
                                "gender"=>$gender,
                                "password"=>$password,
                                "profile_picture"=>'',
                                "admin"=>0
                            );
                        } else {
                            $current_user = array(
                                "id"=>(int)$sql_fetch["current_id"],
                                "firstname"=>$firstname,
                                "lastname"=>$lastname,
                                "name"=>$firstname . ' ' . $lastname,
                                "email"=>$email,
                                "password"=>$password,
                                "profile_picture"=>'',
                                "admin"=>0
                            );
                        }

                        new_log("New Account created:", $current_user);
                        

                        $_SESSION["current_user"] = $current_user;

                        header('Location: ../index.php');
                    }
                }
                
                
                
            }
        } else {
            $msg = 'Fülle Sie bitte alle Felder mit * aus!';
            $empty_error = TRUE;
        } 
    }

    
?>

    <?php include('../inc/header.php'); ?>
    <?php require('../inc/navbar.php'); ?>
    
    <div class="container d-flex justify-content-center">
        <div class="card" style="width:auto;">
            <div class="card-body text-center">
                <p class="card-header bg-white border-0 font-weight-bold">Bibliothek Proenca</p>
                <p class="card-subtitle mb-2"><small>Registrieren</small></p>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md mb-md-1 mb-3">
                                <input value="<?php echo isset($_POST['firstname']) ? $firstname : ''; ?>" type="text" class="form-control d-md-block <?php if($empty_error && (!isset($firstname) or ($firstname == ''))){echo 'border border-danger alert-danger'; }?>" placeholder="Vorname*" name="firstname">
                            </div>
                            <div class="col-md">
                                <input value="<?php echo isset($_POST['lastname']) ? $lastname : ''; ?>" type="text" class="form-control d-md-block <?php if($empty_error && (!isset($lastname) or ($lastname == ''))){echo 'border border-danger alert-danger'; }?>" placeholder="Nachname*" name="lastname">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="gender">
                            <option value="" disabled hidden <?php if(!isset($gender)){echo 'selected';} ?>>Geschlecht</option>
                            <option value="2" <?php if(isset($gender) && $gender == 2){echo 'selected';} ?>>Männlich</option>
                            <option value="1" <?php if(isset($gender) && $gender == 1){echo 'selected';} ?>>Weiblich</option>
                            <option value="3" <?php if(isset($gender) && $gender == 3){echo 'selected';} ?>>Anderes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input value="<?php echo isset($_POST['email']) ? $email : ''; ?>" type="email" class="form-control <?php if(($email_error) or ($empty_error && (!isset($email) or ($email == '')))){echo 'alert-danger border border-danger';} ?>" placeholder="Email*" name="email">
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" class="password-toggle form-control <?php if($empty_error && (!isset($password) or ($password == ''))){echo 'border border-danger alert-danger'; }?>" name="password" placeholder="Passwort*">
                            <div class="input-group-append">
                                <button onclick="passwordToggle()" class="btn btn-outline-secondary px-1" style="height: 45px;" type="button" id="button-addon2"><img class="px-2 password-toggle-image" style="height: 21px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTUuNTU2IDUxNS41NTYiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCA1MTUuNTU2IDUxNS41NTYiIHdpZHRoPSI1MTJweCI+PHBhdGggZD0ibTI1Ny43NzggNjQuNDQ0Yy0xMTkuMTEyIDAtMjIwLjE2OSA4MC43NzQtMjU3Ljc3OCAxOTMuMzM0IDM3LjYwOSAxMTIuNTYgMTM4LjY2NiAxOTMuMzMzIDI1Ny43NzggMTkzLjMzM3MyMjAuMTY5LTgwLjc3NCAyNTcuNzc4LTE5My4zMzNjLTM3LjYwOS0xMTIuNTYtMTM4LjY2Ni0xOTMuMzM0LTI1Ny43NzgtMTkzLjMzNHptMCAzMjIuMjIzYy03MS4xODQgMC0xMjguODg5LTU3LjcwNi0xMjguODg5LTEyOC44ODkgMC03MS4xODQgNTcuNzA1LTEyOC44ODkgMTI4Ljg4OS0xMjguODg5czEyOC44ODkgNTcuNzA1IDEyOC44ODkgMTI4Ljg4OWMwIDcxLjE4Mi01Ny43MDUgMTI4Ljg4OS0xMjguODg5IDEyOC44ODl6IiBmaWxsPSIjMDAwMDAwIi8+PHBhdGggZD0ibTMwMy4zNDcgMjEyLjIwOWMyNS4xNjcgMjUuMTY3IDI1LjE2NyA2NS45NzEgMCA5MS4xMzhzLTY1Ljk3MSAyNS4xNjctOTEuMTM4IDAtMjUuMTY3LTY1Ljk3MSAwLTkxLjEzOCA2NS45NzEtMjUuMTY3IDkxLjEzOCAwIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+Cg==" /></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 pb-1">
                        <input type="password" class="password-toggle form-control <?php if($empty_error && (!isset($password_repeat) or ($password_repeat == ''))){echo 'border border-danger alert-danger'; }?>" name="password_repeat" placeholder="Passwort wiederholen*">
                    </div>
                    <div class="form-group">
                        <label class="mb-0 mt-0 pt-0 text-danger"><small><?php if($msg != ''){echo $msg; } ?></small></label>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Registrieren" name="submit" class="btn btn-success btn-block">
                    </div>
                </form>
                <a href="../account/logout.php?redirect=google" class="btn btn-outline-secondary btn-sm btn-block"><img class="mr-2 pr-2" style="height:28px; border-right: 1px solid #ccc;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTIgNTEyIiBoZWlnaHQ9IjMycHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiB3aWR0aD0iMzJweCI+PGc+PHBhdGggZD0ibTEyMCAyNTZjMC0yNS4zNjcgNi45ODktNDkuMTMgMTkuMTMxLTY5LjQ3N3YtODYuMzA4aC04Ni4zMDhjLTM0LjI1NSA0NC40ODgtNTIuODIzIDk4LjcwNy01Mi44MjMgMTU1Ljc4NXMxOC41NjggMTExLjI5NyA1Mi44MjMgMTU1Ljc4NWg4Ni4zMDh2LTg2LjMwOGMtMTIuMTQyLTIwLjM0Ny0xOS4xMzEtNDQuMTEtMTkuMTMxLTY5LjQ3N3oiIGZpbGw9IiNmYmJkMDAiLz48cGF0aCBkPSJtMjU2IDM5Mi02MCA2MCA2MCA2MGM1Ny4wNzkgMCAxMTEuMjk3LTE4LjU2OCAxNTUuNzg1LTUyLjgyM3YtODYuMjE2aC04Ni4yMTZjLTIwLjUyNSAxMi4xODYtNDQuMzg4IDE5LjAzOS02OS41NjkgMTkuMDM5eiIgZmlsbD0iIzBmOWQ1OCIvPjxwYXRoIGQ9Im0xMzkuMTMxIDMyNS40NzctODYuMzA4IDg2LjMwOGM2Ljc4MiA4LjgwOCAxNC4xNjcgMTcuMjQzIDIyLjE1OCAyNS4yMzUgNDguMzUyIDQ4LjM1MSAxMTIuNjM5IDc0Ljk4IDE4MS4wMTkgNzQuOTh2LTEyMGMtNDkuNjI0IDAtOTMuMTE3LTI2LjcyLTExNi44NjktNjYuNTIzeiIgZmlsbD0iIzMxYWE1MiIvPjxwYXRoIGQ9Im01MTIgMjU2YzAtMTUuNTc1LTEuNDEtMzEuMTc5LTQuMTkyLTQ2LjM3N2wtMi4yNTEtMTIuMjk5aC0yNDkuNTU3djEyMGgxMjEuNDUyYy0xMS43OTQgMjMuNDYxLTI5LjkyOCA0Mi42MDItNTEuODg0IDU1LjYzOGw4Ni4yMTYgODYuMjE2YzguODA4LTYuNzgyIDE3LjI0My0xNC4xNjcgMjUuMjM1LTIyLjE1OCA0OC4zNTItNDguMzUzIDc0Ljk4MS0xMTIuNjQgNzQuOTgxLTE4MS4wMnoiIGZpbGw9IiMzYzc5ZTYiLz48cGF0aCBkPSJtMzUyLjE2NyAxNTkuODMzIDEwLjYwNiAxMC42MDYgODQuODUzLTg0Ljg1Mi0xMC42MDYtMTAuNjA2Yy00OC4zNTItNDguMzUyLTExMi42MzktNzQuOTgxLTE4MS4wMi03NC45ODFsLTYwIDYwIDYwIDYwYzM2LjMyNiAwIDcwLjQ3OSAxNC4xNDYgOTYuMTY3IDM5LjgzM3oiIGZpbGw9IiNjZjJkNDgiLz48cGF0aCBkPSJtMjU2IDEyMHYtMTIwYy02OC4zOCAwLTEzMi42NjcgMjYuNjI5LTE4MS4wMiA3NC45OC03Ljk5MSA3Ljk5MS0xNS4zNzYgMTYuNDI2LTIyLjE1OCAyNS4yMzVsODYuMzA4IDg2LjMwOGMyMy43NTMtMzkuODAzIDY3LjI0Ni02Ni41MjMgMTE2Ljg3LTY2LjUyM3oiIGZpbGw9IiNlYjQxMzIiLz48L2c+PC9zdmc+Cg==" />Weiter mit Google</a>
                <hr>
                <a href="login.php"><small>Ich habe schon ein Konto</small></a>
            </div>
        </div>
    </div>
    

    <?php include('../inc/footer.php'); ?>