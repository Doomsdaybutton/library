<?php
    require('../config/config.php');
    require('../config/db.php');

    $msg = '';
    $email_error = FALSE;
    $empty_error = FALSE;
    $password_error = FALSE;

    if(isset($_POST['submit'])){
        //submit
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);

        if(!empty($email) && !empty($password)){
            //all inputs set
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                //unvalid email
                $email_error = TRUE;
                $msg = 'Tragen Sie bitte eine gültige Email ein!';
            } else {
                //valid email
                $query = "SELECT * FROM users WHERE email = ?";

                $stmt = mysqli_stmt_init($conn);

                if(!mysqli_stmt_prepare($stmt, $query)){
                    $msg = "Database failed!";
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                    $sql_result = mysqli_stmt_get_result($stmt);

                    $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
                
                    if(count($sql_fetch) == 0){
                        //no account with email
                        $email_error = TRUE;
                        $msg = 'Es existiert noch kein Konto mit dieser Email';
                    } elseif(count($sql_fetch) == 1){
                        //perfect email
                        if($sql_fetch[0]['password'] == NULL){
                            //google password
                            $msg = 'Melde dich bitte über Google an.';
                            $empty_error = TRUE;
                            
                        }
                        elseif(password_verify($password, $sql_fetch[0]['password'])){
                            //all perfect!
                            $current_user = array(
                            "id"=>$sql_fetch[0]["id"],
                            "firstname"=>$sql_fetch[0]["firstname"],
                            "lastname"=>$sql_fetch[0]["lastname"],
                            "name"=>$sql_fetch[0]["firstname"] . ' ' . $sql_fetch[0]["lastname"],
                            "email"=>$sql_fetch[0]["email"],
                            "gender"=>$sql_fetch[0]["gender"],
                            "profile_picture"=>$sql_fetch[0]["profile_picture"],
                            "admin"=>$sql_fetch[0]["admin"]
                            );

                            $_SESSION['current_user'] = $current_user;
                            header('Location: ' . ROOT_URL);
                        } else {
                            $password_error = TRUE;
                            $msg = 'Falsches Passwort!';
                        }
                        
                    } else {
                        //multiple accounts with email!
                        $email_error = TRUE;
                        $msg = 'Es existieren mehrere Konten mit dieser Email (diese werden nun automatisch gelöscht)';
                        $lent_media = [];
                        foreach($sql_fetch as $error_user){
                            global $sql_fetch, $lent_media;
                            $error_user_id = $error_user["id"];
                            $query = "SELECT * FROM media WHERE user_id = $error_user_id";
                            $sql_result = mysqli_query($conn, $query);
                            $sql_fetch2 = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
                            if(count($sql_fetch2) != 0){
                                array_push($lent_media, array("user_id" => $error_user_id, "lent_media" => $sql_fetch2));
                            }
                            
                            if(count($sql_fetch2) != 0){
                                if(strpos($msg, "Einige konnten nicht") == FALSE){
                                    $msg .= '<br>Einige konnten nicht gelöscht werden!';
                                }
                                
                            } else {
                                $query = "DELETE FROM users WHERE id = ? AND admin IS NULL";

                                $stmt = mysqli_stmt_init($conn);
                                if(!mysqli_stmt_prepare($stmt, $query)){
                                    $msg = 'Database failed!';
                                } else {
                                    mysqli_stmt_bind_param($stmt, "s", $error_user_id);
                                    mysqli_stmt_execute($stmt);
                                }
                            }
                        }

                        //log
                        if(count($lent_media) != 0){
                            new_log("ERROR: Multiple (" . count($sql_fetch) . ") Accounts with email: \"" . $email . "\"! Some couldn't be deleted!", array("email" => $email, "accounts_with_same_email" => $sql_fetch, "lent_media" => $lent_media), "It could be that the admin was part of those multiple accounts.\nIn this case the admin still wasn't deleted.");
                        } else {
                            new_log("ERROR: Multiple (" . count($sql_fetch) . ") Accounts with email: \"" . $email . "\"! All deleted!", array("email" => $email, "accounts_with_same_email" => $sql_fetch), "It could be that the admin was part of those multiple accounts.\nIn this case the admin still wasn't deleted.");
                        }
                    }
                }
                
            }
        } else {
            $empty_error = TRUE;
            $msg = 'Bitte füllen Sie alle Felder aus!';
        }
    }
?>

    <?php include('../inc/header.php'); ?>
    <?php include('../inc/navbar.php'); ?>
    
    <div class="container d-flex justify-content-center">
        <div class="card" style="width:auto;">
            <div class="card-body text-center">
                <p class="card-header bg-white border-0 font-weight-bold">Bibliothek Proenca</p>
                <p class="card-subtitle mb-2"><small>Anmelden</small></p>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate>
                    <div class="form-group">
                        <input value="<?php echo isset($_POST['email']) ? $email : ''; ?>" type="email" class="form-control <?php if(($email_error) or ($empty_error && (!isset($email) or ($email == '')))){echo 'alert-danger border border-danger';} ?>" placeholder="Email" name="email">
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" class="password-toggle form-control <?php if(($empty_error && (!isset($password) or ($password == ''))) or $password_error){echo 'border border-danger alert-danger'; }?>" name="password" placeholder="Passwort">
                            <div class="input-group-append">
                                <button onclick="passwordToggle()" class="btn btn-outline-secondary px-1" style="height: 45px;" type="button"><img class="px-2 password-toggle-image" style="height: 21px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTUuNTU2IDUxNS41NTYiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCA1MTUuNTU2IDUxNS41NTYiIHdpZHRoPSI1MTJweCI+PHBhdGggZD0ibTI1Ny43NzggNjQuNDQ0Yy0xMTkuMTEyIDAtMjIwLjE2OSA4MC43NzQtMjU3Ljc3OCAxOTMuMzM0IDM3LjYwOSAxMTIuNTYgMTM4LjY2NiAxOTMuMzMzIDI1Ny43NzggMTkzLjMzM3MyMjAuMTY5LTgwLjc3NCAyNTcuNzc4LTE5My4zMzNjLTM3LjYwOS0xMTIuNTYtMTM4LjY2Ni0xOTMuMzM0LTI1Ny43NzgtMTkzLjMzNHptMCAzMjIuMjIzYy03MS4xODQgMC0xMjguODg5LTU3LjcwNi0xMjguODg5LTEyOC44ODkgMC03MS4xODQgNTcuNzA1LTEyOC44ODkgMTI4Ljg4OS0xMjguODg5czEyOC44ODkgNTcuNzA1IDEyOC44ODkgMTI4Ljg4OWMwIDcxLjE4Mi01Ny43MDUgMTI4Ljg4OS0xMjguODg5IDEyOC44ODl6IiBmaWxsPSIjMDAwMDAwIi8+PHBhdGggZD0ibTMwMy4zNDcgMjEyLjIwOWMyNS4xNjcgMjUuMTY3IDI1LjE2NyA2NS45NzEgMCA5MS4xMzhzLTY1Ljk3MSAyNS4xNjctOTEuMTM4IDAtMjUuMTY3LTY1Ljk3MSAwLTkxLjEzOCA2NS45NzEtMjUuMTY3IDkxLjEzOCAwIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+Cg==" /></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="mb-0 mt-0 pt-0 text-danger"><small><?php if($msg != ''){echo $msg; } ?></small></label>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Anmelden" name="submit" class="btn btn-success btn-block">
                    </div>
                </form>
                <a href="logout.php?redirect=login" class="btn btn-outline-secondary btn-sm btn-block"><img class="mr-2 pr-2" style="height:28px; border-right: 1px solid #ccc;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTIgNTEyIiBoZWlnaHQ9IjMycHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiB3aWR0aD0iMzJweCI+PGc+PHBhdGggZD0ibTEyMCAyNTZjMC0yNS4zNjcgNi45ODktNDkuMTMgMTkuMTMxLTY5LjQ3N3YtODYuMzA4aC04Ni4zMDhjLTM0LjI1NSA0NC40ODgtNTIuODIzIDk4LjcwNy01Mi44MjMgMTU1Ljc4NXMxOC41NjggMTExLjI5NyA1Mi44MjMgMTU1Ljc4NWg4Ni4zMDh2LTg2LjMwOGMtMTIuMTQyLTIwLjM0Ny0xOS4xMzEtNDQuMTEtMTkuMTMxLTY5LjQ3N3oiIGZpbGw9IiNmYmJkMDAiLz48cGF0aCBkPSJtMjU2IDM5Mi02MCA2MCA2MCA2MGM1Ny4wNzkgMCAxMTEuMjk3LTE4LjU2OCAxNTUuNzg1LTUyLjgyM3YtODYuMjE2aC04Ni4yMTZjLTIwLjUyNSAxMi4xODYtNDQuMzg4IDE5LjAzOS02OS41NjkgMTkuMDM5eiIgZmlsbD0iIzBmOWQ1OCIvPjxwYXRoIGQ9Im0xMzkuMTMxIDMyNS40NzctODYuMzA4IDg2LjMwOGM2Ljc4MiA4LjgwOCAxNC4xNjcgMTcuMjQzIDIyLjE1OCAyNS4yMzUgNDguMzUyIDQ4LjM1MSAxMTIuNjM5IDc0Ljk4IDE4MS4wMTkgNzQuOTh2LTEyMGMtNDkuNjI0IDAtOTMuMTE3LTI2LjcyLTExNi44NjktNjYuNTIzeiIgZmlsbD0iIzMxYWE1MiIvPjxwYXRoIGQ9Im01MTIgMjU2YzAtMTUuNTc1LTEuNDEtMzEuMTc5LTQuMTkyLTQ2LjM3N2wtMi4yNTEtMTIuMjk5aC0yNDkuNTU3djEyMGgxMjEuNDUyYy0xMS43OTQgMjMuNDYxLTI5LjkyOCA0Mi42MDItNTEuODg0IDU1LjYzOGw4Ni4yMTYgODYuMjE2YzguODA4LTYuNzgyIDE3LjI0My0xNC4xNjcgMjUuMjM1LTIyLjE1OCA0OC4zNTItNDguMzUzIDc0Ljk4MS0xMTIuNjQgNzQuOTgxLTE4MS4wMnoiIGZpbGw9IiMzYzc5ZTYiLz48cGF0aCBkPSJtMzUyLjE2NyAxNTkuODMzIDEwLjYwNiAxMC42MDYgODQuODUzLTg0Ljg1Mi0xMC42MDYtMTAuNjA2Yy00OC4zNTItNDguMzUyLTExMi42MzktNzQuOTgxLTE4MS4wMi03NC45ODFsLTYwIDYwIDYwIDYwYzM2LjMyNiAwIDcwLjQ3OSAxNC4xNDYgOTYuMTY3IDM5LjgzM3oiIGZpbGw9IiNjZjJkNDgiLz48cGF0aCBkPSJtMjU2IDEyMHYtMTIwYy02OC4zOCAwLTEzMi42NjcgMjYuNjI5LTE4MS4wMiA3NC45OC03Ljk5MSA3Ljk5MS0xNS4zNzYgMTYuNDI2LTIyLjE1OCAyNS4yMzVsODYuMzA4IDg2LjMwOGMyMy43NTMtMzkuODAzIDY3LjI0Ni02Ni41MjMgMTE2Ljg3LTY2LjUyM3oiIGZpbGw9IiNlYjQxMzIiLz48L2c+PC9zdmc+Cg==" />Weiter mit Google</a>
                <hr>
                <a href="sign_up.php"><small>Ich habe noch kein Konto</small></a>
            </div>
        </div>
    </div>

    <?php include('../inc/footer.php'); ?>