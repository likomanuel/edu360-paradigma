<?php
require_once 'config/modulo.php';
$modulo = new Modulo();

$key = "test_password_123";
$encryptionKey = Modulo::ENCRYPTION_KEY;

echo "Original: " . $key . PHP_EOL;

$encrypted = $modulo->encryptApiKey($key, $encryptionKey);
echo "Encrypted (base64 of binary IV + binary encrypted): " . $encrypted . PHP_EOL;

$decrypted = $modulo->decryptApiKey($encrypted, $encryptionKey);
echo "Decrypted: " . $decrypted . PHP_EOL;

if ($key === $decrypted) {
    echo "SUCCESS: Logic works!" . PHP_EOL;
} else {
    echo "FAILURE: Logic failed!" . PHP_EOL;
    var_dump($decrypted);
}
