<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db = 'js_crud';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die(json_encode(['message' => 'Database connection failed']));
}

$action = $_REQUEST['action'] ?? '';

if ($action == 'create') {
  $name = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $conn->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
  echo json_encode(['message' => 'User added successfully']);

} elseif ($action == 'read') {
  $res = $conn->query("SELECT * FROM users ORDER BY id DESC");
  $users = [];
  while ($row = $res->fetch_assoc()) {
    $users[] = $row;
  }
  echo json_encode($users);

} elseif ($action == 'update') {
  $id = (int)$_POST['id'];
  $name = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
  echo json_encode(['message' => 'User updated successfully']);

} elseif ($action == 'delete') {
  $id = (int)$_POST['id'];
  $conn->query("DELETE FROM users WHERE id=$id");
  echo json_encode(['message' => 'User deleted successfully']);

} elseif ($action == 'import_csv' && isset($_FILES['excel_file']['tmp_name'])) {
  $file = $_FILES['excel_file']['tmp_name'];

  if (($handle = fopen($file, "r")) !== FALSE) {
      $row = 0;
      $inserted = 0;
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          if ($row == 0) { $row++; continue; } // skip header

          $name = $conn->real_escape_string($data[0]);
          $email = $conn->real_escape_string($data[1]);

          // Skip empty or invalid emails
          if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
          }

          // Check duplicate email
          $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
          if ($check->num_rows > 0) {
            continue;
          }

          $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
          if ($conn->query($sql)) {
            $inserted++;
          }
      }
      fclose($handle);
      echo json_encode(['message' => "✅ Data imported successfully. Rows inserted: $inserted"]);
  } else {
      echo json_encode(['message' => "❌ File could not be read."]);
  }

} else {
  echo json_encode(['message' => 'Invalid action']);
}
?>
