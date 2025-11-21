<?php
// CONFIGURACIÓN DE BASE DE DATOS (Railway)
$host = "hopper.proxy.rlwy.net";   // Cambia si tu host es otro
$db   = "railway";
$user = "root";
$pass = "NInCtNAqoHyRyDMWLJbEtfynXZpTPRDu";
$port = 41725;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// PROCESAR FORMULARIO
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? "";
    $email = $_POST['email'] ?? "";
    $archivoNombre = "";

    // SUBIR ARCHIVO
    if (!empty($_FILES['archivo']['name'])) {
        $destino = "uploads/" . basename($_FILES["archivo"]["name"]);
        if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $destino)) {
            $archivoNombre = basename($_FILES["archivo"]["name"]);
        }
    }

    // INSERTAR EN BD
    $sql = "INSERT INTO clientes (nombre, email, archivo) VALUES (:nombre, :email, :archivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'email'  => $email,
        'archivo' => $archivoNombre
    ]);

    $mensaje = "Registro guardado correctamente!";
}

// CONSULTAR REGISTROS
$stmt = $pdo->query("SELECT * FROM clientes ORDER BY id DESC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 30px;
        }
        input, button {
            padding: 10px;
            width: 100%;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #bbb;
        }
        button {
            background: #007bff;
            color: white;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background: #0056b3;
        }
        .mensaje {
            background: #d4edda;
            padding: 10px;
            border-left: 6px solid #28a745;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #155724;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f6f7;
        }
        .archivo-link {
            color: #007bff;
            text-decoration: none;
        }
        .archivo-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Registro de Clientes</h2>

    <?php if ($mensaje): ?>
        <div class="mensaje"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo" required>
        <input type="file" name="archivo">
        <button type="submit">Guardar Cliente</button>
    </form>

    <h2>Clientes Registrados</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Archivo</th>
            <th>Fecha</th>
        </tr>

        <?php foreach ($clientes as $cli): ?>
        <tr>
            <td><?= $cli['id'] ?></td>
            <td><?= $cli['nombre'] ?></td>
            <td><?= $cli['email'] ?></td>
            <td>
                <?php if ($cli['archivo']): ?>
                    <a class="archivo-link" href="uploads/<?= $cli['archivo'] ?>" target="_blank">
                        Descargar
                    </a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td><?= $cli['fecha'] ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>
