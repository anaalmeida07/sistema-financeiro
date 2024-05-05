<?php
require_once "conexao.php"; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit;
}

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os dados do formulário foram recebidos corretamente
    if (isset($_POST['categoria']) && isset($_POST['dia_recebimento']) && isset($_POST['valor'])) {
        $categoria = $_POST['categoria'];
        $dia_recebimento = $_POST['dia_recebimento'];
        $valor = $_POST['valor'];
        $parcelas_falta = ($categoria == 'divida') ? $_POST['parcelas_falta'] : null;

        // Prepara e executa a consulta SQL para inserir a nova receita
        $sql = "INSERT INTO receitas (usuario_id, categoria, dia_recebimento, valor, parcelas_falta) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isidi", $usuario_id, $categoria, $dia_recebimento, $valor, $parcelas_falta);

        if ($stmt->execute()) {
            // Adiciona as receitas futuras apenas se a data de recebimento da receita principal for futura
            $data_atual = date('Y-m-d');
            $data_recebimento = date('Y-m') . '-' . $dia_recebimento;
            if ($data_atual <= $data_recebimento) {
                $sql_future = "INSERT INTO receitas (usuario_id, categoria, dia_recebimento, valor, parcelas_falta) VALUES (?, ?, ?, ?, ?)";
                $stmt_future = $conn->prepare($sql_future);
                $stmt_future->bind_param("isidi", $usuario_id, $categoria, $dia_recebimento, $valor, $parcelas_falta);
                $stmt_future->execute();
            }
        
            // Redireciona de volta para a página principal após inserção bem-sucedida
            header('Location: adicionarReceita.php');
            exit;
        }else {
            // Em caso de erro na execução da consulta SQL
            echo "Erro ao inserir nova receita: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Caso os dados do formulário não tenham sido recebidos corretamente
        echo "Erro: Dados do formulário incompletos.";
    }
}

// Função para calcular a soma total das receitas do usuário
function calcularTotalReceitas($conn, $usuario_id)
{
    $sql_total = "SELECT SUM(valor) AS total FROM receitas WHERE usuario_id = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $usuario_id);
    $stmt_total->execute();
    $result = $stmt_total->get_result();
    $total = $result->fetch_assoc()['total'];
    return $total;
}

// Busca todas as receitas do usuário
$sql_receitas = "SELECT * FROM receitas WHERE usuario_id = ?";
$stmt_receitas = $conn->prepare($sql_receitas);
$stmt_receitas->bind_param("i", $usuario_id);
$stmt_receitas->execute();
$result_receitas = $stmt_receitas->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Adicionar Receita</title>
    <link rel="stylesheet" href="css/adicionarReceita.css">
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

    <h1 class="bv-title">Adicionar Receita</h1>

    <a href="home.php"><button class="back-button" role="button">Voltar para Home</button></a>
    <!-- Botão para abrir o modal -->
    <button id="openModalBtn" class="back-button">Adicionar Receita</button>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <!-- Conteúdo do modal -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Adicionar Receita</h2>
            <form action="adicionarReceita.php" method="POST">
                <label for="categoria">Categoria:</label>
                <select name="categoria" id="categoria">
                    <option value="salario">Salário</option>
                    <option value="bico">Bico</option>
                    <option value="divida">Dívida a Receber</option>
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
        </div>
    </div>

    <?php
    // Calcula e exibe o valor total das receitas do usuário
    $total_receitas = calcularTotalReceitas($conn, $usuario_id);
    echo "<h2 class='titulo-lado'>Total de Receitas: R$ " . number_format($total_receitas, 2) . "</h2>";

    echo "<h4 class='titulo-lado'>Receitas: </h4>";

    // Exibe as receitas do usuário
    if ($result_receitas->num_rows > 0) {
        while ($row = $result_receitas->fetch_assoc()) {
            // Início da caixa da receita
            echo "<div class='receita-box'>";

            // Exibe os detalhes da receita
            echo "<div class='boxReceita'>";
            echo "<h3>Categoria: " . $row['categoria'] . "</h3>";
            echo "<h3>Dia de Recebimento: " . $row['dia_recebimento'] . "</h3>";
            echo "<h3>Valor: R$ " . $row['valor'] . "</h3>";
            // Exibe o campo "Parcelas Faltantes" apenas se a categoria for "Dívida a Receber"
            if ($row['categoria'] == 'divida') {
                echo "<h3>Parcelas Faltantes: " . ($row['parcelas_falta'] ?? 'N/A') . "</h3>";
            }
            // Adiciona os botões de editar e apagar
            echo "<div class='buttons-container'>";
            echo "<a href='editarReceita.php?id=" . $row['id'] . "'><button class='edit-button'>Editar</button></a>";
            echo "<a href='apagarReceita.php?id=" . $row['id'] . "'><button class='delete-button'>Apagar</button></a>";
            echo "</div>";
            echo "</div>";

            // Fim da caixa da receita
            echo "</div>";
        }
    } else {
        echo "Nenhuma receita encontrada.";
    }

    $stmt_receitas->close();
    ?>

    <script>
        // Função para abrir o modal ao clicar no botão "Adicionar Receita"
        document.getElementById('openModalBtn').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'block';
        });

        // Função para fechar o modal ao clicar no botão "X"
        document.getElementsByClassName('close')[0].addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'none';
        });

        // Função para fechar o modal ao clicar fora da área do modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = 'none';
            }
        };

        // Mostrar ou ocultar o campo "Parcelas Faltantes" com base na categoria selecionada
        document.getElementById('categoria').addEventListener('change', function() {
            var categoria = this.value;
            if (categoria === 'divida') {
                document.getElementById('parcelas_falta_div').style.display = 'block';
            } else {
                document.getElementById('parcelas_falta_div').style.display = 'none';
            }
        });
    </script>

</body>

</html>