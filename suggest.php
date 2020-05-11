<?php
    require('config/config.php');
    require('config/db.php');

    $request = $_REQUEST["q"];

    if($request == "[object HTMLInputElement]"){
        $request = "";
    }

    $german = $english = $french = $spanish = $other = $access = $lang_set = $group_search = FALSE;
    
    if(isset($_REQUEST["german"]) && $_REQUEST["german"] == "true"){
        $german = TRUE;
        $lang_set = TRUE;
    }
    if(isset($_REQUEST["french"]) && $_REQUEST["french"] == "true"){
        $french = TRUE;
        $lang_set = TRUE;
    }
    if(isset($_REQUEST["english"]) && $_REQUEST["english"] == "true"){
        $english = TRUE;
        $lang_set = TRUE;
    }
    if(isset($_REQUEST["spanish"]) && $_REQUEST["spanish"] == "true"){
        $spanish = TRUE;
        $lang_set = TRUE;
    }
    if(isset($_REQUEST["other"]) && $_REQUEST["other"] == "true"){
        $other = TRUE;
        $lang_set = TRUE;
    }
    if(isset($_REQUEST["access"]) && $_REQUEST["access"] == "true"){
        $access = TRUE;
    }
    if(isset($_REQUEST["group_search"]) && $_REQUEST["group_search"] == "true"){
        $group_search = TRUE;
    }


    $_SESSION["request"] = $request;
    $output = array("info" => array(), "media" => array());
    $media = [];
    $tmp = [];
    
    
    $requests = explode(" ", $request);
    $_SESSION["requests"] = $requests;

    $filter = "";
    $lang = "";
    $access_query = "";

    foreach($requests as $q){
        global $request;
        if($lang_set){
            $lang = "AND (FALSE ";
            if($german){
                $lang .= "OR language = 'de'";
            }
            if($french){
                $lang .= "OR language = 'fr'";
            }
            if($english){
                $lang .= "OR language = 'en'";
            }
            if($spanish){
                $lang .= "OR language = 'es'";
            }
            if($other){
                $lang .= "OR language = 'other'";
            }
            $lang .= ") ";
            
        }

        if($access){
            $access_query = "AND user_id = '-1'";
        }

        $filter = $lang . $access_query;

        $_SESSION['filter'] = $filter;
        //klammern ↓ wichtig!
        $query = "SELECT * FROM media WHERE (title LIKE '%$q%' OR subtitle LIKE '%$q%' OR description LIKE '%$q%' OR authors LIKE '%$q%' OR isbns LIKE '%$q%') $filter ORDER BY rating DESC LIMIT 10";
        if($group_search){
            $query = "SELECT * FROM media WHERE (medium_group LIKE '%$request%') ORDER BY volume ASC";
        }
        if(is_numeric($q) && isset($_SESSION['current_user']) && $_SESSION['current_user']['admin']){
            $query = "SELECT * FROM media WHERE (id = $q)";
        }
        $_SESSION['sql_query'] = $query;
        $sql_result = mysqli_query($conn, $query);
        echo mysqli_error($conn);
        if($_SERVER['PHP_SELF'] == "suggest.php"){
            echo mysqli_error($conn);
        }
        $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
        $tmp = array_merge($media, $sql_fetch);
        $media = array_unique($tmp, SORT_REGULAR);
        
    }


    foreach ($media as &$medium) {
        $authors = json_decode($medium['authors']);
        $authors_string = "";
        foreach($authors->authors as $author){
            if($authors_string == ""){
                $authors_string .= $author->author_firstname . " <strong class=\"text-primary\">" . $author->author_lastname . "</strong>";
            } else {
                $authors_string .= ", " . $author->author_firstname . " <strong class=\"text-primary\">" . $author->author_lastname . "</strong>";
            }
        }
        $medium['authors_string'] = $authors_string;

        if(@getimagesize("images/media/" . $medium["id"] . ".png")){
            $medium["image_exists"] = 1;
        } elseif($medium['image'] != ""){
            $medium['image_exists'] = 2;
        } else {
            $medium["image_exists"] = 0;
        }
        
        array_push($output['media'], $medium);
    }

    if($group_search){
        array_push($output['info'], "group search yeyyy!");
    }

    $_SESSION['suggest'] = json_encode($output);

    echo json_encode($output);
?>