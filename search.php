<?php
    require_once __DIR__.'/functions.php';
    if(isset($_POST['submit'])){
        $dbh = connectToDatabase(); 
        $author_name = "%" . ucfirst($_POST['search']) . "%";  
        $resultAuthor = searching($dbh, $author_name);
        $toWrite = resultBuilder($resultAuthor);
        echo $toWrite;
    }
    
// header('Location: /index.php');
