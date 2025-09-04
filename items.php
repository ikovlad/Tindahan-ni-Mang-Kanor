<?php
include 'config.php';

// --- Search and Sort Logic ---
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'item_id_asc';

$sql = "SELECT item_id, item_name, category, price, stock_quantity FROM items";
if (!empty($search)) {
    $sql .= " WHERE item_name LIKE '%$search%' OR category LIKE '%$search%'";
}

switch ($sort) {
    case 'name_asc':
        $sql .= " ORDER BY item_name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY item_name DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'stock_asc':
        $sql .= " ORDER BY stock_quantity ASC";
        break;
    case 'stock_desc':
        $sql .= " ORDER BY stock_quantity DESC";
        break;
    default:
        $sql .= " ORDER BY item_id ASC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items - Tindahan ni Mang Kanor</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <img src="logo.png">
            <p>Sari-sari Store</p>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="customers.php"><i class="fas fa-users"></i> Customer</a></li>
                <li><a href="items.php" class="active"><i class="fas fa-box"></i> Items</a></li>
                <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>ITEMS</h1>
            <div class="header-right">
                <h1>Tindahan ni Mang Kanor</h1>
                <p>Inventory Monitoring System</p>
            </div>
        </header>

        <section class="page-header">
            <div class="search-container">
                <form action="items.php" method="get">
                    <input type="text" name="search" placeholder="Search here" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
            <div class="sort-container">
                 <form action="items.php" method="get" onchange="this.submit()">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <select name="sort">
                        <option value="item_id_asc" <?php if($sort == 'item_id_asc') echo 'selected'; ?>>Sort by ID</option>
                        <option value="name_asc" <?php if($sort == 'name_asc') echo 'selected'; ?>>Sort by Name (A-Z)</option>
                        <option value="name_desc" <?php if($sort == 'name_desc') echo 'selected'; ?>>Sort by Name (Z-A)</option>
                        <option value="price_asc" <?php if($sort == 'price_asc') echo 'selected'; ?>>Sort by Price (Low-High)</option>
                        <option value="price_desc" <?php if($sort == 'price_desc') echo 'selected'; ?>>Sort by Price (High-Low)</option>
                        <option value="stock_asc" <?php if($sort == 'stock_asc') echo 'selected'; ?>>Sort by Stock (Low-High)</option>
                        <option value="stock_desc" <?php if($sort == 'stock_desc') echo 'selected'; ?>>Sort by Stock (High-Low)</option>
                    </select>
                </form>
            </div>
        </section>

        <section class="table-container card">
            <h2>Items Table</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["item_id"]; ?></td>
                            <td><?php echo htmlspecialchars($row["item_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["category"]); ?></td>
                            <td><?php echo format_currency($row["price"]); ?></td>
                            <td><?php echo $row["stock_quantity"]; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>