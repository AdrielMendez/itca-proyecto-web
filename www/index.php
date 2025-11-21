<?php
// CONFIG DE BASE DE DATOS 
$host = "hopper.proxy.rlwy.net";
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

$mensaje = "";

// PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name         = $_POST['name'] ?? "";
    $celular      = $_POST['celular'] ?? "";
    $direccion    = $_POST['direccion'] ?? "";
    $departamento = $_POST['departamento'] ?? "";
    $archivo_final = "";

    // SUBIR ARCHIVO
    if (!empty($_FILES['archivo']['name'])) {

        $nombreArchivo  = basename($_FILES["archivo"]["name"]);
        $destino        = "uploads/" . $nombreArchivo;

        if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $destino)) {
            $archivo_final = $nombreArchivo;
        }
    }

    // INSERTAR EN BD CON TUS CAMPOS
    $sql = "INSERT INTO clientes (name, celular, direccion, departamento, archivo)
            VALUES (:name, :celular, :direccion, :departamento, :archivo)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name"         => $name,
        ":celular"      => $celular,
        ":direccion"    => $direccion,
        ":departamento" => $departamento,
        ":archivo"      => $archivo_final
    ]);

    $mensaje = "Cliente guardado correctamente!";
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
            max-width: 900px;
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
        input, select, button {
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

        <input type="text" name="name" placeholder="Nombre" required>
        <input type="text" name="celular" placeholder="Celular" required>
        <input type="text" name="direccion" placeholder="Dirección" required>

        <select name="departamento" required>
            <option value="">Seleccione departamento</option>
            <option>San Salvador</option>
            <option>La Libertad</option>
            <option>Santa Ana</option>
            <option>Usulután</option>
            <option>San Miguel</option>
                <option>Ahuachapan</option>
                <option>La paz</option>
                <option>Cabañas</option>
        </select>

        <label>Adjuntar archivo:</label>
        <input type="file" name="archivo">

        <button type="submit">Guardar Cliente</button>
    </form>

    <h2>Clientes Registrados</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Celular</th>
            <th>Dirección</th>
            <th>Departamento</th>
            <th>Archivo</th>
        </tr>

        <?php foreach ($clientes as $cli): ?>
        <tr>
            <td><?= $cli['id'] ?></td>
            <td><?= $cli['name'] ?></td>
            <td><?= $cli['celular'] ?></td>
            <td><?= $cli['direccion'] ?></td>
            <td><?= $cli['departamento'] ?></td>
            <td>
                <?php if ($cli['archivo']): ?>
                    <a class="archivo-link" href="uploads/<?= $cli['archivo'] ?>" target="_blank">Descargar</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>
