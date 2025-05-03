<?php
/**
 * Endpoint PHP para fornecer dados de segurança (CSRF token, flash messages)
 * e dados de formulário anteriores para a página cadastro.html via AJAX/Fetch.
 * 
 * Este script é chamado pelo JavaScript em cadastro.html para obter os dados
 * necessários para exibir o formulário de forma segura e com feedback.
 * 
 * @package    SitePIIII
 * @subpackage CadastroSeguroAjax
 */

// --- Inicialização da Sessão --- 
// Inicia a sessão de forma segura (mesmas configurações usadas nos outros scripts)
// É crucial iniciar a sessão para acessar/definir tokens e mensagens.
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

/**
 * Gera um token CSRF se não existir na sessão, ou retorna o existente.
 * @return string O token CSRF.
 */
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Obtém a mensagem flash da sessão e a remove para que seja exibida apenas uma vez.
 * @return array|null A mensagem flash (array com 'type' e 'message') ou null.
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Limpa após obter
        return $message;
    }
    return null;
}

/**
 * Obtém os dados do formulário da sessão (para repopular) e os remove.
 * Aplica htmlspecialchars para segurança antes de enviar ao JavaScript.
 * @return array Os dados do formulário sanitizados ou um array vazio.
 */
function getFormDataAndClear() {
    $formData = [];
    if (isset($_SESSION['form_data'])) {
        foreach ($_SESSION['form_data'] as $key => $value) {
            // Sanitiza todos os valores antes de enviar para o JavaScript
            $formData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        unset($_SESSION['form_data']); // Limpa após obter
    }
    return $formData;
}

// --- Preparação dos Dados para Resposta JSON --- 

// Monta um array associativo com todos os dados que o JavaScript precisa.
$responseData = [
    'csrf_token' => gerarTokenCSRF(),         // O token CSRF atual
    'flash_message' => getFlashMessage(),     // A mensagem flash (ou null)
    'form_data' => getFormDataAndClear()    // Dados do formulário anterior (ou array vazio)
];

// --- Envio da Resposta JSON --- 

// Define o cabeçalho da resposta HTTP para indicar que o conteúdo é JSON.
header('Content-Type: application/json');
// Codifica o array $responseData em formato JSON e o imprime na saída.
// O JavaScript no cadastro.html receberá esta string JSON.
echo json_encode($responseData);
exit; // Termina a execução do script após enviar a resposta.

?>
