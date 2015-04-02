var xmlhttp = new XMLHttpRequest();
var url = "version.txt";

xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var version = xmlhttp.responseText;
        var out = "";
        out += '<a class="zip_download_link" href="https://github.com/2ch774/p2-php/zipball/v' + version + '">Download this project as a .zip file</a>';
        out += '<a class="tar_download_link" href="https://github.com/2ch774/p2-php/tarball/v' + version + '">Download this project as a tar.gz file</a>';

        document.getElementById("downloads").innerHTML = out;
    }
}
xmlhttp.open("GET", url, true);
xmlhttp.send();

