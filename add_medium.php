<?php 
    require('config/config.php');
    require('config/db.php');
    include_once('vendor/autoload.php');

    //9783456852584

    if(!API_KEY){
        echo 'no Api key set!';
        return;
    }

    $msg_class = "danger";
    $msg = "";

    function cleanIsbn($isbn){
        $parts = explode("-", $isbn);
        $isbn = "";
        foreach($parts as $part){
            $isbn .= $part;
        }
        return $isbn;
    }



    

    if(isset($_POST["submit"])){
        
        $query = $_POST['query'];

        $temp = str_replace(" ", "", $query);

        if(!empty($query) && !empty($temp)){
            $google_client->setDeveloperKey(API_KEY);
            $google_service_books = new Google_Service_Books($google_client);
            $optParams = array('maxResults' => 10, 'projection' => 'full');
            $google_results = $google_service_books->volumes->listVolumes("$query", $optParams);
            
            if(count($google_results) != 0){
                $msg = $google_results->totalItems . " Resultat(e)";
                $msg_class = 'success';
            } else {
                $msg = "Keine Resultate!";
                $msg_class = "danger";
            }
            
        } else {
            $msg = "Please enter a String!";
            $msg_class = "danger";
        }

        

    }

    
    

?>

    <?php include('inc/header.php'); ?>
    <?php include('inc/navbar.php'); ?>


    <div class="container" style="max-width:600px;">
        <h1 class="text-center mb-4">New Media</h1>
        <form autocomplete="off" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="row">
                <div class="col-md-2">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <div class="row">
                            <div class="col-lg">
                                <div class="btn-group-toggle">
                                    <label class="btn btn-outline-info py-2 my-0">
                                        <input type="checkbox" autocomplete="off" id="isbn_checkbox" onchange="isbnCheckbox()">ISBN
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="input-group">
                        <input class="form-control ml-md-2 ml-0 my-0 mt-3 mt-md-0 mb-3" id="add_medium_search" name="query" type="text" placeholder="Search" value="<?php echo isset($_POST['query']) ? $_POST['query'] : ""; ?>">
                        <div class="input-group-append">
                            <button onclick="addMediumSearchDelete()" class="btn btn-outline-secondary mr-n2 px-1 mt-3 mt-md-0" style="height: 45px;" type="button"><img class="px-2" style="height: 20px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxnPgoJCQk8cG9seWdvbiBwb2ludHM9IjM1My41NzQsMTc2LjUyNiAzMTMuNDk2LDE3NS4wNTYgMzA0LjgwNyw0MTIuMzQgMzQ0Ljg4NSw0MTMuODA0ICAgICIgZmlsbD0iIzAwMDAwMCIvPgoJCQk8cmVjdCB4PSIyMzUuOTQ4IiB5PSIxNzUuNzkxIiB3aWR0aD0iNDAuMTA0IiBoZWlnaHQ9IjIzNy4yODUiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBvbHlnb24gcG9pbnRzPSIyMDcuMTg2LDQxMi4zMzQgMTk4LjQ5NywxNzUuMDQ5IDE1OC40MTksMTc2LjUyIDE2Ny4xMDksNDEzLjgwNCAgICAiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBhdGggZD0iTTE3LjM3OSw3Ni44Njd2NDAuMTA0aDQxLjc4OUw5Mi4zMiw0OTMuNzA2QzkzLjIyOSw1MDQuMDU5LDEwMS44OTksNTEyLDExMi4yOTIsNTEyaDI4Ni43NCAgICAgYzEwLjM5NCwwLDE5LjA3LTcuOTQ3LDE5Ljk3Mi0xOC4zMDFsMzMuMTUzLTM3Ni43MjhoNDIuNDY0Vjc2Ljg2N0gxNy4zNzl6IE0zODAuNjY1LDQ3MS44OTZIMTMwLjY1NEw5OS40MjYsMTE2Ljk3MWgzMTIuNDc0ICAgICBMMzgwLjY2NSw0NzEuODk2eiIgZmlsbD0iIzAwMDAwMCIvPgoJCTwvZz4KCTwvZz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGQ9Ik0zMjEuNTA0LDBIMTkwLjQ5NmMtMTguNDI4LDAtMzMuNDIsMTQuOTkyLTMzLjQyLDMzLjQydjYzLjQ5OWg0MC4xMDRWNDAuMTA0aDExNy42NHY1Ni44MTVoNDAuMTA0VjMzLjQyICAgIEMzNTQuOTI0LDE0Ljk5MiwzMzkuOTMyLDAsMzIxLjUwNCwweiIgZmlsbD0iIzAwMDAwMCIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" /></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <input type="submit" name="submit" value="Submit" class="btn btn-outline-primary py-2 mb-2 mb-md-0">
                </div>
            </div>
            <p class="text-<?php echo $msg_class; ?>"><?php echo $msg; ?></p>
            <a href="add_medium_medium.php?noid" class="btn btn-outline-secondary btn-sm mt-4 mb-2 justify-content-center d-flex"><img class="mr-3" style="height:20px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1NTEuMTMgNTUxLjEzIiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNTUxLjEzIDU1MS4xMyIgd2lkdGg9IjUxMnB4Ij48cGF0aCBkPSJtMjc1LjU2NSAwYy0xNTEuOTQ0IDAtMjc1LjU2NSAxMjMuNjIxLTI3NS41NjUgMjc1LjU2NXMxMjMuNjIxIDI3NS41NjUgMjc1LjU2NSAyNzUuNTY1IDI3NS41NjUtMTIzLjYyMSAyNzUuNTY1LTI3NS41NjUtMTIzLjYyMS0yNzUuNTY1LTI3NS41NjUtMjc1LjU2NXptMCA1MTYuNjg1Yy0xMzIuOTU1IDAtMjQxLjExOS0xMDguMTY0LTI0MS4xMTktMjQxLjExOXMxMDguMTY0LTI0MS4xMiAyNDEuMTE5LTI0MS4xMiAyNDEuMTIgMTA4LjE2NCAyNDEuMTIgMjQxLjExOS0xMDguMTY1IDI0MS4xMi0yNDEuMTIgMjQxLjEyeiIgZmlsbD0iIzAwMDAwMCIvPjxwYXRoIGQ9Im0yOTIuNzg4IDEzNy43ODNoLTM0LjQ0NnYxMjAuNTZoLTEyMC41NnYzNC40NDZoMTIwLjU2djEyMC41NmgzNC40NDZ2LTEyMC41NmgxMjAuNTZ2LTM0LjQ0NmgtMTIwLjU2eiIgZmlsbD0iIzAwMDAwMCIvPjwvc3ZnPgo=" /><span class="mt-1">Create from scratch</span></a>
        </form>
    </div>

    <?php if(isset($google_results) && count($google_results) != 0): ?>
        <div class="container">
            <?php foreach($google_results as $item): ?>
                <div class="media mb-2 border border-primary rounded p-3" style="height:auto;">
                    <?php if(@getimagesize($item['volumeInfo']['imageLinks']['thumbnail'])): ?>
                        <a href="add_medium_medium.php?id=<?php echo $item['id'] . "&redirect=$query"; ?>"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0" style="height: 190px; object-fit:contain;" src="<?php echo $item['volumeInfo']['imageLinks']['thumbnail']; ?>" alt="Kein Bild verfügbar"></a>
                    <?php else: ?>
                        <a href="add_medium_medium.php?id=<?php echo $item['id'] . "&redirect=$query"; ?>"><img class="mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0" style="height: 190px; object-fit:contain; width:150px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=" alt="Kein Bild verfügbar"></a>
                    <?php endif; ?>
                    <div class="media-body mr-n4 mr-md-auto ml-sm-2 pr-2" style="overflow: hidden;">
                        <h6 class="font-weight-bold mb-0"><?php echo $item['volumeInfo']['title']; ?></h6><?php echo ' ' . $item['volumeInfo']['subtitle']; ?>
                        <p><small><?php
                            if(isset($item->volumeInfo->authors)){
                                $authors = $item->volumeInfo->authors;
                                $author_count = -1;
                                $author_firstname = "";
                                $author_lastname = "";

                                foreach($authors as &$author){
                                    $author_count += 1;
                                    $names = explode(" ", $author);
                                    $author_lastname = $names[count($names) - 1];
                                    $author_firstname = "";
                                    for($i = 0; $i < count($names) - 1; $i++){
                                        $author_firstname .= $names[$i] . " ";
                                    }
                                    if($author_count == 0){
                                        echo $author_firstname . "<strong class=\"text-primary\">" . $author_lastname . "</strong>";
                                    } else {
                                        echo ", " . $author_firstname . "<strong class=\"text-primary\">" . $author_lastname . "</strong>";
                                    }
                                }
                            } else {
                                echo "Kein Autor angegeben.";
                            }
                    
                        ?></small></p>
                        <p><?php echo $item['id']; ?></p>
                        <p class="mt-2 custom-overflow ml-0" style="-webkit-line-clamp: 3; font-size: 12px;"><?php echo isset($item['volumeInfo']['description']) ? $item['volumeInfo']['description'] : 'Keine Beschreibung verfügbar.'; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php include('inc/footer.php'); ?>
    
    