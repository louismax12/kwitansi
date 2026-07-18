# Local Database Configuration

## 1. Connection Details
- Host: localhost
- Database Name: db_kwitansi
- Username: root (default local)
- Password: (kosongkan jika default local)

## 2. Driver Recommendation
Gunakan PDO untuk koneksi yang lebih fleksibel dan aman. Contoh skrip koneksi PHP 5.4:
```php
<?php
$host = 'localhost';
$db   = 'db_kwitansi';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
);

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
```

Dengan total **7 file `.md`** ini, Agent AI Anda akan memiliki pemahaman yang sangat spesifik tentang struktur database lokal Anda, batasan engine MyISAM, serta gaya penulisan kode PHP 5.4 yang aman. 

Apakah Anda ingin saya membantu merinci **logika pengurangan stok manual** di PHP karena ketiadaan fitur *Rollback* pada MyISAM?
