<?php include('signUpController.php'); ?>

<link rel='stylesheet' href='form.css' type='text/css'>

<form class="log-in" action="<?= $_SERVER['PHP_SELF'];?>" method="post">
    <h1 class="title">Welcome!</h1>
    <input type="text" class="input" name="email" value="<?= $email ?>" autofocus placeholder="Email">
    <span class="error"><?= $emailError ?></span>      
    <input type="text" class="input" name="phone" value="<?= $phone ?>" placeholder="Phone Number">
    <span class="error"><?= $phoneError ?></span>      
    <input type="text" class="input" name="username" value="<?= $username ?>" placeholder="Username">
    <span class="error"><?= $usernameError ?></span>      
    <input type="password" class="input" name="password" value="<?= $password ?>" placeholder="Password">
    <span class="error"><?= $passwordError ?></span>      
    <span class="success"><?= $success ?></span>
    <button class="input submit" name= "signUp">Sign Up</button>
    <button class="input submit" type="button" onclick="location.href='login.php'">Log In</button>
  </form>