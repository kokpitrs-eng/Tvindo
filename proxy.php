<?php

$channels = [
    "rcti"  => "https://embed.rctiplus.com/live/rcti/rctiplus",
    "mnctv" => "https://embed.rctiplus.com/live/mnctv/mnctv",
    "gtv"   => "https://embed.rctiplus.com/live/gtv/gtv",
    "inews" => "https://embed.rctiplus.com/live/inews/inewsid"
];

$ch = strtolower($_GET['ch'] ?? 'inews');

if (!isset($channels[$ch])) {
    http_response_code(404);
    exit("Channel tidak ditemukan");
}

$curl = curl_init($channels[$ch]);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => "Mozilla/5.0"
]);

$html = curl_exec($curl);
curl_close($curl);

if (!$html) {
    exit("Gagal mengambil halaman embed.");
}

preg_match_all("/atob\('([^']+)'\)/", $html, $match);

$url = "";

foreach ($match[1] as $b64) {
    $decode = base64_decode($b64);
    if (strpos($decode, ".m3u8") !== false) {
        $url = $decode;
        break;
    }
}

if ($url == "") {
    exit("URL stream tidak ditemukan.");
}

header("Location: ".$url, true, 302);
exit;