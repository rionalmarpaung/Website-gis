<?php
// File: generate_hash.php
$username = 'rional123';
$password = 'rional123';
$nama = 'Rional Admin';

$hash = password_hash($password, PASSWORD_DEFAULT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Password Hash</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f6fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #667eea; }
        .info { background: #e7f3ff; padding: 15px; margin: 20px 0; border-left: 4px solid #2196F3; }
        textarea { width: 100%; padding: 15px; font-family: monospace; font-size: 13px; border: 2px solid #ddd; border-radius: 5px; }
        .btn { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Password Hash Generator</h2>
        
        <div class="info">
            <strong>Informasi Login Baru:</strong><br>
            Username: <code><?php echo $username; ?></code><br>
            Password: <code><?php echo $password; ?></code><br>
            Nama: <code><?php echo $nama; ?></code>
        </div>
        
        <h3>Langkah 1: Copy Query SQL di bawah ini</h3>
        <textarea rows="8" id="sqlQuery">-- Hapus data admin lama dan insert admin baru
DELETE FROM admin;
INSERT INTO admin (username, password, nama) VALUES ('<?php echo $username; ?>', '<?php echo $hash; ?>', '<?php echo $nama; ?>');</textarea>
        
        <button class="btn" onclick="copyQuery()">📋 Copy Query</button>
        
        <h3>Langkah 2: Jalankan di phpMyAdmin</h3>
        <ol>
            <li>Buka <a href="http://localhost/phpmyadmin/" target="_blank">phpMyAdmin</a></li>
            <li>Pilih database <code>gis_indomaret</code></li>
            <li>Klik tab <strong>SQL</strong></li>
            <li>Paste query yang sudah dicopy</li>
            <li>Klik <strong>Go/Kirim</strong></li>
        </ol>
        
        <h3>Langkah 3: Login dengan kredensial baru</h3>
        <p>Setelah query berhasil dijalankan, login dengan:</p>
        <ul>
            <li>Username: <strong><?php echo $username; ?></strong></li>
            <li>Password: <strong><?php echo $password; ?></strong></li>
        </ul>
        
        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>⚠️ Penting:</strong> Hapus file ini setelah selesai untuk keamanan!
        </div>
    </div>
    
    <script>
        function copyQuery() {
            var textarea = document.getElementById('sqlQuery');
            textarea.select();
            document.execCommand('copy');
            alert('Query berhasil dicopy! Sekarang paste di phpMyAdmin.');
        }
    </script>
</body>
</html>
