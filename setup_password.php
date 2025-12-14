<?php
// File: setup_password.php
// Untuk generate password hash yang cocok dengan sistem Anda

require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Setup Password Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        h1 { color: #667eea; margin-bottom: 30px; text-align: center; }
        .info { 
            background: #e7f3ff; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        code { 
            background: #f8f9fa; 
            padding: 3px 8px; 
            border-radius: 4px; 
            color: #e83e8c;
            font-weight: 600;
        }
        .btn { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 12px 25px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 14px;
        }
        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #ffc107;
        }
        table { width: 100%; margin: 15px 0; border-collapse: collapse; }
        table td { padding: 8px; border-bottom: 1px solid #eee; }
        table td:first-child { font-weight: 600; width: 120px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔐 Setup Password Admin</h1>";

// Proses setup password
if(isset($_GET['action']) && $_GET['action'] == 'setup') {
    
    $username = 'rional123';
    $password = 'rional123';
    $nama = 'Rional Admin';
    
    // Generate password hash
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<div class='info'>
            <strong>Informasi Setup:</strong><br>
            <table>
                <tr><td>Username</td><td><code>$username</code></td></tr>
                <tr><td>Password</td><td><code>$password</code></td></tr>
                <tr><td>Nama</td><td>$nama</td></tr>
            </table>
          </div>";
    
    try {
        // Cek apakah tabel admin ada
        $conn->query("SELECT 1 FROM admin LIMIT 1");
        
        // Hapus data lama
        $conn->exec("DELETE FROM admin");
        
        // Insert data baru dengan password hash yang fresh
        $stmt = $conn->prepare("INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $password_hash, $nama]);
        
        if($result) {
            echo "<div class='success'>
                    <h2>✅ Setup Berhasil!</h2>
                    <p style='margin: 15px 0;'>Admin berhasil dibuat dengan password hash yang benar untuk sistem Anda.</p>
                    <table>
                        <tr><td>Username</td><td><code>$username</code></td></tr>
                        <tr><td>Password</td><td><code>$password</code></td></tr>
                    </table>
                    <p style='margin-top: 15px;'>
                        <a href='admin/login.php' class='btn'>🔐 Login Sekarang</a>
                    </p>
                  </div>";
            
            // Test password verify
            $stmt = $conn->query("SELECT password FROM admin WHERE username = '$username'");
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $verify = password_verify($password, $admin['password']);
            
            echo "<div class='info'>
                    <strong>🧪 Test Verifikasi Password:</strong><br>
                    Password hash: <code style='font-size: 10px; word-break: break-all;'>" . $admin['password'] . "</code><br><br>
                    <strong>Hasil verifikasi:</strong> " . ($verify ? '✅ COCOK (Login akan berhasil)' : '❌ TIDAK COCOK (Ada masalah)') . "
                  </div>";
            
        } else {
            echo "<div class='error'>❌ Gagal insert data admin</div>";
        }
        
    } catch(PDOException $e) {
        echo "<div class='error'><strong>❌ Error:</strong><br>" . $e->getMessage() . "</div>";
    }
    
    echo "<div class='warning'>
            <strong>⚠️ PENTING:</strong> Setelah berhasil login, hapus file <code>setup_password.php</code> dari server untuk keamanan!
          </div>";
    
} else {
    // Form awal
    echo "<div class='info'>
            <strong>ℹ️ Informasi:</strong><br>
            File ini akan membuat akun admin baru dengan password hash yang cocok untuk sistem PHP Anda.
          </div>
          
          <div style='text-align: center; margin: 30px 0;'>
              <a href='?action=setup' class='btn' style='padding: 15px 40px; font-size: 16px;'>
                  🚀 Setup Admin Sekarang
              </a>
          </div>
          
          <div class='info'>
              <strong>Login credentials yang akan dibuat:</strong>
              <table style='margin-top: 10px;'>
                  <tr><td>Username</td><td><code>rional123</code></td></tr>
                  <tr><td>Password</td><td><code>rional123</code></td></tr>
              </table>
          </div>";
}

echo "</div></body></html>";
?>
