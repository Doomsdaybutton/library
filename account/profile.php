<?php
    require('../config/config.php');
    require('../config/db.php');

    $user_id = $_SESSION['current_user']['id'];
    $query = "SELECT * FROM media WHERE user_id = '$user_id'";
    $sql_result = mysqli_query($conn, $query);
    $sql_fetch = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

    if(count($sql_fetch) != 0){
        $nodelete = TRUE;
    } else {
        $nodelete = FALSE;
    }

    foreach($sql_fetch as &$medium){
        $authors = json_decode($medium['authors']);
        $authors_string = "";

        foreach($authors->authors as $author){
            if($authors_string != ""){
                $authors_string .= ", " . $author->author_firstname . ' ' . $author->author_lastname;
            } else {
                $authors_string .= $author->author_firstname . ' ' . $author->author_lastname;
            }
            
        }

        $medium['authors_string'] = $authors_string;
    }

?>

    <?php include('../inc/header.php'); ?>
    <?php include('../inc/navbar.php'); ?>

    <div class="container">
        <div class="card text-center">
            <div class="text-center">
                <?php if(empty($_SESSION['current_user']['profile_picture'])): ?>
                    <?php if(@getimagesize(ROOT_URL . 'images/users/' . $_SESSION['current_user']['id'] . '.png')): ?>
                        <img src="<?php echo ROOT_URL . 'images/users/' . $_SESSION['current_user']['id'] . '.png'; ?>" alt="" style="height: 150px; width: 150px; object-fit: cover;" class="rounded-circle mt-5 mb-3 update_image">
                        <a class="alert-link text-info d-block" href="profile_edit.php?profile_picture"><small>Profilbild ändern</small></a>    
                    <?php else: ?>
                        <img style="height: 150px; width: 150px; object-fit: cover;" class="rounded-circle mt-5 mb-3" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9IjUxMnB4Ij48cGF0aCBkPSJtNDM3LjAxOTUzMSA3NC45ODA0NjljLTQ4LjM1MTU2Mi00OC4zNTE1NjMtMTEyLjY0MDYyNS03NC45ODA0NjktMTgxLjAxOTUzMS03NC45ODA0NjktNjguMzgyODEyIDAtMTMyLjY2Nzk2OSAyNi42Mjg5MDYtMTgxLjAxOTUzMSA3NC45ODA0NjktNDguMzUxNTYzIDQ4LjM1MTU2Mi03NC45ODA0NjkgMTEyLjYzNjcxOS03NC45ODA0NjkgMTgxLjAxOTUzMSAwIDY4LjM3ODkwNiAyNi42Mjg5MDYgMTMyLjY2Nzk2OSA3NC45ODA0NjkgMTgxLjAxOTUzMSA0OC4zNTE1NjIgNDguMzUxNTYzIDExMi42MzY3MTkgNzQuOTgwNDY5IDE4MS4wMTk1MzEgNzQuOTgwNDY5IDY4LjM3ODkwNiAwIDEzMi42Njc5NjktMjYuNjI4OTA2IDE4MS4wMTk1MzEtNzQuOTgwNDY5IDQ4LjM1MTU2My00OC4zNTE1NjIgNzQuOTgwNDY5LTExMi42NDA2MjUgNzQuOTgwNDY5LTE4MS4wMTk1MzEgMC02OC4zODI4MTItMjYuNjI4OTA2LTEzMi42Njc5NjktNzQuOTgwNDY5LTE4MS4wMTk1MzF6bS0zMDguNjc5Njg3IDM2Ny40MDYyNWMxMC43MDcwMzEtNjEuNjQ4NDM4IDY0LjEyODkwNi0xMDcuMTIxMDk0IDEyNy42NjAxNTYtMTA3LjEyMTA5NCA2My41MzUxNTYgMCAxMTYuOTUzMTI1IDQ1LjQ3MjY1NiAxMjcuNjYwMTU2IDEwNy4xMjEwOTQtMzYuMzQ3NjU2IDI0Ljk3MjY1Ni04MC4zMjQyMTggMzkuNjEzMjgxLTEyNy42NjAxNTYgMzkuNjEzMjgxcy05MS4zMTI1LTE0LjY0MDYyNS0xMjcuNjYwMTU2LTM5LjYxMzI4MXptNDYuMjYxNzE4LTIxOC41MTk1MzFjMC00NC44ODY3MTkgMzYuNTE1NjI2LTgxLjM5ODQzOCA4MS4zOTg0MzgtODEuMzk4NDM4czgxLjM5ODQzOCAzNi41MTU2MjUgODEuMzk4NDM4IDgxLjM5ODQzOGMwIDQ0Ljg4MjgxMi0zNi41MTU2MjYgODEuMzk4NDM3LTgxLjM5ODQzOCA4MS4zOTg0MzdzLTgxLjM5ODQzOC0zNi41MTU2MjUtODEuMzk4NDM4LTgxLjM5ODQzN3ptMjM1LjA0Mjk2OSAxOTcuNzEwOTM3Yy04LjA3NDIxOS0yOC42OTkyMTktMjQuMTA5Mzc1LTU0LjczODI4MS00Ni41ODU5MzctNzUuMDc4MTI1LTEzLjc4OTA2My0xMi40ODA0NjktMjkuNDg0Mzc1LTIyLjMyODEyNS00Ni4zNTkzNzUtMjkuMjY5NTMxIDMwLjUtMTkuODk0NTMxIDUwLjcwMzEyNS01NC4zMTI1IDUwLjcwMzEyNS05My4zNjMyODEgMC02MS40MjU3ODItNDkuOTc2NTYzLTExMS4zOTg0MzgtMTExLjQwMjM0NC0xMTEuMzk4NDM4cy0xMTEuMzk4NDM4IDQ5Ljk3MjY1Ni0xMTEuMzk4NDM4IDExMS4zOTg0MzhjMCAzOS4wNTA3ODEgMjAuMjAzMTI2IDczLjQ2ODc1IDUwLjY5OTIxOSA5My4zNjMyODEtMTYuODcxMDkzIDYuOTQxNDA2LTMyLjU3MDMxMiAxNi43ODUxNTYtNDYuMzU5Mzc1IDI5LjI2NTYyNS0yMi40NzI2NTYgMjAuMzM5ODQ0LTM4LjUxMTcxOCA0Ni4zNzg5MDYtNDYuNTg1OTM3IDc1LjA3ODEyNS00NC40NzI2NTctNDEuMzAwNzgxLTcyLjM1NTQ2OS0xMDAuMjM4MjgxLTcyLjM1NTQ2OS0xNjUuNTc0MjE5IDAtMTI0LjYxNzE4OCAxMDEuMzgyODEyLTIyNiAyMjYtMjI2czIyNiAxMDEuMzgyODEyIDIyNiAyMjZjMCA2NS4zMzk4NDQtMjcuODgyODEyIDEyNC4yNzczNDQtNzIuMzU1NDY5IDE2NS41NzgxMjV6bTAgMCIgZmlsbD0iIzAwMDAwMCIvPjwvc3ZnPgo=" />
                        <a class="alert-link text-info d-block" href="profile_edit.php?profile_picture"><small>Profilbild festlegen</small></a>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="<?php echo $_SESSION['current_user']['profile_picture']; ?>" alt="" style="height: 150px; width: 150px; object-fit: cover;" class="rounded-circle mt-5 mb-3">
                    <a class="alert-link text-info d-block" href="profile_edit.php?profile_picture"><small>Profilbild ändern</small></a>
                <?php endif; ?>
            </div>
            <div class="card-header text-center bg-white">
                <h5><?php echo $_SESSION['current_user']['name'];?></h5>
            </div>
            <div class="class-body mt-3">
                <table class="table table-sm justify-content-center d-flex">
                    <tr>
                        <th class="pr-md-5 pl-5 pl-sm-1">Vorname</th>
                        <td class="px-md-5 px-0 px-sm-4"><?php echo $_SESSION['current_user']['firstname']; ?><a class="btn btn-secondary btn-sm rounded-circle float-right mr-5 mr-sm-0" href="profile_edit.php?firstname"><img class="float-right mt-n1" style="height:15px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDM4My45NDcgMzgzLjk0NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzgzLjk0NyAzODMuOTQ3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8Zz4KCQkJPHBvbHlnb24gcG9pbnRzPSIwLDMwMy45NDcgMCwzODMuOTQ3IDgwLDM4My45NDcgMzE2LjA1MywxNDcuODkzIDIzNi4wNTMsNjcuODkzICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cGF0aCBkPSJNMzc3LjcwNyw1Ni4wNTNMMzI3Ljg5Myw2LjI0Yy04LjMyLTguMzItMjEuODY3LTguMzItMzAuMTg3LDBsLTM5LjA0LDM5LjA0bDgwLDgwbDM5LjA0LTM5LjA0ICAgICBDMzg2LjAyNyw3Ny45MiwzODYuMDI3LDY0LjM3MywzNzcuNzA3LDU2LjA1M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></td>
                    </tr>
                    <tr>
                        <th class="pr-md-5 pl-5 pl-sm-1">Nachname</th>
                        <td class="px-md-5 px-0 px-sm-4"><?php echo $_SESSION['current_user']['lastname']; ?><a class="btn btn-secondary btn-sm rounded-circle float-right mr-5 mr-sm-0" href="profile_edit.php?lastname"><img class="float-right mt-n1" style="height:15px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDM4My45NDcgMzgzLjk0NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzgzLjk0NyAzODMuOTQ3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8Zz4KCQkJPHBvbHlnb24gcG9pbnRzPSIwLDMwMy45NDcgMCwzODMuOTQ3IDgwLDM4My45NDcgMzE2LjA1MywxNDcuODkzIDIzNi4wNTMsNjcuODkzICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cGF0aCBkPSJNMzc3LjcwNyw1Ni4wNTNMMzI3Ljg5Myw2LjI0Yy04LjMyLTguMzItMjEuODY3LTguMzItMzAuMTg3LDBsLTM5LjA0LDM5LjA0bDgwLDgwbDM5LjA0LTM5LjA0ICAgICBDMzg2LjAyNyw3Ny45MiwzODYuMDI3LDY0LjM3MywzNzcuNzA3LDU2LjA1M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></td>
                    </tr>
                    <tr>
                        <th class="pr-md-5 pl-5 pl-sm-1">Email</th>
                        <?php if(isset($_SESSION['current_user']['password'])): ?>
                            <td class="px-md-5 px-0 px-sm-4"><?php echo $_SESSION['current_user']['email']; ?><a class="btn btn-secondary btn-sm rounded-circle float-right mr-5 mr-sm-0" href="profile_edit.php?email"><img class="float-right mt-n1" style="height:15px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDM4My45NDcgMzgzLjk0NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzgzLjk0NyAzODMuOTQ3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8Zz4KCQkJPHBvbHlnb24gcG9pbnRzPSIwLDMwMy45NDcgMCwzODMuOTQ3IDgwLDM4My45NDcgMzE2LjA1MywxNDcuODkzIDIzNi4wNTMsNjcuODkzICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cGF0aCBkPSJNMzc3LjcwNyw1Ni4wNTNMMzI3Ljg5Myw2LjI0Yy04LjMyLTguMzItMjEuODY3LTguMzItMzAuMTg3LDBsLTM5LjA0LDM5LjA0bDgwLDgwbDM5LjA0LTM5LjA0ICAgICBDMzg2LjAyNyw3Ny45MiwzODYuMDI3LDY0LjM3MywzNzcuNzA3LDU2LjA1M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></td>
                        <?php else: ?>
                            <td class="px-md-5 px-0 px-sm-4"><?php echo $_SESSION['current_user']['email']; ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <th class="pr-md-5 pl-5 pl-sm-1">Geschlecht</th>
                        <td class="px-md-5 px-0 px-sm-4"><?php
                            switch($_SESSION['current_user']['gender']){
                                case 1:
                                    echo 'Weiblich';
                                    break;
                                case 2:
                                    echo 'Männlich';
                                    break;
                                case 3:
                                    echo 'Anderes';
                                    break;
                                default:
                                    echo 'Nicht festgelegt';
                                    break;
                            }
                        ?><a class="btn btn-secondary btn-sm rounded-circle float-right mr-5 mr-sm-0" href="profile_edit.php?gender"><img class="float-right mt-n1" style="height:15px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDM4My45NDcgMzgzLjk0NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzgzLjk0NyAzODMuOTQ3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8Zz4KCQkJPHBvbHlnb24gcG9pbnRzPSIwLDMwMy45NDcgMCwzODMuOTQ3IDgwLDM4My45NDcgMzE2LjA1MywxNDcuODkzIDIzNi4wNTMsNjcuODkzICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cGF0aCBkPSJNMzc3LjcwNyw1Ni4wNTNMMzI3Ljg5Myw2LjI0Yy04LjMyLTguMzItMjEuODY3LTguMzItMzAuMTg3LDBsLTM5LjA0LDM5LjA0bDgwLDgwbDM5LjA0LTM5LjA0ICAgICBDMzg2LjAyNyw3Ny45MiwzODYuMDI3LDY0LjM3MywzNzcuNzA3LDU2LjA1M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></td>
                    </tr>
                    <tr>
                        <th class="pr-md-5 pl-5 pl-sm-1">Passwort</th>
                        <?php if(isset($_SESSION['current_user']['password'])) : ?>
                            <td class="px-md-5 px-0 px-sm-4"><input type="password" value="<?php echo $_SESSION['current_user']['password']; ?>" style="border:none;" class="bg-white" readonly disabled="disabled"><a class="btn btn-secondary btn-sm rounded-circle float-right mr-5 mr-sm-0" href="profile_edit.php?password"><img class="float-right mt-n1" style="height:15px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDM4My45NDcgMzgzLjk0NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzgzLjk0NyAzODMuOTQ3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8Zz4KCQk8Zz4KCQkJPHBvbHlnb24gcG9pbnRzPSIwLDMwMy45NDcgMCwzODMuOTQ3IDgwLDM4My45NDcgMzE2LjA1MywxNDcuODkzIDIzNi4wNTMsNjcuODkzICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cGF0aCBkPSJNMzc3LjcwNyw1Ni4wNTNMMzI3Ljg5Myw2LjI0Yy04LjMyLTguMzItMjEuODY3LTguMzItMzAuMTg3LDBsLTM5LjA0LDM5LjA0bDgwLDgwbDM5LjA0LTM5LjA0ICAgICBDMzg2LjAyNyw3Ny45MiwzODYuMDI3LDY0LjM3MywzNzcuNzA3LDU2LjA1M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQk8L2c+Cgk8L2c+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" /></a></td>
                        <?php else: ?>
                            <td class="px-md-5 px-0 px-sm-4">→ Google</td>
                        <?php endif; ?>
                            
                    </tr>
                </table>
                <div class="container d-block d-sm-none">
                    <div class="row my-4">
                        <div class="col-sm">
                            <a href="logout.php" class="btn btn-outline-secondary btn-block mb-4">Abmelden</a>
                        </div>
                        <?php if(!$nodelete): ?>
                            <div class="col-sm">
                                <a href="delete_profile.php" class="btn btn-outline-danger btn-block">Konto löschen</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="container d-none d-sm-block">
                    <div class="row my-4">
                        <?php if(!$nodelete): ?>
                            <div class="col-sm">
                                <a href="delete_profile.php" class="btn btn-outline-danger btn-block">Konto löschen</a>
                            </div>
                        <?php endif; ?>
                        <div class="col-sm">
                            <a href="logout.php" class="btn btn-outline-secondary btn-block mb-4">Abmelden</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h1 class="text-center mt-4 mb-2">Meine Medien</h1>
        <?php foreach($sql_fetch as &$medium): ?>
            <div class="media mb-2 border border-primary rounded p-3" style="height:auto;">
                <?php if(@getimagesize("../images/media/" . $medium["id"] . '.png')): ?>
                    <a href="../medium.php?id=<?php echo $medium["id"]; ?>&redirect=profile"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 update_image pl-3 pl-md-0" style="height: 190px; object-fit:contain;" src="<?php echo "../images/media/" . $medium["id"] . '.png'; ?>" alt="Kein Bild verfügbar"></a>
                <?php elseif(@getimagesize($medium['image'])): ?>
                    <a href="../medium.php?id=<?php echo $medium["id"]; ?>&redirect=profile"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0" style="height: 190px; object-fit:contain;" src="<?php echo $medium['image']; ?>" alt="Kein Bild verfügbar"></a>
                <?php else: ?>
                    <a href="../medium.php?id=<?php echo $medium["id"]; ?>&redirect=profile"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0" style="height: 190px; object-fit:contain; width:150px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" alt="Kein Bild verfügbar"></a>
                <?php endif; ?>
                <div class="media-body mr-n4 mr-md-auto ml-sm-2 pr-2" style="overflow: hidden;">
                    <h6 class="font-weight-bold mb-0"><?php echo $medium["title"]; ?></h6><?php echo ' ' . $medium["subtitle"]; ?>
                    <p><small><?php echo 'von ' . $medium["authors_string"]; ?></small></p>
                    <p class="mt-2 custom-overflow ml-0" style="-webkit-line-clamp: 3; font-size: 12px;"><?php echo $medium["description"]; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include('../inc/footer.php'); ?>