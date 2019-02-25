<?php
    if (isset($_SESSION['message'])) {
        echo "<div id=\"popup\" class=\"popup\" data-role=\"popup\">
                <div class=\"popup-body\">    
                    <div class=\"popup-content\">
                        <h3 class=\"popup-title\">Notifica</h3>
                        <p>".$_SESSION['message']."</p>
                    </div>
                    <button class=\"popup-exit btn btn-primary\" onclick=\"closePopup()\">Ok</button>
                </div>
            </div>";        
        unset($_SESSION['message']);
        echo "<script>function closePopup() {
                        document.getElementById(\"popup\").remove();
                    }</script>";
    }
?>