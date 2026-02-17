<?php
session_start();

/**
 * INVENTARIO COMPARTIDO (todos los trabajadores)
 * Se guarda en un archivo JSON en el servidor.
 */
$inventoryFile = __DIR__ . "/inventory.json";

// Si el archivo no existe, lo creamos con inventario inicial
if (!file_exists($inventoryFile)) {
    file_put_contents($inventoryFile, json_encode([
        "milk" => 0,
        "softdrink" => 0
    ], JSON_PRETTY_PRINT));
}

// Leer inventario actual
$inventory = json_decode(file_get_contents($inventoryFile), true);
if (!is_array($inventory)) {
    $inventory = ["milk" => 0, "softdrink" => 0];
}

$error = "";

/**
 * TRABAJADOR (por usuario)
 * Se mantiene en sesión.
 */
if (isset($_POST["worker"])) {
    $_SESSION["worker"] = trim($_POST["worker"]);
}

$worker = $_SESSION["worker"] ?? "";

/**
 * Reset: solo limpia el formulario (mantiene worker e inventario)
 */
if (isset($_POST["reset"])) {
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

// Valores actuales del formulario (para repintar)
$product = $_POST["product"] ?? "milk";
$qtyRaw  = $_POST["quantity"] ?? "";
$quantity = is_numeric($qtyRaw) ? (int)$qtyRaw : 0;

// Add / Remove
if (isset($_POST["add"]) || isset($_POST["remove"])) {
    // Validaciones básicas
    if ($worker === "") {
        $error = "Error: worker name is required.";
    } elseif (!in_array($product, ["milk", "softdrink"], true)) {
        $error = "Error: invalid product.";
    } elseif (!is_numeric($qtyRaw) || (int)$qtyRaw <= 0) {
        $error = "Error: quantity must be a positive number.";
    } else {
        if (isset($_POST["add"])) {
            $inventory[$product] += (int)$qtyRaw;
        } else { // remove
            if ($inventory[$product] < (int)$qtyRaw) {
                $error = "Error: you can't remove more units than available.";
            } else {
                $inventory[$product] -= (int)$qtyRaw;
            }
        }

        // Guardar inventario si no hubo error
        if ($error === "") {
            file_put_contents($inventoryFile, json_encode($inventory, JSON_PRETTY_PRINT));
        }
    }
}

// Labels
function productLabel(string $p): string {
    return $p === "softdrink" ? "Soft Drink" : "Milk";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supermarket management</title>
</head>
<body>

<h2>Supermarket management</h2>

<form method="post">
    <label>Worker name:</label><br>
    <input type="text" name="worker" value="<?php echo htmlspecialchars($worker); ?>">
    <br><br>

    <b>Choose product:</b><br>
    <select name="product">
        <option value="milk" <?php echo ($product === "milk") ? "selected" : ""; ?>>Milk</option>
        <option value="softdrink" <?php echo ($product === "softdrink") ? "selected" : ""; ?>>Soft Drink</option>
    </select>
    <br><br>

    <b>Product quantity:</b><br>
    <input type="text" name="quantity" value="<?php echo htmlspecialchars($qtyRaw); ?>">
    <br><br>

    <button type="submit" name="add">add</button>
    <button type="submit" name="remove">remove</button>
    <button type="submit" name="reset">reset</button>
</form>

<?php if ($error !== ""): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<h3>Inventory:</h3>
<p>worker: <?php echo htmlspecialchars($worker === "" ? "-" : $worker); ?></p>
<p>units milk: <?php echo (int)$inventory["milk"]; ?></p>
<p>units soft drink: <?php echo (int)$inventory["softdrink"]; ?></p>

</body>
</html>