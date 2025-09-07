<?php
include 'config.php';

// --- Card Data ---
// 1. Total Items (Stock Quantity)
$total_items_query = $conn->query("SELECT SUM(stock_quantity) as total_stock FROM items");
$total_items = $total_items_query->fetch_assoc()['total_stock'];

// 2. Total Sales
$total_sales_query = $conn->query("SELECT SUM(total_amount) as total_sales FROM transactions");
$total_sales = $total_sales_query->fetch_assoc()['total_sales'];

// 3. Hot Item (Most Sold Quantity)
$hot_item_query = $conn->query("
    SELECT i.item_name, SUM(t.quantity) as total_quantity
    FROM transactions t
    JOIN items i ON t.item_id = i.item_id
    GROUP BY t.item_id
    ORDER BY total_quantity DESC
    LIMIT 1
");
$hot_item = $hot_item_query->fetch_assoc()['item_name'] ?? 'N/A';


// 4. No. of Customers
$total_customers_query = $conn->query("SELECT COUNT(customer_id) as total_customers FROM customer");
$total_customers = $total_customers_query->fetch_assoc()['total_customers'];

// --- Bar Chart Data (Last 7 days of sales from the sample data) ---
// MODIFIED QUERY: Instead of CURDATE(), use the last date from your sample data ('2025-08-20')
// to ensure the chart has data to display.
$weekly_sales_query = $conn->query("
    SELECT 
        DAYNAME(date_added) as day, 
        SUM(total_amount) as total_sales
    FROM transactions
    WHERE date_added BETWEEN '2025-08-14' AND '2025-08-20'
    GROUP BY DAYOFWEEK(date_added), day
    ORDER BY DAYOFWEEK(date_added)
");
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$sales_data = array_fill_keys($days, 0);
while ($row = $weekly_sales_query->fetch_assoc()) {
    $sales_data[$row['day']] = $row['total_sales'];
}

// Reorder days to start from Monday for the chart
$ordered_sales_data = [
    'Monday'    => $sales_data['Monday'] ?? 0,
    'Tuesday'   => $sales_data['Tuesday'] ?? 0,
    'Wednesday' => $sales_data['Wednesday'] ?? 0,
    'Thursday'  => $sales_data['Thursday'] ?? 0,
    'Friday'    => $sales_data['Friday'] ?? 0,
    'Saturday'  => $sales_data['Saturday'] ?? 0,
    'Sunday'    => $sales_data['Sunday'] ?? 0,
];
$bar_chart_labels = json_encode(array_keys($ordered_sales_data));
$bar_chart_values = json_encode(array_values($ordered_sales_data));


// --- Pie Chart Data (Hot Items Distribution) ---
$pie_chart_query = $conn->query("
    SELECT i.item_name, SUM(t.total_amount) as item_sales
    FROM transactions t
    JOIN items i ON t.item_id = i.item_id
    GROUP BY t.item_id
    ORDER BY item_sales DESC
    LIMIT 10
");
$pie_chart_labels = [];
$pie_chart_values = [];
while ($row = $pie_chart_query->fetch_assoc()) {
    $pie_chart_labels[] = $row['item_name'];
    $pie_chart_values[] = $row['item_sales'];
}
$pie_chart_labels = json_encode($pie_chart_labels);
$pie_chart_values = json_encode($pie_chart_values);

// --- Recent Transactions ---
$recent_transactions_query = $conn->query("
    SELECT t.transaction_id, CONCAT(c.first_name, ' ', c.last_name) as customer_name, i.item_name, t.total_amount
    FROM transactions t
    JOIN customer c ON t.customer_id = c.customer_id
    JOIN items i ON t.item_id = i.item_id
    ORDER BY t.transaction_date DESC
    LIMIT 5
");

// --- Top Customers ---
$top_customers_query = $conn->query("
    SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name, SUM(t.total_amount) as total_spent
    FROM transactions t
    JOIN customer c ON t.customer_id = c.customer_id
    GROUP BY t.customer_id
    ORDER BY total_spent DESC
    LIMIT 5
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tindahan ni Mang Kanor</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">
            <img src="logo.png">
            <p>Sari-sari Store</p>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="customers.php"><i class="fas fa-users"></i> Customer</a></li>
                <li><a href="items.php"><i class="fas fa-box"></i> Items</a></li>
                <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>DASHBOARD</h1>
            <div></div>
            <div class="header-right">
                <h1>Tindahan ni Mang Kanor</h1>
                <p>Inventory Monitoring System</p>
            </div>
        </header>

        <section class="dashboard-cards">
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-box-open"></i></div>
                <div class="card-details">
                    <h3>Total Items</h3>
                    <p><?php echo number_format($total_items); ?></p>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="card-details">
                    <h3>No. of Sales</h3>
                    <p><?php echo format_currency($total_sales); ?></p>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-fire"></i></div>
                <div class="card-details">
                    <h3>Hot Item - Most Unit Sold</h3>
                    <p><?php echo htmlspecialchars($hot_item); ?></p>
                </div>
            </div>
            <div class="dashboard-card">
                 <div class="card-icon"><i class="fas fa-users"></i></div>
                <div class="card-details">
                    <h3>No. of Customer</h3>
                    <p><?php echo number_format($total_customers); ?></p>
                </div>
            </div>
        </section>

        <section class="charts-container">
            <div class="card">
                <h3>Weekly Sales</h3>
                <canvas id="salesBarChart"></canvas>
            </div>
            <div class="card clickable-widget" onclick="window.location.href='items.php';">
                <h3>Hot Items - Most Value Sold</h3>
                <canvas id="itemsPieChart"></canvas>
            </div>
        </section>

        <section class="bottom-widgets">
            <div class="card widget clickable-widget" onclick="window.location.href='transactions.php';">
                <h3>Recent Transactions</h3>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Customer</th><th>Item</th><th>Amount</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent_transactions_query->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['transaction_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo format_currency($row['total_amount']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="card widget clickable-widget" onclick="window.location.href='customers.php';">
                <h3>Top Customers</h3>
                <table>
                    <thead>
                        <tr><th>Customer Name</th><th>Total Spent</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $top_customers_query->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo format_currency($row['total_spent']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script>
        // Bar Chart for Weekly Sales
        const barCtx = document.getElementById('salesBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $bar_chart_labels; ?>,
                datasets: [{
                    label: 'Sales',
                    data: <?php echo $bar_chart_values; ?>,
                    backgroundColor: 'rgba(89, 21, 198, 0.8)',
                    borderColor: 'rgba(166, 90, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Pie Chart for Hot Items
        const pieCtx = document.getElementById('itemsPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo $pie_chart_labels; ?>,
                datasets: [{
                    label: 'Total Sales',
                    data: <?php echo $pie_chart_values; ?>,
                    backgroundColor: [
                        '#f65c5cff', '#009900ff', '#2bd7faff', '#ff0505ff',
                        '#ba3aedff', '#21b67aff', '#4c1d95', '#757575ff',
                        '#efff3fff', '#1327ffff'

                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12 }
                    }
                }
            }
        });
    </script>
</body>
</html>