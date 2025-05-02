<?php
// Incluir arquivos de configuração e conexão com o banco de dados (se necessário)
require_once 'config.php';
require_once 'db.php';
// Definir a página padrão
$page = 'index.html';
// Verificar se alguma página foi solicitada na URL
if (isset($_GET['page'])) {
    $page = $_GET['page'];
}
// Incluir a página solicitada
include $page;
?>

<?php
// Incluir arquivos de configuração e conexão com o banco de dados (se necessário)
require_once 'config.php';
require_once 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $nome = $_POST['inputNome'];
    $endereco = $_POST['inputEndereco'];
    $complemento = $_POST['inputComplemento'];
    $cep = $_POST['inputCEP'];
    $bairro = $_POST['inputBairro'];
    $email = $_POST['inputEmail'];
    $senha = $_POST['inputSenha'];
    // Inserir os dados no banco de dados
    $sql = "INSERT INTO cadastro (nome, endereco, complemento, cep, bairro, email, senha) VALUES ('$nome', '$endereco', '$complemento', '$cep', '$bairro', '$email', '$senha')";
    // Executar a consulta SQL de inserção
    if (mysqli_query($conexao, $sql)) {
        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados: " . mysqli_error($conexao);
    }
    // Fechar a conexão com o banco de dados (se necessário)
    mysqli_close($conexao);
}
?>