<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/modalDespesas.css">
    <link rel="stylesheet" href="css/tabelaDespesas.css"> 
    <title>Despesas</title>
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

    <h1 class="h1-index">Visualizar Despesas</h1>

    <div class="content">
        <div class="corpo-index">
            <a href="home.php"><button class="button-19" role="button">Voltar para Home</button></a>
            <button id="openModal" class="button-19" role="button">Adicionar Conta a Pagar</button>
        </div>
        <br>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <!-- Botão para fechar o modal -->
            <span class="close">&times;</span>
            <!-- Conteúdo do modal (formulário de adicionar despesa) -->
            <h2>Cadastro de Despesa</h2>
            <form action="adicionarContaPagar.php" method="post">
                <!-- Campos do formulário -->
                <label for="descricao">Descrição:</label>
                <input type="text" id="descricao" name="descricao" required>
                <br>
                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" required>
                <br>
                <label for="data">Data: </label>
                <input type="date" id="data" name="data" required>
                <br>
                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" required>
                <br>
                <label for="metodo_pagamento">Método de Pagamento:</label>
                <select id="metodo_pagamento" name="metodo_pagamento" required>
                    <option value="cartao_debito">Cartão de Débito</option>
                    <option value="cartao_credito">Cartão de Crédito</option>
                    <option value="pix">PIX</option>
                    <option value="debito_automatico">Débito Automático</option>
                    <option value="dinheiro">Dinheiro</option>
                </select>
                <br>
                <label for="notas">Notas:</label>
                <textarea id="notas" name="notas"></textarea>
                <br>
                <input type="submit" value="Adicionar Despesa">
            </form>
        </div>
    </div>

    <?php
    include 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

    // Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php'); // Redireciona para a página de login se não estiver logado
        exit;
    }

    $usuario_id = $_SESSION['usuario_id']; // Obtém o ID do usuário logado

    // Consulta SQL para selecionar as despesas do usuário logado
    // Prepare a consulta SQL para selecionar as despesas do usuário logado
$sql = "SELECT * FROM despesas WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);

// Verifica se a preparação da consulta foi bem-sucedida
if ($stmt) {
    // Associa o parâmetro e executa a consulta
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Restante do seu código aqui
}


    // Verifica se há resultados na consulta
    if ($result->num_rows > 0) {
        // Exibir cabeçalhos da tabela
        echo "<table>";
        echo "<tr>";
        echo "<th>Descrição</th>";
        echo "<th>Valor</th>";
        echo "<th>Data</th>";
        echo "<th>Categoria</th>";
        echo "<th>Método de Pagamento</th>";
        echo "<th>Notas</th>";
        echo "</tr>";

        // Exibir dados das despesas
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["descricao"]."</td>";
            echo "<td>".$row["valor"]."</td>";
            echo "<td>".$row["data"]."</td>";
            echo "<td>".$row["categoria"]."</td>";
            echo "<td>".$row["metodo_pagamento"]."</td>";
            echo "<td>".$row["notas"]."</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "0 resultados";
    }

    $conn->close(); // Fecha a conexão com o banco de dados
    ?>

    <script>
        // Capturar elementos do DOM
        var modal = document.getElementById('myModal');
        var btn = document.getElementById("openModal");
        var span = document.getElementsByClassName("close")[0];

        // Quando o usuário clicar no botão, abrir o modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Quando o usuário clicar no botão de fechar, fechar o modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Quando o usuário clicar fora do modal, fechar o modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>
