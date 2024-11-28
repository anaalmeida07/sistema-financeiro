<?php


require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmacaoSenha = $_POST['confirmacaoSenha'];
    $sexo = $_POST['sexo'];

    // Validar se todos os campos foram preenchidos
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmacaoSenha)) {
        echo "Por favor, preencha todos os campos.";
        exit();
    }

    // Verificar se a senha e a confirmação são iguais
    if ($senha !== $confirmacaoSenha) {
        echo "As senhas não coincidem.";
        exit();
    }

    // Hash da senha para armazenar no banco de dados
    $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir os dados no banco

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $nome, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['nome_usuario'] = $nome; // Armazena o nome na sessão
            header('Location: home.php?cadastro=sucesso'); // Redireciona para a página inicial
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta.";
    }
}

$conn->close();
?>
