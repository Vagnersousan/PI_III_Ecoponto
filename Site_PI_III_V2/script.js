// Espera o DOM (Document Object Model) estar completamente carregado antes de executar o código jQuery.
$(document).ready(function() {

    // --- Lógica para alternar a visibilidade da senha no formulário --- 
    // Esta seção manipula o clique no ícone de "olho" para mostrar/ocultar a senha.
    // Seleciona o elemento com ID "togglePassword" (o ícone de olho).
    $("#togglePassword").click(function () {
        // Seleciona o campo de input da senha.
        var input = $("#inputSenha");
        // Seleciona o ícone de olho "mostrar" (geralmente o ícone padrão).
        var showIcon = $("#togglePassword"); // Assumindo que este é o ícone de olho aberto/padrão
        // Seleciona o ícone de olho "ocultar" (geralmente um ícone diferente, talvez um olho cortado).
        // IMPORTANTE: O HTML original não parece ter um #hidePassword. Esta lógica pode precisar de ajuste no HTML.
        // Se houver apenas um ícone que muda (ex: classe), a lógica seria diferente.
        // Por enquanto, manteremos a lógica original, mas cientes da possível necessidade de ajuste no HTML.
        var hideIcon = $("#hidePassword"); 

        // Verifica se o tipo atual do input é "password".
        if (input.attr("type") === "password") {
            // Se for senha, muda para "text" para mostrar os caracteres.
            input.attr("type", "text");
            // Esconde o ícone de "mostrar" (olho aberto).
            showIcon.hide();
            // Mostra o ícone de "ocultar" (olho cortado) - SE EXISTIR.
            hideIcon.show();
        } else {
            // Se for texto, muda de volta para "password" para ocultar os caracteres.
            input.attr("type", "password");
            // Esconde o ícone de "ocultar" (olho cortado) - SE EXISTIR.
            hideIcon.hide();
            // Mostra o ícone de "mostrar" (olho aberto).
            showIcon.show();
        }
    });
    // --- Fim da lógica de alternar visibilidade da senha --- 


    // --- Código para inicialização dos gráficos com Chart.js --- 
    // Esta seção é responsável por buscar os elementos <canvas> no HTML e desenhar os gráficos.

    // Obtém a referência aos elementos <canvas> onde os gráficos serão renderizados.
    const ctxGeracaoColeta = document.getElementById("graficoGeracaoColeta");
    const ctxDestinoALC = document.getElementById("graficoDestinoALC");
    const ctxTaxaColeta = document.getElementById("graficoTaxaColeta");

    // Verifica se todos os elementos <canvas> foram encontrados na página atual.
    // Isso evita erros caso o script seja carregado em páginas que não contêm esses gráficos.
    if (ctxGeracaoColeta && ctxDestinoALC && ctxTaxaColeta) {

        // Definição dos dados para os gráficos.
        // Estes dados parecem ser estáticos e foram possivelmente extraídos de um arquivo CSV (residuos_data.csv) anteriormente.
        // Idealmente, para dados dinâmicos, eles seriam carregados via AJAX ou de outra fonte.
        const data = {
            labels: ["Brasil (2022)", "Brasil (2023)", "América Latina & Caribe (2021)"], // Rótulos do eixo X
            generated: [81.8, 80.96, 230.47], // Dados de Geração (Mton)
            collected: [76.1, 75.6, 195.32], // Dados de Coleta (Mton)
            collectionRate: [93.0, 93.4, 84.7], // Taxa de Coleta (%)
            // Dados específicos para o gráfico de Destinação na América Latina e Caribe (2021)
            alc2021_destination: {
                "Aterro Sanitário": 106.16,
                "Aproveitados (Recicl./Compost.)": 10.13,
                "Disposição Inadequada": 94.09,
                "Destino Desconhecido": 20.09
            }
        };

        // Calcula a quantidade de resíduos "Não Coletados".
        // Subtrai a quantidade coletada da gerada para cada ponto de dado.
        const notCollected = data.generated.map((g, i) => parseFloat((g - data.collected[i]).toFixed(2)));

        // --- Gráfico 1: Geração vs Coleta (Gráfico combinado de Barras e Linha) --- 
        new Chart(ctxGeracaoColeta, {
            type: "bar", // Define o tipo base do gráfico como barras.
            data: {
                labels: data.labels, // Usa os rótulos definidos anteriormente.
                datasets: [
                    {
                        label: "Gerado (Mton)", // Rótulo para a série de dados de Geração.
                        data: data.generated, // Dados de Geração.
                        backgroundColor: "rgba(54, 162, 235, 0.7)", // Cor de fundo das barras (Azul).
                        borderColor: "rgba(54, 162, 235, 1)", // Cor da borda das barras.
                        borderWidth: 1, // Largura da borda.
                        order: 2 // Ordem de desenho (barras ficam atrás da linha).
                    },
                    {
                        label: "Coletado (Mton)", // Rótulo para a série de dados de Coleta.
                        data: data.collected, // Dados de Coleta.
                        backgroundColor: "rgba(75, 192, 192, 0.7)", // Cor de fundo das barras (Verde).
                        borderColor: "rgba(75, 192, 192, 1)", // Cor da borda das barras.
                        borderWidth: 1,
                        order: 2 // Ordem de desenho (barras ficam atrás da linha).
                    },
                    {
                        label: "Não Coletado (Mton)", // Rótulo para a série de dados "Não Coletado".
                        data: notCollected, // Dados calculados de Não Coletado.
                        type: "line", // Define o tipo específico desta série como linha.
                        borderColor: "rgba(255, 99, 132, 1)", // Cor da linha (Vermelho).
                        backgroundColor: "rgba(255, 99, 132, 0.5)", // Cor dos pontos na linha.
                        fill: false, // Não preencher a área abaixo da linha.
                        tension: 0.1, // Curvatura da linha (0 para linhas retas).
                        order: 1 // Ordem de desenho (linha fica na frente das barras).
                    }
                ]
            },
            options: {
                // Configurações dos eixos.
                scales: {
                    y: { // Eixo Y (Vertical)
                        beginAtZero: true, // Começa a escala no zero.
                        title: { // Título do eixo Y.
                            display: true,
                            text: "Milhões de Toneladas (Mton)"
                        }
                    },
                    x: { // Eixo X (Horizontal)
                        title: { // Título do eixo X.
                            display: true,
                            text: "Região (Ano)"
                        }
                    }
                },
                responsive: true, // Torna o gráfico responsivo ao tamanho do container.
                maintainAspectRatio: false // MODIFICADO: Permite que o gráfico não mantenha a proporção padrão, adaptando-se melhor ao CSS (importante para responsividade).
            }
        });

        // --- Gráfico 2: Destinação ALC 2021 (Gráfico de Rosca/Doughnut) --- 
        // Prepara os dados específicos para o gráfico de rosca.
        const alcLabels = Object.keys(data.alc2021_destination); // Pega as chaves (nomes das destinações) como rótulos.
        const alcData = Object.values(data.alc2021_destination); // Pega os valores (quantidades) como dados.
        const alcTotal = alcData.reduce((a, b) => a + b, 0); // Calcula o total para usar no cálculo de porcentagem.

        new Chart(ctxDestinoALC, {
            type: "doughnut", // Define o tipo do gráfico como rosca.
            data: {
                labels: alcLabels, // Rótulos das fatias.
                datasets: [{
                    label: "Destinação RSU ALC 2021 (Mton)", // Rótulo do conjunto de dados.
                    data: alcData, // Valores das fatias.
                    backgroundColor: [ // Cores de fundo para cada fatia.
                        "rgba(75, 192, 192, 0.7)", // Verde
                        "rgba(255, 206, 86, 0.7)", // Amarelo
                        "rgba(255, 99, 132, 0.7)", // Vermelho
                        "rgba(153, 102, 255, 0.7)" // Roxo
                    ],
                    borderColor: [ // Cores das bordas das fatias.
                        "rgba(75, 192, 192, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(255, 99, 132, 1)",
                        "rgba(153, 102, 255, 1)"
                    ],
                    borderWidth: 1 // Largura da borda.
                }]
            },
            options: {
                responsive: true, // Responsividade.
                maintainAspectRatio: false, // MODIFICADO: Permite melhor adaptação ao CSS.
                plugins: { // Configuração de plugins do Chart.js.
                    tooltip: { // Configuração das dicas (tooltips) que aparecem ao passar o mouse.
                        callbacks: {
                            // Função para formatar o texto da tooltip.
                            label: function(context) {
                                let label = context.label || ""; // Pega o rótulo da fatia.
                                if (label) {
                                    label += ": ";
                                }
                                const value = context.parsed; // Pega o valor numérico da fatia.
                                // Calcula a porcentagem relativa ao total.
                                const percentage = ((value / alcTotal) * 100).toFixed(1);
                                // Formata o texto final da tooltip.
                                label += `${value.toFixed(2)} Mton (${percentage}%)`;
                                return label;
                            }
                        }
                    },
                    legend: { // Configuração da legenda.
                        position: "top", // Posição da legenda (acima do gráfico).
                    }
                }
            }
        });

        // --- Gráfico 3: Taxa de Coleta (Gráfico de Barras) --- 
        new Chart(ctxTaxaColeta, {
            type: "bar", // Define o tipo como gráfico de barras.
            data: {
                labels: data.labels, // Rótulos do eixo X.
                datasets: [{
                    label: "Taxa de Coleta (%)", // Rótulo da série de dados.
                    data: data.collectionRate, // Dados da taxa de coleta.
                    backgroundColor: [ // Cores de fundo das barras.
                        "rgba(54, 162, 235, 0.7)", // Azul
                        "rgba(75, 192, 192, 0.7)", // Verde
                        "rgba(255, 159, 64, 0.7)" // Laranja
                    ],
                    borderColor: [ // Cores das bordas das barras.
                        "rgba(54, 162, 235, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(255, 159, 64, 1)"
                    ],
                    borderWidth: 1 // Largura da borda.
                }
                // Nenhuma linha de desvio foi adicionada aqui, conforme discussão anterior.
                ]
            },
            options: {
                scales: { // Configuração dos eixos.
                    y: { // Eixo Y (Vertical)
                        beginAtZero: true, // Começa no zero.
                        max: 100, // Define o valor máximo como 100 (para porcentagem).
                        title: { // Título do eixo Y.
                            display: true,
                            text: "Taxa de Coleta (%)"
                        }
                    },
                     x: { // Eixo X (Horizontal)
                        title: { // Título do eixo X.
                            display: true,
                            text: "Região (Ano)"
                        }
                    }
                },
                responsive: true, // Responsividade.
                maintainAspectRatio: false, // MODIFICADO: Permite melhor adaptação ao CSS.
                 plugins: {
                    legend: { // Configuração da legenda.
                        display: false // Oculta a legenda, pois há apenas uma série de dados.
                    }
                }
            }
        });
    } // Fim do bloco if (verifica se os elementos canvas existem)

}); // Fim do $(document).ready()


// --- Função de Login com Replit --- 
// Esta função parece ser um código padrão fornecido pelo Replit para autenticação.
// Ela abre uma janela popup para o usuário autenticar no Replit e, após a conclusão,
// recarrega a página original.
function LoginWithReplit() {
    // Adiciona um listener para esperar a mensagem de conclusão da autenticação do popup.
    window.addEventListener("message", authComplete);

    // Define as dimensões e posição da janela popup de autenticação.
    var h = 500; // Altura
    var w = 350; // Largura
    var left = screen.width / 2 - w / 2; // Posição horizontal centralizada.
    var top = screen.height / 2 - h / 2; // Posição vertical centralizada.

    // Abre a janela popup de autenticação do Replit.
    // O URL inclui o domínio do site atual para que o Replit saiba para onde redirecionar após a autenticação.
    var authWindow = window.open(
        "https://replit.com/auth_with_repl_site?domain=" + location.host,
        "_blank", // Abre em uma nova janela/aba.
        // String de configuração da janela popup (tamanho, sem barras de ferramentas, etc.).
        "modal=yes, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" +
        w +
        ", height=" +
        h +
        ", top=" +
        top +
        ", left=" +
        left
    );

    // Função callback que será chamada quando a janela principal receber uma mensagem.
    function authComplete(e) {
        // Verifica se a mensagem recebida é a confirmação de autenticação completa.
        if (e.data !== "auth_complete") {
            // Se não for a mensagem esperada, ignora.
            return;
        }

        // Se a autenticação foi completa:
        // Remove o listener para não ser chamado novamente.
        window.removeEventListener("message", authComplete);
        // Fecha a janela popup de autenticação.
        authWindow.close();
        // Recarrega a página principal (presumivelmente para refletir o estado de login).
        location.reload();
    }
}
// --- Fim da Função de Login com Replit --- 

