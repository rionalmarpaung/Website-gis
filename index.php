<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG Persebaran Indomaret - Kecamatan Tahunan, Jepara</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        header {
            background: linear-gradient(135deg, #E73127 0%, #C41E3A 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        header h1 { 
            font-size: 28px; 
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        header img.logo-header {
            height: 40px;
            background: white;
            padding: 5px 15px;
            border-radius: 5px;
        }
        
        header p { font-size: 14px; opacity: 0.95; }
        
        .container {
            display: flex;
            height: calc(100vh - 100px);
        }
        
        #map {
            flex: 1;
            height: 100%;
        }
        
        .sidebar {
            width: 380px;
            background: white;
            padding: 20px;
            overflow-y: auto;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar h2 {
            color: #E73127;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .location-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid #E73127;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .location-item:hover {
            background: #fff3cd;
            transform: translateX(-5px);
            box-shadow: 0 3px 10px rgba(231,49,39,0.2);
        }
        
        .location-item h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .location-item h3 img {
            height: 24px;
            object-fit: contain;
        }
        
        .location-item p {
            color: #666;
            font-size: 13px;
            margin: 5px 0;
            line-height: 1.5;
        }
        
        .location-item i {
            color: #E73127;
            margin-right: 5px;
        }
        
        .btn-admin {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #E73127 0%, #C41E3A 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }
        
        .btn-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(231,49,39,0.4);
        }
        
        /* Custom marker dengan logo - menggunakan divIcon */
        .custom-div-icon {
            background: transparent;
            border: none;
        }
        
        .indomaret-marker-icon {
            width: 40px;
            height: 40px;
            background: white;
            border: 3px solid #E73127;
            border-radius: 50%;
            box-shadow: 0 3px 10px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            position: relative;
        }
        
        .indomaret-marker-icon::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 10px solid #E73127;
        }
        
        .indomaret-marker-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        /* Custom popup styling */
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 3px 14px rgba(0,0,0,0.2);
        }
        
        .leaflet-popup-content {
            margin: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .popup-header {
            background: white;
            padding: 12px;
            margin: -15px -15px 10px -15px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 3px solid #E73127;
        }
        
        .popup-header img {
            height: 35px;
            object-fit: contain;
        }
        
        .popup-header h3 {
            color: #E73127;
            margin: 0;
            font-size: 16px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 10px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <header>
        <h1>
            <img src="assets/indomaret.png" alt="Indomaret" class="logo-header">
            Sistem Informasi Geografis
        </h1>
        <p>Persebaran Indomaret di Kecamatan Tahunan, Jepara</p>
    </header>
    
    <div class="container">
        <div id="map"></div>
        
        <div class="sidebar">
            <h2><i class="fas fa-list-ul"></i> Daftar Lokasi Indomaret</h2>
            <div id="location-list">
                <div class="no-data">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
    
    <a href="admin/login.php" class="btn-admin">
        <i class="fas fa-user-shield"></i> Admin Panel
    </a>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Inisialisasi peta dengan center di Kecamatan Tahunan, Jepara
        var map = L.map('map').setView([-6.5894, 110.6669], 14);
        
        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Custom Divicon dengan logo Indomaret
        function createIndomaretIcon() {
            return L.divIcon({
                className: 'custom-div-icon',
                html: `
                    <div class="indomaret-marker-icon">
                        <img src="assets/indomaret.png" alt="Indomaret">
                    </div>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 50],
                popupAnchor: [0, -50]
            });
        }
        
        // Ambil data dari database
        $.ajax({
            url: 'get_locations.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if(data.length === 0) {
                    $('#location-list').html(`
                        <div class="no-data">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada data lokasi.<br>Silakan tambahkan dari Admin Panel.</p>
                        </div>
                    `);
                    return;
                }
                
                var locationList = '';
                var bounds = [];
                
                data.forEach(function(location, index) {
                    // Tambahkan marker ke peta dengan logo Indomaret
                    var indomaretIcon = createIndomaretIcon();
                    
                    var marker = L.marker([parseFloat(location.latitude), parseFloat(location.longitude)], {
                        icon: indomaretIcon,
                        title: location.nama_toko
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div style="min-width: 280px;">
                            <div class="popup-header">
                                <img src="assets/indomaret.png" alt="Indomaret">
                                <h3>${location.nama_toko}</h3>
                            </div>
                            <div style="padding: 10px 0;">
                                <p style="margin: 10px 0;">
                                    <i class="fas fa-map-marker-alt" style="color: #E73127; width: 20px;"></i> 
                                    <strong>Alamat:</strong><br>
                                    <span style="margin-left: 25px;">${location.alamat}</span>
                                </p>
                                <p style="margin: 10px 0;">
                                    <i class="fas fa-map" style="color: #28a745; width: 20px;"></i> 
                                    <strong>Daerah:</strong> ${location.daerah}
                                </p>
                                <p style="margin: 10px 0; font-size: 12px; color: #666;">
                                    <i class="fas fa-crosshairs" style="color: #667eea; width: 20px;"></i> 
                                    <strong>Koordinat:</strong><br>
                                    <span style="margin-left: 25px;">Lat: ${location.latitude}<br>Long: ${location.longitude}</span>
                                </p>
                            </div>
                        </div>
                    `);
                    
                    bounds.push([parseFloat(location.latitude), parseFloat(location.longitude)]);
                    
                    // Tambahkan ke daftar sidebar dengan logo
                    locationList += `
                        <div class="location-item" onclick="showLocation(${location.latitude}, ${location.longitude}, '${location.nama_toko.replace(/'/g, "\\'")}')">
                            <h3>
                                <img src="assets/indomaret.png" alt="Indomaret">
                                ${location.nama_toko}
                            </h3>
                            <p><i class="fas fa-map-marker-alt"></i> ${location.alamat}</p>
                            <p><i class="fas fa-map"></i> <strong>Daerah:</strong> ${location.daerah}</p>
                        </div>
                    `;
                });
                
                // Fit map ke semua marker jika ada data
                if(bounds.length > 0) {
                    map.fitBounds(bounds, { 
                        padding: [50, 50],
                        maxZoom: 15
                    });
                }
                
                $('#location-list').html(locationList);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#location-list').html(`
                    <div class="no-data">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error memuat data.<br>Pastikan database sudah terkonfigurasi dengan benar.</p>
                    </div>
                `);
            }
        });
        
        function showLocation(lat, lng, nama) {
            map.setView([lat, lng], 17);
            
            // Trigger popup untuk marker yang diklik
            map.eachLayer(function(layer) {
                if(layer instanceof L.Marker) {
                    var markerLatLng = layer.getLatLng();
                    if(Math.abs(markerLatLng.lat - lat) < 0.0001 && Math.abs(markerLatLng.lng - lng) < 0.0001) {
                        layer.openPopup();
                    }
                }
            });
        }
    </script>
</body>
</html>
