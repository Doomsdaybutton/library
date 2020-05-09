 <?php
    require('../config/config.php');
    require('../config/db.php');
    
    if(isset($_GET['redirect']) && $_GET['redirect'] == 'login'){
        $_SESSION['google_login'] = TRUE;
    } else if(!isset($_SESSION['google_login'])) {
        $_SESSION['google_login'] = FALSE;
    }

    if (isset($_GET['oauth'])) {
        // Start auth flow by redirecting to Google's auth server
        $login_url = $google_client->createAuthUrl();
        header('Location: ' . filter_var($login_url, FILTER_SANITIZE_URL));
    } else if (isset($_GET['code'])) {
        // Receive auth code from Google, exchange it for an access token, and
        // redirect to your base URL
        $google_client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $google_client->getAccessToken();
        $redirect_uri = ROOT_URL . 'account/google_login.php';
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    } else if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        // You have an access token; use it to call the People API
        $google_client->setAccessToken($_SESSION['access_token']);



        $google_service_oauth = new Google_Service_Oauth2($google_client);

        $google_user_data = $google_service_oauth->userinfo_v2_me->get();


//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------

        $google_user_firstname = $google_user_data['givenName'];
        $google_user_lastname = $google_user_data['familyName'];
        $google_user_email = $google_user_data['email'];
        if($google_user_data['gender'] == 'male'){
            $google_user_gender = 2;
        } elseif($google_user_data['gender'] == 'female'){
            $google_user_gender = 1;
        }
        $google_user_profile_picture = $google_user_data['picture'];


        if(!($_SESSION['google_login'])){
            //sign up
            $low_email = strtolower($google_user_data['email']);
            $query = "SELECT LOWER(email) FROM users WHERE email = ?";

            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $query)){
                $_SESSION["google_login_error_msg"] = 'Database failed! [1]';
                $_SESSION['google_login_error_type'] = 'sign_up';
            } else {
                mysqli_stmt_bind_param($stmt, "s", $low_email);
                mysqli_stmt_execute($stmt);
                $sql_result = mysqli_stmt_get_result($stmt);

                $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
            
                if(count($sql_fetch) != 0){
                    $_SESSION["google_login_error_msg"] = 'Diese Email ist schon belegt!';
                    $_SESSION["google_login_error_type"] = 'sign_up';
                } else {
                    if($google_user_data["gender"] != NULL){
                        $query = "INSERT INTO users(firstname, lastname, email, gender, profile_picture) VALUES(?, ?, ?, ?, ?)";
                    } else {
                        $query = "INSERT INTO users(firstname, lastname, email, profile_picture) VALUES(?, ?, ?, ?)";
                    }

                    $stmt = mysqli_stmt_init($conn);
                    if(!mysqli_stmt_prepare($stmt, $query)){
                        $_SESSION['google_login_error_msg'] = 'Database failed! [2]';
                        $_SESSION['google_login_error_type'] = 'sign_up';
                    } else {
                        if($google_user_data['gender'] != NULL){
                            mysqli_stmt_bind_param($stmt, "sssss", $google_user_firstname, $google_user_lastname, $google_user_email, $google_user_gender, $google_user_profile_picture);
                        } else {
                            mysqli_stmt_bind_param($stmt, "ssss", $google_user_firstname, $google_user_lastname, $google_user_email, $google_user_profile_picture);
                        }

                        mysqli_stmt_execute($stmt);

                        $query = "SELECT LAST_INSERT_ID() AS 'current_id'";

                        $sql_result = mysqli_query($conn, $query);
                        $sql_fetch = mysqli_fetch_assoc($sql_result);
                        
                        mysqli_close($conn);
                        $current_user = array(
                        "id"=>(int)$sql_fetch["current_id"],
                        "firstname"=>$google_user_firstname,
                        "lastname"=>$google_user_lastname,
                        "name"=>$google_user_firstname . ' ' . $google_user_lastname,
                        "email"=>$google_user_email,
                        "gender"=>$google_user_gender,
                        "profile_picture"=>$google_user_profile_picture,
                        "admin"=>0
                        );
                        new_log("New account created with google:", $current_user);
                    }

                    
                }
            }

            
        } else {
            $query = "SELECT * FROM users WHERE email = '$google_user_email'";

            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $query)){
                $_SESSION['google_login_error_msg'] = 'Database failed! [3]';
                $_SESSION['google_login_error_type'] = 'login';
            } else {
                mysqli_stmt_bind_param($stmt, "s", $google_user_email);
                mysqli_stmt_execute($stmt);
                $sql_result = mysqli_stmt_get_result($stmt);

                $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
            
                if(count($sql_fetch) == 0){
                    //no account
                    $_SESSION["google_login_error_msg"] = 'Es existiert noch kein Konto mit dieser Email';
                    $_SESSION["google_login_error_type"] = 'login';
                } else if (count($sql_fetch) == 1){
                    //perfect
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
                    new_log("User logged in with google:", $current_user);
                } else {
                    //multiple accounts!
                    // $_SESSION["google_login_error_msg"] = 'Es existieren mehrere Konten mit dieser Email (diese werden nun automatisch gelöscht)';
                    // $_SESSION["google_login_error_type"] = 'login';
                    // foreach($sql_fetch as $error_user){
                    //     $error_user_id = htmlentities($error_user["id"]);
                    //     $query = "DELETE FROM users WHERE id = ?";
                    //     mysqli_stmt_init($conn);
                    //     if(!mysqli_stmt_prepare($stmt, $query)){
                    //         $_SESSION['google_login_error_msg'] = 'Database failed! [4]';
                    //         $_SESSION['google_login_error_type'] = 'login';
                    //     } else {
                    //         mysqli_stmt_bind_param($stmt, "s", $error_user_id);
                    //         mysqli_stmt_execute($stmt);
                    //     }
                    // }

                    $_SESSION['google_login_error_msg'] = 'Es existieren mehrere Konten mit dieser Email (diese werden nun automatisch gelöscht)';
                    $_SESSION['google_login_error_type'] = 'login';
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
                            if(strpos($_SESSION['google_login_error_msg'], "Einige konnten nicht") == FALSE){
                                $_SESSION['google_login_error_msg'] .= '<br>Einige konnten nicht gelöscht werden!';
                                $_SESSION['google_login_error_type'] = 'login';
                            }
                            
                        } else {
                            $query = "DELETE FROM users WHERE id = ? AND admin IS NULL";

                            $stmt = mysqli_stmt_init($conn);
                            if(!mysqli_stmt_prepare($stmt, $query)){
                                $_SESSION['google_login_error_msg'] = 'Database failed!';
                                $_SESSION['google_login_error_type'] = 'login';
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $error_user_id);
                                mysqli_stmt_execute($stmt);
                            }
                        }
                    }
                    
                    //log
                    if(count($lent_media) != 0){
                        new_log("ERROR: Multiple (" . count($sql_fetch) . ") Accounts with email: \"" . $google_user_email . "\"! Some couldn't be deleted!", array("email" => $google_user_email, "accounts_with_same_email" => $sql_fetch, "lent_media" => $lent_media), "It could be that the admin was part of those multiple accounts.\nIn this case the admin still wasn't deleted.\nLogin through Google!");
                    } else {
                        new_log("ERROR: Multiple (" . count($sql_fetch) . ") Accounts with email: \"" . $google_user_email . "\"! All deleted!", array("email" => $google_user_email, "accounts_with_same_email" => $sql_fetch), "It could be that the admin was part of those multiple accounts.\nIn this case the admin still wasn't deleted.\nLogin through Google!");
                    }
                }
            }
            
        }
        

        $_SESSION["current_user"] = $current_user;
                


//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------

        if(isset($_SESSION['google_login_error_msg'])){
            header('Location: ' . ROOT_URL . 'account/google_login_error.php');
        } else {
            header('Location: ' . ROOT_URL);
        }
        // TODO: Use service object to request People datas
    } else {
        $redirect_uri = ROOT_URL . 'account/google_login.php?oauth';
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    }