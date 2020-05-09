<?php

//logout.php

include('../config/config.php');

//Reset OAuth access token
$google_client->revokeToken();

//Destroy entire session data.
session_destroy();

if(isset($_GET["redirect"]) && $_GET["redirect"] == 'google'){
    header('Location: ' . ROOT_URL . 'account/google_login.php');
} else if(isset($_GET["redirect"]) && $_GET["redirect"] == 'login'){
    header('Location: ' . ROOT_URL . 'account/google_login.php?redirect=login');
} else {
    header('Location: ' . ROOT_URL);
}

?>