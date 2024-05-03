<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/modalDespesas.css">
    <title>Bem-vindo!</title>
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

    <h1 class="h1-index">Vizualizar Despesas</h1>

    <div class="content">
        <div class="corpo-index">
            <a href="home.php"><button class="button-19" role="button">Voltar para Home</button></a>
            <a href="tabelaDespesas.php"><button class="button-19" role="button">Visualizar suas despesas</button></a> <!-- Novo botão -->
            <!-- Botão para abrir o modal --->
            <button id="openModal" class="button-19" role="button">Adicionar Conta a Pagar</button>
        </div>
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
                <label for="data">Data:</label>
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