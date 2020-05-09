<?php
    require('config/config.php');
    require('config/db.php');

    $id = "";

    if(isset($_GET['noid'])){
        $noid = TRUE;
    } else {
        $noid = FALSE;
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
    } elseif(!$noid) {
        echo 'no id!';
    }

    function checkIsbn($isbn13, $isbn10){
        global $conn;
        if($isbn13 != "" || $isbn10 != ""){
            $query = "SELECT * FROM media WHERE isbn LIKE '%$isbn13%' OR isbn LIKE '%$isbn10%'";
        } else {
            return TRUE;
        }
            
        $sql_result = mysqli_query($conn, $query);
        $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

        if(count($sql_fetch) == 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //global variables
    $title = $subtitle = $publisher = $description = $language = $image = $isbn13 = $isbn10 = $main_category = $volume = $medium_index = "";
    $categories = $firstnames = $lastnames = $authors = $isbns = [];
    $already_exists = $image_file_exists = FALSE;
    $medium_group = -1;
    $rating = 0;
    $ratings_count = 0;

    //error variables
    $msg = $image_msg = "";
    $msg_class = "success";
    $isbn13_error = FALSE;
    $isbn10_error = FALSE;
    $empty_author_error = FALSE;
    $author_warning = FALSE;
    $language_error = FALSE;
    $language_warning = FALSE;
    $medium_group_error = FALSE;
    $medium_group_warning = FALSE;
    $volume_error = FALSE;
    $medium_index_error = FALSE;
    $category_error = FALSE;
    $publisher_warning = FALSE;
    $rating_error = FALSE;
    $ratings_count_error = FALSE;

    // load / form
    if(isset($_POST['submit'])){
        //process form

        //group
        $medium_group = htmlentities($_POST['medium_group']);

        //volume
        $volume = htmlentities($_POST['volume']);

        //image
        if(isset($_SESSION['image'])){
            $image = $_SESSION['image'];
        } else {
            $image = "";
        }
        

        //title
        $title = htmlentities($_POST['title']);

        //subtitle
        $subtitle = htmlentities($_POST['subtitle']);

        //publisher
        $publisher = htmlentities($_POST['publisher']);

        //description
        $description = htmlentities($_POST['description']);

        //language
        if(isset($_POST['language'])){
            $language = $_POST['language'];
        } else {
            $language = "";
        }
        
        //isbns
        if(isset($_POST['ISBN_13'])){
            $isbn13 = $_POST['ISBN_13'];
        } else {
            $isbn13 = "";
        }
        if(isset($_POST['ISBN_10'])){
            $isbn10 = $_POST['ISBN_10'];
        } else {
            $isbn10 = "";
        }
        $isbns = array(array("type" => "ISBN_13", "identifier" => $isbn13), array("type" => "ISBN_10", "identifier" => $isbn10));

        //categories
        $categories_string = $_POST['categories'];
        if($categories_string == ""){
            $categories_string = "General";
        }
        $categories_temp = explode(", ", $categories_string);
        $categories = [];
        foreach($categories_temp as $category_temp){
            global $categories;
            array_push($categories, htmlentities(ucfirst($category_temp)));
        }

        //main_category
        if(isset($categories[0])){
            $main_category = $categories[0];
        } else {
            $main_category = "";
        }

        //firstnames
        if(isset($_POST['firstnames'])){
            $firstnames = $_POST['firstnames'];
        } else {
            $firstnames = [];
        }

        //lastnames
        if(isset($_POST['lastnames'])){
            $lastnames = $_POST['lastnames'];
        } else {
            $lastnames = [];
        }

        //authors
        if($firstnames != [] || $lastnames != []){
            for($i = 0; $i < count($firstnames); $i++){
                global $authors, $firstnames, $lastnames;
                array_push($authors, array("author_firstname" => htmlentities(ucfirst($firstnames[$i])), "author_lastname" => htmlentities(ucfirst($lastnames[$i]))));
            }
        }

        //index
        if(isset($authors[0])){
            $medium_index = $authors[0]['author_lastname'];
        } else {
            $medium_index = "";
        }

        //new author
        if($_POST['submit'] == "new_author"){
            array_push($authors, array("author_firstname" => "", "author_lastname" => ""));
        }

        //delete author
        if(strpos($_POST['submit'], "emove_author") == 1){
            $index = (int)substr($_POST['submit'], 13);
            unset($authors[$index]);
            $authors = array_values($authors);
        }

        //isbns
        $isbns = [];

        //isbn13
        if(isset($_POST['ISBN_13'])){
            $isbn13 = $_POST['ISBN_13'];
            array_push($isbns, array("type" => "ISBN_13", "identifier" => $isbn13));
        } else {
            $isbn13 = "";
        }

        //isbn10
        if(isset($_POST['ISBN_10'])){
            $isbn10 = $_POST['ISBN_10'];
            array_push($isbns, array("type" => "ISBN_10", "identifier" => $isbn10));
        } else {
            $isbn10 = "";
        }

        //rating
        $rating = htmlentities($_POST['rating']);

        //ratings_count
        $ratings_count = htmlentities($_POST['ratings_count']);

        //check for isbn13_errors
        if($isbn13 != "" && $_POST['submit'] == "Add Medium"){
            //already exists
            $query = "SELECT * FROM media WHERE isbns LIKE '%$isbn13%'";
            $sql_result = mysqli_query($conn, $query);
            $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

            if(count($sql_fetch) != 0){
                $msg = "This ISBN already exists in the database!";
                $msg_class = "danger";
                $isbn13_error = TRUE;
            }

            //unvalid length
            if(strlen($isbn13) != 13){
                $msg = "A ISBN of unvalid length (" . strlen($isbn13) . ") was given! (Expected length: 13)";
                $msg_class = "danger";
                $isbn13_error = TRUE;
            }

            if(!is_numeric($isbn13)){
                $msg = "The ISBN contains unvalid characters!";
                $msg_class = "danger";
                $isbn13_error = TRUE;
            }
        }

        //check for isbn10_errors
        if($isbn10 != "" && !$isbn13_error && $_POST['submit'] == "Add Medium"){
            //already exists
            $query = "SELECT * FROM media WHERE isbns LIKE '%$isbn10%'";
            $sql_result = mysqli_query($conn, $query);
            $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

            if(count($sql_fetch) != 0){
                $msg = "This ISBN already exists in the database!";
                $msg_class = "danger";
                $isbn10_error = TRUE;
            }

            //unvalid length
            if(strlen($isbn10) != 10){
                $msg = "A ISBN of unvalid length (" . strlen($isbn10) . ") was given! (Expected length: 10)";
                $msg_class = "danger";
                $isbn10_error = TRUE;
            }

            if(!is_numeric($isbn10)){
                $msg = "The ISBN contains unvalid characters!";
                $msg_class = "danger";
                $isbn10_error = TRUE;
            }
        }


        if(!$isbn13_error && !$isbn10_error && $_POST['submit'] == "Add Medium"){
            //check for empty_error
            if(empty($title)){
                $msg = "A title must be given!";
                $msg_class = "danger";
                $empty_error = TRUE;
            } elseif(count($authors) == 0){
                $msg = "An author must be set! (Add one by clicking on the plus sign.)";
                $msg_class = "danger";
                $empty_error = TRUE;
            } elseif(!is_numeric($rating)){
                $msg = "The rating contains unvalid characters!";
                $msg_class = "danger";
                $rating_error = TRUE;
            } elseif((double)$rating > 5 || (double)$rating < 0){
                $msg = "The rating can't be over 5.0 or negative! (0.0 - 5.0)";
                $msg_class = "danger";
                $rating_error = TRUE;
            } elseif(!is_numeric($ratings_count)){
                $msg = "The number of ratings contains unvalid characters!";
                $msg_class = "danger";
                $ratings_count_error = TRUE;
            } elseif(!((int)$ratings_count == $ratings_count)){
                $msg = "The number of ratings must be an integer!" . var_export(is_int(10), true);
                $msg_class = "danger";
                $ratings_count_error = TRUE;
            } elseif((double)$ratings_count < 0){
                $msg = "The number of ratings can't be negative!";
                $msg_class = "danger";
                $ratings_count_error = TRUE;
            } else {
                //check for empty author error
                for($i = 0; $i < count($authors); $i++){
                    global $authors;
                    if($authors[$i]['author_firstname'] == "" && $authors[$i]['author_lastname'] == ""){
                        $msg = "At least one name must be set!";
                        $msg_class = "danger";
                        $empty_author_error = TRUE;
                    }
                }

                //medium_group_error
                if(is_numeric($medium_group)){
                    $query = "SELECT medium_group FROM media WHERE id = '$medium_group'";
                    $sql_result = mysqli_query($conn, $query);
                    $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
                    if(count($sql_fetch) > 1){
                        $msg = "Database failed! [1]" . mysqli_error($conn);
                        $msg_class = "danger";
                        $medium_group_error = TRUE;
                    } elseif(count($sql_fetch) == 1){
                        $medium_group = $sql_fetch[0]['medium_group'];
                    } elseif($medium_group != "-1") {
                        $msg = "Unvalid Group ID! [" . $medium_group . "]" . mysqli_error($conn);
                        $msg_class = "danger";
                        $medium_group_error = TRUE;
                    }
                }
                if(!$medium_group_error && $medium_group != "-1"){
                    if($volume == ""){
                        $query = "SELECT MAX(volume) + 1 as suggest_volume FROM media WHERE medium_group = '$medium_group'";
                        $sql_result = mysqli_query($conn, $query);
                        $sql_fetch = mysqli_fetch_assoc($sql_result);
                        $volume = $sql_fetch['suggest_volume'];
                        echo mysqli_error($conn);
                    }
                    if(!is_numeric($volume)){
                        $msg = "The volume contains unvalid characters!";
                        $volume_error = TRUE;
                        $msg_class = "danger";
                    } else {
                        if((int)$volume <= 0){
                            $msg = "The volume can't be a negative number or zero!";
                            $msg_class = "danger";
                            $volume_error = TRUE;
                        } else {
                            $query = "SELECT * FROM media WHERE medium_group = '$medium_group'";
                            $sql_result = mysqli_query($conn, $query);
                            $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
                            echo mysqli_error($conn);
                            if(count($sql_fetch) == 0){
                                $msg = "<span class=\"text-warning\">You are creating a new group!</span>";
                                $medium_group_warning = TRUE;
                            }
                            for($i = 0; $i < count($sql_fetch); $i++){
                                global $medium_index, $main_category, $medium_group, $volume, $language, $authors;
                                if($sql_fetch[$i]['medium_index'] != $medium_index){
                                    $msg = "The index \"" . $medium_index . "\" doesn't match the index \"" . $sql_fetch[$i]['medium_index'] . "\" of the group \"" . $sql_fetch[$i]['medium_group'] . "\"";
                                    $msg_class = "danger";
                                    $medium_group_error = TRUE;
                                    $medium_index_error = TRUE;
                                } elseif($sql_fetch[$i]['main_category'] != $main_category){
                                    $msg = "The category \"" . $main_category . "\" doesn't match the category \"" . $sql_fetch[$i]['main_category'] . "\" of the group \"" . $sql_fetch[$i]['medium_group'] . "\"";
                                    $msg_class = "danger";
                                    $medium_group_error = TRUE;
                                    $category_error = TRUE;
                                } elseif($sql_fetch[$i]['volume'] == $volume){
                                    $query = "SELECT MAX(volume) + 1 as suggest_volume FROM media WHERE medium_group = '$medium_group'";
                                    $sql_result = mysqli_query($conn, $query);
                                    $suggest_volume = mysqli_fetch_assoc($sql_result);
                                    $msg = "The volume " . $volume . " is already taken. Try " . $suggest_volume['suggest_volume'];
                                    $msg_class = "danger";
                                    $volume_error = TRUE;
                                }
                                echo $msg_class;
                                if($sql_fetch[$i]['language'] != $language && $msg_class != "danger" && $language_warning == FALSE){
                                    if($msg != ""){
                                        $msg .= '<br>';
                                    }
                                    $msg .= "<span class=\"text-warning\">The language \"$language\" doesn't match the language \"" . $sql_fetch[$i]['language'] . "\" of some of the members of the group \"" . $sql_fetch[$i]['medium_group'] . "\"";
                                    $language_warning = TRUE;
                                    $msg_class = "warning";
                                }
                                if($sql_fetch[$i]['publisher'] != $publisher && $msg_class != "danger" && $publisher_warning == FALSE){
                                    if($msg != ""){
                                        $msg .= '<br>';
                                    }
                                    $msg .= "<span class=\"text-warning\">The publisher \"$publisher\" doesn't match the publisher \"" . $sql_fetch[$i]['publisher'] . "\" of some of the members the group \"" . $sql_fetch[$i]['medium_group'] . "\"";
                                    $publisher_warning = TRUE;
                                    $msg_class = "warning";
                                }
                                if($sql_fetch[$i]['authors'] != json_encode(array("authors" => $authors), JSON_UNESCAPED_UNICODE) && $msg_class != "danger" && $author_warning == FALSE){
                                    if($msg != ""){
                                        $msg .= '<br>';
                                    }
                                    $msg .= "<span class=\"text-warning\">The authors don't entirely match the authors from the group \"" . $sql_fetch[$i]['medium_group'] . "\"";
                                    $author_warning = TRUE;
                                    $msg_class = "warning";
                                }
                                if($msg_class == "danger"){
                                    $language_warning = FALSE;
                                    $publisher_warning = FALSE;
                                    $author_warning = FALSE;
                                }
                            }
                        }
                        
                    }
                    
                }

                if(!$empty_author_error && !$medium_group_error && !$volume_error){
                    //string building

                    //authors_string
                    $authors_string = json_encode(array("authors" => $authors), JSON_UNESCAPED_UNICODE);
                    
                    //categories
                    $categories_string = json_encode(array("categories" => $categories), JSON_UNESCAPED_UNICODE);

                    //isbn_string
                    $isbns_string = json_encode(array("isbns" => $isbns), JSON_UNESCAPED_UNICODE);

                    $image_file_exists = file_exists($image);

                    if(!$image_file_exists && $image != ""){
                        $query = "INSERT INTO media (title, subtitle, publisher, authors, language, categories, isbns, description, medium_group, medium_index, main_category, volume, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    } else {
                        $query = "INSERT INTO media (title, subtitle, publisher, authors, language, categories, isbns, description, medium_group, medium_index, main_category, volume) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    }
                    $stmt = mysqli_stmt_init($conn);
                    if(!mysqli_stmt_prepare($stmt, $query)){
                        $msg = "Database failed! [0]" . mysqli_stmt_error($stmt);
                        $msg_class = "danger";
                    } else {
                        if(!$image_file_exists && $image != ""){
                            mysqli_stmt_bind_param($stmt, "sssssssssssss", $title, $subtitle, $publisher, $authors_string, $language, $categories_string, $isbns_string, $description, $medium_group, $medium_index, $main_category, $volume, $image);
                        } else {
                            mysqli_stmt_bind_param($stmt, "ssssssssssss", $title, $subtitle, $publisher, $authors_string, $language, $categories_string, $isbns_string, $description, $medium_group, $medium_index, $main_category, $volume);
                        }
                        mysqli_stmt_execute($stmt);
                    }

                    if($msg_class != "danger"){
                        $query = "SELECT LAST_INSERT_ID() as last_id";
                        $sql_result = mysqli_query($conn, $query);
                        $sql_fetch = mysqli_fetch_assoc($sql_result);
                        $last_id = $sql_fetch['last_id'];

                        if($image_file_exists){
                            rename($image, "images/media/" . (string)$last_id . ".png");
                        }

                        //position stuff
                        $sql_result = mysqli_query($conn, getSortingQuery($last_id));
                        $sql_fetch = mysqli_fetch_assoc($sql_result);

                        //id's
                        $medium_before_id = $sql_fetch['medium_before'];
                        $medium_after_id = $sql_fetch['medium_after'];
                        $current_medium_id = $sql_fetch['current_medium'];

                        //check for errors
                        if($medium_before_id == NULL || $medium_after_id == NULL || $medium_before_id == "NULL" || $medium_after_id == "NULL"){
                            echo 'Database failed! [2]';
                        } else {
                            //medium_before
                            if($medium_before_id != "first"){
                                $query = "SELECT * FROM media WHERE id = $medium_before_id";
                                $sql_result = mysqli_query($conn, $query);
                                $medium_before = mysqli_fetch_assoc($sql_result);
                                $medium_before['type'] = "before";
                            } else {
                                $medium_before = "first";
                            }

                            //medium_after
                            if($medium_after_id != "last"){
                                $query = "SELECT * FROM media WHERE id = $medium_after_id";
                                $sql_result = mysqli_query($conn, $query);
                                $medium_after = mysqli_fetch_assoc($sql_result);
                                $medium_after['type'] = "after";
                            } else {
                                $medium_after = "last";
                            }

                            //current_medium
                            $query = "SELECT * FROM media WHERE id = $current_medium_id";
                            $sql_result = mysqli_query($conn, $query);
                            $current_medium = mysqli_fetch_assoc($sql_result);
                            $current_medium['type'] = "current";

                            //position_media
                            $position_media = array("medium_before" => $medium_before, "current_medium" => $current_medium, "medium_after" => $medium_after);
                        }
                    }
                }
            }
        }


    } elseif(!$noid) {
        //process google request
        $google_medium = json_decode(urlRequest('https://www.googleapis.com/books/v1/volumes/' . $id));
        echo 'https://www.googleapis.com/books/v1/volumes/' . $id;
        //title
        if(isset($google_medium->volumeInfo->title)){
            $title = htmlentities($google_medium->volumeInfo->title);
        }

        //subtitle
        if(isset($google_medium->volumeInfo->subtitle)){
            $subtitle = htmlentities($google_medium->volumeInfo->subtitle);
        }

        //publisher
        if(isset($google_medium->volumeInfo->publisher)){
            $publisher = htmlentities($google_medium->volumeInfo->publisher);
        }

        //description
        if(isset($google_medium->volumeInfo->description)){
            $description = htmlentities(strip_tags($google_medium->volumeInfo->description, '<br>'));
        }

        //language
        if(isset($google_medium->volumeInfo->language)){
            $language = $google_medium->volumeInfo->language;
            if($language != "es" && $language != "de" && $language != "en" && $language != "fr"){
                $language = "other";
            }
        }

        //image
        if(isset($google_medium->volumeInfo->imageLinks->thumbnail)){
            $image = $google_medium->volumeInfo->imageLinks->thumbnail;
            $_SESSION['image'] = $image;
        } else {
            $_SESSION['image'] = "";
        }

        //categories
        if(isset($google_medium->volumeInfo->mainCategory)){
            array_push($categories, $google_medium->volumeInfo->mainCategory);
        }
        if(isset($google_medium->volumeInfo->categories)){
            foreach($google_medium->volumeInfo->categories as $categories_string){
                global $categories;
                $categories = array_unique(array_merge($categories, explode(" / ", $categories_string)));
            };
        }

        //authors
        if(isset($google_medium->volumeInfo->authors)){
            $google_authors = $google_medium->volumeInfo->authors;
            for($i = 0; $i < count($google_authors); $i++){
                global $authors;
                $names = explode(" ", $google_authors[$i]);
                $firstname = "";
                for($j = 0; $j < count($names) - 1; $j++){
                    if($j != 0){
                        $firstname .= " ";
                    }
                    $firstname .= $names[$j];
                }

                array_push($authors, array("author_firstname" => $firstname, "author_lastname" => $names[count($names) -1 ]));

            }
        }

        //isbns
        if(isset($google_medium->volumeInfo->industryIdentifiers)){
            $google_isbns = $google_medium->volumeInfo->industryIdentifiers;
            for($i = 0; $i < count($google_isbns); $i++){
                global $isbns, $isbn13, $isbn10;
                array_push($isbns, array("type" => $google_isbns[$i]->type, "identifier" => $google_isbns[$i]->identifier));

                if($google_isbns[$i]->type == "ISBN_13"){
                    $isbn13 = $google_isbns[$i]->identifier;
                } elseif($google_isbns[$i]->type == "ISBN_10"){
                    $isbn10 = $google_isbns[$i]->identifier;
                }
            }
        } else {
            $isbns = [];
            $isbn13 = "";
            $isbn10 = "";
        }

        //already exists
        {
            if($isbns != []){
                $query = "SELECT * FROM media WHERE isbns LIKE '%$isbn13%' OR isbns LIKE '%$isbn10%'";
                $sql_result = mysqli_query($conn, $query);
                $sql_fetch = mysqli_fetch_all($sql_result);
                echo mysqli_error($conn);
                if(count($sql_fetch) != 0){
                    $already_exists = TRUE;
                } else {
                    $already_exists = FALSE;
                }
            } else {
                $already_exists = FALSE;
            }   
        }

        //rating
        if(isset($google_medium->volumeInfo->averageRating)){
            $rating = htmlentities($google_medium->volumeInfo->averageRating);
        }

        //ratings_count
        if(isset($google_medium->volumeInfo->ratingsCount)){
            $ratings_count = htmlentities($google_medium->volumeInfo->ratingsCount);
        }
    }

    //format globals
    //categories
    if(isset($categories)){
        $categories_string = "";
        foreach($categories as $category){
            if($categories_string != ""){
                $categories_string .= ", ";
            }
            $categories_string .= ucfirst($category);
        }
    }

    //image form
    if(isset($_POST['upload_image'])){
        $target_dir = "images/media/";
        $file = $target_dir . basename($_FILES['image']['name']);
        $file_type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $target_file = $target_dir . "temp.png";
        if($_FILES['image']['tmp_name'] != ""){
            if(!getimagesize($_FILES['image']['tmp_name'])){
                global $image_msg;
                $image_msg = "This file is not an image!";
            } else {
                if(file_exists($target_file)){
                    unlink($target_file);
                }
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
                $_SESSION['image'] = $target_file;
                $image = $target_file;
            }
        } else {
            $image_msg = "Please select a file!";
        }
        
        
    }
    
    var_dump($author_warning);
?>

    <?php include('inc/header.php'); ?>
    <?php include('inc/navbar.php'); ?>

    <div class="container">
        <h1><span><a class="stretched_link" href="javascript:history.go(-1)"><img class="go-back-arrow py-2 align-center mb-2" style="height:35px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ0My41MiA0NDMuNTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ0My41MiA0NDMuNTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0xNDMuNDkyLDIyMS44NjNMMzM2LjIyNiwyOS4xMjljNi42NjMtNi42NjQsNi42NjMtMTcuNDY4LDAtMjQuMTMyYy02LjY2NS02LjY2Mi0xNy40NjgtNi42NjItMjQuMTMyLDBsLTIwNC44LDIwNC44ICAgIGMtNi42NjIsNi42NjQtNi42NjIsMTcuNDY4LDAsMjQuMTMybDIwNC44LDIwNC44YzYuNzgsNi41NDgsMTcuNTg0LDYuMzYsMjQuMTMyLTAuNDJjNi4zODctNi42MTQsNi4zODctMTcuMDk5LDAtMjMuNzEyICAgIEwxNDMuNDkyLDIyMS44NjN6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></span><?php echo $title ?></h1>
        
        <div class="row">
            <div class="col-lg-4 mb-3">
                <?php if(isset($last_id) && @getimagesize("images/media/" . $last_id . ".png")): ?>
                    <img src="<?php echo "images/media/" . $last_id . ".png" ?>" style="width:100%; max-width:300px;" alt="Sorry. No image found.">
                <?php elseif(@getimagesize($image)): ?>
                    <img src="<?php echo $image ?>" style="width:100%; max-width:300px;" alt="Sorry. No image found.">
                <?php else: ?>
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" style="width:58%;" alt="Sorry. No image found.">
                <?php endif; ?>
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="input-group mt-3">
                            <div class="custom-file">
                                <input class="custom-file-input" type="file" name="image" id="image">
                                <label class="custom-file-label pr-5 pl-n5" style="text-overflow:ellipsis; white-space:nowrap; overflow:hidden;" for="image"><?php if(isset($_FILES['image'])){ echo $_FILES['image']['name'];} else { echo 'Bild auswählen...';} ?></label>
                            </div>
                        </div>
                    </div>
                    
                    <input class="btn btn-outline-secondary btn-sm mt-3" type="submit" name="upload_image" value="Upload Image">
                    <?php if(isset($image_msg)): ?>
                        <p class="text-danger"><small><?php echo $image_msg; ?></small></p>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-lg-8">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                    <?php if(isset($last_id)): ?>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="id">ID</label>
                            <input class="col-md-10 form-control alert-success border border-success" value="<?php echo $last_id; ?>" type="text" id="id">
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="position">Position</label>
                            <div class="card col-md-10">
                                <div class="row mt-3 mb-3 no-gutters">
                                    <?php foreach($position_media as $position_medium): ?>
                                        <div class="col-sm">
                                            <div class="card border mx-1 rounded border-primary p-3 mb-3 mb-sm-0 <?php if($position_medium == "first" || $position_medium == "last"){echo 'border-info'; }?>" style="height:300px;">
                                                <?php if($position_medium == "first"): ?>
                                                    <img class="ml-4" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8cmVjdCB4PSI3LjYwNCIgeT0iNDM3LjI2IiBzdHlsZT0iZmlsbDojNUU0QzM2OyIgd2lkdGg9IjQ5Ni43OSIgaGVpZ2h0PSIzMi41NzUiLz4KPHJlY3QgeD0iNy42MDQiIHk9IjQzNy4yNiIgc3R5bGU9ImZpbGw6IzgwNjc0OTsiIHdpZHRoPSI0OTYuNzkiIGhlaWdodD0iMTYuMzY0Ii8+CjxyZWN0IHg9Ijk5LjIxIiB5PSIxMzIuMzEiIHN0eWxlPSJmaWxsOiNBQ0FCQjE7IiB3aWR0aD0iODUuMzYiIGhlaWdodD0iMzA0Ljk0Ii8+CjxyZWN0IHg9IjMxLjk4NCIgeT0iODguMDUiIHN0eWxlPSJmaWxsOiNBMjU4Q0I7IiB3aWR0aD0iNjcuMjIiIGhlaWdodD0iMzQ5LjIiLz4KPGc+Cgk8cG9seWdvbiBzdHlsZT0iZmlsbDojNkYyQzkzOyIgcG9pbnRzPSI4Mi4zOTEsMzUwLjE2NSA4Mi4zOTEsNDE5LjkxMSAzMS45ODQsNDE5LjkxMSAzMS45ODQsNDM3LjI1NCA4Mi4zOTEsNDM3LjI1NCAgICA4Mi4zOTEsNDM3Ljg4MyA5OS4yMDQsNDM3Ljg4MyA5OS4yMDQsNDM3LjI1NCA5OS4yMDQsNDE5LjkxMSA5OS4yMDQsMzUwLjE2NSAgIi8+Cgk8cmVjdCB4PSIzMS45ODQiIHk9IjE3NS43NyIgc3R5bGU9ImZpbGw6IzZGMkM5MzsiIHdpZHRoPSI2Ny4yMiIgaGVpZ2h0PSIxNzQuMzkiLz4KCTxyZWN0IHg9IjgyLjQiIHk9Ijg4LjA1IiBzdHlsZT0iZmlsbDojNkYyQzkzOyIgd2lkdGg9IjE2LjgxMyIgaGVpZ2h0PSI4Ny43MiIvPgo8L2c+CjxyZWN0IHg9IjgyLjQiIHk9IjE3NS43NyIgc3R5bGU9ImZpbGw6IzUxMjE2RDsiIHdpZHRoPSIxNi44MTMiIGhlaWdodD0iMTc3LjE0Ii8+CjxyZWN0IHg9IjE4NC41NiIgeT0iNjIuOTYxIiBzdHlsZT0iZmlsbDojRkVDRTAwOyIgd2lkdGg9IjY3LjIyIiBoZWlnaHQ9IjM3NC4zIi8+CjxyZWN0IHg9IjI1MS43OCIgeT0iNDIuMTciIHN0eWxlPSJmaWxsOiNBRDIyMDE7IiB3aWR0aD0iNzYuODIiIGhlaWdodD0iMzk1LjA4Ii8+CjxyZWN0IHg9IjMyOC42MSIgeT0iODguMDUiIHN0eWxlPSJmaWxsOiMwMTZFRjE7IiB3aWR0aD0iMTAwLjMiIGhlaWdodD0iMzQ5LjIiLz4KPHJlY3QgeD0iNDI4LjkiIHk9IjE1NS43OSIgc3R5bGU9ImZpbGw6IzlDREQwMzsiIHdpZHRoPSI1MS4xMSIgaGVpZ2h0PSIyODEuNDciLz4KPGc+Cgk8cmVjdCB4PSI0MjguOSIgeT0iNDIxLjI2IiBzdHlsZT0iZmlsbDojODZCQzA2OyIgd2lkdGg9IjUxLjExIiBoZWlnaHQ9IjE1Ljk5OCIvPgoJPHJlY3QgeD0iNDI4LjkiIHk9IjE1NS43OSIgc3R5bGU9ImZpbGw6Izg2QkMwNjsiIHdpZHRoPSIxNS41MiIgaGVpZ2h0PSIyODQuNDQiLz4KPC9nPgo8cmVjdCB4PSIzNDMuNSIgeT0iODcuNjIiIHN0eWxlPSJmaWxsOiMyNDg3RkY7IiB3aWR0aD0iNzAuNTIiIGhlaWdodD0iMzMyLjg0Ii8+CjxjaXJjbGUgc3R5bGU9ImZpbGw6I0JEREJGRjsiIGN4PSIzNzguNzYiIGN5PSIxOTcuNzkiIHI9IjI4LjE0Ii8+CjxyZWN0IHg9IjI2Ny42NSIgeT0iNDIuMTciIHN0eWxlPSJmaWxsOiNDRDJBMDE7IiB3aWR0aD0iNDUuMSIgaGVpZ2h0PSIzNzcuNDgiLz4KPGc+Cgk8cmVjdCB4PSIyNTEuNzgiIHk9IjExOS40OSIgc3R5bGU9ImZpbGw6I0ZFQkNBQzsiIHdpZHRoPSI3Ni44MiIgaGVpZ2h0PSIyNC41NDYiLz4KCTxyZWN0IHg9IjI1MS43OCIgeT0iMzM1LjM3IiBzdHlsZT0iZmlsbDojRkVCQ0FDOyIgd2lkdGg9Ijc2LjgyIiBoZWlnaHQ9IjI0LjU0NiIvPgo8L2c+CjxyZWN0IHg9IjE5OS42NSIgeT0iNjIuOTYxIiBzdHlsZT0iZmlsbDojRkVEQTQ0OyIgd2lkdGg9IjM3LjA1IiBoZWlnaHQ9IjM1Ni45NSIvPgo8cmVjdCB4PSIxMTQuMTMiIHk9IjEzMi4zMSIgc3R5bGU9ImZpbGw6I0UwRTBFMjsiIHdpZHRoPSI1NS41MSIgaGVpZ2h0PSIyODcuNzIiLz4KPHBhdGggZD0iTTQ4Ny42Miw0MjkuNjVWMTQ4LjE4MmgtNTEuMTA5VjgwLjQ0OUgzMzYuMjEzVjM0LjU2OGgtOTIuMDMxdjIwLjc4NUgxNzYuOTZ2NjkuMzU1aC03MC4xNTFWODAuNDQ5SDI0LjM4VjQyOS42NUgwdjQ3Ljc4MiAgaDUxMlY0MjkuNjVINDg3LjYyeiBNNDcyLjQxMiwxNjMuMzl2MjY2LjI2aC0zNS45MDFWMTYzLjM5SDQ3Mi40MTJ6IE00MjEuMzAzLDk1LjY1N3Y1Mi41MjVWNDI5LjY1aC04NS4wOXYtNjIuMTE5di0zOS43NTlWMTUxLjY1MyAgdi0zOS43NTlWOTUuNjU3SDQyMS4zMDN6IE0yNTkuMzg5LDM2Ny41MzFoNjEuNjE1djYyLjExOWgtNjEuNjE1VjM2Ny41MzF6IE0yNTkuMzg5LDEyNy4xMDNoNjEuNjE1djkuMzQzaC02MS42MTVWMTI3LjEwM3ogICBNMzIxLjAwNSwxNTEuNjUzdjE3Ni4xMTlIMjU5LjM5VjE1MS42NTNIMzIxLjAwNXogTTI1OS4zODksMzQyLjk4aDYxLjYxNXY5LjM0M2gtNjEuNjE1VjM0Mi45OHogTTI1OS4zODksNDkuNzc2aDYxLjYxNXYzMC42NzMgIHYzMS40NDZoLTYxLjYxNVY1NS4zNTNWNDkuNzc2eiBNMTkyLjE2OCw3MC41NjFoNTIuMDEzdjQxLjMzNHYzOS43NTl2MTc2LjExOXYzOS43NTl2NjIuMTE5aC01Mi4wMTNWMTI0LjcwOFY3MC41NjF6ICAgTTE3Ni45Niw0MjkuNjVoLTExLjk3VjEzOS45MTZoMTEuOTcxTDE3Ni45Niw0MjkuNjVMMTc2Ljk2LDQyOS42NXogTTEzMy45ODcsNDI5LjY1VjEzOS45MTZoMTUuNzk1VjQyOS42NUgxMzMuOTg3eiAgIE0xMTguNzc5LDEzOS45MTZWNDI5LjY1aC0xMS45NzFWMTM5LjkxNkgxMTguNzc5eiBNOTEuNjAxLDM0Mi41NjFIMzkuNTg4VjE4My4zNzRoNTIuMDEzVjM0Mi41NjF6IE05MS42MDEsOTUuNjU3djI5LjA1MXY0My40NTggIEgzOS41ODhWOTUuNjU3SDkxLjYwMXogTTM5LjU4OCwzNTcuNzY5aDUyLjAxM3Y3MS44ODFIMzkuNTg4VjM1Ny43Njl6IE00OTYuNzkyLDQ2Mi4yMjRIMTUuMjA4di0xNy4zNjZoOS4xNzJoNjcuMjIxaDE1LjIwOGg3MC4xNTEgIGgxNS4yMDhoNTIuMDEzaDE1LjIwOGg2MS42MTVoMTUuMjA4aDg1LjA5aDE1LjIwOGg1MS4xMDloOS4xNzJMNDk2Ljc5Miw0NjIuMjI0TDQ5Ni43OTIsNDYyLjIyNHoiLz4KPHBhdGggZD0iTTM3OC43NTgsMjMzLjUzNWMxOS43MDgsMCwzNS43NDQtMTYuMDM0LDM1Ljc0NC0zNS43NDRzLTE2LjAzNC0zNS43NDQtMzUuNzQ0LTM1Ljc0NHMtMzUuNzQ0LDE2LjAzNC0zNS43NDQsMzUuNzQ0ICBTMzU5LjA1LDIzMy41MzUsMzc4Ljc1OCwyMzMuNTM1eiBNMzc4Ljc1OCwxNzcuMjU2YzExLjMyNCwwLDIwLjUzNiw5LjIxMiwyMC41MzYsMjAuNTM2cy05LjIxMiwyMC41MzYtMjAuNTM2LDIwLjUzNiAgcy0yMC41MzYtOS4yMTItMjAuNTM2LTIwLjUzNlMzNjcuNDM1LDE3Ny4yNTYsMzc4Ljc1OCwxNzcuMjU2eiIvPgo8cmVjdCB4PSIzNTAuNjEiIHk9IjI4OC45MiIgd2lkdGg9IjU2LjI4IiBoZWlnaHQ9IjE1LjIwOCIvPgo8cmVjdCB4PSIzNTAuNjEiIHk9IjMxNC44MiIgd2lkdGg9IjU2LjI4IiBoZWlnaHQ9IjE1LjIwOCIvPgo8cmVjdCB4PSIzNTAuNjEiIHk9IjM0MC43NCIgd2lkdGg9IjU2LjI4IiBoZWlnaHQ9IjE1LjIwOCIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" style="max-height: 150px; height:150px; object-fit: contain; width:58%;" alt="Sorry. No image found."/>
                                                    <hr>
                                                    <div class="card-body mx-n3">
                                                        <h6 class="card-title mb-0">Erstes Buch!</h6>
                                                        <span class="card-subtitle"><small>Das Buch "<?php global $current_medium; echo $current_medium['title']; ?>" ist das erste der Bibliothek!</small></span>
                                                    </div>
                                                <?php elseif($position_medium == "last"): ?>
                                                    <img class="ml-4" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8cmVjdCB4PSI3LjYwNCIgeT0iNDM3LjI2IiBzdHlsZT0iZmlsbDojNUU0QzM2OyIgd2lkdGg9IjQ5Ni43OSIgaGVpZ2h0PSIzMi41NzUiLz4KPHJlY3QgeD0iNy42MDQiIHk9IjQzNy4yNiIgc3R5bGU9ImZpbGw6IzgwNjc0OTsiIHdpZHRoPSI0OTYuNzkiIGhlaWdodD0iMTYuMzY0Ii8+CjxyZWN0IHg9Ijk5LjIxIiB5PSIxMzIuMzEiIHN0eWxlPSJmaWxsOiNBQ0FCQjE7IiB3aWR0aD0iODUuMzYiIGhlaWdodD0iMzA0Ljk0Ii8+CjxyZWN0IHg9IjMxLjk4NCIgeT0iODguMDUiIHN0eWxlPSJmaWxsOiNBMjU4Q0I7IiB3aWR0aD0iNjcuMjIiIGhlaWdodD0iMzQ5LjIiLz4KPGc+Cgk8cG9seWdvbiBzdHlsZT0iZmlsbDojNkYyQzkzOyIgcG9pbnRzPSI4Mi4zOTEsMzUwLjE2NSA4Mi4zOTEsNDE5LjkxMSAzMS45ODQsNDE5LjkxMSAzMS45ODQsNDM3LjI1NCA4Mi4zOTEsNDM3LjI1NCAgICA4Mi4zOTEsNDM3Ljg4MyA5OS4yMDQsNDM3Ljg4MyA5OS4yMDQsNDM3LjI1NCA5OS4yMDQsNDE5LjkxMSA5OS4yMDQsMzUwLjE2NSAgIi8+Cgk8cmVjdCB4PSIzMS45ODQiIHk9IjE3NS43NyIgc3R5bGU9ImZpbGw6IzZGMkM5MzsiIHdpZHRoPSI2Ny4yMiIgaGVpZ2h0PSIxNzQuMzkiLz4KCTxyZWN0IHg9IjgyLjQiIHk9Ijg4LjA1IiBzdHlsZT0iZmlsbDojNkYyQzkzOyIgd2lkdGg9IjE2LjgxMyIgaGVpZ2h0PSI4Ny43MiIvPgo8L2c+CjxyZWN0IHg9IjgyLjQiIHk9IjE3NS43NyIgc3R5bGU9ImZpbGw6IzUxMjE2RDsiIHdpZHRoPSIxNi44MTMiIGhlaWdodD0iMTc3LjE0Ii8+CjxyZWN0IHg9IjE4NC41NiIgeT0iNjIuOTYxIiBzdHlsZT0iZmlsbDojRkVDRTAwOyIgd2lkdGg9IjY3LjIyIiBoZWlnaHQ9IjM3NC4zIi8+CjxyZWN0IHg9IjI1MS43OCIgeT0iNDIuMTciIHN0eWxlPSJmaWxsOiNBRDIyMDE7IiB3aWR0aD0iNzYuODIiIGhlaWdodD0iMzk1LjA4Ii8+CjxyZWN0IHg9IjMyOC42MSIgeT0iODguMDUiIHN0eWxlPSJmaWxsOiMwMTZFRjE7IiB3aWR0aD0iMTAwLjMiIGhlaWdodD0iMzQ5LjIiLz4KPHJlY3QgeD0iNDI4LjkiIHk9IjE1NS43OSIgc3R5bGU9ImZpbGw6IzlDREQwMzsiIHdpZHRoPSI1MS4xMSIgaGVpZ2h0PSIyODEuNDciLz4KPGc+Cgk8cmVjdCB4PSI0MjguOSIgeT0iNDIxLjI2IiBzdHlsZT0iZmlsbDojODZCQzA2OyIgd2lkdGg9IjUxLjExIiBoZWlnaHQ9IjE1Ljk5OCIvPgoJPHJlY3QgeD0iNDI4LjkiIHk9IjE1NS43OSIgc3R5bGU9ImZpbGw6Izg2QkMwNjsiIHdpZHRoPSIxNS41MiIgaGVpZ2h0PSIyODQuNDQiLz4KPC9nPgo8cmVjdCB4PSIzNDMuNSIgeT0iODcuNjIiIHN0eWxlPSJmaWxsOiMyNDg3RkY7IiB3aWR0aD0iNzAuNTIiIGhlaWdodD0iMzMyLjg0Ii8+CjxjaXJjbGUgc3R5bGU9ImZpbGw6I0JEREJGRjsiIGN4PSIzNzguNzYiIGN5PSIxOTcuNzkiIHI9IjI4LjE0Ii8+CjxyZWN0IHg9IjI2Ny42NSIgeT0iNDIuMTciIHN0eWxlPSJmaWxsOiNDRDJBMDE7IiB3aWR0aD0iNDUuMSIgaGVpZ2h0PSIzNzcuNDgiLz4KPGc+Cgk8cmVjdCB4PSIyNTEuNzgiIHk9IjExOS40OSIgc3R5bGU9ImZpbGw6I0ZFQkNBQzsiIHdpZHRoPSI3Ni44MiIgaGVpZ2h0PSIyNC41NDYiLz4KCTxyZWN0IHg9IjI1MS43OCIgeT0iMzM1LjM3IiBzdHlsZT0iZmlsbDojRkVCQ0FDOyIgd2lkdGg9Ijc2LjgyIiBoZWlnaHQ9IjI0LjU0NiIvPgo8L2c+CjxyZWN0IHg9IjE5OS42NSIgeT0iNjIuOTYxIiBzdHlsZT0iZmlsbDojRkVEQTQ0OyIgd2lkdGg9IjM3LjA1IiBoZWlnaHQ9IjM1Ni45NSIvPgo8cmVjdCB4PSIxMTQuMTMiIHk9IjEzMi4zMSIgc3R5bGU9ImZpbGw6I0UwRTBFMjsiIHdpZHRoPSI1NS41MSIgaGVpZ2h0PSIyODcuNzIiLz4KPHBhdGggZD0iTTQ4Ny42Miw0MjkuNjVWMTQ4LjE4MmgtNTEuMTA5VjgwLjQ0OUgzMzYuMjEzVjM0LjU2OGgtOTIuMDMxdjIwLjc4NUgxNzYuOTZ2NjkuMzU1aC03MC4xNTFWODAuNDQ5SDI0LjM4VjQyOS42NUgwdjQ3Ljc4MiAgaDUxMlY0MjkuNjVINDg3LjYyeiBNNDcyLjQxMiwxNjMuMzl2MjY2LjI2aC0zNS45MDFWMTYzLjM5SDQ3Mi40MTJ6IE00MjEuMzAzLDk1LjY1N3Y1Mi41MjVWNDI5LjY1aC04NS4wOXYtNjIuMTE5di0zOS43NTlWMTUxLjY1MyAgdi0zOS43NTlWOTUuNjU3SDQyMS4zMDN6IE0yNTkuMzg5LDM2Ny41MzFoNjEuNjE1djYyLjExOWgtNjEuNjE1VjM2Ny41MzF6IE0yNTkuMzg5LDEyNy4xMDNoNjEuNjE1djkuMzQzaC02MS42MTVWMTI3LjEwM3ogICBNMzIxLjAwNSwxNTEuNjUzdjE3Ni4xMTlIMjU5LjM5VjE1MS42NTNIMzIxLjAwNXogTTI1OS4zODksMzQyLjk4aDYxLjYxNXY5LjM0M2gtNjEuNjE1VjM0Mi45OHogTTI1OS4zODksNDkuNzc2aDYxLjYxNXYzMC42NzMgIHYzMS40NDZoLTYxLjYxNVY1NS4zNTNWNDkuNzc2eiBNMTkyLjE2OCw3MC41NjFoNTIuMDEzdjQxLjMzNHYzOS43NTl2MTc2LjExOXYzOS43NTl2NjIuMTE5aC01Mi4wMTNWMTI0LjcwOFY3MC41NjF6ICAgTTE3Ni45Niw0MjkuNjVoLTExLjk3VjEzOS45MTZoMTEuOTcxTDE3Ni45Niw0MjkuNjVMMTc2Ljk2LDQyOS42NXogTTEzMy45ODcsNDI5LjY1VjEzOS45MTZoMTUuNzk1VjQyOS42NUgxMzMuOTg3eiAgIE0xMTguNzc5LDEzOS45MTZWNDI5LjY1aC0xMS45NzFWMTM5LjkxNkgxMTguNzc5eiBNOTEuNjAxLDM0Mi41NjFIMzkuNTg4VjE4My4zNzRoNTIuMDEzVjM0Mi41NjF6IE05MS42MDEsOTUuNjU3djI5LjA1MXY0My40NTggIEgzOS41ODhWOTUuNjU3SDkxLjYwMXogTTM5LjU4OCwzNTcuNzY5aDUyLjAxM3Y3MS44ODFIMzkuNTg4VjM1Ny43Njl6IE00OTYuNzkyLDQ2Mi4yMjRIMTUuMjA4di0xNy4zNjZoOS4xNzJoNjcuMjIxaDE1LjIwOGg3MC4xNTEgIGgxNS4yMDhoNTIuMDEzaDE1LjIwOGg2MS42MTVoMTUuMjA4aDg1LjA5aDE1LjIwOGg1MS4xMDloOS4xNzJMNDk2Ljc5Miw0NjIuMjI0TDQ5Ni43OTIsNDYyLjIyNHoiLz4KPHBhdGggZD0iTTM3OC43NTgsMjMzLjUzNWMxOS43MDgsMCwzNS43NDQtMTYuMDM0LDM1Ljc0NC0zNS43NDRzLTE2LjAzNC0zNS43NDQtMzUuNzQ0LTM1Ljc0NHMtMzUuNzQ0LDE2LjAzNC0zNS43NDQsMzUuNzQ0ICBTMzU5LjA1LDIzMy41MzUsMzc4Ljc1OCwyMzMuNTM1eiBNMzc4Ljc1OCwxNzcuMjU2YzExLjMyNCwwLDIwLjUzNiw5LjIxMiwyMC41MzYsMjAuNTM2cy05LjIxMiwyMC41MzYtMjAuNTM2LDIwLjUzNiAgcy0yMC41MzYtOS4yMTItMjAuNTM2LTIwLjUzNlMzNjcuNDM1LDE3Ny4yNTYsMzc4Ljc1OCwxNzcuMjU2eiIvPgo8cmVjdCB4PSIzNTAuNjEiIHk9IjI4OC45MiIgd2lkdGg9IjU2LjI4IiBoZWlnaHQ9IjE1LjIwOCIvPgo8cmVjdCB4PSIzNTAuNjEiIHk9IjMxNC44MiIgd2lkdGg9IjU2LjI4IiBoZWlnaHQ9IjE1LjIwOCIvPgo8cmVjdCB4PSIzNTAuNjEiIHk9IjM0MC43NCIgd2lkdGg9IjU2LjI4IiBoZWlnaHQ9IjE1LjIwOCIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" style="max-height: 150px; height:150px; object-fit: contain; width:58%;" alt="Sorry. No image found."/>
                                                    <hr>
                                                    <div class="card-body mx-n3">
                                                        <h6 class="card-title mb-0">Letztes Buch!</h6>
                                                        <span class="card-subtitle"><small>Das Buch "<?php global $current_medium; echo $current_medium['title']; ?>" ist das letzte der Bibliothek!</small></span>
                                                    </div>
                                                <?php else: ?>
                                                    <a href="medium.php?id=<?php echo $position_medium['id']; ?>">
                                                        <?php if(@getimagesize("images/media/" . $position_medium['id'] . ".png")): ?>
                                                            <img src="<?php echo "images/media/" . $position_medium['id'] . ".png" ?>" style="max-height: 150px; height:150px; object-fit: contain; width:100%; max-width:300px;" alt="Sorry. No image found.">
                                                        <?php elseif(@getimagesize($position_medium['image'])): ?>
                                                            <img src="<?php echo $position_medium['image'] ?>" style="max-height: 150px; height:150px; object-fit: contain; width:100%; max-width:300px;" alt="Sorry. No image found.">
                                                        <?php else: ?>
                                                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" style="max-height: 150px; height:150px; object-fit: contain; width:58%;" alt="Sorry. No image found.">
                                                        <?php endif; ?>
                                                    </a>
                                                    <hr>
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-0"><?php echo $position_medium['title']; ?></h6>
                                                        <span class="card-subtitle"><small><?php echo $position_medium['main_category']; ?></small></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="title">Title</label>
                        <input class="col-md-10 form-control <?php if($empty_error && $title == ""){ echo 'alert-danger border border-danger';} ?>" value="<?php echo isset($title) ? $title : (isset($medium->volumeInfo->title) ? $medium->volumeInfo->title : ''); ?>" placeholder="Title" type="text" id="title" name="title">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="subtitle">Subtitle</label>
                        <input class="col-md-10 form-control" value="<?php echo isset($subtitle) ? $subtitle : (isset($medium->volumeInfo->subtitle) ? $medium->volumeInfo->subtitle : ''); ?>" placeholder="Subtitle" type="text" id="subtitle" name="subtitle">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="publisher">Publisher</label>
                        <input class="col-md-10 form-control <?php if($publisher_warning){ echo 'alert-warning border border-warning'; } ?>" value="<?php echo isset($publisher) ? $publisher : (isset($medium->volumeInfo->publisher) ? $medium->volumeInfo->publisher : ''); ?>" placeholder="Publisher" type="text" id="publisher" name="publisher">
                    </div>
                    <?php for($i = 0; $i < count($authors); $i++): ?>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="firstname">Firstname(s)</label>
                            <input class="col-md-10 form-control <?php if(($empty_error && $authors[$i]['author_firstname'] == "" && $authors[$i]['author_lastname'] == "") || ($empty_author_error && $authors[$i]['author_firstname'] == "" && $authors[$i]['author_lastname'] == "")){ echo 'alert-danger border border-danger';} elseif($author_warning){echo 'alert-warning border border-warning';} ?>" value="<?php echo $authors[$i]['author_firstname']; ?>" placeholder="Firstname(s)" type="text" id="firstname" name="firstnames[<?php echo $i; ?>]">
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="lastname">Lastname(s)</label>
                            <input class="col-md-10 form-control <?php if(($empty_error && $authors[$i]['author_lastname'] == "" && $authors[$i]['author_firstname'] == "") || ($empty_author_error && $authors[$i]['author_firstname'] == "" && $authors[$i]['author_lastname'] == "") || ($medium_index_error && $i == 0)){ echo 'alert-danger border border-danger';} elseif($author_warning){ echo 'alert-warning border border-warning';} elseif($i == 0){ echo 'alert-info border border-info';} ?>" value="<?php echo $authors[$i]['author_lastname']; ?>" placeholder="Lastname(s)" type="text" id="lastname" name="lastnames[<?php echo $i; ?>]">
                        </div>
                        <div class="form-group row justify-content-center">
                            <button type="submit" value="remove_author<?php echo $i; ?>" name="submit" class="mr-3" style="border: none; background: none;"><img class="mr-3" style="height:20px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxnPgoJCQk8cG9seWdvbiBwb2ludHM9IjM1My41NzQsMTc2LjUyNiAzMTMuNDk2LDE3NS4wNTYgMzA0LjgwNyw0MTIuMzQgMzQ0Ljg4NSw0MTMuODA0ICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cmVjdCB4PSIyMzUuOTQ4IiB5PSIxNzUuNzkxIiB3aWR0aD0iNDAuMTA0IiBoZWlnaHQ9IjIzNy4yODUiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBvbHlnb24gcG9pbnRzPSIyMDcuMTg2LDQxMi4zMzQgMTk4LjQ5NywxNzUuMDQ5IDE1OC40MTksMTc2LjUyIDE2Ny4xMDksNDEzLjgwNCAgICAiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBhdGggZD0iTTE3LjM3OSw3Ni44Njd2NDAuMTA0aDQxLjc4OUw5Mi4zMiw0OTMuNzA2QzkzLjIyOSw1MDQuMDU5LDEwMS44OTksNTEyLDExMi4yOTIsNTEyaDI4Ni43NCAgICAgYzEwLjM5NCwwLDE5LjA3LTcuOTQ3LDE5Ljk3Mi0xOC4zMDFsMzMuMTUzLTM3Ni43MjhoNDIuNDY0Vjc2Ljg2N0gxNy4zNzl6IE0zODAuNjY1LDQ3MS44OTZIMTMwLjY1NEw5OS40MjYsMTE2Ljk3MWgzMTIuNDc0ICAgICBMMzgwLjY2NSw0NzEuODk2eiIgZmlsbD0iIzAwMDAwMCIvPgoJCTwvZz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0zMjEuNTA0LDBIMTkwLjQ5NmMtMTguNDI4LDAtMzMuNDIsMTQuOTkyLTMzLjQyLDMzLjQydjYzLjQ5OWg0MC4xMDRWNDAuMTA0aDExNy42NHY1Ni44MTVoNDAuMTA0VjMzLjQyICAgIEMzNTQuOTI0LDE0Ljk5MiwzMzkuOTMyLDAsMzIxLjUwNCwweiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /><span><small class="mt-2 text-danger">Delete Author</small></span></button>
                        </div>
                    <?php endfor; ?>
                    <div class="form-group row justify-content-center">
                        <button type="submit" value="new_author" name="submit" class="mr-3" style="border: none; background: none;"><img class="mr-3" style="height:30px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1NTEuMTMgNTUxLjEzIiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNTUxLjEzIDU1MS4xMyIgd2lkdGg9IjUxMnB4Ij48cGF0aCBkPSJtMjc1LjU2NSAwYy0xNTEuOTQ0IDAtMjc1LjU2NSAxMjMuNjIxLTI3NS41NjUgMjc1LjU2NXMxMjMuNjIxIDI3NS41NjUgMjc1LjU2NSAyNzUuNTY1IDI3NS41NjUtMTIzLjYyMSAyNzUuNTY1LTI3NS41NjUtMTIzLjYyMS0yNzUuNTY1LTI3NS41NjUtMjc1LjU2NXptMCA1MTYuNjg1Yy0xMzIuOTU1IDAtMjQxLjExOS0xMDguMTY0LTI0MS4xMTktMjQxLjExOXMxMDguMTY0LTI0MS4xMiAyNDEuMTE5LTI0MS4xMiAyNDEuMTIgMTA4LjE2NCAyNDEuMTIgMjQxLjExOS0xMDguMTY1IDI0MS4xMi0yNDEuMTIgMjQxLjEyeiIgZmlsbD0iIzAwMDAwMCIvPjxwYXRoIGQ9Im0yOTIuNzg4IDEzNy43ODNoLTM0LjQ0NnYxMjAuNTZoLTEyMC41NnYzNC40NDZoMTIwLjU2djEyMC41NmgzNC40NDZ2LTEyMC41NmgxMjAuNTZ2LTM0LjQ0NmgtMTIwLjU2eiIgZmlsbD0iIzAwMDAwMCIvPjwvc3ZnPgo=" /><span><small class="mt-2">New Author</small></span></button>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="language">Language</label>
                        <select id="language" class="form-control col-md-10 <?php if($language_error){ echo 'alert-danger border border-danger'; } elseif($language_warning){ echo 'alert-warning border border-warning';} ?>" name="language">
                            <?php if($language == ""): ?>
                                <option value="" selected disabled hidden>Language</option>
                            <?php endif; ?>
                            <option value="en" <?php if($language == "en"){echo 'selected'; }?>>English</option>
                            <option value="de" <?php if($language == "de"){echo 'selected'; }?>>Deutsch</option>
                            <option value="es" <?php if($language == "es"){echo 'selected'; }?>>Español</option>
                            <option value="fr" <?php if($language == "fr"){echo 'selected'; }?>>Français</option>
                            <option value="other" <?php if($language == "other"){echo 'selected'; }?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="categories">Categories</label>
                        <input class="col-md-10 form-control <?php if($category_error){ echo 'alert-danger border border-danger'; }?>" value="<?php echo $categories_string; ?>" placeholder="Categories" type="text" id="categories" name="categories">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="ISBN_13">ISBN_13</label>
                        <input class="col-md-10 form-control <?php if($isbn13_error){ echo 'alert-danger border border-danger'; } ?>" value="<?php echo $isbn13; ?>" placeholder="ISBN_13" type="text" id="ISBN_13" name="ISBN_13">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="ISBN_10">ISBN_10</label>
                        <input class="col-md-10 form-control <?php if($isbn10_error){ echo 'alert-danger border border-danger'; } ?>" value="<?php echo $isbn10; ?>" placeholder="ISBN_10" type="text" id="ISBN_10" name="ISBN_10">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="description">Description</label>
                        <textarea class="col-md-10 form-control" name="description" id="description" rows="10" id="description" placeholder="Description"><?php echo $description ?></textarea>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="medium_group">Group</label>
                        <div class="col-md-10">
                            <div class="row justify-content-between">
                                <div class="col-md pl-0">
                                    <input class="form-control <?php if($medium_group_error){ echo 'alert-danger border border-danger';} elseif($medium_group_warning){echo 'alert-warning border border-warning';} else { echo '';} ?>" value="<?php echo $medium_group; ?>" placeholder="Group" type="text" id="medium_group" name="medium_group">
                                </div>
                                <div class="col-md pr-0">
                                    <input class="form-control <?php echo $volume_error ? 'alert-danger border border-danger' : ''; ?>" value="<?php echo $volume; ?>" placeholder="Volume" type="text" id="volume" name="volume">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="rating">Rating</label>
                        <div class="col-md-10">
                            <div class="row justify-content-between">
                                <div class="col-md pl-0">
                                    <input class="form-control <?php if($rating_error){ echo 'alert-danger border border-danger';} else { echo '';} ?>" value="<?php echo $rating; ?>" placeholder="Rating" type="text" id="rating" name="rating">
                                </div>
                                <div class="col-md pr-0">
                                    <input class="form-control <?php echo $ratings_count_error ? 'alert-danger border border-danger' : ''; ?>" value="<?php echo $ratings_count; ?>" placeholder="Number of ratings" type="text" id="ratings_count" name="ratings_count">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(!$already_exists): ?>
                        <input type="submit" value="Add Medium" name="submit" class="btn btn-success float-right">
                    <?php else: ?>
                        <a href="medium.php?id=86" class="float-right"><button class="btn btn-outline-danger">Already exists in the database!</button></a>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                    <?php if(isset($msg)): ?>
                        <p class="float-right mt-1 <?php echo $msg_class == "success" ? 'text-success' : 'text-danger'; ?>"><small><?php echo $msg; ?></small></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
    </div>

    <?php include('inc/footer.php'); ?>