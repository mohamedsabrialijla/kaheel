<?php

$html = file_get_contents(__DIR__.'/_kh2_projects.html')
    .file_get_contents(__DIR__.'/_kh2.html')
    .file_get_contents(__DIR__.'/_kh2_packages.html');
preg_match_all('#/__l5e/assets-v1/([a-f0-9-]+)/([^"\'?]+)#', $html, $m);
$dir = dirname(__DIR__, 2).'/public/kaheel/forma';
foreach (array_unique($m[0]) as $path) {
    $file = basename($path);
    $out = $dir.'/'.$file;
    if (is_file($out) && filesize($out) > 1000) {
        echo "skip $file\n";
        continue;
    }
    $data = file_get_contents('https://kh2.synap-tyx.com'.$path);
    file_put_contents($out, $data);
    echo $file.' '.strlen((string) $data)."\n";
}
