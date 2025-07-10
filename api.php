<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = 'root';
$db = 'js_crud';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  echo json_encode(['message' => 'Database connection failed']);
  exit;
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'create') {
  $name = $conn->real_escape_string($_POST['name']);
  $contact = $conn->real_escape_string($_POST['contact_number']);
  $email = $conn->real_escape_string($_POST['email']);

  $sql = "INSERT INTO users (name, contact, email) VALUES ('$name', '$contact', '$email')";
  if ($conn->query($sql)) {
    echo json_encode(['message' => '✅ User added successfully']);
  } else {
    echo json_encode(['message' => '❌ Insert failed: ' . $conn->error]);
  }
  exit;

} elseif ($action === 'read') {
  $res = $conn->query("SELECT * FROM users ORDER BY id DESC");
  $users = [];
  while ($row = $res->fetch_assoc()) {
    $users[] = $row;
  }
  echo json_encode($users);
  exit;

} elseif ($action === 'update') {
  $id = (int)$_POST['id'];
  $name = $conn->real_escape_string($_POST['name']);
  $contact = $conn->real_escape_string($_POST['contact_number']);
  $email = $conn->real_escape_string($_POST['email']);

  $sql = "UPDATE users SET name='$name', contact='$contact', email='$email' WHERE id=$id";
  if ($conn->query($sql)) {
    echo json_encode(['message' => '✅ User updated successfully']);
  } else {
    echo json_encode(['message' => '❌ Update failed: ' . $conn->error]);
  }
  exit;

} elseif ($action === 'delete') {
  $id = (int)$_POST['id'];
  $sql = "DELETE FROM users WHERE id=$id";
  if ($conn->query($sql)) {
    echo json_encode(['message' => '✅ User deleted successfully']);
  } else {
    echo json_encode(['message' => '❌ Delete failed: ' . $conn->error]);
  }
  exit;

} elseif ($action === 'import_csv' && isset($_FILES['excel_file']['tmp_name'])) {
  $file = $_FILES['excel_file']['tmp_name'];

  if (($handle = fopen($file, "r")) !== false) {
    $row = 0;
    $inserted = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
      if ($row == 0) { $row++; continue; }

      $name = $conn->real_escape_string($data[0]);
      $contact = $conn->real_escape_string($data[1]);
      $email = $conn->real_escape_string($data[2]);

      if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        continue;
      }

      $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
      if ($check->num_rows > 0) {
        continue;
      }

      $sql = "INSERT INTO users (name, contact, email) VALUES ('$name', '$contact', '$email')";
      if ($conn->query($sql)) {
        $inserted++;
      }
    }
    fclose($handle);
    echo json_encode(['message' => "✅ Data imported successfully. Rows inserted: $inserted"]);
  } else {
    echo json_encode(['message' => "❌ File could not be read."]);
  }
  exit;

} else {
  echo json_encode(['message' => '❌ Invalid action']);
  exit;
}
?>
