<?php

require_once "conexao.php";

// Verifica se a variável $_GET['id'] está definida
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta SQL para obter as informações da conta com o ID especificado
    $sql = "SELECT cu.id_conta, c.banco, c.tipoConta FROM `conta_usuario` cu 
            INNER JOIN `usuarios` u ON cu.id_usuario = u.id_usuario 
            INNER JOIN `conta` c ON cu.id_conta = c.id_conta 
            WHERE c.id_conta = ? AND u.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Recupera os dados da conta
        $row = $result->fetch_assoc();
        $banco = $row['banco'];
        $tipoConta = $row['tipoConta'];

        // Se o formulário for enviado (submit), atualiza os dados
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            // Verifica se os índices estão definidos antes de acessá-los
            if(isset($_POST['submit'])) {
                $banco = $_POST['banco'];
                $tipoConta = $_POST['tipoConta'];

                // Atualiza a consulta para usar um marcador de posição (?) para o ID
                $sql_update_conta = "UPDATE `conta` SET `banco`=?, `tipoConta`=? WHERE id_conta=?";

                $stmt_update_conta = $conn->prepare($sql_update_conta);
                $stmt_update_conta->bind_param("ssi", $banco, $tipoConta, $id);

                // Atualiza a consulta para usar um marcador de posição (?) para o ID
                $sql_update_conta_usuario = "UPDATE `conta_usuario` SET `banco`=?, `tipoConta`=? WHERE id_conta=?";

                $stmt_update_conta_usuario = $conn->prepare($sql_update_conta_usuario);
                $stmt_update_conta_usuario->bind_param("ssi", $banco, $tipoConta, $id);

                // Executa as atualizações nas duas tabelas
                if($stmt_update_conta->execute() && $stmt_update_conta_usuario->execute()){
                    header('Location: exibirConta.php');
                    exit;
                } else {
                    echo "Erro ao atualizar conta: ". $stmt_update_conta->error;
                }

                $stmt_update_conta->close();
                $stmt_update_conta_usuario->close();
            }
        }
    } else {
        echo "Nenhuma conta encontrada com o ID especificado.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID da conta não especificado.";
}

?>


<div class="form">
    <!-- Adiciona um campo oculto para passar o ID da conta -->
    <form method="post" action="editarConta.php?id=<?php echo $id; ?>" id="formEditarConta">
        <label for="nome-banco">Banco</label>
        <!-- Preenche o valor do campo com os dados recuperados do banco de dados -->
        <input id="nome-banco" type="text" name="banco" placeholder="nome do banco" value="<?php echo $banco; ?>" />

        <label for="tipo-conta">Tipo de conta</label>
        <!-- Preenche o valor do campo com os dados recuperados do banco de dados -->
        <input id="tipo-conta" type="text" name="tipoConta" placeholder="Tipo de Conta" value="<?php echo $tipoConta; ?>" />

        <button class="button-45" type="submit" name="submit" role="button">salvar</button>
    </form>
</div>
