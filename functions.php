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
    };

    function fillArticles($dbh, $articles){
        $addArticles = "INSERT INTO articles (author_id, title, body) VALUES (:author_id, :title, :body);";
        $article_ids = [];
        try {
            $sth = $dbh->prepare($addArticles);
            foreach($articles as $article){
                $sth->execute([
                    'author_id' => $article['author_id'],
                    'title' => $article['title'],
                    'body' => $article['body'],
                ]);
                $id = $dbh->lastInsertId();
                array_push($article_ids, $id);
            }

    
        } catch (PDOException $e) {
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
            exit;
        }
        return $article_ids;
    }

    function createDataComments($ids, $faker){
        $data = [];
        foreach($ids as $id){
            $data[] = [
                'article_id' => $id,
                'email' => $faker->email(),
                'comment' => $faker->text(200),
            ];
        }
        return $data;
    }

    function fillComments($dbh, $comments){
        $addComments = "INSERT INTO comments (article_id, email, comment) VALUES (:article_id, :email, :comment);";
        try {
            $sth = $dbh->prepare($addComments);
            foreach($comments as $comment){
                $sth->execute([
                    'article_id' => $comment['article_id'],
                    'email' => $comment['email'],
                    'comment' => $comment['comment'],
                ]);
            }
        } catch (PDOException $e) {
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
            exit;
        }
    }

    function getData($dbh){
        $sql = "SELECT
        authors.id,
        authors.name,
        COUNT(DISTINCT articles.id) AS posts,
        COUNT(DISTINCT comments.id) AS messages
        FROM authors
        LEFT JOIN articles ON authors.id = articles.author_id
        LEFT JOIN comments ON articles.id = comments.article_id
        GROUP BY authors.id
        ORDER BY posts DESC;";

        try{
            $sth = $dbh->query($sql);
            $result = $sth->fetchAll(PDO::FETCH_OBJ);
            // echo "<pre>";
            // var_dump($result);

        }catch (PDOException $e) {
            die("Error! Code: {$e->getCode()}. Message: {$e->getMessage()}".PHP_EOL);
            exit;
        }
        return $result;
    }
    function buildTable($data){
        if($data){
            echo "
            <table class='faker-table'>
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Posts</th>
                    <th>Messages</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            ";
            $posts_amt = 0;
            $messages_amt = 0;
            foreach($data as $row){
                $posts_amt+= $row->posts;
                $messages_amt+= $row->messages;
                echo "<tr>
                        <td>{$row->name}</td>
                        <td class='yellow'>{$row->posts}</td>
                        <td class='yellow'>{$row->messages}</td>
                        <td><a href='/delete.php?id={$row->id}'><img src='/img/delete.png' alt='delete'></a></td>
                    </tr>
                ";
            }
            echo "
            <tr> <td></td> <td class='yellow'>{$posts_amt}</td> <td class='yellow'>{$messages_amt}</td><td></td></tr>
            </tbody>
                    </table>";
        }
    }