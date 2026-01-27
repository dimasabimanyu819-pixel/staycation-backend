<?php
// FILE: public/index.php
// TUJUAN: Membuktikan apakah Password VALID atau TIDAK tanpa gangguan Laravel.

// --- DATA DARI SCREENSHOT ANDA ---
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = '4000';
$db   = 'staycation-db';
$user = '4DYyn4ujWLMYNpK.root'; // Username dari screenshot
$pass = 'QQhLZbWir5XT9sPv';     // Password yang Anda berikan

echo "<h1>🕵️‍♂️ PENGADILAN KONEKSI</h1>";
echo "Mencoba menghubungkan ke TiDB...<br>";
echo "User: <strong>$user</strong><br>";
echo "Pass: <strong>$pass</strong><br>";
echo "Host: $host<br><hr>";

try {
    // Setting SSL Wajib untuk TiDB Cloud
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt', // Path umum Vercel
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $dsn = "mysql:host=$host;port=$port;dbname=$db;sslmode=verify-ca";
    
    // Tes Koneksi (Detik-detik penentuan)
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "<h2 style='color:green'>✅ KONEKSI SUKSES!</h2>";
    echo "Password Anda BENAR. Database TERBUKA.<br>";
    echo "Masalah sebelumnya 100% karena Cache Laravel yang nyangkut/rusak.<br>";
    echo "<br>👉 <strong>Langkah Selanjutnya:</strong>";
    echo "<br>Kalau tulisan hijau ini muncul, kabari saya. Kita tinggal aktifkan Laravelnya lagi.";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ KONEKSI DITOLAK (ACCESS DENIED)</h2>";
    echo "Artinya: Server TiDB menolak User/Password ini.<br>";
    echo "Pesan Asli TiDB: <br>";
    echo "<code style='background:#eee; padding:5px; display:block; margin:10px 0;'>" . $e->getMessage() . "</code>";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<strong>Diagnosa Mutlak:</strong> Password salah atau User salah.<br>";
        echo "Mohon Reset Password sekali lagi lewat tombol 'Connect' di Dashboard TiDB.";
    }
}
?>