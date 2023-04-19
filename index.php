<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/functions.php';

$faker = Faker\Factory::create();

$dbh = connectToDatabase();
$authors = createDataAuthor(5, $faker);


$author_ids = fillAuthors($dbh, $authors);
$articles = createDataArticle($author_ids, $faker);

fillArticles($dbh, $articles);
