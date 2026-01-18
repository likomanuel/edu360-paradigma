<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';

$clientId = $_ENV['SNOW_CLIENT_ID'];
$clientSecret = $_ENV['SNOW_CLIENT_SECRET'];

echo "Client ID: $clientId\n";
echo "Client Secret: " . substr($clientSecret, 0, 5) . "...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.snov.io/v1/oauth/access_token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'grant_type' => 'client_credentials'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$responseRaw = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status (Token): $httpStatus\n";
echo "Raw Response (Token): $responseRaw\n";

$response = json_decode($responseRaw, true);

if (isset($response['access_token'])) {
    $accessToken = $response['access_token'];
    echo "Access Token received.\n";

    $email = "alfonsi.acosta@gmail.com";
    $url = "https://api.snov.io/v1/get-profile-by-email";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['email' => $email]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $profileRaw = curl_exec($ch);
    $profileStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Status (Profile): $profileStatus\n";
    echo "Raw Response (Profile): $profileRaw\n";
} else {
    echo "Failed to get access token.\n";
}
