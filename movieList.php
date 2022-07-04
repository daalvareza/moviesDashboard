<?php 
/**
 * Movie List View (Search Movies)
 */

// Starts the session
session_start();
// If the session is not set, redirect to login
if(!isset($_SESSION['USERDATA']['USERNAME'])){
    header("location:login.php");
    exit;
}
include('movieListController.php'); 
?>

<link rel='stylesheet' href='form.css' type='text/css'>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src='table.js' type='text/javascript'></script>
<h1 class="title">Search Movies!</h1>
<form class="movies" action="<?= $_SERVER['PHP_SELF'];?>" method="post">
<div class="filters">
    <div class="filter-holder">
        <div class="filter-label">Title</div>
        <input type="text" class="input" name="movieTitle" value="<?= $title ?>" placeholder="Title">
        <span class="error"><?= $error ?></span>
    </div>
    <div class="filter-holder">
        <div class="filter-label">Year</div>
        <input type="text" class="input" name="movieYear" value="<?= $year ?>" placeholder="Year">
    </div>
    <button class="input submit" name="search">Search</button>
    <button class="input submit" name="save">Save</button>
    <span class="success"><?= $success ?></span>
    </div>
</form>
<button class="input submit logout" type="button" onclick="location.href='logout.php'">Log Out</button>
<?php
    // Define the columns orderable
    $columsSettings = [
        "Title"  => ["orderable" => "Title"],
        "Year"   => ["orderable" => "Year"],
        "imdbID" => ["orderable" => "imbID"],
        "Type"   => ["orderable" => "Type"]
    ];
    // Define the pagination settings
    $paginationSettings = [
        "pagination" => "yes",
        "rowsToShow" => 10,
        "inputShow"  => "yes",
        "hitsNumber" => "yes",
    ];
    // Validate if the data has already returned
    if ($data) {
        // Map the array of the data to set the image of the poster
        $data = array_map(function($movie) {
            $movie['Poster'] = "<img class=\"poster\" src=\"{$movie['Poster']}\"></img>";
            return $movie;
        }, $data);
        // Render the table
        echo $movies->jsDataTable($data, "tableMovies", $columsSettings, [], [], $paginationSettings);
    }
?>