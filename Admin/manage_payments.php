<?php
require_once "./includes/header.php";
if (!isLoggedIn()) {
    header('Location: ./admin_login.php');
    exit();
}

// Handle payment status update via GET
if (isset($_GET['payment_id'], $_GET['action'])) {
    $payment_id = intval($_GET['payment_id']);
    $action = $_GET['action'];

    if ($action === 'success') {
        $stmt = $conn->prepare("UPDATE bookings SET payment_status = 'SUCCESS' WHERE id = ?");
    } elseif ($action === 'pending') {
        $stmt = $conn->prepare("UPDATE bookings SET payment_status = 'PENDING' WHERE id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_payments.php");
        exit();
    }
}

// Handle search/filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$method_filter = isset($_GET['method']) ? $_GET['method'] : '';

// Base query
$query = "SELECT b.id as booking_id, b.booking_reference, b.total_amount, b.payment_status, b.payment_method, b.payment_ref, b.booking_date,
                 u.username, u.email,
                 r.startin, r.destination,
                 v.vehicle_number,
                 rd.routing_date
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN routes r ON b.route_id = r.id
          JOIN vehicle_lists v ON b.vehicle_id = v.id
          JOIN route_date rd ON b.route_date_id = rd.id
          WHERE 1=1";

// Apply search
$params = [];
$types = "";
if ($search) {
    $query .= " AND (b.booking_reference LIKE ? OR u.username LIKE ?)";
    $search_param = "%$search%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $types .= "ss";
}

// Apply status filter
if ($status_filter) {
    $query .= " AND b.payment_status = ?";
    $params[] = &$status_filter;
    $types .= "s";
}

// Apply method filter
if ($method_filter) {
    $query .= " AND b.payment_method = ?";
    $params[] = &$method_filter;
    $types .= "s";
}

$query .= " ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="dashboard-section">
    <div class="container">
        <h1 class="section-title">Manage Payments</h1>

        <!-- Filter Form -->
        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search by Booking Ref or Username" value="<?= htmlspecialchars($search) ?>">
            <select name="status">
                <option value="">All Status</option>
                <option value="PENDING" <?= $status_filter === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                <option value="SUCCESS" <?= $status_filter === 'SUCCESS' ? 'selected' : '' ?>>SUCCESS</option>
            </select>
            <select name="method">
                <option value="">All Methods</option>
                <option value="Khalti" <?= $method_filter === 'Khalti' ? 'selected' : '' ?>>Khalti</option>
                <option value="eSewa" <?= $method_filter === 'eSewa' ? 'selected' : '' ?>>eSewa</option>
                <option value="Card" <?= $method_filter === 'Card' ? 'selected' : '' ?>>Card</option>
            </select>
            <button type="submit">Filter</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Booking Ref</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Route</th>
                    <th>Vehicle</th>
                    <th>Travel Date</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Method</th>
                    <th>Payment Ref</th>
                    <th>Booking Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['booking_reference']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['startin'] . ' → ' . $row['destination']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($row['routing_date']) ?></td>
                    <td><?= htmlspecialchars($row['total_amount']) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td><?= htmlspecialchars($row['payment_ref']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td>
                        <?php if($row['payment_status'] === 'PENDING'): ?>
                            <a href="?payment_id=<?= $row['booking_id'] ?>&action=success" class="btn btn-success">Approve</a>
                        <?php else: ?>
                            <a href="?payment_id=<?= $row['booking_id'] ?>&action=pending" class="btn btn-warning">Undo</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<style>
    .filter-form {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-form input, .filter-form select, .filter-form button {
        padding: 6px 10px;
        font-size: 14px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        font-size: 14px;
    }
    .table th {
        background: #f4f4f4;
    }
    .btn {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 4px;
        text-decoration: none;
        color: #fff;
    }
    .btn-success { background: green; }
    .btn-warning { background: orange; }
</style>

<?php
$stmt->close();
?>
