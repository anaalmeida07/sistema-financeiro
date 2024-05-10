<?php
require_once "conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit;
}

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $banco = $_POST['banco'];
    $tipoConta = $_POST['tipoConta'];

    // Insere a nova conta na tabela conta_usuario
    $sql_conta_usuario = "INSERT INTO conta_usuario (banco, tipoConta, id_usuario) VALUES (?, ?, ?)";
    $stmt_conta_usuario = $conn->prepare($sql_conta_usuario);
    $stmt_conta_usuario->bind_param("ssi", $banco, $tipoConta, $usuario_id);

    if ($stmt_conta_usuario->execute()) {
        header('Location: exibirConta.php');
        exit;
    } else {
        echo "Erro ao inserir nova conta: " . $stmt_conta_usuario->error;
    }

    $stmt_conta_usuario->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Cadastro de Conta</title>
</head>

<body>
    <h2>Cadastro de Conta</h2>
    <form action="adicionarConta.php" method="post">
        <label for="banco">Banco:</label>
        <input type="text" id="banco" name="banco" required><br><br>

        <label for="tipoConta">Tipo de Conta:</label>
        <input type="text" id="tipoConta" name="tipoConta" required><br><br>

        <input type="submit" name="submit" value="Cadastrar">
    </form>
</body>

</html>
