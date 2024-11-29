<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Bem-vindo!</title>
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/extrato.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <a href="home.php">
        <button type="button" class="btn btn-outline-primary">Volte pra home</button>
    </a>

    <!-- Extrato -->
    <div class="extrato">
        <div class="fundo-extrato">
            <h2 class="bv-home">Extrato</h2>
        </div>
        <br>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="filtro">Mostrar transações dos últimos:</label>
            <select name="filtro" id="filtro">
                <option value="5">5 dias</option>
                <option value="10">10 dias</option>
                <option value="30">30 dias</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
        <hr>
        <?php
        require_once 'conexao.php';
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];

            // Verificar se o filtro foi enviado
            $filtro_dias = isset($_POST['filtro']) ? $_POST['filtro'] : 5;

            $sql = "
            SELECT 'despesa' AS tipo, valor, categoria, data_despesa AS data, cb.nome AS conta
            FROM despesas_usuario du
            JOIN contas_bancarias cb ON du.conta_id = cb.id
            WHERE du.usuario_id = ? AND data_despesa >= DATE_SUB(CURDATE(), INTERVAL $filtro_dias DAY)
            UNION
            SELECT 'receita' AS tipo, valor, categoria, data_recebimento AS data, cb.nome AS conta
            FROM receitas_usuario ru
            JOIN contas_bancarias cb ON ru.conta_destino_id = cb.id
            WHERE ru.usuario_id = ? AND data_recebimento >= DATE_SUB(CURDATE(), INTERVAL $filtro_dias DAY)
            ORDER BY data DESC
        ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuario_id, $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['tipo'] == 'despesa') {
                        echo "<p class='despesa'>Despesa: - R$ {$row['valor']} ({$row['categoria']})</p>";
                    } elseif ($row['tipo'] == 'receita') {
                        echo "<p class='receita'>Receita: + R$ {$row['valor']} ({$row['categoria']})</p>";
                    }
                    echo "<p class='conta'>Conta: {$row['conta']}</p>";
                    echo "<p class='data'>Data: {$row['data']}</p>";
                    echo "<hr>";
                }
            } else {
                echo "<p class='bv-home'>Nenhum registro de transação nos últimos $filtro_dias dias.</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='bv-home'>Nenhum usuário logado.</p>";
        }
        ?>
    </div>

</body>

</html>