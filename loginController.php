<?php

//read the json file and save it in an array to compare if the user has already been registered
$users = file_get_contents('users.json');
$users = json_decode($users, true);

//Define variables and set empty values
$usernameLoginError = $passwordLoginError = $successLogin = "";
$usernameLogin = $passwordLogin = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    if (empty($_POST["usernameLogin"])) { //Validate if the input of the username isn't empty
        $usernameLoginError = "Username is required";
    } else {
        $usernameLogin = cleanInput($_POST["usernameLogin"]);
        //Check if the username just contains letters
        if (!preg_match("/^[a-zA-Z]*$/", $usernameLogin)) {
            $usernameLoginError = "Only letters allowed";
        } elseif (!array_key_exists($usernameLogin, $users)) {     //Check if the username already exist
            $usernameLoginError = "Username doesn't exist";
        }
    }

    if (empty($_POST["passwordLogin"])) { //Validate if the input of the password isn't empty
        $passwordLoginError = "Password is required";
    } else {
        $passwordLogin = cleanInput($_POST["passwordLogin"]);
        //Check if at least one letter is uppercase
        if (!preg_match("/(?=.*[A-Z])/", $passwordLogin)) {
            $passwordLoginError = "At least on letter must be uppercased";
        } elseif(!preg_match("/(?=.*[\*\-\.])/", $passwordLogin)) {    //Check if the password have at least one of the special characters: *-.
            $passwordLoginError = "At least on of these special characters: *-.";
        } elseif(!preg_match("/(?=.*[A-Z])(?=.*[\*\-\.])(.{6})$/", $passwordLogin)) {   //Check if the password have 6 characters
            $passwordLoginError = "Must contain 6 characters";
        } elseif(array_key_exists($usernameLogin, $users)) {    //Check if the password corresponds to the username
            if ($users[$usernameLogin] != $passwordLogin) {
                $passwordLoginError = "Password is not correct";
            }
        } 
    }

    /**
     * If the username exist in the json file and the password corresponds with the username
     * start the session with the data of the user and redirects to the movie list page
     */
    if (isset($users[$usernameLogin]) && $users[$usernameLogin] == $passwordLogin) {
        session_start();
        $_SESSION['USERDATA']['USERNAME'] = $users[$usernameLogin];
        header("location:movieList.php");
        exit;
    }
}

//Clean the data input deleting unnecessary spaces, slashes and html special characters
function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>