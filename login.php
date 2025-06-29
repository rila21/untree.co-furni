<?php
// --- Database connection ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dbfurniture";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// --- Handle form submission ---
$info = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];
  $mode = $_POST["mode"];

  if ($mode == "register") {
    $email = $_POST["email"];
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO tblogin (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed, $email);
    if ($stmt->execute()) {
      $info = "Registrasi berhasil! Silakan login.";
    } else {
      $info = "Registrasi gagal: Username mungkin sudah digunakan.";
    }
  } else if ($mode == "login") {
    $stmt = $conn->prepare("SELECT password FROM tblogin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $stmt->bind_result($hashed);
      $stmt->fetch();
      if (password_verify($password, $hashed)) {
        header("Location: index.html");
        exit();
      } else {
        $info = "Password salah.";
      }
    } else {
      $info = "Username tidak ditemukan.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login & Register</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 320px;
    }

    h2 {
      text-align: center;
    }

    form input {
      width: 100%;
      margin: 10px 0;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    form button {
      width: 100%;
      padding: 10px;
      background: #007BFF;
      border: none;
      color: white;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }

    form button:hover {
      background: #0056b3;
    }

    .hidden {
      display: none;
    }

    #toggle-link {
      text-align: center;
      margin-top: 10px;
    }

    #toggle-link a {
      color: #007BFF;
      cursor: pointer;
      text-decoration: none;
    }

    #toggle-link a:hover {
      text-decoration: underline;
    }

    .info {
      text-align: center;
      margin-top: 10px;
      color: #d00;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 id="form-title">Login</h2>
    <?php if ($info): ?>
      <div class="info"><?= $info ?></div>
    <?php endif; ?>
    <form method="POST" id="auth-form">
      <input type="hidden" name="mode" id="mode" value="login">
      <input type="text" name="username" id="username" placeholder="Username" required />
      <input type="password" name="password" id="password" placeholder="Password" required />
      <div id="register-fields" class="hidden">
        <input type="email" name="email" id="email" placeholder="Email" required />
      </div>
      <button type="submit">Submit</button>
      <p id="toggle-link">Belum punya akun? <a href="#" onclick="toggleForm()">Register</a></p>
    </form>
  </div>

  <script>
    let isLogin = true;

    function toggleForm() {
      isLogin = !isLogin;
      document.getElementById("form-title").textContent = isLogin ? "Login" : "Register";
      document.getElementById("mode").value = isLogin ? "login" : "register";
      document.getElementById("register-fields").classList.toggle("hidden", isLogin);
      document.getElementById("toggle-link").innerHTML = isLogin
        ? 'Belum punya akun? <a href="#" onclick="toggleForm()">Register</a>'
        : 'Sudah punya akun? <a href="#" onclick="toggleForm()">Login</a>';
    }
  </script>
</body>
</html>
