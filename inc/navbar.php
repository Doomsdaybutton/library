<?php 
    if($_SERVER['PHP_SELF'] == "/library/admin.php" || $_SERVER['PHP_SELF'] == "/library/add_medium.php" || $_SERVER['PHP_SELF'] == "/library/manage_media.php" || $_SERVER['PHP_SELF'] == "/library/add_medium_medium.php" || $_SERVER['PHP_SELF'] == "/library/manage_medium.php"){
        $adminpage = TRUE;
    } else {
        $adminpage = FALSE;
    }

    if(file_exists('ABSOLUTE_PATH' . "images/media/temp.png") && $_SERVER['PHP_SELF'] != "/library/manage_medium.php" && $_SERVER['PHP_SELF'] != "/library/add_medium_medium.php"){
        unlink('ABSOLUTE_PATH' . "images/media/temp.png");
    }

    if($_SERVER['PHP_SELF'] != "/library/manage_medium.php" && $_SERVER['PHP_SELF'] != "/library/add_medium_medium.php" && isset($_SESSION)){
        unset($_SESSION['image']);
        unset($_SESSION['lent_until']);
        unset($_SESSION['available']);
        unset($_SESSION['user_id']);
    }
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 navbar-nav sticky-top">
    <div class="container">
        <a href="<?php echo ROOT_URL; ?>" class="navbar-brand py-0 ml-0">Heimbibliothek Proenca</a>
        <button class="navbar-toggler mt-1" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar" stlye="position:relative;" >
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>">Home</a>
                </li>
                <?php if(!$adminpage): ?>
                    <li class="nav-item">
                        <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>account/sign_up.php">Registrieren</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>media.php">Katalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>imprint.php">Impressum</a>
                    </li>
                    <?php if(isset($_SESSION['current_user']) && $_SESSION['current_user']['admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>admin.php">Admin</a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>manage_media.php">Manage Media</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>add_medium.php">New Media</a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link py-0 text-light" href="<?php echo ROOT_URL; ?>admin.php">Admin</a>
                        </li>
                <?php endif; ?>
            </ul>
            
            <?php if(isset($_SESSION['current_user']['id'])): ?>
                <?php if(empty($_SESSION['current_user']['profile_picture'])): ?>
                    <?php if(@getimagesize(ROOT_URL . 'images/users/' . $_SESSION['current_user']['id'] . '.png')): ?>
                        <a href="<?php echo ROOT_URL . 'account/profile.php'; ?>"><img alt="test" src="<?php echo ROOT_URL . 'images/users/' . $_SESSION['current_user']['id'] . '.png'; ?>" style="width: 2.6em; height: 2.6em; cursor:pointer; object-fit:cover;" class="rounded-circle update_image"></a>
                    <?php else: ?>
                        <a href="<?php echo ROOT_URL . 'account/profile.php'; ?>"><img style="width: 2.6em; height: 2.6em; cursor:pointer;" class="rounded-circle" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgd2lkdGg9IjUxMiI+PGc+PHBhdGggZD0ibTQzNy4wMTk1MzEgNzQuOTgwNDY5Yy00OC4zNTE1NjItNDguMzUxNTYzLTExMi42NDA2MjUtNzQuOTgwNDY5LTE4MS4wMTk1MzEtNzQuOTgwNDY5LTY4LjM4MjgxMiAwLTEzMi42Njc5NjkgMjYuNjI4OTA2LTE4MS4wMTk1MzEgNzQuOTgwNDY5LTQ4LjM1MTU2MyA0OC4zNTE1NjItNzQuOTgwNDY5IDExMi42MzY3MTktNzQuOTgwNDY5IDE4MS4wMTk1MzEgMCA2OC4zNzg5MDYgMjYuNjI4OTA2IDEzMi42Njc5NjkgNzQuOTgwNDY5IDE4MS4wMTk1MzEgNDguMzUxNTYyIDQ4LjM1MTU2MyAxMTIuNjM2NzE5IDc0Ljk4MDQ2OSAxODEuMDE5NTMxIDc0Ljk4MDQ2OSA2OC4zNzg5MDYgMCAxMzIuNjY3OTY5LTI2LjYyODkwNiAxODEuMDE5NTMxLTc0Ljk4MDQ2OSA0OC4zNTE1NjMtNDguMzUxNTYyIDc0Ljk4MDQ2OS0xMTIuNjQwNjI1IDc0Ljk4MDQ2OS0xODEuMDE5NTMxIDAtNjguMzgyODEyLTI2LjYyODkwNi0xMzIuNjY3OTY5LTc0Ljk4MDQ2OS0xODEuMDE5NTMxem0tMzA4LjY3OTY4NyAzNjcuNDA2MjVjMTAuNzA3MDMxLTYxLjY0ODQzOCA2NC4xMjg5MDYtMTA3LjEyMTA5NCAxMjcuNjYwMTU2LTEwNy4xMjEwOTQgNjMuNTM1MTU2IDAgMTE2Ljk1MzEyNSA0NS40NzI2NTYgMTI3LjY2MDE1NiAxMDcuMTIxMDk0LTM2LjM0NzY1NiAyNC45NzI2NTYtODAuMzI0MjE4IDM5LjYxMzI4MS0xMjcuNjYwMTU2IDM5LjYxMzI4MXMtOTEuMzEyNS0xNC42NDA2MjUtMTI3LjY2MDE1Ni0zOS42MTMyODF6bTQ2LjI2MTcxOC0yMTguNTE5NTMxYzAtNDQuODg2NzE5IDM2LjUxNTYyNi04MS4zOTg0MzggODEuMzk4NDM4LTgxLjM5ODQzOHM4MS4zOTg0MzggMzYuNTE1NjI1IDgxLjM5ODQzOCA4MS4zOTg0MzhjMCA0NC44ODI4MTItMzYuNTE1NjI2IDgxLjM5ODQzNy04MS4zOTg0MzggODEuMzk4NDM3cy04MS4zOTg0MzgtMzYuNTE1NjI1LTgxLjM5ODQzOC04MS4zOTg0Mzd6bTIzNS4wNDI5NjkgMTk3LjcxMDkzN2MtOC4wNzQyMTktMjguNjk5MjE5LTI0LjEwOTM3NS01NC43MzgyODEtNDYuNTg1OTM3LTc1LjA3ODEyNS0xMy43ODkwNjMtMTIuNDgwNDY5LTI5LjQ4NDM3NS0yMi4zMjgxMjUtNDYuMzU5Mzc1LTI5LjI2OTUzMSAzMC41LTE5Ljg5NDUzMSA1MC43MDMxMjUtNTQuMzEyNSA1MC43MDMxMjUtOTMuMzYzMjgxIDAtNjEuNDI1NzgyLTQ5Ljk3NjU2My0xMTEuMzk4NDM4LTExMS40MDIzNDQtMTExLjM5ODQzOHMtMTExLjM5ODQzOCA0OS45NzI2NTYtMTExLjM5ODQzOCAxMTEuMzk4NDM4YzAgMzkuMDUwNzgxIDIwLjIwMzEyNiA3My40Njg3NSA1MC42OTkyMTkgOTMuMzYzMjgxLTE2Ljg3MTA5MyA2Ljk0MTQwNi0zMi41NzAzMTIgMTYuNzg1MTU2LTQ2LjM1OTM3NSAyOS4yNjU2MjUtMjIuNDcyNjU2IDIwLjMzOTg0NC0zOC41MTE3MTggNDYuMzc4OTA2LTQ2LjU4NTkzNyA3NS4wNzgxMjUtNDQuNDcyNjU3LTQxLjMwMDc4MS03Mi4zNTU0NjktMTAwLjIzODI4MS03Mi4zNTU0NjktMTY1LjU3NDIxOSAwLTEyNC42MTcxODggMTAxLjM4MjgxMi0yMjYgMjI2LTIyNnMyMjYgMTAxLjM4MjgxMiAyMjYgMjI2YzAgNjUuMzM5ODQ0LTI3Ljg4MjgxMiAxMjQuMjc3MzQ0LTcyLjM1NTQ2OSAxNjUuNTc4MTI1em0wIDAiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiIHN0eWxlPSJmaWxsOiNGRkZGRkYiPjwvcGF0aD48L2c+IDwvc3ZnPg==" /></a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo ROOT_URL . 'account/profile.php'; ?>"><img src="<?php echo $_SESSION['current_user']['profile_picture']; ?>" style="width: 2.6em; height: 2.6em; cursor:pointer; object-fit:cover;" class="rounded-circle"></a>
                <?php endif; ?>
            <?php else: ?>
                <a class="nav-link text-light py-0 my-0" href="<?php echo ROOT_URL . 'account/login.php'; ?>">
                    <svg class="bi bi-person" width="2em" height="2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M13 14s1 0 1-1-1-4-6-4-6 3-6 4 1 1 1 1h10zm-9.995-.944v-.002.002zM3.022 13h9.956a.274.274 0 00.014-.002l.008-.002c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664a1.05 1.05 0 00.022.004zm9.974.056v-.002.002zM8 7a2 2 0 100-4 2 2 0 000 4zm3-2a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>