<script language="javascript">


function load(){
    updateImage();
    updateAnchor();

    if(document.getElementById("german") != null){
        german = document.getElementById("german").checked;
        french = document.getElementById("french").checked;
        english = document.getElementById("english").checked;
        spanish = document.getElementById("spanish").checked;
        other = document.getElementById("other").checked;
        access = document.getElementById("access").checked;
        group_search = document.getElementById("group_search").checked

        if(document.getElementsByClassName("is_manage_page").length == 1){
            showSuggestions(document.getElementById("media_search").value, true);
        } else {
            showSuggestions(document.getElementById("media_search").value, false);
        }
    }
    
}

function updateImage() {

   images = document.getElementsByClassName("update_image");

    for (i = 0; i < images.length; i++) {
        image = images.item(i);
        image.src = image.src.split("?")[0] + "?" + new Date().getTime();
    }

   setTimeout("updateImage()", 30000);

}

function updateAnchor() {
    anchors = document.getElementsByClassName("update_anchor");

    for (i = 0; i < anchors.length; i++){
        anchor = anchors.item(i);
        anchor.href = anchor.href.split("?")[0] + "?" + new Date().getTime();
    }

    setTimeout("updateAnchor()", 30000)
}


function showSuggestions(str, manage = false) {
    console.log("request made");
    console.log("manage: " + manage);
    var output_string;
    var xhttp = new XMLHttpRequest();
    xhttp.responseType = "json";
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            
            output = this.response;

            output_string = "";

            if(output != null && output.media != null){
                var media = output.media;
                console.log(output);
                if(!manage){
                    for(i = 0; i < media.length; i += 1){
                        var temp = output_string;
                        if(media[i].image_exists == 1){
                            output_string = temp + "<div style=\"height:auto;\" class=\"media overflow-hidden mb-2 border border-primary rounded p-3\"><a class=\"stretched_link\" style=\"max-width:45%;\" href=\"medium.php?id=" + media[i].id + "&redirect=media\"><img class=\"mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0 update_image\" style=\"height: 190px; max-width:100%; object-fit:contain;\" src=\"images/media/" + media[i].id + ".png\" alt=\"Kein Bild verfügbar\"></a><div class=\"media-body mr-n4 mr-md-auto ml-sm-2 pr-2\" style=\"overflow: hidden;\"><h6 class=\"font-weight-bold mb-0\">" + media[i].title + "</h6>" + media[i].subtitle + "<p><small>von " + media[i].authors_string + "</small></p><p class=\"mt-2 custom-overflow ml-0\" style=\"-webkit-line-clamp: 3; font-size: 12px;\">" + media[i].description + "</p></div></div>";
                        } else if(media[i].image_exists == 2){
                            output_string = temp + "<div style=\"height:auto;\" class=\"media overflow-hidden mb-2 border border-primary rounded p-3\"><a class=\"stretched_link\" style=\"max-width:45%;\" href=\"medium.php?id=" + media[i].id + "&redirect=media\"><img class=\"mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0\" style=\"height: 190px; max-width:100%; object-fit:contain;\" src=\"" + media[i].image + "\" alt=\"Kein Bild verfügbar\"></a><div class=\"media-body mr-n4 mr-md-auto ml-sm-2 pr-2\" style=\"overflow: hidden;\"><h6 class=\"font-weight-bold mb-0\">" + media[i].title + "</h6>" + media[i].subtitle + "<p><small>von " + media[i].authors_string + "</small></p><p class=\"mt-2 custom-overflow ml-0\" style=\"-webkit-line-clamp: 3; font-size: 12px;\">" + media[i].description + "</p></div></div>";
                        } else {
                            output_string = temp + "<div style=\"height:auto;\" class=\"media overflow-hidden mb-2 border border-primary rounded p-3\"><a class=\"stretched_link\" style=\"max-width:45%;\" href=\"medium.php?id=" + media[i].id + "&redirect=media\"><img class=\"mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0\" style=\"height: 190px; max-width:100%; object-fit:contain; width:150px;\" src=\"data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=\" alt=\"Kein Bild verfügbar\"></a><div class=\"media-body mr-n4 mr-md-auto ml-sm-2 pr-2\" style=\"overflow: hidden;\"><h6 class=\"font-weight-bold mb-0\">" + media[i].title + "</h6>" + media[i].subtitle + "<p><small>von " + media[i].author_firstname + " " + media[i].author_lastname + "</small></p><p class=\"mt-2 custom-overflow ml-0\" style=\"-webkit-line-clamp: 3; font-size: 12px;\">" + media[i].description + "</p></div></div>";
                        }
                        
                    }
                } else {
                    for(i = 0; i < media.length; i += 1){
                        var temp = output_string;
                        if(media[i].image_exists == 1){
                            output_string = temp + "<div style=\"height:auto;\" class=\"media overflow-hidden mb-2 border border-primary rounded p-3\"><a class=\"stretched_link\" style=\"max-width:45%;\" href=\"manage_medium.php?id=" + media[i].id + "&redirect=manage\"><img class=\"mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0 update_image\" style=\"height: 190px; max-width:100%; object-fit:contain;\" src=\"images/media/" + media[i].id + ".png\" alt=\"Kein Bild verfügbar\"></a><div class=\"media-body mr-n4 mr-md-auto ml-sm-2 pr-2\" style=\"overflow: hidden;\"><h6 class=\"font-weight-bold mb-0\">" + media[i].title + "</h6>" + media[i].subtitle + "<p><small>von " + media[i].authors_string + "</small></p><p class=\"mt-2 custom-overflow ml-0\" style=\"-webkit-line-clamp: 3; font-size: 12px;\">" + media[i].description + "</p></div></div>";
                        } else if(media[i].image_exists == 2){
                            output_string = temp + "<div style=\"height:auto;\" class=\"media overflow-hidden mb-2 border border-primary rounded p-3\"><a class=\"stretched_link\" style=\"max-width:45%;\" href=\"manage_medium.php?id=" + media[i].id + "&redirect=manage\"><img class=\"mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0\" style=\"height: 190px; max-width:100%; object-fit:contain;\" src=\"" + media[i].image + "\" alt=\"Kein Bild verfügbar\"></a><div class=\"media-body mr-n4 mr-md-auto ml-sm-2 pr-2\" style=\"overflow: hidden;\"><h6 class=\"font-weight-bold mb-0\">" + media[i].title + "</h6>" + media[i].subtitle + "<p><small>von " + media[i].authors_string + "</small></p><p class=\"mt-2 custom-overflow ml-0\" style=\"-webkit-line-clamp: 3; font-size: 12px;\">" + media[i].description + "</p></div></div>";
                        } else {
                            output_string = temp + "<div style=\"height:auto;\" class=\"media overflow-hidden mb-2 border border-primary rounded p-3\"><a class=\"stretched_link\" style=\"max-width:45%;\" href=\"manage_medium.php?id=" + media[i].id + "&redirect=manage\"><img class=\"mr-md-3 ml-n4 ml-md-auto mr-1 pl-3 pl-md-0\" style=\"height: 190px; max-width:100%; object-fit:contain; width:150px;\" src=\"data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPHBhdGggZD0iTTQzMS41LDI1M1Y0MGgtMzMxYy0xMS4wMjgsMC0yMCw4Ljk3Mi0yMCwyMHYzMzUuNDRjNi4yNi0yLjIyLDEyLjk4OS0zLjQ0LDIwLTMuNDRoMjBWMTAwYzAtMTEuMDQ2LDguOTU0LTIwLDIwLTIwICBzMjAsOC45NTQsMjAsMjB2MjkyaDI3MXYtMzljMC0xMS4wNDYsOC45NTQtMjAsMjAtMjBzMjAsOC45NTQsMjAsMjB2NTljMCwxMS4wNDYtOC45NTQsMjAtMjAsMjBoLTM1MWMtMTEuMDI4LDAtMjAsOC45NzItMjAsMjAgIHM4Ljk3MiwyMCwyMCwyMGgzNTFjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjBzLTguOTU0LDIwLTIwLDIwaC0zNTFjLTMzLjA4NCwwLTYwLTI2LjkxNi02MC02MFY2MGMwLTMzLjA4NCwyNi45MTYtNjAsNjAtNjBoMzUxICBjMTEuMDQ2LDAsMjAsOC45NTQsMjAsMjB2MjMzYzAsMTEuMDQ2LTguOTU0LDIwLTIwLDIwUzQzMS41LDI2NC4wNDYsNDMxLjUsMjUzeiBNMjkyLjUsMjk4TDI5Mi41LDI5OGMtMTEuMDQ2LDAtMjAsOC45NTQtMjAsMjAgIHM4Ljk1NCwyMCwyMCwyMGwwLDBjMTEuMDQ2LDAsMjAtOC45NTQsMjAtMjBTMzAzLjU0NiwyOTgsMjkyLjUsMjk4eiBNMzEzLjQ5MywyNTIuNzEzYzAtNi40ODMsMy43MDMtMTIuMjU0LDkuNDM0LTE0LjcwMSAgYzI3LjY5MS0xMS44MjEsNDUuNTgtMzguOTE1LDQ1LjU3My02OS4wMjJjMC0wLjI4MS0wLjAwNi0wLjU1OS0wLjAxOC0wLjgzN0MzNjguMDMxLDEyNy4xODcsMzM0LjU2NCw5NCwyOTMuNDkzLDk0ICBjLTQxLjM1MiwwLTc0Ljk5MywzMy42NDItNzQuOTkzLDc0Ljk5M2MwLDExLjA0Niw4Ljk1NCwyMCwyMCwyMHMyMC04Ljk1NCwyMC0yMGMwLTE5LjI5NSwxNS42OTgtMzQuOTkzLDM0Ljk5My0zNC45OTMgIGMxOS4yOTYsMCwzNC45OTQsMTUuNjk4LDM0Ljk5NCwzNC45OTNjMCwwLjE5MywwLjAwMywwLjM4NiwwLjAwOCwwLjU3OGMtMC4yMjEsMTMuODI3LTguNTIxLDI2LjIwOC0yMS4yNzMsMzEuNjUzICBjLTIwLjQ4OSw4Ljc0Ny0zMy43MjksMjguOTU3LTMzLjcyOSw1MS40ODhWMjU0YzAsMTEuMDQ2LDguOTU0LDIwLDIwLDIwczIwLTguOTU0LDIwLTIwVjI1Mi43MTN6IiBmaWxsPSIjMDAwMDAwIi8+CgoKCgoKCgoKCgoKCgoKCjwvc3ZnPgo=\" alt=\"Kein Bild verfügbar\"></a><div class=\"media-body mr-n4 mr-md-auto ml-sm-2 pr-2\" style=\"overflow: hidden;\"><h6 class=\"font-weight-bold mb-0\">" + media[i].title + "</h6>" + media[i].subtitle + "<p><small>von " + media[i].author_firstname + " " + media[i].author_lastname + "</small></p><p class=\"mt-2 custom-overflow ml-0\" style=\"-webkit-line-clamp: 3; font-size: 12px;\">" + media[i].description + "</p></div></div>";
                        }
                        
                    }
                }
            } else {
                output_string = "<h6 class=\"text-danger text-center\">Es wurden keine Resultate gefunden!</h6><div style=\"margin-bottom:300px;\"></div>";
            }


            if(output_string.length == 0){
                output_string = "<h6 class=\"text-danger text-center\">Es wurden keine Resultate gefunden!</h6><div style=\"margin-bottom:300px;\"></div>";
            }
            
            document.getElementById('suggestions').innerHTML = output_string;
        }
    };
    
    var query = "suggest.php?q=" + str;
    if(german){
        query += "&german=true";
    }
    if(french){
        query += "&french=true";
    }
    if(english){
        query += "&english=true";
    }
    if(spanish){
        query += "&spanish=true";
    }
    if(other){
        query += "&other=true";
    }
    if(access){
        query += "&access=true";
    }
    if(group_search){
        query += "&group_search=true";
    }

    xhttp.open("GET", query, true);
    xhttp.send();
    
}

function languageCheckbox(manage = false) {
    console.log("checkbox change");
    german = document.getElementById("german").checked;
    french = document.getElementById("french").checked;
    english = document.getElementById("english").checked;
    spanish = document.getElementById("spanish").checked;
    other = document.getElementById("other").checked;
    console.log(german);
    console.log(french);
    console.log(english);
    console.log(spanish);
    console.log(other);

    console.log(document.getElementById("media_search").value);
    showSuggestions(document.getElementById("media_search").value, manage);
    
    
}

function accessCheckbox(manage = false) {
    console.log("access checkbox change");
    access = document.getElementById("access").checked;
    console.log(access);
    console.log(document.getElementById("media_search").value);
    showSuggestions(document.getElementById("media_search").value, manage);
}

function groupSearchCheckbox(manage = false){
    console.log("group search checkbox change");
    group_search = document.getElementById("group_search").checked;
    console.log(group_search);
    showSuggestions(document.getElementById("media_search").value, manage);
}

function test() {
    console.log("hi");
}

function isbnCheckbox() {
    console.log("isbnnnnn");
    var temp = document.getElementById("add_medium_search").value;
    var result = "";
    if(document.getElementById("isbn_checkbox").checked){
        if(temp != ""){
            result = "isbn:" + temp;
        } else {
            result = "isbn:";
        }
    } else {
        if(temp.search("isbn:") != -1){
            result = temp.replace("isbn:", "");
        }
    }
    
    document.getElementById("add_medium_search").value = result;
}


function addMediumSearchDelete(){
    if(document.getElementById("isbn_checkbox").checked){
        document.getElementById("add_medium_search").value = "isbn:";
    } else {
        document.getElementById("add_medium_search").value = "";
    }
    
}

function passwordToggle(){
    var passwords = document.getElementsByClassName("password-toggle");
    var images = document.getElementsByClassName("password-toggle-image");
    var image = images[0];
    console.log(image.src);
    for(i = 0; i < passwords.length; i++){
        console.log(passwords[i].name);
        if(passwords[i].type == 'password'){
            console.log('hey ho');
            passwords[i].type = 'text';
            image.src = 'data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ2OS40NCA0NjkuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ2OS40NCA0NjkuNDQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8Zz4KCTxnPgoJCTxnPgoJCQk8cGF0aCBkPSJNMjMxLjE0NywxNjAuMzczbDY3LjIsNjcuMmwwLjMyLTMuNTJjMC0zNS4zMDctMjguNjkzLTY0LTY0LTY0TDIzMS4xNDcsMTYwLjM3M3oiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBhdGggZD0iTTIzNC42NjcsMTE3LjM4N2M1OC44OCwwLDEwNi42NjcsNDcuNzg3LDEwNi42NjcsMTA2LjY2N2MwLDEzLjc2LTIuNzczLDI2Ljg4LTcuNTczLDM4LjkzM2w2Mi40LDYyLjQgICAgIGMzMi4yMTMtMjYuODgsNTcuNi02MS42NTMsNzMuMjgtMTAxLjMzM2MtMzcuMDEzLTkzLjY1My0xMjgtMTYwLTIzNC43NzMtMTYwYy0yOS44NjcsMC01OC40NTMsNS4zMzMtODUuMDEzLDE0LjkzM2w0Ni4wOCw0NS45NzMgICAgIEMyMDcuNzg3LDEyMC4yNjcsMjIwLjkwNywxMTcuMzg3LDIzNC42NjcsMTE3LjM4N3oiIGZpbGw9IiMwMDAwMDAiLz4KCQkJPHBhdGggZD0iTTIxLjMzMyw1OS4yNTNsNDguNjQsNDguNjRsOS43MDcsOS43MDdDNDQuNDgsMTQ1LjEyLDE2LjY0LDE4MS43MDcsMCwyMjQuMDUzYzM2LjkwNyw5My42NTMsMTI4LDE2MCwyMzQuNjY3LDE2MCAgICAgYzMzLjA2NywwLDY0LjY0LTYuNCw5My41NDctMTguMDI3bDkuMDY3LDkuMDY3bDYyLjE4Nyw2Mi4yOTNsMjcuMi0yNy4wOTNMNDguNTMzLDMyLjA1M0wyMS4zMzMsNTkuMjUzeiBNMTM5LjMwNywxNzcuMTIgICAgIGwzMi45NiwzMi45NmMtMC45Niw0LjU4Ny0xLjYsOS4xNzMtMS42LDEzLjk3M2MwLDM1LjMwNywyOC42OTMsNjQsNjQsNjRjNC44LDAsOS4zODctMC42NCwxMy44NjctMS42bDMyLjk2LDMyLjk2ICAgICBjLTE0LjE4Nyw3LjA0LTI5Ljk3MywxMS4zMDctNDYuODI3LDExLjMwN0MxNzUuNzg3LDMzMC43MiwxMjgsMjgyLjkzMywxMjgsMjI0LjA1M0MxMjgsMjA3LjIsMTMyLjI2NywxOTEuNDEzLDEzOS4zMDcsMTc3LjEyeiIgZmlsbD0iIzAwMDAwMCIvPgoJCTwvZz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K';
        } else {
            passwords[i].type = 'password';
            image.src = 'data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTUuNTU2IDUxNS41NTYiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCA1MTUuNTU2IDUxNS41NTYiIHdpZHRoPSI1MTJweCI+PHBhdGggZD0ibTI1Ny43NzggNjQuNDQ0Yy0xMTkuMTEyIDAtMjIwLjE2OSA4MC43NzQtMjU3Ljc3OCAxOTMuMzM0IDM3LjYwOSAxMTIuNTYgMTM4LjY2NiAxOTMuMzMzIDI1Ny43NzggMTkzLjMzM3MyMjAuMTY5LTgwLjc3NCAyNTcuNzc4LTE5My4zMzNjLTM3LjYwOS0xMTIuNTYtMTM4LjY2Ni0xOTMuMzM0LTI1Ny43NzgtMTkzLjMzNHptMCAzMjIuMjIzYy03MS4xODQgMC0xMjguODg5LTU3LjcwNi0xMjguODg5LTEyOC44ODkgMC03MS4xODQgNTcuNzA1LTEyOC44ODkgMTI4Ljg4OS0xMjguODg5czEyOC44ODkgNTcuNzA1IDEyOC44ODkgMTI4Ljg4OWMwIDcxLjE4Mi01Ny43MDUgMTI4Ljg4OS0xMjguODg5IDEyOC44ODl6IiBmaWxsPSIjMDAwMDAwIi8+PHBhdGggZD0ibTMwMy4zNDcgMjEyLjIwOWMyNS4xNjcgMjUuMTY3IDI1LjE2NyA2NS45NzEgMCA5MS4xMzhzLTY1Ljk3MSAyNS4xNjctOTEuMTM4IDAtMjUuMTY3LTY1Ljk3MSAwLTkxLjEzOCA2NS45NzEtMjUuMTY3IDkxLjEzOCAwIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+Cg==';
        }
    }
}

function runRating(rating, medium_id){
    console.log(rating);
    var xhttp = new XMLHttpRequest();
    xhttp.responseType = "json";
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            output = this.response;
            console.log(output);
            document.getElementById("new_rating_number").innerHTML = output.new_rating.toFixed(2);
        }
    };
    var query = "rate.php?rating=" + rating + "&medium_id=" + medium_id;
    xhttp.open("GET", query, true);
    xhttp.send();
}

</script>




<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heimbibliothek Proenca</title>
    <link rel="stylesheet" href="https://bootswatch.com/4/lux/bootstrap.min.css">
    <link rel="stylesheet" class="update_anchor" href="<?php echo ROOT_URL; ?>styles/styles.css">
    <script src="https://kit.fontawesome.com/7d6343b755.js" crossorigin="anonymous"></script>
</head>
<body onload="load();">
    