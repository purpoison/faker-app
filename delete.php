<?php
    require_once __DIR__.'/functions.php';
    $dbh = connectToDatabase(); 
    if(isset($_GET['id'])){
        $author_id = (int)$_GET['id'];
        try {
            $sql = "DELETE FROM authors WHERE id = :id;";
            $sth = $dbh->prepare($sql);
            $sth->execute(
                [':id'=> $author_id]
            );
        } catch (PDOException $e) {
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
            exit;
        }
        $dbh = null;
        $sth = null;
    }
header('Location: /index.php');