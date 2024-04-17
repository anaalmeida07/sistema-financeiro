<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>Bem-vindo!</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" href="img/gatinho.png" type="image/x-icon">
</head>

<body>
    <div class="barra">
        <h1>Neneko <img src="img/gatinho.png" alt="logo"></h1>
        <nav>
            <ul>
                <li><a href="paginas/sobre/sobre.php">Sobre nós</a></li>
                <li><a href="">Templates</a></li>
                <li><a href="logout.php">Logout</a></li>
                <!-- Link para a página logout.php -->
            </ul>
        </nav>
    </div>
    <h1 class="bv-home">Bem-vindo!</h1>
    <div class="content">
        <div class="row">
            <div class="pair">
                <a href="exibirConta.php">
                    <div class="card">
                        <h4>LISTAGEM DE CONTAS</h4>
                        <p>Organize suas contas em uma lista dinâmica e de fácil entendimento </p>
                    </div>
                </a>
                <br>
                <a href="modalDespesa.php">
                    <div class="card">
                        <h4>DESPESAS</h4>
                        <p>Tenha tudo sobre controle adicionando suas despesas aqui!</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="pair">
                <a href="#">
                    <div class="card">
                        <h4>ADICIONAR RECEITA</h4>
                        <p>Tenha tudo sobre controle adicionando suas receitas aqui!</p>
                    </div>
                </a>
            </div>
        </div>
    </div>


</body>

</html>
