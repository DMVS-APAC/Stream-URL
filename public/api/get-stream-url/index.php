<?php
require __DIR__ . '/../../../vendor/autoload.php';

// Specify the path to the directory containing your .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../..');

// Load the environment variables
$dotenv->load();

define("API_KEY", $_ENV["API_KEY"]);
define("API_SECRET", $_ENV["API_SECRET"]);
const DAILYMOTION_API_BASE_URL = "https://partner.api.dailymotion.com";

$allowedDomains = ['https://stream-url.test', 'https://stream-url.dmvs-apac.com'];

$referer = $_SERVER['HTTP_REFERER'] ?? '';

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    $allowed = false;

    foreach ($allowedDomains as $domain) {
        if (strpos($referer, $domain) === 0) {
            $allowed = true;
            break;
        }
    }

    if (!$allowed) {
        http_response_code(403);
        die('Access denied'); // Or handle the unauthorized access as needed
    }
} else {
    // Assume direct access is allowed or handle accordingly
}

$videoId = isset($_GET['videoid']) ? htmlspecialchars($_GET['videoid']) : '';

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $clientIp = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $clientIp = ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? '182.253.50.99' : $_SERVER['REMOTE_ADDR'] ;
}

$videoFormats = isset($_GET['videoformats']) ? htmlspecialchars($_GET['videoformats']) : 'stream_live_hls_url';

if ($videoId == '') {
    http_response_code(403);
    die('Video ID is not specified');
}

function auth() {
	$authUrl = DAILYMOTION_API_BASE_URL . "/oauth/v1/token";
	$ch = curl_init($authUrl);
 
    curl_setopt_array(
        $ch,
        [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                "scope"=>"read_video_streams",
                "grant_type"=> "client_credentials",
                "client_id" => API_KEY,
                "client_secret" => API_SECRET,
            ],
            CURLOPT_TIMEOUT => 2,
            CURLOPT_RETURNTRANSFER => true,
        ]
    );
 
    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true)["access_token"];
}

function fetchStreamUrls(string $videoId, string $clientIp, string $videoFormats= "stream_live_hls_url"): string {
    $ch = curl_init(
        DAILYMOTION_API_BASE_URL . "/rest/video/{$videoId}"
        . "?client_ip={$clientIp}"
        . "&fields={$videoFormats}"
    );
 
    curl_setopt_array(
        $ch,
        [
            CURLOPT_HTTPHEADER => ["authorization: Bearer " . auth()],
            CURLOPT_TIMEOUT => 2,
            CURLOPT_RETURNTRANSFER => true,
        ]
    );
 
    $response = curl_exec($ch);
 
    curl_close($ch);

    $json_array = json_decode($response);
    $json_array->client_ip = $clientIp;

    return json_encode($json_array);
}

echo fetchStreamUrls($videoId, $clientIp, $videoFormats);
