<script>
        "use strict"; //necessario per strict mode
        // creare un'array associativo "messaggio -> errore"           
        var err_array = {
                'name' : 'Nome non valido.',
                'description' : 'Descrizione non valida.',
                'available_pos' : 'Il numero di posizioni disponibili deve essere compreso tra 1 e 300.',
                'upload_picture' : 'Errore nel caricamento dell\'immagine. Assicurati che il tipo di file sia supportato (JPG, PNG, BMP) e che le dimensioni non superino i 4MB',
                'address' : 'Indirizzo non valido.',
                'mysql' : 'Inserimento non riuscito, attendere qualche istante e riprovare.'
        };

        <?php
            $tempError = ($error_flag) ? $error_message : "";
            echo 'var id_errore = "'.$tempError.'";';
        ?>
        
        /** ----- operazione di recupero dati se non validi ----- */
        function loadPostData( jQuery ) {
            // ----- ricaricare dati inviati non validi -----
            <?php
                if ($error_flag) {
                    if (!empty($_POST[$name])) {
                        echo 'document.getElementById("'.$name.'").value="'.sanitize_inputString($_POST[$name]).'";';
                    }
                    if (!empty($_POST[$description])) {
                        echo 'document.getElementById("'.$description.'").value="'.sanitize_inputString($_POST[$description]).'";';
                    }
                    if (!empty($_POST[$available_pos])) {
                        echo 'document.getElementById("'.$available_pos.'").value="'.intval($_POST[$available_pos]).'";';
                    }
                    if (!empty($_POST[$address])) {
                        echo 'document.getElementById("'.$address.'").value="'.sanitize_inputString($_POST[$address]).'";';
                    }
                }
            ?>
   
            if (id_errore == "mysql") {
                var field = document.getElementById("userMessage");      
                field.insertAdjacentHTML( 'beforeend', "<p style='color: red'>"+err_array[id_errore]+"</p>");   
            } else if (id_errore != "") {
                var field = document.getElementById(id_errore);
                field.setCustomValidity(err_array[id_errore]); // fa apparire la finestrella di html 5 con la scritta che comunica errore
                field.setAttribute("onclick", "this.setCustomValidity('');");         
                field.setAttribute("onchange", "this.setCustomValidity('');  this.style.border='';");         
                field.style.border = "2px solid red";
                field.style.borderRadius = "4px";
                document.getElementById("submit").click(); // show the validity dialog                   
            }        
        }
        <?php
            if ($error_flag) // se errore allora comunica all'utente ciò quando la pagina è ricaricata (funzione jquery)
                echo '$(document).ready(loadPostData);'
        ?>
</script>