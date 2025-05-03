<?php
/**
 * Script PHP para processar o cadastro de novos usuários com segurança aprimorada.
 * 
 * Este script recebe os dados enviados via POST do formulário 'cadastro.html'.
 * Implementa validações robustas, sanitização de entradas, proteção contra CSRF,
 * hashing seguro de senhas e tratamento de erros aprimorado para aumentar a segurança.
 * 
 * @package    SitePIIII
 * @subpackage CadastroSeguro
 */

// --- Inicialização da Sessão --- 
// Essencial para armazenar tokens CSRF e mensagens de feedback (flash messages).
// Deve ser chamado antes de qualquer saída para o navegador.
if (session_status() == PHP_SESSION_NONE) {
    // Configurações de segurança da sessão (recomendado)
    session_set_cookie_params([
        'lifetime' => 3600, // Tempo de vida do cookie da sessão (1 hora)
        'path' => '/', // Caminho onde o cookie estará disponível
        'domain' => $_SERVER['HTTP_HOST'], // Domínio atual
        'secure' => isset($_SERVER['HTTPS']), // True se HTTPS estiver ativo
        'httponly' => true, // Impede acesso ao cookie via JavaScript (protege contra XSS)
        'samesite' => 'Lax' // Proteção adicional contra CSRF
    ]);
    session_start();
}

// --- Configuração de Exibição de Erros --- 
// Em produção, é recomendado desativar a exibição de erros e logá-los em ficheiro.
error_reporting(E_ALL);
ini_set("display_errors", 0); // 0 = Desativado em produção
ini_set("log_errors", 1); // 1 = Ativar log de erros
// ini_set("error_log", "/caminho/para/seu/php-error.log"); // Especificar ficheiro de log

// --- Inclusão de Arquivos Necessários --- 
require_once "db.php"; // Contém a função getConnection() para conexão PDO.

// --- Funções Auxiliares de Segurança --- 

/**
 * Gera um token CSRF e armazena-o na sessão.
 * @return string O token CSRF gerado.
 */
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Gera um token aleatório seguro
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida o token CSRF enviado com o armazenado na sessão.
 * @param string $tokenEnviado O token recebido do formulário.
 * @return bool True se o token for válido, False caso contrário.
 */
function validarTokenCSRF($tokenEnviado) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenEnviado);
}

/**
 * Sanitiza uma string para prevenir XSS.
 * @param string|null $input A string de entrada.
 * @return string A string sanitizada.
 */
function sanitizarString($input) {
    return $input ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : '';
}

/**
 * Define uma mensagem flash (feedback para o usuário) na sessão.
 * @param string $tipo 'success', 'error', 'warning', 'info'
 * @param string $mensagem A mensagem a ser exibida.
 */
function setFlashMessage($tipo, $mensagem) {
    $_SESSION['flash_message'] = ['type' => $tipo, 'message' => $mensagem];
}

/**
 * Redireciona o usuário para uma URL e termina o script.
 * @param string $url A URL de destino.
 */
function redirecionar($url) {
    header("Location: " . $url);
    exit;
}

// --- Verificação do Método da Requisição e Token CSRF --- 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Validação do Token CSRF
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        // Token inválido ou ausente - possível ataque CSRF
        error_log("Falha na validação do token CSRF em cadastrar_usuario.php");
        setFlashMessage('error', 'Erro de segurança ao processar o formulário. Tente novamente.');
        redirecionar("cadastro.html");
    }
    // Regenera o token após o uso para maior segurança
    unset($_SESSION['csrf_token']);
    gerarTokenCSRF(); // Gera um novo para o próximo request

    // --- Coleta e Sanitização dos Dados --- 
    // Aplica sanitização básica a todos os campos para prevenir XSS.
    $nome = sanitizarString($_POST["inputNome"]);
    $endereco = sanitizarString($_POST["inputEndereco"]);
    $complemento = sanitizarString($_POST["inputComplemento"]); // Opcional
    $cep = sanitizarString($_POST["inputCEP"]);
    $bairro = sanitizarString($_POST["inputBairro"]); // Opcional
    $email = isset($_POST["inputEmail"]) ? trim($_POST["inputEmail"]) : ''; // Email precisa de validação específica
    $senha = isset($_POST["inputSenha"]) ? $_POST["inputSenha"] : ''; // Senha não é sanitizada com htmlspecialchars, mas validada

    // --- Validações Robustas --- 
    $erros = []; // Array para armazenar mensagens de erro

    // 2. Validação de Campos Obrigatórios
    if (empty($nome)) { $erros[] = "O campo Nome é obrigatório."; }
    if (empty($endereco)) { $erros[] = "O campo Endereço é obrigatório."; }
    if (empty($cep)) { $erros[] = "O campo CEP é obrigatório."; }
    if (empty($email)) { $erros[] = "O campo E-mail é obrigatório."; }
    if (empty($senha)) { $erros[] = "O campo Senha é obrigatório."; }

    // 3. Validação de Formato do E-mail
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O formato do e-mail fornecido é inválido.";
    }

    // 4. Validação do Formato do CEP (Exemplo: 00000-000)
    if (!empty($cep) && !preg_match('/^\d{5}-\d{3}$/', $cep)) {
        $erros[] = "O formato do CEP deve ser 00000-000.";
    }

    // 5. Validação de Comprimento dos Campos (Exemplos)
    if (mb_strlen($nome) > 100) { $erros[] = "O Nome não pode exceder 100 caracteres."; }
    if (mb_strlen($endereco) > 255) { $erros[] = "O Endereço não pode exceder 255 caracteres."; }
    // Adicionar validações de comprimento para outros campos se necessário...

    // 6. Validação de Complexidade da Senha (Exemplo: Mínimo 8 caracteres)
    if (mb_strlen($senha) < 8) {
        $erros[] = "A senha deve ter no mínimo 8 caracteres.";
    }
    // Poderia adicionar mais regras: exigir letras maiúsculas, minúsculas, números, símbolos.
    // Exemplo mais complexo (pelo menos 1 número, 1 letra maiúscula, 1 minúscula):
    /*
    if (!preg_match('/[A-Z]/', $senha)) { $erros[] = "A senha deve conter pelo menos uma letra maiúscula."; }
    if (!preg_match('/[a-z]/', $senha)) { $erros[] = "A senha deve conter pelo menos uma letra minúscula."; }
    if (!preg_match('/\d/', $senha)) { $erros[] = "A senha deve conter pelo menos um número."; }
    */

    // --- Processamento Após Validação --- 
    if (!empty($erros)) {
        // Se houver erros de validação, armazena-os na sessão e redireciona de volta para o formulário.
        setFlashMessage('error', implode('<br>', $erros)); // Junta os erros numa única mensagem
        // Opcional: Armazenar os dados submetidos (exceto senha) para repopular o formulário
        $_SESSION['form_data'] = $_POST;
        unset($_SESSION['form_data']['inputSenha']); // Nunca armazenar senha
        redirecionar("cadastro.html");
    }

    // --- Segurança da Senha (Hashing) --- 
    // Usa password_hash() com o algoritmo padrão (atualmente bcrypt).
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    if ($senhaHash === false) {
        // Falha ao gerar o hash (raro, mas possível)
        error_log("Falha ao gerar hash de senha em cadastrar_usuario.php");
        setFlashMessage('error', 'Ocorreu um erro crítico ao processar a senha. Tente novamente.');
        redirecionar("cadastro.html");
    }

    // --- Conexão com o Banco de Dados --- 
    $conn = getConnection();
    if (!$conn) {
        error_log("Falha crítica ao obter conexão do banco de dados em cadastrar_usuario.php");
        setFlashMessage('error', 'Erro crítico no sistema (DB Connection). Tente novamente mais tarde.');
        redirecionar("cadastro.html");
    }

    // --- Preparação e Execução da Consulta SQL (INSERT com Prepared Statements) --- 
    $sql = "INSERT INTO cadastro (nome, endereco, complemento, cep, bairro, email, senha) 
            VALUES (:nome, :endereco, :complemento, :cep, :bairro, :email, :senha)";

    try {
        $stmt = $conn->prepare($sql);

        // Associa os valores aos placeholders nomeados (:placeholder)
        // Isso melhora a legibilidade e segurança.
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':complemento', $complemento);
        $stmt->bindParam(':cep', $cep);
        $stmt->bindParam(':bairro', $bairro);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);

        $stmt->execute();

        // --- Feedback de Sucesso --- 
        setFlashMessage('success', 'Cadastro realizado com sucesso! Bem-vindo(a)!');
        // Limpa dados do formulário da sessão se existirem
        unset($_SESSION['form_data']); 
        redirecionar("index.html"); // Redireciona para a página inicial ou de login

    } catch (PDOException $e) {
        // --- Tratamento de Erros PDO --- 
        // Verifica código de erro para duplicação de chave (ex: email já existe)
        if ($e->getCode() == 23000 || $e->getCode() == '23000') { // Código pode ser string ou int
             setFlashMessage('error', 'Erro: Este e-mail já está cadastrado. Utilize outro e-mail ou tente fazer login.');
             // Opcional: Armazenar dados para repopular
             $_SESSION['form_data'] = $_POST;
             unset($_SESSION['form_data']['inputSenha']);
             redirecionar("cadastro.html");
        } else {
            // Outros erros de banco de dados
            error_log("Erro PDO [cadastrar_usuario.php]: " . $e->getMessage() . " | SQLState: " . $e->getCode()); 
            setFlashMessage('error', 'Ocorreu um erro inesperado ao processar seu cadastro. Tente novamente mais tarde.');
            // Opcional: Armazenar dados para repopular
             $_SESSION['form_data'] = $_POST;
             unset($_SESSION['form_data']['inputSenha']);
            redirecionar("cadastro.html");
        }
    }

} else {
    // --- Método de Requisição Inválido --- 
    // Se não for POST, redireciona para o formulário.
    // Gera um token CSRF para o formulário que será carregado.
    gerarTokenCSRF(); 
    redirecionar("cadastro.html");
}

?>