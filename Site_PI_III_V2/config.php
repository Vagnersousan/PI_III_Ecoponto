<?php
/**
 * Arquivo de Configuração Geral da Aplicação.
 * 
 * Define constantes e variáveis globais utilizadas em diferentes partes do site,
 * como nome do site, URL base, configurações de upload, etc.
 */

// --- Configurações Gerais ---

// Define o nome do site (usado em títulos, etc.)
$site_name = "Cadê o Ecoponto";

// Define o URL base da aplicação (IMPORTANTE: Ajustar para o ambiente de produção)
// Usar "http://localhost/cade_o_ecoponto" para desenvolvimento local.
// Para produção, usar o domínio real, ex: "http://www.cadeoecoponto.eco.br"
$base_url = "http://localhost/cade_o_ecoponto"; // ATENÇÃO: Verificar/Ajustar este valor!

// --- Configurações de Upload (se aplicável) ---
// Define o caminho relativo para a pasta onde arquivos enviados (uploads) serão armazenados.
// Certifique-se de que esta pasta exista e tenha as permissões corretas de escrita no servidor.
$uploads_path = "uploads/";

// Define o tamanho máximo permitido para um arquivo de upload (em bytes).
// 10485760 bytes = 10 * 1024 * 1024 = 10 MB
$max_upload_size = 10485760; // 10 MB

// Define as extensões de arquivos permitidas para upload (em minúsculas).
// Adicione ou remova extensões conforme a necessidade da aplicação.
$allowed_extensions = ["jpg", "jpeg", "png", "pdf", "doc", "docx"];

// --- Outras Configurações (Exemplos) ---

// Define o fuso horário padrão da aplicação (Ex: para funções de data/hora)
// date_default_timezone_set("America/Sao_Paulo");

// Configurações de E-mail (se a aplicação enviar e-mails)
// define("EMAIL_FROM", "nao-responda@cadeoecoponto.eco.br");
// define("EMAIL_SMTP_HOST", "smtp.example.com");
// define("EMAIL_SMTP_PORT", 587);
// define("EMAIL_SMTP_USER", "usuario_smtp");
// define("EMAIL_SMTP_PASS", "senha_smtp");

?>

