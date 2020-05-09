<?php
    require('config/config.php');
    require('config/db.php');

    //rating
    $rating = $_GET['rating'];

    //medium_id
    $medium_id = $_GET['medium_id'];

    //user_id
    $user_id = $_SESSION['current_user']['id'];


    //check if insert or update
    $query = "SELECT * FROM ratings WHERE user_id = $user_id AND medium_id = $medium_id";
    $sql_result = mysqli_query($conn, $query);
    $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

    if(isset($sql_fetch[0])){
        $old_rating = $sql_fetch[0]['rating'];
    }


    //insert or update
    if(count($sql_fetch) == 0){
        //insert
        $query = "INSERT INTO ratings (user_id, medium_id, rating) VALUES ('$user_id', '$medium_id', '$rating')";
    } elseif(count($sql_fetch) == 1){
        //update
        $query = "UPDATE ratings SET rating = '$rating' WHERE user_id = '$user_id' AND medium_id = '$medium_id'";
    } else {
        //error
        new_log("ERROR: There are " . count($sql_fetch) . " ratings with same user_id and medium_id", array("ratings" => $sql_fetch, "user_id" => $user_id, "medium_id" => $medium_id), "error[0]");
        echo 'error[0]';
    }

    //update / insert ratings
    if(!mysqli_query($conn, $query)){
        echo 'error[1]';
        new_log("ERROR: SQL error when trying to run rating.", array("query" => $query), "SQL error: " . mysqli_error($conn) . "\nerror[1]");
    }

    //update medium average
    //current rating data
    $query = "SELECT * FROM media WHERE id = '$medium_id'";
    $sql_result = mysqli_query($conn, $query);
    $rated_medium = mysqli_fetch_assoc($sql_result);

    //current_rating
    $current_rating = $rated_medium['rating'];

    //current_ratings_count
    $current_ratings_count = $rated_medium['ratings_count'];

    $new_rating = -1;

    if(count($sql_fetch) == 0){
        //hasn't rated yet
        $new_rating = ((($current_ratings_count * $current_rating) + $rating) / ($current_ratings_count + 1));
        $new_ratings_count = $current_ratings_count + 1;
    } elseif(count($sql_fetch) == 1){
        $new_rating = (($current_ratings_count * $current_rating) - $old_rating + $rating) / $current_ratings_count;
        $new_ratings_count = $current_ratings_count;
    } else {
        new_log("ERROR: There are " . count($sql_fetch) . " ratings with same user_id and medium_id [2]", array("ratings" => $sql_fetch, "user_id" => $user_id, "medium_id" => $medium_id), "This is the second log of the same error!\nerror[2]");
        echo 'error[2]';
    }
    //update average rating
    if($new_rating != -1){
        $query = "UPDATE media SET rating = '$new_rating', ratings_count = '$new_ratings_count' WHERE id = $medium_id";
        if(!mysqli_query($conn, $query)){
            echo 'error[3]';
            new_log("ERROR: SQL error when trying to run rating.", array("query" => $query), "SQL error: " . mysqli_error($conn). "\nerror[3]");
        }
    }

    echo json_encode(array("new_rating" => $new_rating));
?>