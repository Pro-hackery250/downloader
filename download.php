<?php
// download.php - Optimized download handler

// --------------------
// CONFIGURATION - UPDATED PATH
// --------------------
$yt_dlp = "C:\\xampp\\htdocs\\website\\downloader-main\\yt-dlp.exe"; // Updated to your correct path
$downloadsDir = __DIR__ . "/downloads"; // This will be C:\xampp\htdocs\website\downloader-main\downloads
$maxFileAge = 3600; // Delete files older than 1 hour

// Create downloads directory if it doesn't exist
if (!file_exists($downloadsDir)) {
    mkdir($downloadsDir, 0777, true);
}

// Clean old files
cleanOldFiles($downloadsDir, $maxFileAge);

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --------------------
// AUDIOMACK HELPERS
// --------------------
function amOAuthSign($method, $url, $params) {
    $consumerSecret = 'bd8a07e9f23fbe9d808646b730f89b8e';
    ksort($params);
    $pairs = [];
    foreach ($params as $k => $v) {
        $pairs[] = rawurlencode($k) . '=' . rawurlencode($v);
    }
    $paramString = implode('&', $pairs);
    $baseString = strtoupper($method) . '&' . rawurlencode($url) . '&' . rawurlencode($paramString);
    $signingKey = rawurlencode($consumerSecret) . '&';
    return base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));
}

function amSignedUrl($path, $params = []) {
    $apiBase = 'https://api.audiomack.com/v1';
    $url = $apiBase . $path;
    $oauth = [
        'oauth_consumer_key' => 'audiomack-web',
        'oauth_nonce' => bin2hex(random_bytes(16)),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => (string)time(),
        'oauth_version' => '1.0',
    ];
    $all = array_merge($params, $oauth);
    $sig = amOAuthSign('GET', $url, $all);
    $all['oauth_signature'] = $sig;
    return $url . '?' . http_build_query($all, '', '&', PHP_QUERY_RFC3986);
}

function parseAudiomackUrl($url) {
    $path = parse_url($url, PHP_URL_PATH);
    if (!$path) return null;
    $parts = array_values(array_filter(explode('/', $path)));
    // new style: artist/song/slug
    if (count($parts) >= 3 && $parts[1] === 'song') {
        return [$parts[0], $parts[2]];
    }
    // old style: song/artist/slug
    if (count($parts) >= 3 && $parts[0] === 'song') {
        return [$parts[1], $parts[2]];
    }
    return null;
}

function amApiGet($path, $params = []) {
    $url = amSignedUrl($path, $params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Referer: https://audiomack.com/']);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || !$resp) return null;
    $data = json_decode($resp, true);
    return is_array($data) ? $data : null;
}

function amMetadata($artist, $slug) {
    $data = amApiGet('/music/song/' . rawurlencode($artist) . '/' . rawurlencode($slug));
    if (!$data || empty($data['results'])) return null;
    $r = $data['results'];
    return [
        'id' => (string)($r['id'] ?? ''),
        'title' => $r['title'] ?? 'Unknown',
        'thumbnail' => $r['image'] ?? ($r['image_base'] ?? ''),
        'channel' => $r['artist'] ?? 'Unknown',
        'duration' => formatDuration((int)($r['duration'] ?? 0)),
        'duration_seconds' => (int)($r['duration'] ?? 0),
        'artist_slug' => $artist,
        'song_slug' => $slug,
        'url_slug' => $r['url_slug'] ?? $slug,
        'stream_only' => $r['stream_only'] ?? '',
        'source' => 'audiomack',
    ];
}

function amPlayUrl($songId, $section) {
    $data = amApiGet('/music/play/' . rawurlencode($songId), [
        'environment' => 'desktop-web',
        'hq' => 'true',
        'section' => $section,
    ]);
    if (!$data) return null;
    // data may be top-level or wrapped
    if (isset($data['signedUrl'])) return $data['signedUrl'];
    if (isset($data['results']['signedUrl'])) return $data['results']['signedUrl'];
    if (isset($data['data']['signedUrl'])) return $data['data']['signedUrl'];
    return null;
}

function amSearch($query) {
    $data = amApiGet('/livesearch', [
        'q' => $query,
        'show' => 'music',
        'sort' => 'popular',
        'limit' => '20',
        'page' => '1',
    ]);
    if (!$data || empty($data['results']) || !is_array($data['results'])) return [];
    $out = [];
    foreach ($data['results'] as $r) {
        $artistSlug = isset($r['uploader']['url_slug']) ? $r['uploader']['url_slug'] : '';
        $songSlug = $r['url_slug'] ?? '';
        $out[] = [
            'id' => (string)($r['id'] ?? ''),
            'title' => $r['title'] ?? 'Unknown',
            'artist' => $r['artist'] ?? 'Unknown',
            'thumbnail' => $r['image'] ?? '',
            'duration' => formatDuration((int)($r['duration'] ?? 0)),
            'duration_seconds' => (int)($r['duration'] ?? 0),
            'artist_slug' => $artistSlug,
            'song_slug' => $songSlug,
            'url' => 'https://audiomack.com/' . $artistSlug . '/song/' . $songSlug,
            'source' => 'audiomack',
        ];
    }
    return $out;
}

// --------------------
// HANDLE METADATA REQUEST (for video preview card)
// --------------------
if (isset($_GET['type']) && $_GET['type'] === 'metadata') {
    header('Content-Type: application/json');
    // Never let PHP warnings corrupt the JSON response.
    ini_set('display_errors', '0');
    
    $url = $_GET['url'] ?? '';
    if (empty($url)) {
        echo json_encode(['error' => 'Missing URL parameter']);
        exit;
    }

    // Audiomack metadata
    if (stripos($url, 'audiomack.com') !== false) {
        $parsed = parseAudiomackUrl($url);
        if (!$parsed) {
            echo json_encode(['error' => 'Invalid Audiomack URL']);
            exit;
        }
        $meta = amMetadata($parsed[0], $parsed[1]);
        if (!$meta) {
            echo json_encode(['error' => 'Failed to fetch track information']);
            exit;
        }
        echo json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
    
    // Extract video ID from URL
    $videoId = extractVideoId($url);
    if (!$videoId) {
        echo json_encode(['error' => 'Invalid YouTube URL']);
        exit;
    }
    
    // Build a canonical URL from the validated ID only (prevents command injection).
    $safeUrl = 'https://www.youtube.com/watch?v=' . $videoId;
    $command = escapeshellarg($yt_dlp)
        . ' --dump-json --no-playlist --no-check-certificate --no-warnings --no-update '
        . escapeshellarg($safeUrl) . ' 2>&1';
    
    $output = [];
    exec($command, $output, $status);
    
    // Warnings may be mixed into the same stream, so pick the JSON line.
    $jsonLine = null;
    foreach (array_reverse($output) as $line) {
        $line = trim($line);
        if ($line !== '' && $line[0] === '{') {
            $jsonLine = $line;
            break;
        }
    }
    
    if ($status !== 0 || !$jsonLine) {
        echo json_encode(['error' => 'Failed to fetch video information']);
        exit;
    }
    
    $data = json_decode($jsonLine, true);
    
    if (!$data) {
        echo json_encode(['error' => 'Failed to parse video data']);
        exit;
    }
    
    // Get values with defaults
    $title = isset($data['title']) ? $data['title'] : 'Unknown';
    $thumbnail = isset($data['thumbnail']) ? $data['thumbnail'] : 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg';
    $channel = isset($data['uploader']) ? $data['uploader'] : (isset($data['channel']) ? $data['channel'] : 'Unknown');
    $duration = isset($data['duration']) ? $data['duration'] : 0;
    $views = isset($data['view_count']) ? $data['view_count'] : 0;
    $uploadDate = isset($data['upload_date']) ? $data['upload_date'] : '';
    $description = isset($data['description']) ? substr($data['description'], 0, 200) : '';
    
    // Format the response
    $response = [
        'id' => $videoId,
        'title' => $title,
        'thumbnail' => $thumbnail,
        'channel' => $channel,
        'duration' => formatDuration($duration),
        'duration_seconds' => $duration,
        'views' => formatViews($views),
        'upload_date' => formatDate($uploadDate),
        'description' => $description,
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// --------------------
// HELPER FUNCTIONS FOR METADATA
// --------------------
function extractVideoId($url) {
    // Pattern for various YouTube URL formats
    $patterns = [
        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
        '/^([a-zA-Z0-9_-]{11})$/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function formatDuration($seconds) {
    if ($seconds == 0) return '0:00';
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    
    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }
    return sprintf('%d:%02d', $minutes, $secs);
}

function formatViews($views) {
    if ($views == 0) return '0 views';
    if ($views >= 1000000000) {
        return round($views / 1000000000, 1) . 'B views';
    }
    if ($views >= 1000000) {
        return round($views / 1000000, 1) . 'M views';
    }
    if ($views >= 1000) {
        return round($views / 1000, 1) . 'K views';
    }
    return $views . ' views';
}

function formatDate($date) {
    if (empty($date)) return '';

    // yt-dlp returns dates as YYYYMMDD.
    if (preg_match('/^\d{8}$/', $date)) {
        $parsed = DateTime::createFromFormat('Ymd', $date);
        return $parsed ? $parsed->format('M j, Y') : $date;
    }

    $timestamp = strtotime($date);
    return $timestamp ? date('M j, Y', $timestamp) : $date;
}


// --------------------
// HANDLE AUDIOMACK SEARCH
// --------------------
if (isset($_GET['type']) && $_GET['type'] === 'audiomack_search') {
    header('Content-Type: application/json');
    ini_set('display_errors', '0');
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode(['error' => 'Missing query']);
        exit;
    }
    $results = amSearch($q);
    echo json_encode(['results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// --------------------
// VALIDATE INPUT
// --------------------
if (empty($_GET['id']) || empty($_GET['type'])) {
    die(json_encode(['error' => 'Missing required parameters']));
}

$videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id']);
$type = $_GET['type'];
$quality = $_GET['quality'] ?? "720p";
$platform = $_GET['platform'] ?? '';

// --------------------
// AUDIOMACK DOWNLOAD
// --------------------
if ($platform === 'audiomack') {
    $artist = $_GET['artist'] ?? '';
    $slug = $_GET['slug'] ?? '';
    if (empty($artist) || empty($slug)) {
        $u = $_GET['url'] ?? '';
        $parsed = parseAudiomackUrl($u);
        if ($parsed) { $artist = $parsed[0]; $slug = $parsed[1]; }
    }
    if (empty($artist) || empty($slug)) {
        http_response_code(400);
        die(json_encode(['error' => 'Missing Audiomack track info']));
    }
    $sid = $videoId;
    if (empty($sid)) {
        $meta = amMetadata($artist, $slug);
        if (!$meta) { http_response_code(500); die(json_encode(['error'=>'Failed to fetch track info'])); }
        $sid = $meta['id'];
    }
    $section = '/' . $artist . '/song/' . $slug;
    $streamUrl = amPlayUrl($sid, $section);
    if (!$streamUrl) {
        http_response_code(500);
        die(json_encode(['error' => 'Failed to get stream URL']));
    }
    $uniqueId = uniqid("am_") . "_" . $sid;
    $outputFile = "$downloadsDir/$uniqueId.m4a";
    // download stream
    $ch = curl_init($streamUrl);
    $fp = fopen($outputFile, 'w+');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    if (!file_exists($outputFile) || filesize($outputFile) < 1024 || $httpCode !== 200) {
        @unlink($outputFile);
        http_response_code(500);
        die(json_encode(['error' => 'Download failed']));
    }
    $fileName = "audio_" . $sid . ".m4a";
    while (ob_get_level()) ob_end_clean();
    header("Content-Description: File Transfer");
    header("Content-Type: audio/mp4");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize($outputFile));
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    header("Expires: 0");
    $handle = fopen($outputFile, 'rb');
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);
    }
    unlink($outputFile);
    exit;
}

// --------------------
// FORMAT SELECTION (Optimized for speed)
// --------------------
switch ($quality) {
    case "360p":
        $format = "best[height<=360]";
        break;
    case "720p":
        $format = "best[height<=720]";
        break;
    case "1080p":
        $format = "best[height<=1080]";
        break;
    default:
        $format = "best";
}

if ($type === "audio") {
    $format = "bestaudio";
    $ext = "mp3";
} else {
    $ext = "mp4";
}

// --------------------
// GENERATE OUTPUT PATH
// --------------------
$uniqueId = uniqid("yt_") . "_" . $videoId;
$outputFile = "$downloadsDir/$uniqueId.$ext";
$url = "https://www.youtube.com/watch?v=$videoId";

// --------------------
// BUILD COMMAND (Optimized for speed)
// --------------------
if ($type === "audio") {
    // For audio: extract audio and convert to MP3
    $command = "\"$yt_dlp\" -x --audio-format mp3 --audio-quality 0 -f bestaudio -o \"$outputFile\" --no-playlist --no-check-certificate --quiet \"$url\" 2>&1";
} else {
    // For video: download best quality in requested resolution
    $command = "\"$yt_dlp\" -f \"$format\" -o \"$outputFile\" --no-playlist --no-check-certificate --quiet --merge-output-format mp4 \"$url\" 2>&1";
}

// Log the command for debugging (optional)
error_log("Executing: " . $command);

// --------------------
// EXECUTE DOWNLOAD
// --------------------
exec($command, $output, $status);

// Check if download was successful
if (!file_exists($outputFile) || filesize($outputFile) < 1024) {
    // Try alternative format if first attempt failed
    if ($type === "video") {
        $altCommand = "\"$yt_dlp\" -f best -o \"$outputFile\" --no-playlist --no-check-certificate --quiet \"$url\" 2>&1";
        exec($altCommand, $altOutput, $altStatus);
    } elseif ($type === "audio") {
        $altCommand = "\"$yt_dlp\" -f bestaudio -o \"$outputFile\" --no-playlist --no-check-certificate --quiet \"$url\" 2>&1";
        exec($altCommand, $altOutput, $altStatus);
    }
}

// Final verification
if (!file_exists($outputFile) || filesize($outputFile) < 1024) {
    http_response_code(500);
    $errorMsg = "Download failed. Command output: " . implode("\n", $output);
    error_log($errorMsg);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Download failed', 'details' => $errorMsg]);
    } else {
        die("Download failed! Please try again. Error: " . htmlspecialchars($errorMsg));
    }
    exit;
}

// --------------------
// STREAM TO BROWSER (Optimized for speed)
// --------------------
$fileName = $type === 'audio' ? "audio_$videoId.mp3" : "video_$videoId.mp4";

// Clear output buffers
while (ob_get_level()) ob_end_clean();

// Set headers for download
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($outputFile));
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");
header("Expires: 0");

// Stream file in chunks for better performance
$handle = fopen($outputFile, 'rb');
if ($handle) {
    while (!feof($handle)) {
        echo fread($handle, 8192);
        flush();
    }
    fclose($handle);
}

// --------------------
// CLEAN UP (optional - you can comment this out if you want to keep files)
// --------------------
unlink($outputFile);
exit;

// --------------------
// HELPER FUNCTIONS
// --------------------
function cleanOldFiles($dir, $maxAge) {
    if (!is_dir($dir)) return;
    
    $now = time();
    $files = glob($dir . "/*.{mp4,mp3,m4a,webm}", GLOB_BRACE);
    
    foreach ($files as $file) {
        if (is_file($file) && ($now - filemtime($file)) > $maxAge) {
            unlink($file);
        }
    }
}
?>