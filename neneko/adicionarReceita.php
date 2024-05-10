<?php
require_once "conexao.php";

    // Verifica se o usuário está logado
    if(isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
    
        // Restante do código aqui
    } else {
        // Se o usuário não estiver logado, redirecione para a página de login
        header("Location: login.php");
        exit;
    }

    

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os dados do formulário foram recebidos corretamente
    if (isset($_POST['categoria']) && isset($_POST['dia_recebimento']) && isset($_POST['valor']) && isset($_POST['conta'])) {
        $categoria = $_POST['categoria'];
        $dia_recebimento = $_POST['dia_recebimento'];
        $valor = $_POST['valor'];
        $conta_id = $_POST['conta'];
        $parcelas_falta = ($categoria == 'divida') ? $_POST['parcelas_falta'] : null;


    

        // Verifica se uma nova categoria foi inserida
        if (!empty($_POST['nova_categoria'])) {
            $nova_categoria = $_POST['nova_categoria'];

            // Insere a nova categoria no banco de dados
            $sql_insere_categoria = "INSERT INTO categorias (id_usuario, categoria) VALUES (?, ?)";
            
            $stmt_insere_categoria = $conn->prepare($sql_insere_categoria);
            $stmt_insere_categoria->bind_param("si", $nova_categoria, $usuario_id);


            $stmt_insere_categoria->execute();

            // Define a nova categoria para ser usada
            $categoria = $nova_categoria;
        }

        // Prepara e executa a consulta SQL para inserir a nova receita
        $sql = "INSERT INTO receitas (id_usuario, categoria, dia_recebimento, valor, conta_bancaria, parcelas_falta) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isidsi", $usuario_id, $categoria, $dia_recebimento, $valor, $conta_id, $parcelas_falta);

        // Executa a consulta
        if ($stmt->execute()) {
            // Adiciona as receitas futuras apenas se a data de recebimento da receita principal for futura
            $data_atual = date('Y-m-d');
            $data_recebimento = date('Y-m') . '-' . $dia_recebimento;
            if ($data_atual <= $data_recebimento) {
                $stmt_future = $conn->prepare($sql);
                $stmt_future->bind_param("isidii", $usuario_id, $categoria, $dia_recebimento, $valor, $conta_id, $parcelas_falta);
                $stmt_future->execute();
            }
        
            // Redireciona de volta para a página principal após inserção bem-sucedida
            header('Location: adicionarReceita.php');
            exit;
        } else {
            // Em caso de erro na execução da consulta SQL
            echo "Erro ao inserir nova receita: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Caso os dados do formulário não tenham sido recebidos corretamente
        echo "Erro: Dados do formulário incompletos.";
    }
}



// Busca todas as contas bancárias do usuário
$sql_contas = "SELECT * FROM conta_usuario WHERE id_usuario = ?";
$stmt_contas = $conn->prepare($sql_contas);
$stmt_contas->bind_param("i", $usuario_id);
$stmt_contas->execute();
$result_contas = $stmt_contas->get_result();

// Função para calcular a soma total das receitas do usuário
function calcularTotalReceitas($conn, $usuario_id)
{
    $sql_total = "SELECT SUM(valor) AS total FROM receitas WHERE id_usuario = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $usuario_id);
    $stmt_total->execute();
    $result = $stmt_total->get_result();
    $total = $result->fetch_assoc()['total'];
    return $total;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Receita</title>
</head>

<body>
    <h1>Adicionar Receita</h1>

    <form action="adicionarReceita.php" method="POST">
        <label for="categoria">Categoria:</label>
        <select name="categoria" id="categoria">
            <option value="salario">Salário</option>
            <option value="bico">Bico</option>
            <option value="divida">Dívida a Receber</option>
        </select><br>

        <!-- Campo para inserir uma nova categoria -->
        <label for="nova_categoria">Nova Categoria:</label>
        <input type="text" name="nova_categoria" id="nova_categoria"><br>
        <!-- Botão para salvar a nova categoria -->
        <button type="button" onclick="salvarNovaCategoria()">Salvar Categoria</button><br>

        <!-- Adiciona o campo para selecionar a conta bancária -->
        <label for="conta">Conta Bancária:</label>
        <select name="conta" id="conta">
            <?php
            if ($result_contas->num_rows > 0) {
                while ($row_conta = $result_contas->fetch_assoc()) {
                    echo '<option value="' . $row_conta['id_conta'] . '">' . $row_conta['banco'] . ' - ' . $row_conta['tipoConta'] . '</option>';
                }
            }
            ?>
        </select><br>

        <label for="dia_recebimento">Dia de Recebimento:</label>
        <input type="number" name="dia_recebimento" id="dia_recebimento" min="1" max="31" required><br>

        <label for="valor">Valor:</label>
        <input type="text" name="valor" id="valor" required><br>

        <div id="parcelas_falta_div" style="display: none;">
            <label for="parcelas_falta">Parcelas Faltantes:</label>
            <input type="number" name="parcelas_falta" id="parcelas_falta" min="1"><br>
        </div>

        <input type="submit" value="Adicionar Receita">
    </form>

    <?php
    // Calcula e exibe o valor total das receitas do usuário
    $total_receitas = calcularTotalReceitas($conn, $usuario_id);
    echo "<p>Total de Receitas: R$ " . number_format($total_receitas, 2) . "</p>";
    ?>

    <script>
    function salvarNovaCategoria() {
        var novaCategoria = document.getElementById('nova_categoria').value;
        if (novaCategoria.trim() !== '') {
            var categoriaSelect = document.getElementById('categoria');
            var option = document.createElement("option");
            option.text = novaCategoria;
            option.value = novaCategoria;
            categoriaSelect.add(option);
            document.getElementById('nova_categoria').value = '';
        }
    }
    </script>

</body>

</html>
