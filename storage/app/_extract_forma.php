<?php

$home = '{{ $home }}';
$asset = "asset('kaheel/forma/%s')";

$files = [
    '_kh2.html' => 'home',
    '_kh2_packages.html' => 'furniture',
    '_kh2_process.html' => 'process',
    '_kh2_projects.html' => 'projects',
    '_kh2_about.html' => 'about',
    '_kh2_who-we-serve.html' => 'audience',
    '_kh2_contact.html' => 'contact',
];

function extractBetween(string $html, string $startNeedle, string $endNeedle): string
{
    $start = strpos($html, $startNeedle);
    $end = strrpos($html, $endNeedle);
    if ($start === false || $end === false || $end <= $start) {
        return '';
    }

    return substr($html, $start, $end - $start);
}

function rewrite(string $html): string
{
    $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
    $html = preg_replace_callback('#/__l5e/assets-v1/[^"\']+/([^"\']+)#', function ($m) {
        return "{{ asset('kaheel/forma/".$m[1]."') }}";
    }, $html);
    $map = [
        'href="/"' => 'href="{{ $home }}"',
        'href="/packages"' => 'href="{{ $home }}/packages"',
        'href="/process"' => 'href="{{ $home }}/process"',
        'href="/projects"' => 'href="{{ $home }}/projects"',
        'href="/who-we-serve"' => 'href="{{ $home }}/who-we-serve"',
        'href="/about"' => 'href="{{ $home }}/about"',
        'href="/contact"' => 'href="{{ $home }}/contact"',
        'href="/auth"' => 'href="{{ $home }}/auth"',
        'href="/order"' => 'href="{{ $home }}/order"',
    ];
    $html = strtr($html, $map);

    return $html;
}

$dir = __DIR__;
$outDir = dirname(__DIR__, 2).'/resources/views/kaheel/pages';
if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

foreach ($files as $file => $name) {
    $html = file_get_contents($dir.'/'.$file);
    $header = extractBetween($html, '<header class="sticky top-0', '</header>');
    $footer = extractBetween($html, '<footer class="bg-sidebar', '</footer>');
    $mainStart = strpos($html, '</header>');
    $mainEnd = strrpos($html, '<footer class="bg-sidebar');
    $main = $mainStart !== false && $mainEnd !== false
        ? substr($html, $mainStart + strlen('</header>'), $mainEnd - ($mainStart + strlen('</header>')))
        : '';
    $header = rewrite($header.'</header>');
    $footer = rewrite($footer.'</footer>');
    $main = rewrite($main);
    file_put_contents($outDir.'/_'.$name.'_header.html', $header);
    file_put_contents($outDir.'/_'.$name.'_main.html', $main);
    file_put_contents($outDir.'/_'.$name.'_footer.html', $footer);
    echo $name.' header '.strlen($header).' main '.strlen($main).' footer '.strlen($footer).PHP_EOL;
}
