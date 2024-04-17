<?php
require_once "conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit;
}

// ID do usuário logado
$id_usuario = $_SESSION['usuario_id'];

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os dados do formulário foram recebidos corretamente
    if (isset($_POST['banco']) && isset($_POST['tipoConta'])) {
        $banco = $_POST['banco'];
        $tipoConta = $_POST['tipoConta'];

        // Prepara e executa a consulta SQL para inserir a nova conta
        $sql = "INSERT INTO `conta` (`id_conta`, `banco`, `tipoConta`) VALUES (NULL, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $banco, $tipoConta);

        if ($stmt->execute()) {
            // Obtém o ID da conta recém-inserida
            $id_conta = $stmt->insert_id;

            // Prepara e executa a consulta SQL para inserir o relacionamento entre conta e usuário
            $sql_conta_usuario = "INSERT INTO `conta_usuario` (`id_conta`, `id_usuario`) VALUES (?, ?)";
            $stmt_conta_usuario = $conn->prepare($sql_conta_usuario);
            $stmt_conta_usuario->bind_param("ii", $id_conta, $id_usuario);
            $stmt_conta_usuario->execute();

            // Redireciona de volta para a página principal após inserção bem-sucedida
            header('Location: exibirConta.php');
            exit;
        } else {
            // Em caso de erro na execução da consulta SQL
            echo "Erro ao inserir nova conta: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Caso os dados do formulário não tenham sido recebidos corretamente
        echo "Erro: Dados do formulário incompletos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Listagem de Contas</title>
    <link rel="stylesheet" href="css/exibirConta.css">
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="#">Sobre nós</a></li>
                <li><a href="#">Templates</a></li>
                <li><a href="logout.php">Logout</a></li>
                <!-- Link para a página logout.php -->
            </ul>
        </nav>
    </div>

    <h1 class="h1-exibir">Listagem de Contas</h1>

    <?php
    // Botão de voltar para a página home
    echo "<a href='home.php'><button class='add-button'>Voltar</button></a>";
    // Botão para adicionar conta
    echo "<a href='adicionarConta.php'><button class='add-button'>Adicionar Conta</button></a>";

    // Busca todas as contas de banco associadas ao usuário logado
    $sql = "SELECT * FROM `conta_usuario` cu JOIN `conta` c ON cu.id_conta = c.id_conta WHERE cu.`id_usuario` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Exibe as contas de banco associadas ao usuário logado
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Início da caixa da conta
            echo "<div class='conta-box'>";

            // Exibe os detalhes da conta
            echo "<div class='boxConta'>";
            echo "<h3>BANCO: " . $row['banco'] . "</h3>";
            echo "<h3>TIPO DE CONTA: " . $row['tipoConta'] . "</h3>";
            // Adiciona os botões de editar e apagar
            echo "<div class='buttons-container'>";
            echo "<a href='editarConta.php?id=" . $row['id_conta'] . "'><button class='edit-button'>Editar</button></a>";
            echo "<a href='apagarConta.php?id=" . $row['id_conta'] . "'><button class='delete-button'>Apagar</button></a>";
            echo "</div>";
            echo "</div>";

            // Fim da caixa da conta
            echo "</div>";
        }
    } else {
        echo "Nenhuma conta encontrada.";
    }

    $stmt->close();
    $conn->close();
    ?>

</body>

</html>