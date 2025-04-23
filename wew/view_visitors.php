<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header('Location: login.php');
  exit();
}

require 'db.php';

$filter = $_GET['filter'] ?? 'all';

switch ($filter) {
  case 'daily':
    $query = "SELECT * FROM visitors WHERE DATE(created_at) = CURDATE()";
    break;
  case 'weekly':
    $query = "SELECT * FROM visitors WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
    break;
  case 'monthly':
    $query = "SELECT * FROM visitors WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    break;
  default:
    $query = "SELECT * FROM visitors ORDER BY created_at DESC";
    break;
}

$results = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Visitors</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .filter-buttons {
      text-align: center;
      margin: 2rem 0 1rem;
    }

    .filter-buttons a {
      margin: 0 10px;
      padding: 0.5rem 1rem;
      background-color: #1976d2;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
    }

    .filter-buttons a.active,
    .filter-buttons a:hover {
      background-color: #004ba0;
    }

    table {
      width: 90%;
      margin: auto;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    th, td {
      padding: 0.75rem;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #388e3c;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>
  <header class="dashboard-header">
    <div class="header-title">Visitor Records</div>
    <a href="dashboard.php" class="logout-btn">← Back to Dashboard</a>
  </header>

  <div class="filter-buttons">
    <a href="?filter=daily" class="<?= $filter == 'daily' ? 'active' : '' ?>">Today</a>
    <a href="?filter=weekly" class="<?= $filter == 'weekly' ? 'active' : '' ?>">This Week</a>
    <a href="?filter=monthly" class="<?= $filter == 'monthly' ? 'active' : '' ?>">This Month</a>
    <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">All</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Agency</th>
        <th>Purpose</th>
        <th>Visit Time</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($results->num_rows > 0): ?>
        <?php while ($row = $results->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['fullName']) ?></td>
            <td><?= htmlspecialchars($row['agency']) ?></td>
            <td><?= htmlspecialchars($row['purpose']) ?></td>
            <td><?= date("F j, Y - g:i A", strtotime($row['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">No visitor records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
