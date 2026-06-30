<?php
$content = file_get_contents('.env');
// Check if UTF-16 LE BOM exists or if there's null bytes
if (strpos($content, "\0") !== false) {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
    $content = str_replace("\xEF\xBB\xBF", '', $content); // remove BOM
}

$lines = explode("\n", $content);
$out = [];
foreach($lines as $l) {
    $l = trim($l);
    if (empty($l)) continue;
    if (strpos($l, 'USE_PUBLIC_TEMP_STORAGE') === false && strpos($l, 'U\S\E') === false) {
        $out[] = $l;
    }
}
$out[] = "USE_PUBLIC_TEMP_STORAGE=true";
file_put_contents('.env', implode("\n", $out));
