<?php
    require('config/config.php');
    require('config/db.php');

    $query = "SELECT * FROM media ORDER BY main_category, medium_index, medium_group DESC, volume, title LIMIT 10";
    $sql_result = mysqli_query($conn, $query);
    $media = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);


    foreach($media as &$medium){
        $authors = json_decode($medium['authors']);
        $authors_string = "";

        foreach($authors->authors as &$author){
            if($authors_string != ""){
                $authors_string .= ", " . $author->author_firstname . ' ' . htmlentities($author->author_lastname);
            } else {
                $authors_string .= $author->author_firstname . ' ' . $author->author_lastname;
            }
            
        }

        $medium['authors_string'] = $authors_string;
    }
?>


<?php include('inc/header.php'); ?>
<?php include('inc/navbar.php'); ?>

<div class="is_manage_page"></div>
<div class="container">
    <h1 class="text-center mb-4">Manage Media</h1>
    <form autocomplete="off">
        <input class="form-control mb-3" id="media_search" type="text" onkeyup="showSuggestions(this.value, true)" placeholder="Suche...">
    </form>

    <button class="btn btn-outline-secondary btn-sm btn-block mb-3" type="button" data-toggle="collapse" aria-expanded="false" data-target="#filter" aria-controls="filter"><img class="d-inline mr-3" style="height:30px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIuMDExIDUxMi4wMTEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMi4wMTEgNTEyLjAxMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTUwNS43NTUsMTIzLjU5MmMtOC4zNDEtOC4zNDEtMjEuODI0LTguMzQxLTMwLjE2NSwwTDI1Ni4wMDUsMzQzLjE3NkwzNi40MjEsMTIzLjU5MmMtOC4zNDEtOC4zNDEtMjEuODI0LTguMzQxLTMwLjE2NSwwICAgIHMtOC4zNDEsMjEuODI0LDAsMzAuMTY1bDIzNC42NjcsMjM0LjY2N2M0LjE2LDQuMTYsOS42MjEsNi4yNTEsMTUuMDgzLDYuMjUxYzUuNDYyLDAsMTAuOTIzLTIuMDkxLDE1LjA4My02LjI1MWwyMzQuNjY3LTIzNC42NjcgICAgQzUxNC4wOTYsMTQ1LjQxNiw1MTQuMDk2LDEzMS45MzMsNTA1Ljc1NSwxMjMuNTkyeiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /><h6 class="d-inline">Filter</h6></button>
    <div id="filter" class="collapse">
        <div class="card card-body mb-3">
            <div class="row mb-3">
                <div class="col-md-12 text-center mb-3">
                    <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                        <div class="row">
                            <div class="col-lg">
                                <div class="btn-group-toggle btn-group-sm">
                                    <label class="btn btn-outline-info mx-1 mb-2" style="width:150px;">
                                        <input type="checkbox" id="german" autocomplete="off" onchange="languageCheckbox(true)">Deutsch
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg">
                                <div class="btn-group-toggle btn-group-sm">
                                    <label class="btn btn-outline-info mx-1 mb-2" style="width:150px;">
                                        <input type="checkbox" id="french" autocomplete="off" onchange="languageCheckbox(true)">Français
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg">
                                <div class="btn-group-toggle btn-group-sm">
                                    <label class="btn btn-outline-info mx-1 mb-2" style="width:150px;">
                                        <input type="checkbox" id="english" autocomplete="off" onchange="languageCheckbox(true)">English
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg">
                                <div class="btn-group-toggle btn-group-sm">
                                    <label class="btn btn-outline-info mx-1 mb-2" style="width:150px;">
                                        <input type="checkbox" id="spanish" autocomplete="off" onchange="languageCheckbox(true)">Español
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg">
                                <div class="btn-group-toggle btn-group-sm">
                                    <label class="btn btn-outline-info mx-1 mb-2" style="width:150px;">
                                        <input type="checkbox" id="other" autocomplete="off" onchange="languageCheckbox(true)">Andere
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center mb-3">
                    <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                        <div class="row">
                            <div class="col-md">
                                <div class="btn-group-toggle btn-group-sm">
                                    <label class="btn btn-outline-success mx-1 mb-2" style="width:150px;">
                                        <input type="checkbox" id="access" autocomplete="off" onchange="accessCheckbox(true)">Nur Verfügbare
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="suggestions">
        <?php foreach($media as &$medium): ?>
            <div class="media mb-2 border border-primary rounded p-3" style="height:auto;">
                <?php if(@getimagesize("images/media/" . $medium["id"] . '.png')): ?>
                    <a href="medium.php?id=<?php echo $medium["id"]; ?>&redirect=media"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 update_image pl-3 pl-md-0" style="height: 190px; object-fit:contain;" src="<?php echo "images/media/" . $medium["id"] . '.png'; ?>" alt="Kein Bild verfügbar"></a>
                <?php elseif(@getimagesize($medium['image'])): ?>
                    <a href="medium.php?id=<?php echo $medium["id"]; ?>&redirect=media"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0" style="height: 190px; object-fit:contain;" src="<?php echo $medium['image']; ?>" alt="Kein Bild verfügbar"></a>
                <?php else: ?>
                    <a href="medium.php?id=<?php echo $medium["id"]; ?>&redirect=media"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0" style="height: 190px; object-fit:contain; width:150px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" alt="Kein Bild verfügbar"></a>
                <?php endif; ?>
                <div class="media-body mr-n4 mr-md-auto ml-sm-2 pr-2" style="overflow: hidden;">
                    <h6 class="font-weight-bold mb-0"><?php echo $medium["title"]; ?></h6><?php echo ' ' . $medium["subtitle"]; ?>
                    <p><small><?php echo 'von ' . $medium["authors_string"]; ?></small></p>
                    <p class="mt-2 custom-overflow ml-0" style="-webkit-line-clamp: 3; font-size: 12px;"><?php echo $medium["description"]; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('inc/footer.php'); ?>