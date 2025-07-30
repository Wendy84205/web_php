<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=food_web", "root", "08042005");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}