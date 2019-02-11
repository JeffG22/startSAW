<?php
    // My session start function support timestamp management
    function my_session_start() {
        
        // before of everything, if it is not active yet, to handle and use session we have to call start()
        if (session_status() != PHP_SESSION_ACTIVE) { 
            //use_strict_mode must always be enabled for many security reasons.
            ini_set('session.use_strict_mode', 1);
            session_start();
        }

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
        // Call session_create_id() while session is active to 
        // make sure collision free.
        if (session_status() != PHP_SESSION_ACTIVE) {
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
        // WARNING: Never use confidential strings for prefix!
        $newid = session_create_id();
        $_SESSION['new_session_id'] = $newid;
        // Set deleted timestamp. Session data must not be deleted immediately for reasons.
        $_SESSION['deleted_time'] = time();
        // Finish session
        session_commit();
        // Make sure to accept user defined session ID
        // NOTE: You must enable use_strict_mode for normal operations.
        
        // Set new custome session ID
        session_id($newid);
        // Start with custome session ID
        ini_set('session.use_strict_mode', 0);
        session_start();
        ini_set('session.use_strict_mode', 1);
        // New session does not need them
        unset($_SESSION['deleted_time']);
        unset($_SESSION['new_session_id']);
    }

    // Logout unsetting the session
    function my_session_logout() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
        // unset all of the session variables in this way
        session_unset();
        // unset($_SESSION["user"]); only "user"
        // $_SESSION = array(); the same
        if(session_destroy())
        {
            header("Location: index.php");
        }
    }

    // "go away" for protected area
    function my_session_is_valid() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
        // verifica dati da sessione utente
        // 1 - non loggato
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header("Location: http://www.yourdomain.com/index.php");
        }

        // 2 - loggato, esistente e sessione valida
    }
?>