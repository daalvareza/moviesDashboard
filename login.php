<?php include('loginController.php'); ?>

<link rel='stylesheet' href='form.css' type='text/css'>

<form class="log-in" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
    <h1 class="title">Welcome!</h1>
    <input type="text" class="input" name="usernameLogin" placeholder="Username">
    <span class="error"><?= $usernameLoginError ?></span>      
    <input type="password" class="input" name="passwordLogin" placeholder="Password">
    <span class="error"><?= $passwordLoginError ?></span>      
    <span class="success"><?= $successLogin ?></span>
    <button class="input submit" name="login">Log In</button>
    <button class="input submit" type="button" onclick="location.href='signUp.php'">Sign Up</button>
  </form>