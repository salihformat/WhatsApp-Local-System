<?php
$lines = file('.env');
$out = [];
foreach($lines as $l) {
    if (strpos($l, 'USE_PUBLIC_TEMP_STORAGE') === false && strpos($l, 'U\S\E') === false) {
        $out[] = $l;
    }
}
$out[] = "\nUSE_PUBLIC_TEMP_STORAGE=true\n";
file_put_contents('.env', implode("", $out));
