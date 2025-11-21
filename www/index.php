<?php

// Ajusta tus datos de Railway
$host = "hopper.proxy.rlwy.net";
$port = 41725;
$dbname = "railway";
$user = "root";
$pass = "NInCtNAqoHyRyDMWLJbEtfynXZpTPRDu";

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Guardar datos del formulario
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $celular = $_POST["celular"];
    $direccion = $_POST["direccion"];
    $departamento = $_POST["departamento"];

    $sql = "INSERT INTO clientes (name, celular, direccion, departamento)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$name, $celular, $direccion, $departamento])) {
        $mensaje = "Cliente guardado correctamente.";
    } else {
        $mensaje = "Error al guardar.";
    }
}

// Obtener lista de clientes
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Clientes - LAMP + Railway</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; padding: 30px; }
        form { margin-bottom: 30px; }
        input { display:block; margin:10px 0; padding:8px; width:250px; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border:1px solid #ccc; padding:10px; }
    </style>
</head>
<body>

<h1>Registro de Clientes</h1>

<?php if ($mensaje): ?>
<p><strong><?= $mensaje ?></strong></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" placeholder="Nombre" required>
    <input type="text" name="celular" placeholder="Celular" required>
    <input type="text" name="direccion" placeholder="Dirección" required>
    <input type="text" name="departamento" placeholder="Departamento" required>

    <button type="submit">Guardar Cliente</button>
</form>

<h2>Clientes registrados</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Celular</th>
        <th>Dirección</th>
        <th>Departamento</th>
    </tr>

    <?php foreach ($clientes as $c): ?>
    <tr>
        <td><?= $c['id'] ?></td>
        <td><?= $c['name'] ?></td>
        <td><?= $c['celular'] ?></td>
        <td><?= $c['direccion'] ?></td>
        <td><?= $c['departamento'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
