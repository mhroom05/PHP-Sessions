<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Product catalog (same as shop)
$products = [
    "Laptop"   => ["price" => 900],
    "Mouse"    => ["price" => 20],
    "Keyboard" => ["price" => 45],
    "Monitor"  => ["price" => 180]
];

// Finish order
if (isset($_POST['finish'])) {
    session_unset();
    session_destroy();
    header("Location: home.php");
    exit;
}

// Calculate totals
$totalPrice = 0;
$totalItems = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item => $qty) {
        $totalItems += $qty;
        $totalPrice += $products[$item]['price'] * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-size: 2em;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .checkout-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }

        .empty-text {
            font-size: 1.3em;
            color: #999;
            margin-bottom: 30px;
        }

        .cart-items {
            margin-bottom: 30px;
        }

        .cart-items h2 {
            font-size: 1.3em;
            margin-bottom: 20px;
            color: #333;
        }

        .items-list {
            list-style: none;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .items-list li {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }

        .items-list li:last-child {
            border-bottom: none;
        }

        .items-list li:hover {
            background: #f0f0f0;
        }

        .item-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 15px;
            min-width: 30px;
        }

        .item-name {
            flex-grow: 1;
            font-size: 1.05em;
            font-weight: 500;
        }

        .summary {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            color: #999;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
        }

        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 1em;
        }

        .btn-finish {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-finish:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-back {
            background: #f0f0f0;
            color: #333;
        }

        .btn-back:hover {
            background: #e0e0e0;
            transform: translateY(-3px);
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .checkout-card {
                padding: 25px;
            }

            .summary {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <h1>✅ Checkout</h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <span style="background: rgba(255, 255, 255, 0.2); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9em;">👤 <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <a href="home.php" class="nav-link">🏠 Home</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="checkout-card">
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <div class="empty-text">Your cart is empty</div>
                    <a href="shop.php" class="btn btn-back">← Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <h2>Order Summary</h2>
                    <ul class="items-list">
                        <?php
                        $counter = 1;
                        foreach ($_SESSION['cart'] as $item => $qty):
                        ?>
                            <li>
                                <span class="item-number"><?php echo $counter; ?></span>
                                <span class="item-name">
                                    <?php echo htmlspecialchars($item); ?>
                                    <strong>(x<?php echo $qty; ?>)</strong>
                                </span>
                            </li>
                        <?php
                            $counter++;
                        endforeach;
                        ?>

                    </ul>
                </div>

                <div class="summary">
                    <div class="summary-item">
                        <span class="summary-label">Total Items</span>
                        <span class="summary-value"><?php echo $totalItems; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Price</span>
                        <span class="summary-value">€<?php echo number_format($totalPrice, 2); ?></span>
                    </div>
                </div>

                <div class="actions">
                    <form method="post" style="display: inline;">
                        <button type="submit" name="finish" class="btn btn-finish">✓ Complete Order</button>
                    </form>
                    <a href="shop.php" class="btn btn-back">← Back to Shop</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

</body>

</html>