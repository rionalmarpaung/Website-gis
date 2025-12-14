<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data lokasi
$stmt = $conn->query("SELECT * FROM lokasi_indomaret ORDER BY id DESC");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIG Indomaret</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 { font-size: 24px; }
        .navbar-right { display: flex; gap: 20px; align-items: center; }
        .navbar a { color: white; text-decoration: none; }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover { background: #5568d3; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-success { background: #27ae60; color: white; }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .actions { display: flex; gap: 10px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
        <div class="navbar-right">
            <span>Halo, <?php echo $_SESSION['admin_nama']; ?></span>
            <a href="../index.php" target="_blank"><i class="fas fa-eye"></i> Lihat Peta</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>Kelola Data Lokasi Indomaret</h2>
            <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Lokasi Baru</a>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Toko</th>
                        <th>Alamat</th>
                        <th>Daerah</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($locations) > 0): ?>
                        <?php $no = 1; foreach($locations as $loc): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($loc['nama_toko']); ?></td>
                            <td><?php echo htmlspecialchars($loc['alamat']); ?></td>
                            <td><?php echo htmlspecialchars($loc['daerah']); ?></td>
                            <td><?php echo $loc['latitude']; ?></td>
                            <td><?php echo $loc['longitude']; ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $loc['id']; ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="hapus.php?id=<?php echo $loc['id']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus?')" 
                                   class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Belum ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
