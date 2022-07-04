<?php

session_start();    //Starst the session
session_destroy();  //Destroy started session
header("location:login.php");   //Redirect to login page
exit;

?>