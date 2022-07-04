<?php

//Define variables and set empty values
$usernameError = $phoneError = $emailError = $passwordError = $success = "";
$username = $phone = $email = $password = "";

//Form is submitted with POST method
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signUp"])) {
    if (empty($_POST["username"])) {  //Validate if the input of the username isn't empty
        $usernameError = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
        //Check if the username just contains letters
        if (!preg_match("/^[a-zA-Z]*$/", $username)) {
            $usernameError = "Only letters allowed";
        }
    }

    if (empty($_POST["phone"])) {     //Validate if the input of the phone isn't empty
        $phoneError = "Phone is required";
    } else {
        $phone = cleanInput($_POST["phone"]);
        //Check if the phone contains a + at the beginning
        if (!preg_match("/^[+]/", $phone)) {
            $phoneError = "Must contain a '+' at the beginning";
        } elseif (!preg_match("/^\d{9}$/", preg_replace("/[+]/", "", $phone))) {  //Check if the phone have 9 digits
            $phoneError = "It must be a 9 digit number";
        } elseif (!preg_match("/^[+]+\d{9}$/", $phone)) {
            $phoneError = "Not a valid number";
        }
    }

    if (empty($_POST["email"])) {     //Validate if the input of the email isn't empty
        $emailError = "Email is required";
    } else {
        $email = cleanInput($_POST["email"]);
        //Check if is a valid email
        if (!preg_match("/\S+@\S+\.\S+/", $email)) {
            $emailError = "Not a valid email";
        }
    }

    if (empty($_POST["password"])) {      //Validate if the input of the password isn't empty
        $passwordError = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
        //Check if at least one letter is uppercase
        if (!preg_match("/(?=.*[A-Z])/", $password)) {
            $passwordError = "At least one letter must be uppercased";
        } elseif (!preg_match("/(?=.*[\*\-\.])/", $password)) {  //Check if the password have at least one of the special characters: *-.
            $passwordError = "At least one of these special characters: *-.";
        } elseif (!preg_match("/(?=.*[A-Z])(?=.*[\*\-\.])(.{6})$/", $password)) {    //Check if the password have 6 characters
            $passwordError = "Must contain 6 characters";
        }
    }

    /**
     * If there are not errors, username and password are passed into an array wich is then converted
     * to json and passed to a flat file.
     * It is assumed that the file with the json already exists, so it is readed and converted to an array,
     * it is validated, and then the new username and password is added to it 
     * and then it is converted back to json and written to the file.
     */

    if ($usernameError=="" && $emailError=="" && $phoneError=="" && $passwordError=="") {
        $users = array();
        $users = file_get_contents('users.json');
        $users = json_decode($users, true);
        if (array_key_exists($username,$users)) {     //It is validated that is not in the json file
            $usernameError = "Username already exist";
        } else {
            print_r("Correct!");
            $success = "Account created successfully!";
            $users[$username] = $password;
            file_put_contents('users.json', json_encode($users));
        }
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