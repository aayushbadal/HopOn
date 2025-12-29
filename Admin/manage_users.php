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
                    <th>No.</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                <tr>   
                    <td><?= $i ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <a href="user_view.php?id=<?= $user['id'] ?>" class="view-btn">
                            Details
                        </a>
                    </td>
                    <?php $i++;  ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
