<?php

// Datos Railway
$host = "hopper.proxy.rlwy.net";
$port = 41725;
$user = "root";
$password = "NInCtNAqoHyRyDMWLJbEtfynXZpTPRDu";
$dbname = "railway";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["name"] ?? "";
    $celular = $_POST["celular"] ?? "";
    $direccion = $_POST["direccion"] ?? "";
    $departamento = $_POST["departamento"] ?? "";
    $archivo_final = null;

    // Ruta de uploads dentro del contenedor
    $uploads_dir = __DIR__ . "/uploads/";

    // Subir archivo
    if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === 0) {

        $filename = time() . "_" . basename($_FILES["archivo"]["name"]);
        $target = $uploads_dir . $filename;

        if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $target)) {
            $archivo_final = $filename;
        } else {
            $message = "Error al subir archivo.";
        }
    }

    // Guardar en BD
    $sql = "INSERT INTO clientes (name, celular, direccion, departamento, archivo) 
            VALUES (:name, :celular, :direccion, :departamento, :archivo)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":celular" => $celular,
        ":direccion" => $direccion,
        ":departamento" => $departamento,
        ":archivo" => $archivo_final
    ]);

    $message = "Cliente guardado correctamente";
}

?>

<!DOCTYPE html>
<html>
<body>
<h2>Registrar cliente</h2>

<?php if ($message) echo "<p><b>$message</b></p>"; ?>

<form method="POST" enctype="multipart/form-data">
    Nombre: <input name="name"><br><br>
    Celular: <input name="celular"><br><br>
    Dirección: <input name="direccion"><br><br>
    Departamento: <input name="departamento"><br><br>
    
    Archivo (opcional):
    <input type="file" name="archivo"><br><br>
    
    <button type="submit">Guardar</button>
</form>

</body>
</html>
