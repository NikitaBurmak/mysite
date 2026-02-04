<?php
$dsn = 'pgsql:host=127.0.0.1;port=5432;dbname=myanecdotes';
$user = 'Nikita';
$pass = '2008';

try {
    $dbh = new PDO($dsn, $user, $pass);
    echo "Connected!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
