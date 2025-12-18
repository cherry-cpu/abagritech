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

    header('Content-Type: application/sql');
    header('Content-Disposition: attachment;filename=applications.sql');

    $sql = "INSERT INTO `exam_applications` (`".implode('`,`', array_keys($apps[0]??[]))."`) VALUES\n";
    $lines = [];
    foreach($apps as $app){
        $vals = array_map(function($v) use ($pdo){
            return "'".str_replace("'", "''", $v)."'";
        }, $app);
        $lines[] = "(".implode(",", $vals).")";
    }
    $sql .= implode(",\n",$lines).";";
    echo $sql;

}catch(PDOException $e){
    error_log("EXPORT SQL ERROR: ".$e->getMessage());
    die("Database error");
}
