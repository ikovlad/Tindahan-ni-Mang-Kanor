<?php
include 'config.php';

// --- Search and Sort Logic ---
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

$sql = "
    SELECT t.transaction_id, CONCAT(c.first_name, ' ', c.last_name) as customer_name, 
           i.item_name, t.quantity, t.total_amount, t.transaction_date
    FROM transactions t
    JOIN customer c ON t.customer_id = c.customer_id
    JOIN items i ON t.item_id = i.item_id
";

if (!empty($search)) {
    $sql .= " WHERE c.first_name LIKE '%$search%' OR c.last_name LIKE '%$search%' OR i.item_name LIKE '%$search%'";
}

switch ($sort) {
    case 'date_asc':
        $sql .= " ORDER BY t.transaction_date ASC";
        break;
    case 'amount_asc':
        $sql .= " ORDER BY t.total_amount ASC";
        break;
    case 'amount_desc':
        $sql .= " ORDER BY t.total_amount DESC";
        break;
    default: // date_desc
        $sql .= " ORDER BY t.transaction_date DESC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Tindahan ni Mang Kanor</title>
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
                <li><a href="items.php"><i class="fas fa-box"></i> Items</a></li>
                <li><a href="transactions.php" class="active"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>TRANSACTIONS</h1>
            <div class="header-right">
                <h1>Tindahan ni Mang Kanor</h1>
                <p>Inventory Monitoring System</p>
            </div>
        </header>

        <section class="page-header">
            <div class="search-container">
                <form action="transactions.php" method="get">
                    <input type="text" name="search" placeholder="Search customer or item" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
            <div class="sort-container">
                 <form action="transactions.php" method="get" onchange="this.submit()">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <select name="sort">
                        <option value="date_desc" <?php if($sort == 'date_desc') echo 'selected'; ?>>Sort by Date (Newest)</option>
                        <option value="date_asc" <?php if($sort == 'date_asc') echo 'selected'; ?>>Sort by Date (Oldest)</option>
                        <option value="amount_desc" <?php if($sort == 'amount_desc') echo 'selected'; ?>>Sort by Amount (High-Low)</option>
                        <option value="amount_asc" <?php if($sort == 'amount_asc') echo 'selected'; ?>>Sort by Amount (Low-High)</option>
                    </select>
                </form>
            </div>
        </section>

        <section class="table-container card">
            <h2>Transactions Table</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                     <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["transaction_id"]; ?></td>
                            <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["item_name"]); ?></td>
                            <td><?php echo $row["quantity"]; ?></td>
                            <td><?php echo format_currency($row["total_amount"]); ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row["transaction_date"])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No transactions found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>