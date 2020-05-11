<?php
    define('ROOT_URL', 'http://localhost/library/');
    define('ABSOLUTE_PATH', 'C:/xampp/htdocs/library/');
    if(!defined('API_KEY')){
      $api_key = file_get_contents(ABSOLUTE_PATH . 'config/google_books_api_key.txt');
      define('API_KEY', $api_key);
    }
    if(!defined('SQL_PASSWORD')){
      define('SQL_PASSWORD', file_get_contents(ABSOLUTE_PATH . 'config/sql_password.txt'));
    }
    session_start();

    //Include Google Client Library for PHP autoload file
    require_once 'C:/xampp/htdocs/library/vendor/autoload.php';

    //Make object of Google API Client for call Google API
    $google_client = new Google_Client();
    $google_client->setAuthConfig('C:/xampp/htdocs/library/config/client_secret.json');
    $google_client->addScope('profile');
    $google_client->addScope('email');
    $google_client->setRedirectUri(ROOT_URL . 'account/google_login.php');
    $google_client->setPrompt('select_account consent');

    function urlRequest($url, $setUserAgent=false, $usePost=false, $additionalHeaders='', $content='') {
		return file_get_contents($url, false, stream_context_create(array('http' => array('method' => (($usePost) ? 'POST' : 'GET'), 'header' => "User-Agent: ".(($setUserAgent) ? $setUserAgent : $_SERVER['HTTP_USER_AGENT'])."\r\n"."Content-Type: application/x-www-form-urlencoded;charset=UTF-8\r\n".$additionalHeaders, 'content' => $content))));
    }

    function new_log($header, $msg = "", $note = ""){
      $final_log = "[" . date("d.m.Y | H:i:s") . "] ";
      $final_log .= $header . "\n[---------- | --------]   ";
      if($msg != ""){
        if(is_string($msg)){
          $final_log .= " " . $msg;
        } else {
          $var_string = str_replace("\n", "\n[---------- | --------]   ", var_export($msg, true));
          $final_log .= $var_string;
        }
      }
      if(isset($_SESSION['current_user'])){
        $final_log .= "\n[---------- | --------] --- Logged by: ---\n[---------- | --------]   " . str_replace("\n", "\n[---------- | --------]   ", var_export($_SESSION['current_user'], true));
      }
      $final_log .= "\n[---------- | --------] --- Page: ---\n[---------- | --------]   " . $_SERVER['PHP_SELF'];
      if($note != ""){
        $final_log .= "\n[---------- | --------] --- Note: ---\n[---------- | --------]   " . str_replace("\n", "\n[---------- | --------]   ", $note);
      }
      $final_log .= "\n[---------- | --------] --- END ---\n\n--------------------------------------------------\n";
      file_put_contents(ABSOLUTE_PATH . 'log.txt', $final_log . "\n", FILE_APPEND);
    }

    function array_empty($arr){
        foreach($arr as $item){
            if($item == ""){
                return TRUE;
            }
        }
        return FALSE;
    }

    
