<?php
// --------------------
// CONFIGURATION
// --------------------
$yt_dlp = "C:\\xampp\\htdocs\\over\\downloader\\yt-dlp.exe"; // path to yt-dlp.exe
$downloadsDir = __DIR__ . "/downloads"; // temp folder

if (!file_exists($downloadsDir)) mkdir($downloadsDir, 0777, true);

// --------------------
// VALIDATE INPUT
// --------------------
if (empty($_GET['id']) || empty($_GET['type'])) {
    die("Error: Missing required parameters (id or type).");
}

$videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id']);
$type = $_GET['type'];
$quality = $_GET['quality'] ?? "720p";

// --------------------
// CHOOSE FORMAT
// --------------------
switch ($quality) {
    case "360p":
        $format = "bestvideo[height<=360]+bestaudio/best[height<=360]";
        break;
    case "720p":
        $format = "bestvideo[height<=720]+bestaudio/best[height<=720]";
        break;
    case "1080p":
        $format = "bestvideo[height<=1080]+bestaudio/best[height<=1080]";
        break;
    default:
        $format = "best";
}

if ($type === "audio") {
    $format = "bestaudio";
}

// --------------------
// FILE PATHS
// --------------------
$uniqueId = uniqid("yt_");
$outputFile = "$downloadsDir/$uniqueId.mp4";

// --------------------
// DOWNLOAD & MERGE (with FFmpeg)
// --------------------
$command = "\"$yt_dlp\" -f \"$format\" --no-mtime --merge-output-format mp4 -o \"$outputFile\" https://www.youtube.com/watch?v=$videoId";
exec($command, $output, $status);

// --------------------
// VERIFY DOWNLOAD
// --------------------
if (!file_exists($outputFile) || filesize($outputFile) < 1000) {
    die("Download failed! Please check yt-dlp and ffmpeg installation.");
}

// --------------------
// FORCE DOWNLOAD TO USER PC
// --------------------
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"{$videoId}.mp4\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($outputFile));

// clear output buffer before file output
ob_clean();
flush();
readfile($outputFile);

// --------------------
// OPTIONAL CLEAN-UP
// --------------------
unlink($outputFile);
exit;
?>
https://elements.envato.com/web-templates/%5D