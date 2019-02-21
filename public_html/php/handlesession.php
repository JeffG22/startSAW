<?php

    function safe_session_start() {
        // before of everything, if it is not active yet, to handle and use session we have to call start()
        if (session_status() != PHP_SESSION_ACTIVE) { 
            // Use_strict_mode must always be enabled for many security reasons.
            // Prevents users from deciding their own session id, forcing a new generation instead
            // As well as the flag httponly
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1); // to prevent Cross-Site Scripting Attack to steal cookies
            ini_set('session.cookie_secure', 1); // Sidejacking
            session_start();
        }
    }
    // My session start function support timestamp management
    function my_session_start() {
        
        safe_session_start();

        // Do not allow to use too old session ID
        if (isset($_SESSION['deleted_time'])) {
            if ($_SESSION['deleted_time'] < time() - 300) {
                // Should not happen usually. This could be attack or due to unstable network.
                // Remove all authentication status of this users session.
                remove_all_authentication_flag_from_active_sessions($_SESSION['user']);
                throw(new DestroyedSessionAccessException);
            }
            if (isset($_SESSION['new_session_id'])) {
                // Not fully expired yet. Could be lost cookie by unstable network.
                // Try again to set proper session ID cookie.
                // NOTE: Do not try to set session ID again if you would like to remove authentication flag.
                session_commit();
                session_id($_SESSION['new_session_id']);
                // New session ID should exist
                ini_set('session.use_strict_mode', 1);
                session_start();
                return;
            }
       }
        //session_destroy(); // cancella solo l'identificatore di sessione
        //session_start(); // si ricrea l'identificatore di sessione
        // verificare correttezza dei dati dell'utente
        // impostare variabili di sessione e presentazione servizi
    }

    // My session regenerate id function, SHOULD BE CALLED EACH 15 MINUTES
    function my_session_regenerate_id() {

        // session_regenerate_id();

        // Call session_create_id() while session is active to 
        // make sure collision free.
        safe_session_start();
        $newid = session_create_id();
        $_SESSION['new_session_id'] = $newid;
        // Set deleted timestamp. Session data must not be deleted immediately for reasons.
        $_SESSION['deleted_time'] = time();
        // Finish session
        session_commit();
        // Make sure to accept user defined session ID
        // NOTE: You must enable use_strict_mode for normal operations.
        
        // Set new custom session ID
        session_id($newid);
        // Start with custom session ID
        //ini_set('session.use_strict_mode', 0);
        ini_set('session.use_strict_mode', 1);
        session_start();
        // New session does not need them
        unset($_SESSION['deleted_time']);
        unset($_SESSION['new_session_id']);
    }

    // Logout unsetting the session
    function my_session_logout() {
        safe_session_start();
        // unset all of the session variables in this way
        session_unset();
        // unset($_SESSION["user"]); only "user"
        // $_SESSION = array(); the same
        if(session_destroy())
        {
            header("Location: index.php");
        }
    }

    function my_session_login($idUtente, $person) {
        // variabili di sessione
        $_SESSION['userId'] = $idUtente;
        $_SESSION['type'] = ($person) ? "person" : "organization";

        // Collect information about the device and the identity for security reasons
        $aip = $_SERVER['REMOTE_ADDR'];
        $bip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['ident'] = hash("sha256", $aip . $bip . $agent);
    }

    // "go away" for protected area
    function my_session_is_valid() {
        // Collect this information on every request
        $aip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $bip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $bip = $aip;
        }
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ident = hash("sha256", $aip . $bip . $agent); // Do this every time the client makes a request to the server, after authenticating
        
        safe_session_start();
        
        // 0 - verifica della coerenza dei dati del dispositivo loggato
        if (!isset($_SESSION['ident']))
            return false;
        if ($ident != $_SESSION['ident'])
        {
            end_session();
            return false;
        }

        // 1 - verifica dati da sessione utente, non loggato
        if (!empty($_SESSION['userId']) && ($_SESSION['type'] == "person" || $_SESSION['type'] == "organization"))
            return true;

        // 2 - loggato, esistente e sessione valida
        return false;
    }
?>