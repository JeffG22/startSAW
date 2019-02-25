<?php

    function safe_session_start() {
        // before of everything, if it is not active yet, to handle and use session we have to call start()
        if (session_status() != PHP_SESSION_ACTIVE) { 
            // Use_strict_mode must always be enabled for many security reasons.
            // As well as the flag httponly and cookie_secure
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1); // to prevent Cross-Site Scripting Attack to steal cookies
            //ini_set('session.cookie_secure', 1); // Sidejacking
            session_start();
        }
    }

    function security_variables() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) 
            $aip = $_SERVER["REMOTE_ADDR"]; 
        else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
            $aip = $_SERVER["HTTP_CLIENT_IP"]; 
        else
            $aip = '';
        $aip = ($aip != '') ? long2ip(ip2long($aip) & ip2long("255.255.0.0")) : ''; // just the first two ip numbers
        return hash("sha256", $aip . $agent);
    }

    function isDeletedOrInactive() {
        // Deleted: Should not happen usually. This could be attack or due to unstable network.
        // Inactive: due to user's inactivity
        if ((isset($_SESSION['deleted_time']) && $_SESSION['deleted_time'] < time() - 120) ||
            (isset($_SESSION['user_last_activity']) && $_SESSION['user_last_activity'] < time() - 1440)) {
            $_SESSION = array(); // Remove all authentication status of this users session.
            return true;
        }
        return false;
    }

    function isAnOldSession() {
        // Old: every session should be renewed at least each 15 mins
        if (isset($_SESSION['created_time']) && $_SESSION['created_time'] < time() - 900)
            return true;
        return false;
    }

    // My session start function
    function my_session_start() {
        safe_session_start();
        if (isDeletedOrInactive() || isAnOldSession()) // Do not allow to use too old session ID
            my_session_regenerate_id(); // regenerate the session
        $_SESSION['user_last_activity'] = time();
    }

    // My session regenerate id function
    function my_session_regenerate_id() {
        safe_session_start();
        // -- renewing -- using a timestamp to avoid error with multiple requests (e.g. Ajax)
        $_SESSION['deleted_time'] = time() + 120; // Set deleted timestamp. Session data must not be deleted immediately.
        session_regenerate_id(); // Create new session without destroying the old one
        unset($_SESSION['deleted_time']); // New session does not need it
        $_SESSION['created_time'] = time();
    }

    // Logout unsetting the session
    function my_session_logout() {
        session_unset(); // unset all of the session variables in this way
        //session_destroy();
        safe_session_start();
        my_session_regenerate_id();
        //header("Location: index.php");
    }

    function my_session_login($idUtente, $person, $name) {
        // ----- renewing the sid -----
        // It is a best practice when the user changes its privileges
        my_session_regenerate_id();
        
        // ----- User session variables -----
        $_SESSION['userId'] = $idUtente;
        $_SESSION['type'] = ($person) ? "person" : "organization";
        $_SESSION['name'] = $name;

        // ----- Security session variables (agent and ip) -----
        $_SESSION['identity'] =  security_variables();        
    }

    // "go away" for protected area
    function my_session_is_valid() {       
        // ----- checks on renewing, user data and security variables -----
        // too old session
        if (isDeletedOrInactive()) {
            my_session_regenerate_id(); // regenerate the session
            return false;
        }
        // not signed up
        if (!isset($_SESSION['identity']))
            return false;
        // signed up but with a different ip or agent
        if (security_variables() != $_SESSION['identity']) {
            $_SESSION = array(); // removed all flags
            my_session_regenerate_id(); // regenerate the session
            return false;
        }
        // user signed up
        if (!empty($_SESSION['userId']) && ($_SESSION['type'] == "person" || $_SESSION['type'] == "organization"))
            return true;
        // something went wrong...
        return false;
    }
?>