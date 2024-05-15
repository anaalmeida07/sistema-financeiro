<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>Bem-vindo!</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
    <style>
        .conta-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="paginas/sobre/sobre.php">Sobre nós</a></li>
                <li><a href="#">Templates</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <h1 class="bv-home">Bem-vindo!</h1>

    <div class="form-container">
        <h2>Adicionar Nova Conta Bancária</h2>
        <form action="adicionar_conta.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br><br>

            <label for="tipo_conta">Tipo de Conta:</label>
            <input type="text" id="tipo_conta" name="tipo_conta" required><br><br>

            <label for="saldo">Saldo:</label>
            <input type="text" id="saldo" name="saldo" required><br><br>

            <input type="submit" value="Adicionar Conta">
        </form>
    </div>

    <br>

    <div class="contas">
        <?php
        // Inclui o arquivo de conexão com o banco de dados
        require_once 'conexao.php';

        // Obtém o ID do usuário logado
        $usuario_id = $_SESSION['usuario_id'];

        // Prepara a consulta para obter as contas bancárias do usuário
        $sql = "SELECT nome, saldo FROM contas_bancarias WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se há contas bancárias
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Exibe as contas bancárias em boxes
                echo "<div class='conta-box'>";
                echo "<h3>{$row['nome']}</h3>";
                echo "<p>Saldo: {$row['saldo']}</p>";
                echo "</div>";
            }
        } else {
            // Caso não haja contas bancárias
            echo "<p>Nenhuma conta bancária encontrada.</p>";
        }

        // Fecha a conexão e a declaração
        $stmt->close();
        $conn->close();
        ?>
    </div>

</body>

</html>
