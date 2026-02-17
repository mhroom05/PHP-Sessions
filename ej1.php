<?php
session_start();

// Inicializar array en sesión si no existe
if (!isset($_SESSION['numbers'])) {
    $_SESSION['numbers'] = [10, 20, 30];
}

$average = null;

// Botón Modify
if (isset($_POST['modify'])) {
    $position = $_POST['position'];
    $newValue = $_POST['newValue'];

    if (is_numeric($newValue)) {
        $_SESSION['numbers'][$position] = $newValue;
    }
}

// Botón Average
if (isset($_POST['average'])) {
    $average = array_sum($_SESSION['numbers']) / count($_SESSION['numbers']);
}

// Botón Reset (solo limpia formulario)
if (isset($_POST['reset'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modify array saved in session</title>
</head>
<body>

<h2>Modify array saved in session</h2>

<form method="post">

    <label>Position to modify:</label>
    <select name="position">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
    </select>
    <br><br>

    <label>New value:</label>
    <input type="text" name="newValue">
    <br><br>

    <button type="submit" name="modify">Modify</button>
    <button type="submit" name="average">Average</button>
    <button type="submit" name="reset">Reset</button>

</form>

<h3>Current array:
    <?php echo implode(", ", $_SESSION['numbers']); ?>
</h3>

<?php if ($average !== null): ?>
    <h3>Average: <?php echo round($average, 2); ?></h3>
<?php endif; ?>

</body>
</html>