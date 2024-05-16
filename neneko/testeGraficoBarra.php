<?php
include 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];

// SQL para buscar receitas agrupadas em intervalos de 5 dias
$sqlReceitas = "
    SELECT 
        DATE_FORMAT(DATE_SUB(data_recebimento, INTERVAL (DAY(data_recebimento) - 1) % 5 DAY), '%Y-%m-%d') as periodo, 
        SUM(valor) as total_receitas
    FROM 
        receitas_usuario
    WHERE 
        usuario_id = ? AND data_recebimento >= DATE_SUB(CURDATE(), INTERVAL 20 DAY)
    GROUP BY 
        periodo
    ORDER BY 
        periodo
";

// SQL para buscar despesas agrupadas em intervalos de 5 dias
$sqlDespesas = "
    SELECT 
        DATE_FORMAT(DATE_SUB(data_despesa, INTERVAL (DAY(data_despesa) - 1) % 5 DAY), '%Y-%m-%d') as periodo, 
        SUM(valor) as total_despesas
    FROM 
        despesas_usuario
    WHERE 
        usuario_id = ? AND data_despesa >= DATE_SUB(CURDATE(), INTERVAL 20 DAY)
    GROUP BY 
        periodo
    ORDER BY 
        periodo
";

$stmtReceitas = $conn->prepare($sqlReceitas);
$stmtReceitas->bind_param("i", $usuario_id);
$stmtReceitas->execute();
$resultReceitas = $stmtReceitas->get_result();

$stmtDespesas = $conn->prepare($sqlDespesas);
$stmtDespesas->bind_param("i", $usuario_id);
$stmtDespesas->execute();
$resultDespesas = $stmtDespesas->get_result();

$receitas = [];
while ($row = $resultReceitas->fetch_assoc()) {
    $receitas[$row['periodo']] = floatval($row['total_receitas']);
}

$despesas = [];
while ($row = $resultDespesas->fetch_assoc()) {
    $despesas[$row['periodo']] = floatval($row['total_despesas']);
}

// Fechar conexões
$stmtReceitas->close();
$stmtDespesas->close();
$conn->close();

// Gerar períodos de 5 em 5 dias nos últimos 20 dias
$periodos = [];
for ($i = 0; $i < 4; $i++) {
    $periodos[] = date('Y-m-d', strtotime("-" . ($i * 5) . " days"));
}
$periodos = array_reverse($periodos); // Inverter para ordem cronológica

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Desempenho Financeiro</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Data', 'Receitas', 'Despesas'],
                <?php
                foreach ($periodos as $periodo) {
                    $total_receitas = isset($receitas[$periodo]) ? $receitas[$periodo] : 0;
                    $total_despesas = isset($despesas[$periodo]) ? $despesas[$periodo] : 0;
                    echo "['$periodo', $total_receitas, $total_despesas],";
                }
                ?>
            ]);

            var options = {
                title: 'Receitas e Despesas dos Últimos 20 Dias',
                curveType: 'function',
                legend: { position: 'bottom' },
                hAxis: {
                    title: 'Data',
                    format: 'dd-MM-yyyy'
                },
                vAxis: {
                    title: 'Valor'
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>
</head>

<body>
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
</body>

</html>
