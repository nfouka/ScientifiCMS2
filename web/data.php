<?php


try {
    $dbh = new PDO('mysql:host=localhost;dbname=database', 'root', 'root');
    $data = array();
    foreach($dbh->query('SELECT * FROM `symfony_demo_post`') as $row) {
         $data[] = array(
            'id' =>  $row['id'] , 
            'title' =>  $row['title'] , 
            'summary' =>  $row['summary'] , 
            'slug' =>  $row['slug'] , 
            'publishedAt' =>  $row['id'] , 
        ) ; 
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

echo json_encode($data);