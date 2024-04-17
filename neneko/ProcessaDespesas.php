        <?php
        require_once "conexao.php";

        // Verifica se o usuário está logado
        if (!isset($_SESSION["usuario_id"])) {
            header("Location: login.php");
            exit;
        }

        // Obtém o ID do usuário logado
        $id_usuario = $_SESSION['usuario_id'];

        // Prepara e executa a consulta SQL para selecionar as despesas do usuário
        $sql_despesas = "SELECT * FROM despesas WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql_despesas);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se foram encontradas despesas
        if ($result->num_rows > 0) {
            echo "<h2>Despesas do Usuário:</h2>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Descrição</th><th>Valor</th><th>Data</th><th>Categoria</th><th>Método de Pagamento</th><th>Notas</th></tr>";
            // Exibe os dados das despesas
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id_usuario"] . "</td>";
                echo "<td>" . $row["descricao"] . "</td>";
                echo "<td>" . $row["valor"] . "</td>";
                echo "<td>" . $row["data"] . "</td>";
                echo "<td>" . $row["categoria"] . "</td>";
                echo "<td>" . $row["metodo_pagamento"] . "</td>";
                echo "<td>" . $row["notas"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Nenhuma despesa encontrada.";
        }

        // Fecha o statement
        $stmt->close();

        // Fecha a conexão com o banco de dados
        $conn->close();
        ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>oiiii</title>
    <link rel="stylesheet" href="css/tabelaDespesas.css">
</head>
<body>
    
</body>
</html>



