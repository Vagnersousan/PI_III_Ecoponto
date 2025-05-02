<?php
/**
 * Script PHP para processar o cadastro de novos usuários.
 * 
 * Este script recebe os dados enviados através do método POST a partir do formulário
 * localizado em 'cadastro.html'. Ele realiza uma validação mínima (verificando se
 * o método da requisição é POST) e, em seguida, tenta inserir as informações
 * do novo usuário na tabela 'cadastro' do banco de dados.
 * 
 * Inclui tratamento básico de erros e feedback para o usuário através de JavaScript.
 * 
 * @package    SitePIIII
 * @subpackage Cadastro
 */

// --- Configuração de Exibição de Erros --- 
// É altamente recomendável desativar a exibição de erros em ambiente de produção
// por motivos de segurança. Durante o desenvolvimento, habilitar a exibição ajuda na depuração.
error_reporting(E_ALL); // Reporta todos os tipos de erros PHP.
ini_set("display_errors", 1); // Configura o PHP para exibir os erros na saída (1 = Ativado, 0 = Desativado).

// --- Inclusão de Arquivos Necessários --- 
// Inclui o arquivo 'db.php', que presume-se conter a lógica para estabelecer
// a conexão com o banco de dados (provavelmente usando PDO).
// 'require_once' é usado para garantir que o arquivo seja incluído apenas uma vez.
// Se o arquivo não for encontrado, um erro fatal será gerado, interrompendo o script.
require_once "db.php";

// --- Verificação do Método da Requisição HTTP --- 
// Verifica se a página foi acessada através de uma requisição POST.
// Formulários de cadastro devem enviar dados via POST por segurança e capacidade.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Coleta dos Dados Enviados pelo Formulário --- 
    // Os dados são acessados através da variável superglobal $_POST, que contém
    // um array associativo com os dados enviados.
    // 
    // IMPORTANTE: Nenhuma validação ou sanitização significativa está sendo aplicada aqui.
    // Isso representa um RISCO DE SEGURANÇA considerável (ex: SQL Injection, Cross-Site Scripting - XSS).
    // Em um ambiente real, é ESSENCIAL validar (formato, tipo, obrigatoriedade) e
    // sanitizar (limpar dados potencialmente maliciosos) CADA um dos campos recebidos.
    $nome = $_POST["inputNome"];
    $endereco = $_POST["inputEndereco"];
    $complemento = $_POST["inputComplemento"]; // Campo opcional, pode estar vazio.
    $cep = $_POST["inputCEP"];
    $bairro = $_POST["inputBairro"]; // Campo opcional, pode estar vazio.
    $email = $_POST["inputEmail"];
    $senha = $_POST["inputSenha"]; // A senha está sendo capturada em texto plano.

    // --- Validação e Sanitização (Exemplos Mínimos - DEVEM SER EXPANDIDOS) ---
    // Esta seção demonstra exemplos básicos de validação. Uma aplicação real
    // necessitaria de validações muito mais robustas.
    
    // Exemplo 1: Verificar se campos obrigatórios estão preenchidos.
    if (empty($nome) || empty($endereco) || empty($cep) || empty($email) || empty($senha)) {
        // Se algum campo obrigatório estiver vazio, exibe um alerta JavaScript e interrompe o script.
        // 'die()' ou 'exit()' interrompem a execução do script.
        // 'window.history.back()' tenta retornar o usuário à página anterior (o formulário).
        die("<script>alert('Erro: Todos os campos obrigatórios (Nome, Endereço, CEP, E-mail, Senha) devem ser preenchidos.'); window.history.back();</script>");
    }
    
    // Exemplo 2: Validar o formato do e-mail.
    // 'filter_var' com 'FILTER_VALIDATE_EMAIL' é uma forma padrão de verificar a sintaxe do e-mail.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Erro: O formato do e-mail fornecido é inválido.'); window.history.back();</script>");
    }
    
    // Exemplo 3: Sanitizar o nome (remover tags HTML e caracteres potencialmente perigosos).
    // 'FILTER_SANITIZE_STRING' está obsoleto a partir do PHP 8.0. Use 'htmlspecialchars' ou outras abordagens.
    // $nome = filter_var($nome, FILTER_SANITIZE_STRING); // Obsoleto
    $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); // Alternativa mais segura
    
    // TODO: Aplicar validação e sanitização adequadas a TODOS os outros campos ($endereco, $cep, $bairro, etc.).
    // Considerar validação de formato do CEP, limites de tamanho para os campos, etc.

    // --- Segurança da Senha (Hashing) --- 
    // ARMAZENAR SENHAS EM TEXTO PLANO É UM RISCO GRAVE DE SEGURANÇA!
    // Utiliza a função 'password_hash()' do PHP para criar um hash seguro da senha.
    // 'PASSWORD_DEFAULT' seleciona automaticamente o algoritmo de hash mais forte disponível no servidor (atualmente bcrypt).
    // O hash resultante inclui o algoritmo usado e um salt gerado aleatoriamente, tornando-o seguro contra ataques comuns.
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // --- Conexão com o Banco de Dados --- 
    // Chama a função 'getConnection()' (definida em 'db.php') para obter um objeto de conexão PDO.
    $conn = getConnection();

    // Verifica se a conexão foi estabelecida com sucesso.
    // A função getConnection() idealmente já trataria falhas de conexão (ex: logando o erro e/ou interrompendo).
    if (!$conn) {
        // Adiciona uma verificação extra por segurança.
        error_log("Falha crítica ao obter conexão do banco de dados em cadastrar_usuario.php");
        die("<script>alert('Erro crítico no sistema. Não foi possível conectar ao banco de dados. Tente novamente mais tarde.'); window.history.back();</script>");
    }

    // --- Preparação e Execução da Consulta SQL (INSERT) --- 
    // Define a instrução SQL para inserir um novo registro na tabela 'cadastro'.
    // Utiliza 'placeholders' (?) para os valores. Isso é essencial para usar Prepared Statements,
    // que previnem ataques de Injeção de SQL.
    $sql = "INSERT INTO cadastro (nome, endereco, complemento, cep, bairro, email, senha) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    try {
        // Prepara a instrução SQL para execução. O PDO verifica a sintaxe da query.
        $stmt = $conn->prepare($sql);

        // Executa a instrução preparada, passando os valores reais para os placeholders.
        // Os valores são passados como um array na ordem correspondente aos placeholders (?).
        // IMPORTANTE: Passa o '$senhaHash' em vez da senha original '$senha'.
        $stmt->execute([$nome, $endereco, $complemento, $cep, $bairro, $email, $senhaHash]);

        // --- Feedback de Sucesso ao Usuário --- 
        // Se a execução foi bem-sucedida (nenhuma exceção foi lançada).
        // Exibe um alerta JavaScript informando o sucesso e redireciona o usuário para a página inicial ('index.html').
        // Uma abordagem melhor seria redirecionar primeiro e mostrar a mensagem na página de destino
        // (usando variáveis de sessão, por exemplo - "flash messages").
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = 'index.html';</script>";
        exit; // Termina a execução do script após o redirecionamento.

    } catch (PDOException $e) {
        // --- Tratamento de Erros na Execução da Query --- 
        // Captura exceções do tipo PDOException, que indicam erros relacionados ao banco de dados.
        
        // Verifica o código de erro SQLSTATE. '23000' geralmente indica uma violação de restrição de integridade,
        // como uma chave única duplicada (por exemplo, tentar cadastrar um e-mail que já existe).
        // O código exato pode variar ligeiramente dependendo do SGBD.
        if ($e->getCode() == 23000) { 
             // Informa ao usuário que o e-mail já está em uso.
             echo "<script>alert('Erro: Este e-mail já está cadastrado em nosso sistema. Por favor, utilize outro e-mail ou tente fazer login.'); window.history.back();</script>";
        } else {
            // Para outros tipos de erros de banco de dados, loga a mensagem de erro detalhada
            // para análise posterior pelo administrador do sistema.
            error_log("Erro PDO ao executar INSERT em cadastrar_usuario.php: " . $e->getMessage()); 
            
            // Exibe uma mensagem genérica de erro para o usuário, sem expor detalhes técnicos.
            echo "<script>alert('Ocorreu um erro inesperado ao processar seu cadastro. Por favor, tente novamente mais tarde.'); window.history.back();</script>";
            // Em desenvolvimento, pode ser útil descomentar a linha abaixo para ver o erro exato:
            // echo "Erro no banco de dados: " . $e->getMessage(); 
        }
        exit; // Termina a execução do script após tratar o erro.
    }

} else {
    // --- Tratamento para Método de Requisição Inválido --- 
    // Se a página foi acessada por um método diferente de POST (ex: GET diretamente pela URL).
    // Redireciona o usuário de volta para a página do formulário de cadastro.
    // Isso evita que o script tente processar dados inexistentes.
    // echo "Método de requisição inválido."; // Mensagem alternativa (menos usual)
    header("Location: cadastro.html"); // Envia um cabeçalho HTTP para redirecionar o navegador.
    exit; // Termina a execução do script após o redirecionamento.
}

?>

