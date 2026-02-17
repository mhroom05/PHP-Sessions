<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Product catalog (price + initial stock)
$products = [
    "Laptop"   => ["price" => 900, "stock" => 5],
    "Mouse"    => ["price" => 20,  "stock" => 10],
    "Keyboard" => ["price" => 45,  "stock" => 7],
    "Monitor"  => ["price" => 180, "stock" => 4]
];

// Initialize stock in session (only once)
if (!isset($_SESSION['stock'])) {
    foreach ($products as $name => $data) {
        $_SESSION['stock'][$name] = $data['stock'];
    }
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart
if (isset($_POST['product'])) {
    $product = $_POST['product'];

    if (
        isset($_SESSION['stock'][$product]) &&
        $_SESSION['stock'][$product] >= 0 
    ) {
        // Increase quantity in cart
        if (!isset($_SESSION['cart'][$product])) {
            $_SESSION['cart'][$product] = 1;
        } else {
            $_SESSION['cart'][$product]++;
        }

        // Decrease stock
        $_SESSION['stock'][$product]--;
    }
}

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item => $qty) {
    $totalPrice += $products[$item]['price'] * $qty; 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-size: 2em;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .cart-info {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .user-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            color: #999;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #667eea;
        }

        .cart-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 0.95em;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-logout {
            background: #f0f0f0;
            color: #333;
        }

        .btn-logout:hover {
            background: #e0e0e0;
        }

        .products-section h2 {
            font-size: 1.8em;
            margin-bottom: 30px;
            color: #333;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .product-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            font-size: 3em;
        }

        .product-info {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-name {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .product-price {
            font-size: 1.5em;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-card form {
            display: flex;
        }

        .product-card button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .product-card button:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .cart-info {
                flex-direction: column;
                text-align: center;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <h1>🛍️ Shop</h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <span style="background: rgba(255, 255, 255, 0.2); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9em;">👤 <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <div class="nav-links">
                    <a href="home.php">🏠 Home</a>
                    <a href="login.php">🚪 Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="cart-info">
        <div class="user-info">
    <div class="info-item">
        <span class="info-label">Items in Cart</span>
        <span class="info-value">
            <?= array_sum($_SESSION['cart']); ?>
        </span>
    </div>

    <div class="info-item">
        <span class="info-label">Cart Details</span>
        <span class="info-value" style="font-size:0.95em; font-weight:500; color:#333;">
            <?php if (empty($_SESSION['cart'])): ?>
                Empty
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $item => $qty): ?>
                    <?= htmlspecialchars($item) ?>
                    (<?= $qty ?> × €<?= number_format($products[$item]['price'], 2) ?>)
                    = €<?= number_format($products[$item]['price'] * $qty, 2) ?><br>
                <?php endforeach; ?>
            <?php endif; ?>
        </span>
    </div>

    <div class="info-item">
        <span class="info-label">Total Price</span>
        <span class="info-value">
            €<?= number_format($totalPrice, 2); ?>
        </span>
    </div>
</div>

            
            <div class="cart-actions">
                <a href="checkout.php" class="btn btn-checkout">🛒 Go to Checkout</a>
            </div>
        </div>

        <div class="products-section">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php
                $icons = [
                    "Laptop"   => "💻",
                    "Mouse"    => "🖱️",
                    "Keyboard" => "⌨️",
                    "Monitor"  => "🖥️"
                ];

                foreach ($products as $name => $data):
                    $price = $data['price'];
                    $stock = $_SESSION['stock'][$name];
                ?>
                    <div class="product-card">
                        <div class="product-icon">
                            <?= $icons[$name] ?? "📦"; ?>
                        </div>

                        <div class="product-info">
                            <div class="product-name">
                                <?= htmlspecialchars($name); ?>
                            </div>

                            <div class="product-price">
                                €<?= number_format($price, 2); ?>
                            </div>

                            <div style="margin-bottom:12px; font-weight:600;
                        color: <?= $stock > 0 ? '#28a745' : '#dc3545' ?>">
                                Stock: <?= $stock ?>
                            </div>

                            <form method="post">
                                <input type="hidden" name="product" value="<?= htmlspecialchars($name); ?>">

                                <button type="submit" <?= $stock === 0 ? 'disabled' : '' ?>>
                                    <?= $stock === 0 ? 'Out of stock' : 'Add to Cart' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</body>

</html>