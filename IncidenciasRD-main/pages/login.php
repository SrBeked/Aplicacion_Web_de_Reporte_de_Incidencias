<?php
session_start();
$prefix = '..';
include $prefix . '/partials/head.php';
include $prefix . '/partials/header.php';

include "../db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["rol"] = $usuario["rol"];
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Credenciales inválidas";
    }
}
?>

<main class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <h1 class="text-2xl font-bold">Iniciar sesion</h1>
  <p class="text-slate-600 mt-2">Accede a tu cuenta</p>

  <?php if (!empty($error)): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded-xl mt-4 text-center">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" id="loginForm" class="mt-8 space-y-4 bg-white p-6 rounded-2xl ring-1 ring-slate-200">
    <div>
      <label class="block text-sm font-medium">Correo</label>
      <input type="email" name="email" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" placeholder="tucorreo@ejemplo.com" />
    </div>
    <div>
      <label class="block text-sm font-medium">Clave</label>
      <input type="password" name="password" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" placeholder="••••••••" />
    </div>
    <button type="submit" class="w-full rounded-2xl bg-indigo-600 text-white font-semibold px-4 py-2 hover:shadow">Entrar</button>
  </form>

  <p class="mt-4 text-sm">No tienes cuenta? <a href="register.php" class="text-indigo-600 font-semibold">Registrate</a></p>
</main>

<?php include $prefix . '/partials/footer.php'; ?>

