<?php
// Arquivo de conexão com o banco de dados
$host = 'localhost';
$dbname = 'aproximati';
$user = 'root';
$pass = ''; 

try {
    // Tenta criar a conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    // Se a conexão falhar, exibe uma mensagem de erro clara e encerra o script.
    throw new PDOException($e->getMessage());
}
?>