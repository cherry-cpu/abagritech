<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') exit;

try{
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

    $stmt = $pdo->query("SELECT * FROM exam_applications");
    $apps = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=applications.csv');

    $output = fopen('php://output','w');
    fputcsv($output,array_keys($apps[0] ?? []));
    foreach($apps as $app){
        fputcsv($output,$app);
    }
    fclose($output);

}catch(PDOException $e){
    error_log("EXPORT CSV ERROR: ".$e->getMessage());
    die("Database error");
}
