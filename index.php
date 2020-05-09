<?php
    require('config/config.php');
    require('config/db.php');



    function updateColor($medium){
        $today = date_create(date("d.m.Y"));
        if(date_diff($today, date_create($medium["lent_until"]))->days > 10){
            return 'primary';
        } elseif(date_diff($today, date_create($medium["lent_until"]))->days > 5) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    //define query (sql code)
    $query = 'SELECT * FROM media WHERE user_id = -1 ORDER BY rating DESC LIMIT 3';

    //get data
    $result = mysqli_query($conn, $query);

    //format data
    $recommended_media = mysqli_fetch_all($result, MYSQLI_ASSOC);


    foreach($recommended_media as &$recommended_medium){
        $authors = json_decode($recommended_medium['authors']);
        $authors_string = "";

        foreach($authors->authors as $author){
            if($authors_string != ""){
                $authors_string .= ", " . $author->author_firstname . " <strong class=\"text-primary font-weight-bold\">" . $author->author_lastname . "</strong>";
            } else {
                $authors_string .= $author->author_firstname . " <strong class=\"text-primary font-weight-bold\">" . $author->author_lastname . "</strong>";
            }
            
        }

        $recommended_medium['authors_string'] = $authors_string;
    }


    mysqli_free_result($result);

    $warning = "";

    if(isset($_SESSION['current_user'])){
        $id = $_SESSION['current_user']['id'];


        $query = "SELECT title, lent_until, id FROM media WHERE user_id = '$id'";
        $sql_result = mysqli_query($conn, $query);
        $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

        $critical = 0;
        $critical_title = "";
        $warning_link = "";

        foreach($sql_fetch as $medium){
            if(updateColor($medium) == 'danger'){
                $critical += 1;
                $critical_title = $medium['title'];
                $critical_lent_until = date_format(date_create($medium['lent_until']), "d.m.Y");
                $critical_id = $medium['id'];
            }
        }

        if($critical == 1){
            $warning = "Du musst das Medium \"" . $critical_title . "\" bis " . $critical_lent_until . " zurückgeben!";
            $warning_link = "medium.php?id=" . $critical_id;
        } elseif ($critical > 1){
            $warning = "Du musst " . $critical . " Medien schon bald zurückgeben!";
        }
    }

?>

    <?php include('inc/header.php'); ?>
    <?php require('inc/navbar.php'); ?>


    <div class="container">
        <?php if($warning != ""): ?>
            <div class="card mb-3 d-block" style="height:45px; width:auto; border: 1px solid red;">
                <img class="d-inline py-2 px-2 mx-auto" style="height:100%;" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9IjUxMiI+PGc+PHBhdGggZD0ibTI3Ny4zMzIwMzEgMzg0YzAgMTEuNzgxMjUtOS41NTA3ODEgMjEuMzMyMDMxLTIxLjMzMjAzMSAyMS4zMzIwMzFzLTIxLjMzMjAzMS05LjU1MDc4MS0yMS4zMzIwMzEtMjEuMzMyMDMxIDkuNTUwNzgxLTIxLjMzMjAzMSAyMS4zMzIwMzEtMjEuMzMyMDMxIDIxLjMzMjAzMSA5LjU1MDc4MSAyMS4zMzIwMzEgMjEuMzMyMDMxem0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiIHN0eWxlPSJmaWxsOiNDMTIwMjAiPjwvcGF0aD48cGF0aCBkPSJtMjU2IDMyMGMtOC44MzIwMzEgMC0xNi03LjE2Nzk2OS0xNi0xNnYtMTgxLjMzMjAzMWMwLTguODMyMDMxIDcuMTY3OTY5LTE2IDE2LTE2czE2IDcuMTY3OTY5IDE2IDE2djE4MS4zMzIwMzFjMCA4LjgzMjAzMS03LjE2Nzk2OSAxNi0xNiAxNnptMCAwIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIiBzdHlsZT0iZmlsbDojQzEyMDIwIj48L3BhdGg+PHBhdGggZD0ibTI1NiA1MTJjLTE0MS4xNjQwNjIgMC0yNTYtMTE0LjgzNTkzOC0yNTYtMjU2czExNC44MzU5MzgtMjU2IDI1Ni0yNTYgMjU2IDExNC44MzU5MzggMjU2IDI1Ni0xMTQuODM1OTM4IDI1Ni0yNTYgMjU2em0wLTQ4MGMtMTIzLjUxOTUzMSAwLTIyNCAxMDAuNDgwNDY5LTIyNCAyMjRzMTAwLjQ4MDQ2OSAyMjQgMjI0IDIyNCAyMjQtMTAwLjQ4MDQ2OSAyMjQtMjI0LTEwMC40ODA0NjktMjI0LTIyNC0yMjR6bTAgMCIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBkYXRhLW9sZF9jb2xvcj0iIzAwMDAwMCIgc3R5bGU9ImZpbGw6I0MxMjAyMCI+PC9wYXRoPjwvZz4gPC9zdmc+" />
                <span class="d-inline py-0 mr-3">Warning!</span>
                <span class="d-inline" style="font-size:13px;"><?php echo $warning; ?></span>
                <a href="<?php echo $warning_link == "" ? 'account/profile.php' : $warning_link; ?>" class="stretched-link">
                    <img class="d-inline py-2 px-2 mt-1 float-right" style="height:80%;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMi4wMDIgNTEyLjAwMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyLjAwMiA1MTIuMDAyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzg4LjQyNSwyNDEuOTUxTDE1MS42MDksNS43OWMtNy43NTktNy43MzMtMjAuMzIxLTcuNzItMjguMDY3LDAuMDRjLTcuNzQsNy43NTktNy43MiwyMC4zMjgsMC4wNCwyOC4wNjdsMjIyLjcyLDIyMi4xMDUgICAgTDEyMy41NzQsNDc4LjEwNmMtNy43NTksNy43NC03Ljc3OSwyMC4zMDEtMC4wNCwyOC4wNjFjMy44ODMsMy44OSw4Ljk3LDUuODM1LDE0LjA1Nyw1LjgzNWM1LjA3NCwwLDEwLjE0MS0xLjkzMiwxNC4wMTctNS43OTUgICAgbDIzNi44MTctMjM2LjE1NWMzLjczNy0zLjcxOCw1LjgzNC04Ljc3OCw1LjgzNC0xNC4wNVMzOTIuMTU2LDI0NS42NzYsMzg4LjQyNSwyNDEuOTUxeiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" />
                </a>
            </div>
        <?php endif; ?>


            <div class="jumbotron">
                <h1>Heim&shy;bibliothek - Wie geht das?</h1>
                <p class="lead">Wilkommen zur Bibliothek der Familie Proenca</p>
                <hr class="my-4">
                <p>Hier kannst du Bücher der Familie Proenca ausleihen und im Katalog von tausenden Medien blättern.</p>
                <div class="row" style="max-width:400px;">
                    <div class="col-sm">
                        <a href="account/sign_up.php" class="btn btn-primary mr-3 mb-3 mb-sm-0" role="button" style="width:150px;">Leg los!</a>
                    </div>
                    <div class="col-sm">
                        <a href="learn.php" class="btn btn-outline-info" role="button" style="width:150px;">Lerne mehr!</a>
                    </div>
                </div>
            </div>
            <div class="row mb-3" style="height:auto;">
                <?php foreach($recommended_media as &$recommended_medium) : ?>
                    <div class="col-lg">
                        <div class="card overflow-hidden">
                            <div style="height:350px;">
                                <?php if(@getimagesize("images/media/" . $recommended_medium["id"] . ".png")): ?>
                                    <img src="images/media/<?php echo $recommended_medium["id"]; ?>.png" alt="Sorry. No image available" class="update_image card-img-top img-thumbnail rounded py-2" style="height: 100%; object-fit: contain;">
                                <?php elseif($recommended_medium['image'] != ""): ?>
                                    <img src="<?php echo $recommended_medium['image']; ?>" alt="Sorry. No image available" class="card-img-top img-thumbnail rounded py-2" style="height: 100%; object-fit: contain;">
                                <?php else: ?>
                                    <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" alt="Sorry. No image available" class="card-img-top img-thumbnail rounded py-2" style="height: 100%; object-fit: contain;">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-0 custom-overflow" style="height:20px; -webkit-line-clamp: 1;"><?php echo $recommended_medium["title"]; ?></h5>
                                <p class="card-subtitle mb-2 py-1 custom-overflow" style="height:40px; -webkit-line-clamp: 1;"><?php echo $recommended_medium["subtitle"]; ?></p>
                                <p class="card-subtitle custom-overflow" style="height:40px; -webkit-line-clamp: 2;">von <?php echo $recommended_medium["authors_string"]; ?></p>
                                <hr class="py-2 mb-0">
                                <p class="card-text custom-overflow" style="font-size:12px;"><?php echo $recommended_medium["description"]; ?></p>
                                <a href="<?php echo 'medium.php?id=' . $recommended_medium["id"]; ?>" class="btn btn-primary btn-sm float-right stretched-link mb-2">Zeig mir mehr!</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
    </div>
    

    <?php include('inc/footer.php'); ?>