<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['inputNome'];
    $endereco = $_POST['inputEndereco'];
    $complemento = $_POST['inputComplemento'];
    $cep = $_POST['inputCEP'];
    $bairro = $_POST['inputBairro'];
    $email = $_POST['inputEmail'];
    $senha = $_POST['inputSenha'];
    
    // Adicione a conexão com o banco de dados antes de executar a inserção
    $conn = getConnection();
    $sql = "INSERT INTO cadastro (nome, endereco, complemento, cep, bairro, email, senha) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $endereco, $complemento, $cep, $bairro, $email, $senha]);
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = 'index.html';</script>";
    } catch (PDOException $e) {
        echo "Erro no banco de dados: " . $e->getMessage();
    }
}
?>