<?php
include 'config.php';

// --- Search and Sort Logic ---
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'customer_id_asc';

$sql = "SELECT customer_id, first_name, last_name, contact_number, address FROM customer";
if (!empty($search)) {
    $sql .= " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR address LIKE '%$search%'";
}

switch ($sort) {
    case 'name_asc':
        $sql .= " ORDER BY first_name ASC, last_name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY first_name DESC, last_name DESC";
        break;
    default:
        $sql .= " ORDER BY customer_id ASC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Tindahan ni Mang Kanor</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <img src="logo.png">
            <h4>Sari-sari Store</h4>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="customers.php" class="active"><i class="fas fa-users"></i> Customer</a></li>
                <li><a href="items.php"><i class="fas fa-box"></i> Items</a></li>
                <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>CUSTOMER</h1>
            <div class="header-right">
                <h1>Tindahan ni Mang Kanor</h1>
                <p>Inventory Monitoring System</p>
            </div>
        </header>

        <section class="page-header">
            <div class="search-container">
                <form action="customers.php" method="get">
                    <input type="text" name="search" placeholder="Search here" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
            <div class="sort-container">
                 <form action="customers.php" method="get" onchange="this.submit()">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <select name="sort">
                        <option value="customer_id_asc" <?php if($sort == 'customer_id_asc') echo 'selected'; ?>>Sort by ID</option>
                        <option value="name_asc" <?php if($sort == 'name_asc') echo 'selected'; ?>>Sort by Name (A-Z)</option>
                        <option value="name_desc" <?php if($sort == 'name_desc') echo 'selected'; ?>>Sort by Name (Z-A)</option>
                    </select>
                </form>
            </div>
        </section>

        <section class="table-container card">
            <h2>CUSTOMER TABLE</h2>
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Contact No.</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["customer_id"]; ?></td>
                            <td><?php echo htmlspecialchars($row["first_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["last_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["contact_number"]); ?></td>
                            <td><?php echo htmlspecialchars($row["address"]); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No customers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>