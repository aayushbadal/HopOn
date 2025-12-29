<?php
require_once "./includes/header.php";

if (!isLoggedIn()) {
    header("Location: admin_login.php");
    exit();
}

/* Fetch users */
$sql = "SELECT id, username, email FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<section class="dashboard-section">
    <div class="container">
        <h2 class="section-title">Manage Users</h2>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <a href="user_view.php?id=<?= $user['id'] ?>" class="view-btn">
                            Details
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
