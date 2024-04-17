<?php
require_once "conexao.php";

// Verifica se os dados do formulário foram recebidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se todas as variáveis esperadas foram recebidas
    if (isset($_POST['descricao']) && isset($_POST['valor']) && isset($_POST['data']) && isset($_POST['categoria']) && isset($_POST['metodo_pagamento']) && isset($_POST['notas'])) {
        // Atribui os valores recebidos a variáveis locais
        $descricao = $_POST['descricao'];
        $valor = $_POST['valor'];
        $data = $_POST['data'];
        $categoria = $_POST['categoria'];
        $metodo_pagamento = $_POST['metodo_pagamento'];
        $notas = $_POST['notas'];

        // Obtém o ID do usuário logado - suponha que você já tenha a sessão iniciada
       
        $id_usuario = $_SESSION['usuario_id'];

        // Prepara e executa a consulta SQL para inserir a nova despesa
        $sql = "INSERT INTO `despesas` (`id_usuario`, `descricao`, `valor`, `data`, `categoria`, `metodo_pagamento`, `notas`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Liga os parâmetros à consulta preparada  
        $stmt->bind_param("issdsss", $id_usuario, $descricao, $valor, $data, $categoria, $metodo_pagamento, $notas);

        // Executa a consulta preparada
        if ($stmt->execute()) {
            // Exibe um alerta de sucesso em JavaScript
            echo "<script>alert('Despesa adicionada com sucesso!');</script>";
            // Redireciona de volta para a página de onde veio
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Em caso de erro na execução da consulta SQL
            echo "Erro ao adicionar nova despesa: " . $stmt->error;
        }

        // Fecha a instrução preparada
        $stmt->close();
    } else {
        // Caso algum dos dados do formulário não tenha sido recebido corretamente
        echo "Erro: Dados do formulário incompletos.";
    }
}
// Fecha a conexão com o banco de dados
$conn->close();
?>
