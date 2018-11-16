<?php
/**
    Hydrid CAD/MDT - Computer Aided Dispatch / Mobile Data Terminal for use in GTA V Role-playing Communities.
    Copyright (C) 2018 - Hydrid Development Team

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
**/
require 'includes/connect.php';
session_start();
include('includes/config.php');
require 'classes/lib/password.php';

if (isset($_POST['registerbtn'])) {
    //Pull the variables from the form
    $username_form = !empty($_POST['username']) ? trim($_POST['username']) : null;
    $email_form      = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $pass_form       = !empty($_POST['password']) ? trim($_POST['password']) : null;
    $discord_form       = !empty($_POST['discord']) ? trim($_POST['discord']) : null;
    //Sanitize the variables, prevents xss, etc.
    $username        = strip_tags($username_form);
    $email           = strip_tags($email_form);
    $pass            = strip_tags($pass_form);
    $discord            = strip_tags($discord_form);
    //Add any checks (length, etc here....)
    if (strlen($pass) < 6) {
        header('Location: ' . $url_register . '?password=short');
        exit();
    } elseif (strlen($pass) > 120) {
        header('Location: ' . $url_register . '?password=long');
        exit();
    } elseif (strlen($username) > 36) {
        header('Location: ' . $url_register . '?username=long');
        exit();
    }
    //Continue the execution, check if email is taken.
    $sql  = "SELECT COUNT(email) AS num FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['num'] > 0) {
        header('Location: ' . $url_register . '?email=taken');
        exit();
    }
    //Continue the execution, check if username is taken.
    $sql  = "SELECT COUNT(username) AS num FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['num'] > 0) {
        header('Location: ' . $url_register . '?username=taken');
        exit();
    }
    if (discordModule_isInstalled) {
      if ($settings_sign_up_verification_db === "yes") {
        //if everything passes, than continue
        $uvusergroup = "Unverified";
        $passwordHash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
        $sql          = "INSERT INTO users (username, email, password, usergroup, join_date, join_ip, discord) VALUES (:username, :email, :password, :usergroup, :join_date, :join_ip, :discord)";
        $stmt         = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':usergroup', $uvusergroup);
        $stmt->bindValue(':join_date', $us_date);
        $stmt->bindValue(':join_ip', $ip);
        $stmt->bindValue(':discord', $discord);
        $result = $stmt->execute();
        if ($result) {
            //redirect
            header('Location: ' . $url_welcome . '');
            exit();
        }
      } else {
        //if everything passes, than continue
        $passwordHash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
        $sql          = "INSERT INTO users (username, email, password, join_date, join_ip, discord) VALUES (:username, :email, :password, :join_date, :join_ip, :discord)";
        $stmt         = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':join_date', $us_date);
        $stmt->bindValue(':join_ip', $ip);
        $stmt->bindValue(':discord', $discord);
        $result = $stmt->execute();
        if ($result) {
            //redirect
            header('Location: ' . $url_welcome . '');
            exit();
        }
      }
    } else {
      if ($settings_sign_up_verification_db === "yes") {
        //if everything passes, than continue
        $uvusergroup = "Unverified";
        $passwordHash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
        $sql          = "INSERT INTO users (username, email, password, usergroup, join_date, join_ip) VALUES (:username, :email, :password, :usergroup, :join_date, :join_ip)";
        $stmt         = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':usergroup', $uvusergroup);
        $stmt->bindValue(':join_date', $us_date);
        $stmt->bindValue(':join_ip', $ip);
        $result = $stmt->execute();
        if ($result) {
            //redirect
            header('Location: ' . $url_welcome . '');
            exit();
        }
      } else {
        //if everything passes, than continue
        $passwordHash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
        $sql          = "INSERT INTO users (username, email, password, join_date, join_ip) VALUES (:username, :email, :password, :join_date, :join_ip)";
        $stmt         = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':join_date', $us_date);
        $stmt->bindValue(':join_ip', $ip);
        $result = $stmt->execute();
        if ($result) {
            //redirect
            header('Location: ' . $url_welcome . '');
            exit();
        }
      }
    }
}

//Error Messages
if (isset($_GET['password']) && strip_tags($_GET['password']) === 'short') {
   $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> Please use a longer password.</div>';
} elseif (isset($_GET['password']) && strip_tags($_GET['password']) === 'long') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> Please use a shorter password.</div>';
} elseif (isset($_GET['username']) && strip_tags($_GET['username']) === 'long') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> Please use a shorter username.</div>';
} elseif (isset($_GET['email']) && strip_tags($_GET['email']) === 'taken') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> That email is already in-use.</div>';
} elseif (isset($_GET['email']) && strip_tags($_GET['email']) === 'taken') {
  $message = '<div class="alert alert-danger" role="alert"><strong>ERROR:</strong> That username is already in-use.</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Register";
include('includes/header.php')
?>
   <body>
      <div class="container">
         <div class="main">
            <img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/>
            <div class="main-header">
               Please Register To Continue
            </div>
            <?php print($message); ?>
            <form method="post" action="register.php">
               <div class="row">
                  <div class="col">
                     <div class="form-group">
                        <input type="text" name="username" class="form-control" maxlength="36" placeholder="Username" title="This must be the name you use on discord." data-lpignore="true" required />
                     </div>
                  </div>
                  <div class="col">
                     <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" title="This must be a valid email." data-lpignore="true" required />
                     </div>
                  </div>
               </div>
               <?php if (discordModule_isInstalled): ?>
                 <div class="form-group">
                    <input type="text" name="discord" class="form-control" placeholder="Discord#Tag" data-lpignore="true" required />
                 </div>
               <?php endif; ?>
               <div class="form-group">
                  <input type="password" name="password" class="form-control" placeholder="Password" title="Please do not use a common password." data-lpignore="true" required />
               </div>
               <div class="form-group">
                  <input class="btn btn-block btn-primary" name="registerbtn" id="registerbtn" type="submit" value="Finish Signup">
               </div>
               <text>Already have an account? <a href="<?php print($url_login) ?>">Login</a></text>
               <?php echo $ftter; ?>
            </form>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
