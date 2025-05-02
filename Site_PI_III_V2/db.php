<?php
/**
 * Arquivo de configuração da conexão com o banco de dados.
 * 
 * Este arquivo define uma função para estabelecer uma conexão PDO
 * com o banco de dados MySQL utilizado pela aplicação.
 */

/**
 * Estabelece e retorna uma conexão PDO com o banco de dados.
 *
 * Utiliza as credenciais definidas para conectar ao banco 'eco_cadastro'.
 * Configura o PDO para lançar exceções em caso de erro e define o charset para UTF-8.
 *
 * @return PDO|null Retorna um objeto PDO em caso de sucesso ou null em caso de falha.
 */
function getConnection() {
    // --- Configurações de Conexão ---
    $host = 'localhost';      // Endereço do servidor do banco de dados (geralmente localhost)
    $dbname = 'eco_cadastro'; // Nome do banco de dados
    $user = 'eco';            // Nome de usuário para acesso ao banco
    $pass = 'Eloysa010307';   // Senha para acesso ao banco (ATENÇÃO: Armazenar senhas em código é inseguro para produção! Considere usar variáveis de ambiente.)

    try {
        // Tenta estabelecer a conexão PDO
        $conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8", // DSN (Data Source Name) especificando driver, host, dbname e charset
            $user, // Usuário do banco
            $pass  // Senha do banco
        );

        // --- Configurações do PDO ---
        // Define o modo de erro para Exception: Lança PDOExceptions em caso de erro.
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Define o modo de fetch padrão para associative array (opcional, mas comum)
        // $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Retorna o objeto de conexão PDO bem-sucedido
        return $conn;

    } catch (PDOException $e) {
        // --- Tratamento de Erro ---
        // Em caso de falha na conexão, captura a exceção PDOException
        // Exibe uma mensagem de erro genérica (em produção, logar o erro em vez de exibir)
        // echo 'Erro ao conectar ao banco de dados: ' . $e->getMessage(); // Comentado para não expor detalhes do erro ao usuário final
        
        // Em um ambiente de produção, seria melhor logar o erro e talvez retornar null ou lançar uma exceção personalizada.
        error_log("Erro de conexão PDO: " . $e->getMessage()); // Exemplo de log de erro
        die("Erro crítico: Não foi possível conectar ao banco de dados. Por favor, tente novamente mais tarde."); // Interrompe a execução com mensagem genérica
        
        // return null; // Alternativa: retornar null para indicar falha
    }
}
?>

