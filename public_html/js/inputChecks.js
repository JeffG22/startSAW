"use strict";
function checkPicture() {
    /** If supported by browser, checks file type and size.
        These checks are repeated server-side. */
    if (window.FileReader) 
    {
        fileSize = document.getElementById("upload_picture").files[0].size;
        fileType = document.getElementById("upload_picture").files[0].type;
        try {
            if (!["image/png", "image/jpeg", "image/jpg", "image/bmp"].includes(fileType)) {
                throw "Attenzione: formato file non supportato. Puoi caricare immagini in formato JPEG, PNG e BMP.";
            } else if (fileSize > 4194304) {// Max size = 4MB
                throw "Attenzione: il file caricato è troppo grosso. La dimensione massima consentita è 4MB.";
            }
        } 
        catch (err) {
            alert(err);
            document.getElementById("upload_picture").value = null;
        }
    }
}