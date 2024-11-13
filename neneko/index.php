<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Bem-vindo!!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="barra">
        <img class="logotipo" src="img/gatinho.png" alt="logo">

        <!-- Menu Central -->
        <nav class="menu-central">
            <ul>
                <li><a href="#content">sobre</a></li>
                <li><a href="#sobre">sobre</a></li>
            </ul>
        </nav>

        <!-- Menu Direito -->
        <nav class="menu-direita">
            <ul>
                <li><a href="cadastrar.php">Cadastrar</a></li>
                <li><a href="login.php">Entrar</a></li>
            </ul>
        </nav>
    </div>

    <!-- Página 1 -->
    <div id="content" class="content page">
        <div class="corpo-index">
            <h1 class="h1-nome">NENEKO</h1>
            <h2 class="h2-subtitulo">Gestão financeira sem complicação, seu dinheiro no controle com praticidade.</h2>
            <div class="img-container">
                <img src="img/exemplo.png" alt="exemplo" class="img-exemplo">
                <h2 class="h2-textoExemplo"><strong>Praticidade</strong> com seu dinheiro,<br>
                    em qualquer <strong>lugar</strong> e a qualquer <strong>momento</strong>.</h2>
            </div>
        </div>
    </div>

    <!-- Página 2 -->
    <div id="sobre" class="sobre page">
        <h1 class="pag1"><strong>Não sabe como gerenciar seu dinheiro?</strong></h1>
        <h2>O <strong>NENEKO</strong> é o sistema perfeito para você que chega ao final <br> do mês sem saber onde foi parar o seu dinheiro.</h2>

        <h1 class="pag2"><strong>Gerencie seus<br> cartões de forma <br> simples</strong></h1>
        <h2 class="pag2">Acompanhe seus gastos,<br>organize suas faturas e evite <br>surpresas no final do mês.</h2>
        <img src="img/exemplo.png" alt="exemplo" class="img-exemplo-pag2">
    </div>
</body>

</html>
