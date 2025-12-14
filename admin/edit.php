<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$success = '';
$error = '';

// Ambil data lokasi
$stmt = $conn->prepare("SELECT * FROM lokasi_indomaret WHERE id = ?");
$stmt->execute([$id]);
$location = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$location) {
    header('Location: dashboard.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_toko = trim($_POST['nama_toko']);
    $alamat = trim($_POST['alamat']);
    $daerah = trim($_POST['daerah']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    
    // Validasi input
    if(empty($nama_toko) || empty($alamat) || empty($daerah) || empty($latitude) || empty($longitude)) {
        $error = 'Semua field harus diisi!';
    } elseif(!is_numeric($latitude) || !is_numeric($longitude)) {
        $error = 'Latitude dan Longitude harus berupa angka!';
    } elseif($latitude < -90 || $latitude > 90) {
        $error = 'Latitude harus antara -90 sampai 90!';
    } elseif($longitude < -180 || $longitude > 180) {
        $error = 'Longitude harus antara -180 sampai 180!';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE lokasi_indomaret SET nama_toko=?, alamat=?, daerah=?, latitude=?, longitude=? WHERE id=?");
            $stmt->execute([$nama_toko, $alamat, $daerah, $latitude, $longitude, $id]);
            $success = 'Data berhasil diupdate! <a href="dashboard.php" style="color: #155724; font-weight: 600;">Lihat daftar</a>';
            
            // Refresh data
            $stmt = $conn->prepare("SELECT * FROM lokasi_indomaret WHERE id = ?");
            $stmt->execute([$id]);
            $location = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $error = 'Gagal mengupdate data: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lokasi - Admin SIG</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; }
        
        .navbar {
            background: linear-gradient(135deg, #E73127 0%, #C41E3A 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 { font-size: 24px; }
        .navbar a { 
            color: white; 
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .navbar a:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        label i {
            color: #E73127;
            margin-right: 5px;
        }
        
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #E73127;
            box-shadow: 0 0 0 3px rgba(231,49,39,0.1);
        }
        
        textarea { min-height: 100px; resize: vertical; }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #E73127 0%, #C41E3A 100%);
            color: white; 
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231,49,39,0.4);
        }
        
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        
        #map {
            width: 100%;
            height: 450px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #e0e0e0;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .alert-success { 
            background: #d4edda; 
            color: #155724; 
            border-left: 4px solid #28a745;
        }
        
        .alert-error { 
            background: #f8d7da; 
            color: #721c24; 
            border-left: 4px solid #dc3545;
        }
        
        .instruction {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
        }
        
        .instruction i {
            color: #856404;
            margin-right: 8px;
        }
        
        .instruction strong {
            color: #856404;
        }
        
        .instruction ul {
            margin: 10px 0 0 25px;
            color: #856404;
        }
        
        .instruction ul li {
            margin: 5px 0;
        }
        
        .required {
            color: #dc3545;
        }
        
        .coordinate-helper {
            background: #e7f3ff;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 13px;
            border-left: 4px solid #2196F3;
        }
        
        .coordinate-helper a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 600;
        }
        
        .coordinate-helper a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1><i class="fas fa-edit"></i> Edit Lokasi Indomaret</h1>
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    
    <div class="container">
        <div class="card">
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <div class="instruction">
                <i class="fas fa-info-circle"></i>
                <strong>Cara mengubah koordinat:</strong>
                <ul>
                    <li><strong>Opsi 1:</strong> Klik pada peta di bawah untuk memindahkan lokasi marker</li>
                    <li><strong>Opsi 2:</strong> Edit manual koordinat dari Google Maps</li>
                </ul>
            </div>
            
            <div id="map"></div>
            
            <form method="POST" class="form-section">
                <div class="form-group">
                    <label><i class="fas fa-store"></i> Nama Toko <span class="required">*</span></label>
                    <input type="text" 
                           name="nama_toko" 
                           required 
                           value="<?php echo htmlspecialchars($location['nama_toko']); ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marked-alt"></i> Alamat Lengkap <span class="required">*</span></label>
                    <textarea name="alamat" required><?php echo htmlspecialchars($location['alamat']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map"></i> Daerah <span class="required">*</span></label>
                    <input type="text" 
                           name="daerah" 
                           required 
                           value="<?php echo htmlspecialchars($location['daerah']); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-crosshairs"></i> Latitude <span class="required">*</span></label>
                        <input type="text" 
                               id="latitude" 
                               name="latitude" 
                               required
                               placeholder="Contoh: -6.5894"
                               value="<?php echo $location['latitude']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-crosshairs"></i> Longitude <span class="required">*</span></label>
                        <input type="text" 
                               id="longitude" 
                               name="longitude" 
                               required
                               placeholder="Contoh: 110.6669"
                               value="<?php echo $location['longitude']; ?>">
                    </div>
                </div>
                
                <div class="coordinate-helper">
                    <i class="fas fa-lightbulb"></i> 
                    <strong>Tips:</strong> Klik peta untuk update koordinat otomatis, atau edit manual di field Latitude & Longitude.
                </div>
                
                <div style="margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Data
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Koordinat awal dari database
        var initialLat = <?php echo $location['latitude']; ?>;
        var initialLng = <?php echo $location['longitude']; ?>;
        
        // Inisialisasi peta
        var map = L.map('map').setView([initialLat, initialLng], 15);
        
        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Custom Divicon dengan logo Indomaret
        var indomaretIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background: white; border: 3px solid #E73127; border-radius: 50% 50% 50% 0; box-shadow: 0 3px 10px rgba(0,0,0,0.3); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; transform: rotate(-45deg); padding: 5px;'><img src='../assets/indomaret.png' style='transform: rotate(45deg); width: 100%; height: 100%; object-fit: contain;' alt='Indomaret'></div>",
            iconSize: [50, 50],
            iconAnchor: [17, 50],
            popupAnchor: [0, -50]
        });
        
        // Tambahkan marker awal
        var marker = L.marker([initialLat, initialLng], {icon: indomaretIcon})
            .addTo(map)
            .bindPopup('<strong><?php echo htmlspecialchars($location['nama_toko']); ?></strong><br>Klik peta untuk memindahkan marker');
        
        // Event click pada peta untuk memindahkan marker
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            
            // Update posisi marker
            marker.setLatLng([lat, lng]);
            marker.openPopup();
            
            // Update input koordinat
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        });
        
        // Jika user mengetik koordinat manual, update marker di peta
        function updateMarkerFromInput() {
            var lat = parseFloat(document.getElementById('latitude').value);
            var lng = parseFloat(document.getElementById('longitude').value);
            
            if(!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                // Update posisi marker
                marker.setLatLng([lat, lng]);
                
                // Pindahkan peta ke lokasi marker
                map.setView([lat, lng], 16);
                
                marker.bindPopup('<strong>Lokasi diupdate</strong><br>Lat: ' + lat.toFixed(8) + '<br>Lng: ' + lng.toFixed(8)).openPopup();
            }
        }
        
        // Event listener untuk input manual
        document.getElementById('latitude').addEventListener('change', updateMarkerFromInput);
        document.getElementById('longitude').addEventListener('change', updateMarkerFromInput);
        document.getElementById('latitude').addEventListener('blur', updateMarkerFromInput);
        document.getElementById('longitude').addEventListener('blur', updateMarkerFromInput);
    </script>
</body>
</html>
