<?php

namespace samjoyce\slugger;

require 'src/samjoyce/slugger/slugger.php';


$database = 'mysql:dbname=samholidays;host=127.0.0.1';
$user = 'root';
$password = '';

try {
    $db = new \PDO($database, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


$slug = new slugger($db);

echo 'slug: ';

echo $slug->set('properties')->create('lake-buddy', array('91', 'hunger'));

