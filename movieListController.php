<?php

error_reporting(E_ALL ^ E_NOTICE);

//Define variables and set empty values
$success = $error = "";
$name = $title = $year = "";

include('class-Movies.php');

$movies = new Movies();

/**
 * The button "Search" is for show the API response in a table
 * the title of the movie searched is always required, the year is optional
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["movieTitle"])) {
        $title = $_POST["movieTitle"];
        $year  = $_POST["movieYear"];
        $url   = $movies->buildURL($title, $year);
        $data  = $movies->requestInfo($url);
        /**
         * The button "Save" is for create a json file with the info returned by the API, 
         * create the file and copy the API response
         */
        if (isset($_POST["save"])) {
            $storage = fopen("movieList.txt", "w");
            fwrite($storage, json_encode($data));
            $success = "A file with your search was created!";
        }
    } else {
        $error = "Title is required";
    }
}

?>