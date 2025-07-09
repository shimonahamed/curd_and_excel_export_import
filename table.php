<?php
session_start();

$report = $_SESSION['import_report'] ?? null;

// রিপোর্ট একবার দেখানোর পর session ক্লিয়ার করতে চাইলে:
unset($_SESSION['import_report']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Import Excel to MySQL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
  <div class="container mt-5">
  <?php if ($report): ?>
        <p>✅ Inserted: <?= htmlspecialchars($report['inserted']) ?> rows</p>
        <p>⛔ Skipped (empty email): <?= htmlspecialchars($report['skipped_empty']) ?></p>
        <p>❌ Skipped (invalid email): <?= htmlspecialchars($report['skipped_invalid']) ?></p>
        <p>🚫 Skipped (duplicate email): <?= htmlspecialchars($report['skipped_duplicate']) ?></p>
    <?php else: ?>
        <p>No import report available.</p>
    <?php endif; ?>

    <a href="index.html">Go Back</a>
    <h1 class="text-center">Import Excel (.csv) File to MySQL</h1>
    <p class="text-center">Upload a .csv file to import data into the database.</p>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="excel_file"  required>
      <button class="btn btn-secondary" type="submit">📤 Upload & Import</button>
    </form>
  </div>

</body>
</html>