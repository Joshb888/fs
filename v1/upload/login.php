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

if (isset($_POST['loginbtn'])) {
    //Pull from form
    $username_form        = !empty($_POST['username']) ? trim($_POST['username']) : null;
    $passwordAttempt_form = !empty($_POST['password']) ? trim($_POST['password']) : null;
    //Sanitize
    $username      = strip_tags($username_form);
    $passwordAttempt      = strip_tags($passwordAttempt_form);
    //Execute
    $sql             = "SELECT user_id, username, password FROM users WHERE username = :username";
    $stmt            = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user === false) {
        header('Location: ' . $url_login . '?user=notfound');
        exit();
    } else {
        $validPassword = password_verify($passwordAttempt, $user['password']);
        if ($validPassword) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['logged_in'] = time();
            header('Location: ' . $url_index . '?logged=in');
            exit();
        } else {
            header('Location: ' . $url_login . '?password=invalid');
            exit();
        }
    }
}
//Error Msgs
if (isset($_GET['user']) && strip_tags($_GET['user']) === 'notfound') {
   $message = '<div class="alert alert-danger" role="alert">That account was not found in our system.</div>';
} elseif (isset($_GET['password']) && strip_tags($_GET['password']) === 'invalid') {
  $message = '<div class="alert alert-danger" role="alert">Your password was wrong.</div>';
} elseif (isset($_GET['unverified']) && strip_tags($_GET['unverified']) === 'true') {
  $message = '<div class="alert alert-danger" role="alert">Your account is not verified.</div>';
} elseif (isset($_GET['account']) && strip_tags($_GET['account']) === 'banned') {
  $message = '<div class="alert alert-danger" role="alert">Your account has been banned. Please contact staff.</div>';
} elseif (isset($_GET['update']) && strip_tags($_GET['update']) === 'ip') {
  $message = '<div class="alert alert-danger" role="alert"><strong>Hydrid is currently being updated. This is a panel wide update, thus please do not message the server owner about this.</i></strong></div>';
} elseif (isset($_GET['settings']) && strip_tags($_GET['settings']) === 'updated') {
   $message = '<div class="alert alert-success" role="alert">Your settings have been updated. Please login again!</div>';
}
?>
<!DOCTYPE html>
<html>
<?php
$page_name = "Login";
include('includes/header.php')
?>
      <div class="container">
         <div class="main">
            <img src="assets/imgs/los_santos.png" class="main-logo" draggable="false"/>
            <div class="main-header">
               Hydrid Login
            </div>
            <?php print($message); ?>
            <form method="post" action="login.php">
              <div class="form-group">
                 <input type="text" name="username" class="form-control" placeholder="Username" maxlength="36" data-lpignore="true" required />
              </div>
               <div class="form-group">
                  <input type="password" name="password" class="form-control" placeholder="Password" data-lpignore="true" required />
               </div>
               <div class="form-group">
                  <input class="btn btn-block btn-primary" name="loginbtn" id="loginbtn" type="submit" value="Login">
               </div>
               <text>Need an account? <a href="<?php print($url_register) ?>">Register</a></text>
               <?php echo $ftter; ?>
            </form>
         </div>
      </div>
      <?php include('includes/js.php'); ?>
   </body>
</html>
