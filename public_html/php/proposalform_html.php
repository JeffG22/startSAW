<div class="container">
        <div class="form-group">
            <form enctype="multipart/form-data" class="form-in" id="input_proposal"  method="POST" 
                <?php
                    if ($editing) 
                        echo "action=\"edit_proposal.php\"";
                    else 
                        echo "action=\"new_proposal.php\"";
                ?>
            >
            <!-- ^ enctype is necessary to encode picture correctly ^ -->

                <!-- div to show error message -->                
                <div id="userMessage">
                </div>

                <?php
                    if ($editing) 
                        echo "<legend>Modifica la tua proposta</legend>";
                    else 
                        echo "<legend>Inserisci nuova proposta</legend>";
                ?>
                <!-- Name -->
                <label for="name">Nome proposta: </label>&emsp;
                <input type="text" name="name" id="name" class="form-control input-in" 
                    minlength="3" maxlength="100" required>

                <!-- Description -->
                <label for="description">Descrizione: </label>&emsp;
                <textarea name="description" id="description" rows="6" class="form-control" 
                    minlength="10" maxlength="50000" required></textarea>

                <!-- Upload picture -->
                <label for="upload_picture">Carica un'immagine:</label>
                <!-- This hidden field is used by php to avoid uploading large files.
                Files lager than 4MB are not blocked by this, but upload stops at 4M
                and the file is not sent, thus preventing user from waiting for a file
                that will be rejected server-side.-->
                <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
                <input type="file" name="upload_picture" id="upload_picture" accept="image/png, image/jpeg, image/jpg, image/bmp"  onchange="checkPicture()">
                
                <!-- Address -->
                <label for="address">Indirizzo: </label>&emsp;
                <input type="text" name="address" id="address" class="form-control input-in">

                <!-- Number of available positions-->
                <label for="available_positions">Numero volontari richiesti: </label>&emsp;
                <input type="number" name="available_positions"  id="available_positions" class="form-control input-in"
                    min="1" max="500" required>
            <?php
                if ($editing) {
                    echo "<input type='hidden' name='proposal_id' value='".$proposal_id."'>";
                    echo "<input type='hidden' name='edited' value='true'>";
                }
            ?>
                <!-- Submit -->
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary" id="submit">
                    <?php
                    if ($editing) 
                        echo "Modifica proposta";
                    else 
                        echo "Crea proposta";
                    ?>
                    </button>
                </div>
            </form>
        </div>
    </div>