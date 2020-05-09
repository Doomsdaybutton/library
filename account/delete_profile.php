<?php 
    require('../config/config.php');
    require('../config/db.php');

    $id = $_SESSION['current_user']['id'];
    $query = "DELETE FROM users WHERE id = '$id'";
    if(mysqli_query($conn, $query)){
        $_SESSION['msg'] = 'Das Profil wurde erfolgreich gelöscht!';
        $_SESSION['msg_class'] = 'success';
    } else {
        $_SESSION['msg'] = 'Das Profil konnte nicht gelöscht werden.';
        $_SESSION['msg_class'] = 'danger';
    }
    if($_SESSION['msg_class'] == 'danger'){
        new_log("ERROR: SQL error when trying to delete profile!", array("profile_to_be_deleted" => $_SESSION['current_user']), "SQL error message: " . mysqli_error($conn));
    } else {
        new_log("Deleted Profile", array("deleted_profile" => $_SESSION['current_user']));
    }
    session_destroy();
    header('Location: ' . ROOT_URL);