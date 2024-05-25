<?php
session_start();

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Verifica se a meta já existe
    $sql_check = "SELECT id FROM metas_usuario WHERE usuario_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $usuario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($result_check->num_rows > 0) {
        // Meta já existe, atualiza
        $sql_update = "UPDATE metas_usuario SET valor_meta = ?, valor_atual = valor_atual + ? WHERE usuario_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("dii", $_POST['valor_meta'], $_POST['valor_guardar'], $usuario_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Meta não existe, insere
        $sql_insert = "INSERT INTO metas_usuario (usuario_id, valor_meta, valor_atual) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("idd", $usuario_id, $_POST['valor_meta'], $_POST['valor_guardar']);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    // Atualiza o saldo da conta bancária selecionada
    $conta_id = $_POST['conta_id'];
    $valor_guardar = $_POST['valor_guardar'];

    // Verifica saldo da conta bancária
    $sql_saldo = "SELECT saldo FROM contas_bancarias WHERE id = ? AND usuario_id = ?";
    $stmt_saldo = $conn->prepare($sql_saldo);
    $stmt_saldo->bind_param("ii", $conta_id, $usuario_id);
    $stmt_saldo->execute();
    $result_saldo = $stmt_saldo->get_result();

    if ($result_saldo->num_rows > 0) {
        $saldo_atual = $result_saldo->fetch_assoc()['saldo'];

        // Verifica se há saldo suficiente
        if ($saldo_atual >= $valor_guardar) {
            // Subtrai o valor da conta bancária
            $novo_saldo = $saldo_atual - $valor_guardar;
            $sql_subtrair = "UPDATE contas_bancarias SET saldo = ? WHERE id = ?";
            $stmt_subtrair = $conn->prepare($sql_subtrair);
            $stmt_subtrair->bind_param("di", $novo_saldo, $conta_id);
            $stmt_subtrair->execute();
            $stmt_subtrair->close();

            // Insere transação no extrato
            $descricao = "Meta: Economia para meta financeira";
            $sql_extrato = "INSERT INTO extrato (usuario_id, descricao, valor, tipo) VALUES (?, ?, ?, ?)";
            $stmt_extrato = $conn->prepare($sql_extrato);
            $tipo = 'meta';
            $stmt_extrato->bind_param("isds", $usuario_id, $descricao, $valor_guardar, $tipo);
            $stmt_extrato->execute();
            $stmt_extrato->close();

            // Redireciona para home.php
            header("Location: home.php");
            exit();
        } else {
            // Saldo insuficiente, exibe mensagem de erro
            $_SESSION['message'] = "Saldo insuficiente na conta selecionada.";
            $_SESSION['message_type'] = "danger";
            header("Location: home.php");
            exit();
        }
    } else {
        // Conta bancária não encontrada ou não pertence ao usuário
        $_SESSION['message'] = "Conta bancária não encontrada ou não pertence ao usuário.";
        $_SESSION['message_type'] = "danger";
        header("Location: home.php");
        exit();
    }
} else {
    // Redireciona para a página inicial se não houver uma sessão válida ou se o método não for POST
    header("Location: index.php");
    exit();
}
?>
