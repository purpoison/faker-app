<?php
    function connectToDatabase() {
        $dsn = 'mysql:host=localhost;dbname=fakerApp';
        $user = 'root';
        $password = '';
    
        try {
            $dbh = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {    
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
        }
        return $dbh;
    };

    function createDataAuthor($num, $faker){
        $data = [];
        for($i = 0; $i < $num; $i++){
            $data[] = [
                'name' => $faker->name(),
                'email' => $faker->email(),
                'address' => $faker->words(3, true),
                'website' => $faker->safeEmailDomain(),
                'phone' => $faker->phoneNumber()
            ];
        }
        return $data;
    }

    function createDataArticle($ids, $faker){
        $data = [];
        foreach($ids as $id){
            $data[] = [
                'author_id' => $id,
                'title' => $faker->sentence(),
                'body' => $faker->text(100),
            ];
        }
        return $data;
    }
    function fillAuthors($dbh, $authors){
        $author_ids = [];
        try {
            $addAuthors = "INSERT INTO authors (name, email, address, website, phone) VALUES (:name, :email, :address, :website, :phone);";
            $sth = $dbh->prepare($addAuthors);
    
            foreach($authors as $author){
    
                $sth->execute([
                    'name' => $author['name'],
                    'email' => $author['email'],
                    'address' => $author['address'],
                    'website' => $author['website'],
                    'phone' => $author['phone']
                ]);
                $id = $dbh->lastInsertId();
                array_push($author_ids, $id);
            }
    
        } catch (PDOException $e) {
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
            exit;
        }
    
        return $author_ids;
    
        $dbh = null;
        $sth = null;
    };

    function fillArticles($dbh, $articles){
        $addArticles = "INSERT INTO articles (author_id, title, body) VALUES (:author_id, :title, :body);";
        try {
            $sth = $dbh->prepare($addArticles);
            foreach($articles as $article){
                $sth->execute([
                    'author_id' => $article['author_id'],
                    'title' => $article['title'],
                    'body' => $article['body'],
                ]);
            }
    
        } catch (PDOException $e) {
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
            exit;
        }
        $dbh = null;
        $sth = null;
    }