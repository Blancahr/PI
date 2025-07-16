<?php

/* $password = "12345";
 $new = password_hash($password, PASSWORD_DEFAULT);
var_dump(password_verify($password, $new));

echo $new;

die(); */

session_start();
include 'config/connexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'tipo_usuario' => $usuario['tipo_usuario']
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Biblioteca Escolar</title>
  <link rel="shortcut icon" href="img/logo-biblioteca.PNG" type="image/x-icon">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

  <div class="bg-white p-6 sm:p-8 rounded shadow-md w-full max-w-md">
    
    <div class="flex justify-center mb-4">
      <img src="img/logo-biblioteca.PNG" alt="Logo Biblioteca" class="h-16">
    </div>

    <h1 class="text-2xl font-bold mb-6 text-center text-blue-700">Biblioteca Escolar</h1>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-600 p-2 rounded mb-4 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-4">
        <label for="email" class="block text-gray-700">Correo electrónico</label>
        <input type="email" name="email" id="email"
          class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>
      <div class="mb-6">
        <label for="password" class="block text-gray-700">Contraseña</label>
        <input type="password" name="password" id="password"
          class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      </div>
      <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">Ingresar</button>
    </form>
  </div>

</body>

</html>
