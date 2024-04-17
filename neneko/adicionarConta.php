<?php

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $banco = $_POST['banco'];
        $tipoConta = $_POST['tipoConta'];

        // Insere a nova conta na tabela conta
        $sql_conta = "INSERT INTO `conta` (`id_conta`, `banco`, `tipoConta`) VALUES (NULL, ?, ?)";
        $stmt_conta = $conn->prepare($sql_conta);
        $stmt_conta->bind_param("ss", $banco, $tipoConta);

        if ($stmt_conta->execute()) {
            // Obtém o ID da conta recém-inserida
            $id_conta = $stmt_conta->insert_id;

            // Insere a relação entre conta e usuário na tabela conta_usuario
            $sql_conta_usuario = "INSERT INTO `conta_usuario` (`id_conta`, `id_usuario`) VALUES (?, ?)";
            $stmt_conta_usuario = $conn->prepare($sql_conta_usuario);
            $stmt_conta_usuario->bind_param("ii", $id_conta, $_SESSION['usuario_id']);

            if ($stmt_conta_usuario->execute()) {
                header('Location: exibirConta.php');
                exit;
            } else {
                echo "Erro ao inserir relação conta-usuario: " . $stmt_conta_usuario->error;
            }
        } else {
            echo "Erro ao inserir nova conta: " . $stmt_conta->error;
        }

        $stmt_conta->close();
        $stmt_conta_usuario->close();
    }
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
