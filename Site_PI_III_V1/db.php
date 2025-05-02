<?php
function getConnection() {
    $host = 'localhost';
    $dbname = 'eco_cadastro';
    $user = 'eco';
    $pass = 'Eloysa010307';
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    }
}
?>