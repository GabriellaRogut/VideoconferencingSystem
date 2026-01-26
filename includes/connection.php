<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "VideoconferencingSystem";

    try {
        $connection = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    
    session_start();
    