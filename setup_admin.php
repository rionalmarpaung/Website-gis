<?php
require_once 'config.php';

// Set username dan password yang diinginkan
$username = 'rional123';
$password = 'rional123';
$nama = 'Rional Admin';

// Generate hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Hapus semua admin lama
    $conn->exec("DELETE FROM admin");
    
    // Insert admin baru
    $stmt = $conn->prepare("INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password_hash, $nama]);
    
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Setup Admin Berhasil</title>
        <style>
            body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; }
            .success-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; max-width: 500px; }
            h1 { color: #28a745; margin-bottom: 20px; }
            .info { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left; border-left: 4px solid #28a745; }
            .info strong { display: block; margin-bottom: 10px; color: #155724; }
            code { background: #f8f9fa; padding: 3px 8px; border-radius: 4px; color: #667eea; font-weight: 600; }
            .btn { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; transition: all 0.3s; }
            .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102,126,234,0.4); }
            .warning { background: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 20px; font-size: 13px; color: #856404; border-left: 4px solid #ffc107; }
        </style>
    </head>
    <body>
        <div class='success-box'>
            <h1>✅ Setup Admin Berhasil!</h1>
            <div class='info'>
                <strong>Login Credentials:</strong>
                Username: <code>$username</code><br>
                Password: <code>$password</code>
            </div>
            <p>Admin berhasil dibuat dengan password yang sudah di-hash dengan benar.</p>
            <a href='admin/login.php' class='btn'>🔐 Login Sekarang</a>
            <div class='warning'>
                <strong>⚠️ Penting:</strong> Hapus file <code>setup_admin.php</code> setelah login berhasil untuk keamanan!
            </div>
        </div>
    </body>
    </html>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
