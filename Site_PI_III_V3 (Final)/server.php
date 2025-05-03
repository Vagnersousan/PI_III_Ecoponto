<?php
/**
 * Script de Roteamento Básico (Controlador Frontal Simples).
 * 
 * Este script age como um ponto de entrada único (ou um roteador muito básico)
 * para incluir diferentes páginas HTML com base em um parâmetro na URL.
 */

// --- Inclusão de Dependências --- 
// Inclui arquivos de configuração e conexão com o banco de dados.
// Embora não sejam diretamente usados neste roteador simples, 
// podem ser necessários pelas páginas incluídas.
require_once 'config.php'; 
require_once 'db.php';

// --- Lógica de Roteamento --- 

// Define a página padrão a ser incluída se nenhum parâmetro 'page' for fornecido.
$default_page = 'index.html';

// Inicializa a variável $page_to_include com a página padrão.
$page_to_include = $default_page;

// Verifica se o parâmetro 'page' foi passado na URL via GET.
if (isset($_GET['page'])) {
    // Obtém o nome do arquivo solicitado.
    $requested_page = $_GET['page'];

    // --- Validação de Segurança (Whitelist) --- 
    // É CRUCIAL validar o valor de $requested_page para evitar 
    // vulnerabilidades de Inclusão de Arquivo Local (LFI).
    // Define uma lista (whitelist) de páginas permitidas.
    $allowed_pages = [
        'index.html',
        'a_importancia_do_descarte_correto.html',
        'tipos_de_Residuos_aceitos_no_Ecoponto.html',
        'Endereco_dos_Ecopontos.html',
        'Dados_Abertos.html',
        'cadastro.html'
        // Adicione outras páginas HTML permitidas aqui
    ];

    // Verifica se a página solicitada está na lista de permitidas.
    // E também verifica se o arquivo realmente existe.
    if (in_array($requested_page, $allowed_pages) && file_exists($requested_page)) {
        // Se for uma página válida e existente, define-a como a página a ser incluída.
        $page_to_include = $requested_page;
    } else {
        // Se a página não for permitida ou não existir, 
        // opcionalmente, pode-se redirecionar para uma página de erro 404
        // ou simplesmente carregar a página padrão.
        // header("HTTP/1.0 404 Not Found");
        // include '404.html';
        // exit;
        
        // Por simplicidade, apenas voltamos para a página padrão.
        $page_to_include = $default_page;
        error_log("Tentativa de acesso a página inválida ou inexistente: " . $requested_page);
    }
}

// --- Inclusão da Página --- 
// Inclui o arquivo HTML final determinado pela lógica de roteamento.
include $page_to_include;

// --- Fim do Script ---
?>

