<?php
session_start();
$prefix = '..';
include $prefix . '/partials/head.php';
include $prefix . '/partials/header.php';

include "../db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm"] ?? "";

    if ($password !== $confirm) {
        $error = "Las contraseñas no coinciden";
    } else {
        $checkSql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $error = "Este correo ya está registrado";
        } else {
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'reportero')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nombre, $email, $password);

            if ($stmt->execute()) {
                $_SESSION["usuario_id"] = $stmt->insert_id;
                $_SESSION["nombre"] = $nombre;
                $_SESSION["rol"] = "reportero";
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Error al registrar el usuario";
            }
        }
    }
}
?>

<main class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <h1 class="text-2xl font-bold">Crear cuenta</h1>
  <p class="text-slate-600 mt-2">Registrate para comenzar a reportar incidencias.</p>

  <?php if (!empty($error)): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded-xl mt-4 text-center">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" id="registerForm" class="mt-8 space-y-4 bg-white p-6 rounded-2xl ring-1 ring-slate-200">
    <div>
      <label class="block text-sm font-medium">Nombre</label>
      <input type="text" name="nombre" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" placeholder="Tu nombre" />
    </div>
    <div>
      <label class="block text-sm font-medium">Correo</label>
      <input type="email" name="email" required class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" placeholder="tucorreo@ejemplo.com" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Clave</label>
        <input type="password" name="password" required minlength="6" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" />
      </div>
      <div>
        <label class="block text-sm font-medium">Confirmacion</label>
        <input type="password" name="confirm" required minlength="6" class="mt-1 w-full rounded-xl ring-1 ring-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600" />
      </div>
    </div>
    <button type="submit" class="w-full rounded-2xl bg-indigo-600 text-white font-semibold px-4 py-2 hover:shadow">Crear cuenta</button>
  </form>

  <p class="mt-4 text-sm">Ya tienes cuenta? <a href="login.php" class="text-indigo-600 font-semibold">Inicia sesion</a></p>
</main>

<?php include $prefix . '/partials/footer.php'; ?>
