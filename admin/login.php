<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* From Uiverse.io by SyedShahzaib7 - structure improved */
        body {
            background: #171717;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .wrapper {
            width: 420px;
            background: linear-gradient(90deg, rgba(2,0,36,1) 9%, rgba(9,9,121,1) 68%, rgba(0,91,255,1) 97%);
            backdrop-filter: blur(9px);
            color: #fff;
            border-radius: 16px;
            padding: 40px 40px 30px 40px;
            box-sizing: border-box;
            box-shadow: 0 8px 32px 0 rgba(2,0,36,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-login {
            font-size: 36px;
            text-align: center;
            margin-bottom: 32px;
            font-weight: 500;
        }
        .wrapper form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .wrapper .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin-bottom: 24px;
            margin-top: 0;
            display: flex;
            align-items: center;
        }
        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 0 20px;
            box-sizing: border-box;
            transition: border 0.2s;
        }
        .input-box input:focus {
            border: 2px solid #fff;
        }
        .input-box input::placeholder {
            color: #fff;
            opacity: 0.8;
        }
        .wrapper .remember-forgot {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            font-size: 14.5px;
            margin: 0 0 18px 0;
        }
        .remember-forgot label {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .remember-forgot label input {
            accent-color: #fff;
            margin-right: 3px;
        }
        .wrapper .btn {
            width: 160px;
            height: 45px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            font-size: 18px;
            color: #333;
            font-weight: 600;
            margin: 18px 0 0 0;
            align-self: center;
            transition: background 0.2s, color 0.2s;
        }
        .wrapper .btn:hover {
            background: #e0e0e0;
            color: #222;
        }
    </style>
</head>
<body>
<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $conn = new mysqli('localhost', 'root', '', 'scheme');
    if ($conn->connect_error) {
        $error = 'Database connection failed!';
    } else {
        $stmt = $conn->prepare('SELECT AdminID, Email, PasswordHash, Status FROM Admins WHERE Email = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['Status'] !== 'Active') {
                $error = 'Account is inactive.';
            } elseif (password_verify($password, $row['PasswordHash'])) {
                // Successful login
                session_start();
                $_SESSION['admin_id'] = $row['AdminID'];
                $_SESSION['admin_email'] = $row['Email'];
                header('Location: dashboard/index.php');
                exit;
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Admin not found.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
    <div class="wrapper">
        <form action="" method="post" autocomplete="off">
            <p class="form-login">Login</p>
            <?php if ($error): ?>
                <div style="color: #ffb3b3; text-align:center; margin-bottom: 18px; font-size: 15px;"> <?= htmlspecialchars($error) ?> </div>
            <?php endif; ?>
            <div class="input-box">
                <input required name="username" placeholder="Username" type="text" autocomplete="off" />
            </div>
            <div class="input-box">
                <input required name="password" placeholder="Password" type="password" autocomplete="off" />
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" name="remember" />Remember Me</label>
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</body>
</html> 