<?php
    require('config/config.php');
    require('config/db.php');

    if(isset($_GET["id"])){
        $id = $_GET["id"];
    } else {
        echo "id is not set";
    }

    //define query (sql code)
    $query = 'SELECT * FROM media WHERE id = ' . $id;
    //get data
    $result = mysqli_query($conn, $query);
    //format data
    $medium = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    $authors_string = "";
    $medium['authors_string'] = "";
    $authors = json_decode($medium['authors']);
    $author_count = 0;
    foreach($authors->authors as $author){
        $author_count += 1;
        if($author_count == 1){
            $authors_string .= $author->author_firstname . " <strong class=\"text-primary\">" . $author->author_lastname . "</strong>";
        } else {
            $authors_string .= ", " . $author->author_firstname . " <strong class=\"text-primary\">" . $author->author_lastname . "</strong>";
        }
        
    }
    $medium['authors_string'] = $authors_string;


    $isbns = json_decode($medium['isbns'], TRUE);

    $categories = json_decode($medium['categories']);
    $categories_string = "";
    for($i = 0; $i < count($categories->categories); $i++){
        if($i != 0){
            $categories_string .= ", ";
        }
        $categories_string .= $categories->categories[$i];
    }
    $medium['categories_string'] = $categories_string;


    //$medium["lent_until"] -> DateTime
    //date() : returns String
    //date_create(): returns DateTime
    //date_interval_create_from_date_string(): returns DateTimeInterval
    //date_format(): returns String
    //date_diff(): returns DateInterval

    $today = date_create(date("d.m.Y"));
    $id = $medium["id"];
    $medium["lent_until"] = date_create($medium["lent_until"]);
    $date_class = "primary";


    function toString($date, $format = "eu"){
        if($format == "us"){
            return date_format($date, "Y-m-d");
        } else {
            return date_format($date, "d.m.Y");
        }
    }

    function toFormat($string, $format = "eu"){
        $date = date_create($string);
        if($format == "us"){
            return toString($date, "us");
        } else {
            return toString($date);
        }
    }

    function stringAdd($string, $amount = "1 month", $toString = TRUE){
        if($toString){
            return toString(date_add(date_create($string), date_interval_create_from_date_string($amount)));
        } else {
            return date_add(date_create($string), date_interval_create_from_date_string($amount));
        }
        
    }

    

    //update status color
    function updateColor(){
        global $medium, $date_class, $today;
        if(date_diff($today, $medium["lent_until"])->days > 10){
            $date_class = 'primary';
        } elseif(date_diff($today, $medium["lent_until"])->days > 5) {
            $date_class = 'warning';
        } else {
            $date_class = 'danger';
        }

    }

    function updateSql(){
        global $conn, $medium, $id;
        $extended = $medium["extended"];
        $lent_until = toString($medium["lent_until"], "us");
        $user_id = $medium["user_id"];
        $query = "UPDATE media SET extended = '$extended', lent_until = '$lent_until', user_id = '$user_id' WHERE id ='$id'";
        if(mysqli_query($conn, $query)){
            return TRUE;
        } else {
            return FALSE;
        }
    }




    if(isset($_POST["lend"])){
        $temp = clone $today;
        $medium["lent_until"] = date_add($temp, date_interval_create_from_date_string("1 month"));
        $medium["user_id"] = $_SESSION['current_user']['id'];
        updateColor();
        updateSql();
        new_log("Medium lent: " . $medium['title'], array("medium" => $medium, "user" => $_SESSION['current_user']));
    }



    if(isset($_POST["extend"])){
        $medium["lent_until"] = date_add($medium["lent_until"], date_interval_create_from_date_string("1 month"));
        $medium["extended"] = strval(((int)$medium["extended"]) + 1);
        updateColor();
        updateSql();
        new_log("Medium extended: " . $medium['title'], array("medium" => $medium, "user" => $_SESSION['current_user']));
    }

    //<span class="font-weight-bold color-black"><?php echo $medium["author_lastname"];
    updateColor();

    if(isset($_SESSION['current_user'])){
        $user_id = $_SESSION['current_user']['id'];
        $query = "SELECT * FROM ratings WHERE user_id = '$user_id' AND medium_id = '$id'";
        $sql_result = mysqli_query($conn, $query);
        $sql_fetch = mysqli_fetch_assoc($sql_result);
    }
    

?>

    <?php include('inc/header.php'); ?>
    <?php require('inc/navbar.php'); ?>


    <div class="container">
        <h1><span><a class="stretched_link" href="javascript:history.go(-1)"><img class="go-back-arrow py-2 align-center mb-2" style="height:35px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ0My41MiA0NDMuNTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ0My41MiA0NDMuNTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0xNDMuNDkyLDIyMS44NjNMMzM2LjIyNiwyOS4xMjljNi42NjMtNi42NjQsNi42NjMtMTcuNDY4LDAtMjQuMTMyYy02LjY2NS02LjY2Mi0xNy40NjgtNi42NjItMjQuMTMyLDBsLTIwNC44LDIwNC44ICAgIGMtNi42NjIsNi42NjQtNi42NjIsMTcuNDY4LDAsMjQuMTMybDIwNC44LDIwNC44YzYuNzgsNi41NDgsMTcuNTg0LDYuMzYsMjQuMTMyLTAuNDJjNi4zODctNi42MTQsNi4zODctMTcuMDk5LDAtMjMuNzEyICAgIEwxNDMuNDkyLDIyMS44NjN6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></span><?php echo ' ' . $medium["title"]; ?></h1>
        <div class="row">
            <div class="col-lg-4">
                <?php if(@getimagesize("images/media/" . $medium["id"] . ".png")): ?>
                    <img class="update_image" src="images/media/<?php echo $medium["id"]; ?>.png" style="width:58%;" alt="Sorry. No image found.">
                <?php elseif($medium['image'] != ""): ?>
                    <img src="<?php echo $medium['image']; ?>" style="width:58%; max-width:200px;" alt="Sorry. No image found.">
                <?php else: ?>
                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" style="width:58%;" alt="Sorry. No image found.">
                <?php endif; ?>
                <div class="w-100"></div>
                <?php if(isset($_SESSION['current_user'])): ?>
                    <div class="border border-warning rounded mt-3">
                        <div class="pt-3 pl-3">
                            <h6>Lass deine Meinung da!</h6>
                            <fieldset class="rating mt-1 ml-n1">
                                <input <?php echo isset($sql_fetch['rating']) ? ($sql_fetch['rating'] == '5' ? 'checked ' : '') : ''; ?>onchange="runRating(5, <?php echo $medium['id']; ?>)" type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Rocks!">5 stars</label>
                                <input <?php echo isset($sql_fetch['rating']) ? ($sql_fetch['rating'] == '4' ? 'checked ' : '') : ''; ?>onchange="runRating(4, <?php echo $medium['id']; ?>)" type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Pretty good">4 stars</label>
                                <input <?php echo isset($sql_fetch['rating']) ? ($sql_fetch['rating'] == '3' ? 'checked ' : '') : ''; ?>onchange="runRating(3, <?php echo $medium['id']; ?>)" type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Meh">3 stars</label>
                                <input <?php echo isset($sql_fetch['rating']) ? ($sql_fetch['rating'] == '2' ? 'checked ' : '') : ''; ?>onchange="runRating(2, <?php echo $medium['id']; ?>)" type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Kinda bad">2 stars</label>
                                <input <?php echo isset($sql_fetch['rating']) ? ($sql_fetch['rating'] == '1' ? 'checked ' : '') : ''; ?>onchange="runRating(1, <?php echo $medium['id']; ?>)" type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Sucks big time">1 star</label>
                            </fieldset>
                            <div class="clearfix"></div>
                            <hr class="mr-3">
                            <p>Durschnitt: <span id="new_rating_number"><?php echo $medium['rating']; ?></span> <span style="color: #ea0; font-size: 20px;">★</span></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="border border-warning rounded mt-3">
                        <div class="pt-3 pl-3">
                            <h6 class="pr-3">Melde dich <a href="login.php">hier</a> an um das Buch zu bewerten!</h6>
                            <hr class="mr-3">
                            <p>Durschnitt: <span id="new_rating_number"><?php echo $medium['rating']; ?></span> <span style="color: #ea0; font-size: 20px;">★</span></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-8 table-responsive-lg">
                <table class="table table-sm">
                    <tr>
                        <th scope="row" class="pr-3">Titel</th>
                        <td><?php echo $medium["title"]; ?></td>
                    </tr>
                    <tr>
                        <th scope="row" class="pr-3">Untertitel</th>
                        <td><?php echo $medium["subtitle"]; ?></td>
                    </tr>
                    <tr>
                        <th scope="row" class="pr-3"><?php echo $author_count > 1 ? 'Autor / Innen' : 'Autor / In'; ?></th>
                        <td><?php echo $medium["authors_string"]; ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row" class="pr-3">Sprache</th>
                        <td><?php
                        switch ($medium['language']) {
                            case 'de':
                                echo 'Deutsch';
                                break;
                            case 'fr':
                                echo 'Français';
                                break;
                            case 'en':
                                echo 'English';
                                break;
                            case 'es':
                                echo 'Español';
                                break;
                            case 'other':
                                echo 'Andere';
                                break;
                            default:
                                echo 'Not set';
                                break;
                        }
                        ?></td>
                    </tr>
                    <tr>
                        <th scope="row" class="pr-3">Verlag</th>
                        <td><?php echo $medium["publisher"] . ' '; ?></td>
                    </tr>
                    <?php foreach($isbns['isbns'] as $isbn): ?>
                        <tr>
                            <th scope="row" class="pr-3"><?php echo $isbn['type']; ?></th>
                            <td><?php echo $isbn['identifier']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th scope="row" class="pr-3">Kategorien</th>
                        <td><?php echo $medium['categories_string']; ?></td>
                    </tr>
                    <tr class="">
                        <th scope="row" class="pr-3">Beschreibung</th>
                        <td><?php echo $medium["description"]; ?></td>
                    </tr>
                    <?php if($medium['medium_group'] != "-1"): ?>
                        <tr>
                            <th scope="row" class="pr-3">Reihe</th>
                            <td><?php echo $medium["volume"] . ". Band in der Reihe <a href=\"media.php?q=" . $medium['medium_group'] . "&group=true\"><strong class=\"text-primary\">" . $medium['medium_group'] . "</strong></a>"; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th scope="row" class="pr-3">ID</th>
                        <td><?php echo $medium["id"]; ?></td>
                    </tr>
                </table>
                <?php if(isset($_SESSION['current_user'])): ?>
                    <?php if($medium["user_id"] == -1): ?>
                        <form action="<?php echo $_SERVER["PHP_SELF"] . "?id=" . $id; ?>" method="POST">
                            <input class="btn btn-primary float-right" type="submit" name="lend" value="Ausleihen für 1 Monat">
                        </form>
                    <?php elseif($medium["user_id"] == $_SESSION['current_user']['id']): ?>
                        <p class="btn <?php if($date_class == "primary"){echo 'btn-outline-success'; } else {echo "btn-outline-" . $date_class; } ?> float-right">In deinem Besitz<br><span style="font-size: 12px;">bis <?php echo toString($medium["lent_until"]);?></span></p>
                        <div class="clearfix"></div>
                        <?php if((int)$medium["extended"] < 4): ?>
                            <form action="<?php echo $_SERVER["PHP_SELF"] . "?id=" . $id; ?>" method="POST">
                                <input type="submit" class="float-right mt-n4 mr-n4 btn btn-link" name="extend" value="Verlängern">
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="btn btn-outline-danger float-right">Nicht verfügbar<br><span class="text-primary" style="font-size: 12px;">bis <?php echo toString($medium["lent_until"]); ?></span></p>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="account/login.php" class="btn btn-outline-info float-right">Anmelden<br><span style="font-size: 10px;">um den Status des Mediums zu sehen</span></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    


    

    <?php include('inc/footer.php'); ?>