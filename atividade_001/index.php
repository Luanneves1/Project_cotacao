<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Conversor de Moeda</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

    <h2>Conversor de Moeda</h2>

    <form method="post" action="index.php">
        <label for="valor">Valor a ser convertido:</label>
        <input type="text" id="valor" name="valor" placeholder="Digite o valor em R$">

        <label for="moeda_origem">Moeda de origem:</label>
        <select id="moeda_origem" name="moeda_origem">
            <option value="BRL">Real brasileiro (BRL)</option>
            <option value="USD">Dólar americano (USD)</option>
            <option value="EUR">Euro (EUR)</option>
            <option value="JPY">Iene japonês (JPY)</option>
            <!-- Adicione outras moedas conforme necessário -->
        </select>

        <label for="moeda_destino">Moeda de destino:</label>
        <select id="moeda_destino" name="moeda_destino">
            <option value="BRL">Real brasileiro (BRL)</option>
            <option value="USD">Dólar americano (USD)</option>
            <option value="EUR">Euro (EUR)</option>
            <option value="JPY">Iene japonês (JPY)</option>
            <!-- Adicione outras moedas conforme necessário -->
        </select>

        <button type="submit">Converter</button>
    </form>

    <?php
    // Função para buscar a cotação da API
    function buscar_cotacao($moeda, $data_inicial, $data_final) {
        $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaPeriodo(moeda=@moeda,dataInicial=@dataInicial,dataFinalCotacao=@dataFinalCotacao)?@moeda='$moeda'&@dataInicial='$data_inicial'&@dataFinalCotacao='$data_final'&\$top=1&\$orderby=dataHoraCotacao%20desc&\$format=json&\$select=cotacaoCompra";
        
        $response = json_decode(file_get_contents($url),true);
        return $response["value"][0]["cotacaoCompra"];

      
    }

    // Data inicial e final
    $inicio = date("m-d-Y", strtotime("-7 days"));
    $fim = date("m-d-Y");

    // Verifica se o formulário foi submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica se todos os campos necessários foram preenchidos
        if (isset($_POST["valor"], $_POST["moeda_origem"], $_POST["moeda_destino"])) {
            // Recebe os valores enviados pelo formulário
            $valor = floatval($_POST["valor"]); // Converte para float
            $moeda_origem = $_POST["moeda_origem"];
            $moeda_destino = $_POST["moeda_destino"];

            if ($moeda_origem == $moeda_destino) {
                // Se a moeda de origem é igual a moeda de destino
                $valor_convertido = $valor;
            } else {
                // Busca a cotação da moeda de origem para BRL se a moeda de origem não for BRL
                if ($moeda_origem != 'BRL') {
                    $cotacao_origem = buscar_cotacao($moeda_origem, $inicio, $fim);
                    $valor_em_reais = $valor * $cotacao_origem;
                } else {
                    $valor_em_reais = $valor;
                }

                // Busca a cotação de BRL para a moeda de destino se a moeda de destino não for BRL
                if ($moeda_destino != 'BRL') {
                    $cotacao_destino = buscar_cotacao($moeda_destino, $inicio, $fim);
                    $valor_convertido = $valor_em_reais / $cotacao_destino;
                } else {
                    $valor_convertido = $valor_em_reais;
                }
            }

            // Exibe o resultado formatado
            echo '<div class="result-box">';
            echo '<div class="result">Resultado da conversão:</div>';
            echo '<div><strong>' . number_format($valor, 2, ',', '.') . ' ' . $moeda_origem . '</strong> equivale a <strong>' . number_format($valor_convertido, 2, ',', '.') . ' ' . $moeda_destino . '</strong></div>';
            echo '</div>';
        } else {
            // Caso algum campo esteja faltando
            echo '<div class="result-box">';
            echo '<div class="result">Erro:</div>';
            echo '<div>Por favor, preencha todos os campos do formulário.</div>';
            echo '</div>';
        }
    }
    ?>

</body>
</html>