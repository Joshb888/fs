<?php

// MySQL Injection Prevention
function escapestring($value)
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        die('Could not connect: ' . $conn->connect_error);
    }
    return strip_tags(mysqli_real_escape_string($conn, $value));
}

// Insert into Database
function dbquery($sql, $returnresult = true)
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        error_log('MySQL could not connect: ' . $conn->connect_error);
        return $conn->connect_error;
    }
    $return = array();
    $result = mysqli_query($conn, $sql);
    if ($returnresult) {
        if (mysqli_num_rows($result) != 0) {
            while ($r = $result->fetch_assoc()) {
                array_push($return, $r);
            }
        } else {
            $return = array();
        }
    } else {
        $return = array();
    }
    return $return;
}

// Log Function
function logAction($action, $user) {
    global $time;
    global $us_date;
    dbquery('INSERT INTO logs (action, username, timestamp) VALUES ("' . escapestring($action) . '", "' . escapestring($user) . '", "' . $time . ' ' . $us_date . '")', false);
    //plugins::call('logAction', array("action" => $action, "user" => $user));
}

// Throw Visual Error (Only works after Header is loaded)
function throwError($error, $log = false) {
    if ($log) {
        error_log('Error Handler: ' . $error);
    }
    // Load Toastr JavaScript and CSS
    echo '
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script type="text/javascript">
            if(window.toastr != undefined) {
                if (typeof jQuery == "undefined") {
                    alert("Error Handler: ' . $error . '")
                } else {
                    toastr.error("' . $error . '", "Error Handler")
                }
            } else {
                alert("Error Handler: ' . $error . '")
            }
        </script>
    ';
}

// Throw Notification (Only works after Header is loaded)
function clientNotify($type, $error) {
    // Load Toastr JavaScript and CSS
    echo '
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script type="text/javascript">
            if(window.toastr != undefined) {
                if (typeof jQuery == "undefined") {
                    alert("System: ' . $error . '")
                } else {
                    toastr.' . $type . '("' . $error . '", "System")
                }
            } else {
                alert("System: ' . $error . '")
            }
        </script>
    ';
}

// Login Function
function userLogin($username, $passwordAttempt) {
    global $pdo;
    global $url;

    $error    = array();
    $users = dbquery('SELECT * FROM users WHERE username="' . escapestring($username) . '"');
    if (empty($users)) {
        $error['msg']      = "That account couldn't be found in our Database.";
        echo json_encode($error);
        exit();
    } else {
        $user = $users[0];
        if (password_verify($passwordAttempt, $user['password'])) {
            // Get Settings
            $settings = dbquery('SELECT * FROM settings')[0];
            // Check If Verified
            $user = dbquery('SELECT * FROM users WHERE user_id="'. escapestring($user['user_id']) . '"')[0];
            if ($user['usergroup'] == "Unverified") {
                // Check Site Settings
                if ($settings['validation_enabled'] == "Yes" || $settings['validation_enabled'] == "yes") {
                    $error['msg']  = "Your account is pending Validation from an Admin.";
                    $error['status']   = true;    
                    echo json_encode($error);
                    exit();
                } else {
                    dbquery('UPDATE users SET usergroup="User" WHERE user_id="' . escapestring($user['user_id']) . '"');
                }
            } else {
                // Create Session
                $error['msg']      = "";
                echo json_encode($error);
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['logged_in'] = time();
                // header('Location: ' . $url['index'] . '?logged=in');
                exit();
            }
        } else {
            $error['msg']      = "Your password was invalid. Please try again!";
            echo json_encode($error);
            exit(); 
        }
    }
}


// Register Function
function userRegister($username, $pass, $discord = NULL) {
    global $pdo;
    global $us_date;
    global $ip;
    global $url;

    // Length Checks
    if (strlen($pass) < 6) {
        header('Location: ' . $url['register'] . '?password=short');
        exit();
    } elseif (strlen($pass) > 120) {
        header('Location: ' . $url['register'] . '?password=long');
        exit();
    } elseif (strlen($username) > 36) {
        header('Location: ' . $url['register'] . '?username=long');
        exit();
    }

    // Check Username Status
    if (dbquery('SELECT COUNT(username) as count FROM users WHERE username="' . escapestring($username) . '"')[0]['count'] > 0) {
        header('Location: ' . $url['register'] . '?username=taken');
        exit();
    }

    $settings = dbquery('SELECT * FROM settings')[0];

    if (discordModule_isInstalled) {
        $password = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
        if($settings['validation_enabled'] == "Yes" || $settings['validation_enabled'] == "yes") {
            dbquery('INSERT INTO users (username, password, usergroup, join_date, join_ip, discord) VALUES (
                "' . escapestring($username) . '",
                "' . $password . '",
                "Unverified",
                "' . escapestring($us_date) . '",
                "' . escapestring($ip) . '",
                "' . escapestring($discord) . '"
            )', false);
        } else {
            dbquery('INSERT INTO users (username, password, usergroup, join_date, join_ip, discord) VALUES (
                "' . escapestring($username) . '",
                "' . $password . '",
                "User",
                "' . escapestring($us_date) . '",
                "' . escapestring($ip) . '",
                "' . escapestring($discord) . '"
            )', false);
        }
        // plugins::call('userRegistered', array("user" => $username, "discord" => $discord));
        header('Location: ' . $url['welcome'] . '');
        exit();
    } else {
        $password = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
        if($settings['validation_enabled'] == "Yes" || $settings['validation_enabled'] == "yes") {
            dbquery('INSERT INTO users (username, password, usergroup, join_date, join_ip, discord) VALUES (
                "' . escapestring($username) . '",
                "' . $password . '",
                "Unverified",
                "' . escapestring($us_date) . '",
                "' . escapestring($ip) . '",
                "' . escapestring($discord) . '"
            )', false);
        } else {
            dbquery('INSERT INTO users (username, password, usergroup, join_date, join_ip) VALUES (
                "' . escapestring($username) . '",
                "' . $password . '",
                "User",
                "' . escapestring($us_date) . '",
                "' . escapestring($ip) . '"
            )', false);
        }
        //plugins::call('userRegistered', array("user" => $username));
        header('Location: ' . $url['welcome'] . '');
        exit();
    }
}

// Create Identity Function
function createIdentity($identifier) {
    global $url;
    global $identity_approval_needed;
    global $user_id;
    global $user_username;

    // Check If Identifier Already Taken
    if (dbquery('SELECT COUNT(identifier) as count FROM identities WHERE identifier="' . escapestring($_POST['identifier']) . '"')[0]['count'] > 0) {
        header('Location: ' . $url['index'] . '?identifier=taken');
        exit();
    }

    if ($identity_approval_needed === "no") {
      dbquery('INSERT INTO identities (identifier, user, is_leo user_name) VALUES ("' . escapestring($_POST['identifier']) . '", "' . escapestring($user_id) . '", "Yes", ' . escapestring($user_username) . '")', false);
      header('Location: ' . $url['index'] . '?identifier=created');
    } else {
      dbquery('INSERT INTO identities (identifier, user, status, user_name) VALUES ("' . escapestring($_POST['identifier']) . '", "' . escapestring($user_id) . '", "Approval Needed", "' . escapestring($user_username) . '")', false);
      header('Location: ' . $url['index'] . '?identifier=approval');
    }
}

function deleteIdentityLEO($identity_id_update, $identifier_update, $leo_supervisor_update){
    global $url;
    global $user_username;

    dbquery('DELETE FROM identities WHERE identity_id = "' . $identity_id_update . '"', false);
    logAction('(LEO) DELETED '. $identity_id_update .'', $user_username . ' / ' . $_SESSION['identifier']);
    header('Location: ' . $url['leo_supervisor_view_all_identities'] . '?id=deleted');
    exit();
}

function editIdentityLEO($identity_id_update, $identifier_update, $leo_update, $leo_supervisor_update, $is_dispatch_update) {
    global $url;
    global $user_username;

    dbquery('UPDATE identities SET `identifier`="' . escapestring($identifier_update) . '", `is_leo`="' . escapestring($leo_update) . '", `leo_supervisor`="' . escapestring($leo_supervisor_update) . '", `is_dispatch`="' . escapestring($is_dispatch_update) . '" WHERE identity_id="' . $identity_id_update . '"', false);
    logAction('(LEO) EDITED '. $identity_id_update .'', $user_username . ' / ' . $_SESSION['identifier']);
    header('Location: ' . $url['leo_supervisor_view_all_identities'] . '?id=edited');
    exit();
}
