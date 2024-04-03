<?php
require './lib/.php';
error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED);

$yamlfile = "contoh.yaml";
$outputfile = "contoh.html";

try {
    if (isset($argv)) {
        if (isset($argv[1])) $yamlfile = $argv[1];
        $EOL = "\n";
    } else {
        $EOL = "<br>\n";
    }
    Log::$EOL = $EOL;

    //read yaml
    Log::info("Reading " . $yamlfile . " " . filesize($yamlfile) . " bytes");
    $x = (new Yaml())->load($yamlfile);
    if (isset($x['output'])) {
        $outputfile = $x['output'];
    } else {
        Log::error("Tidak ada 'output'");
        exit(1);
    }

    //generate
    ob_start();
    parse($x);
    $output = "\n" . ob_get_clean();
    LOG::info("Generated " . strlen($output) . " bytes");

    //replace
    $text = file_get_contents($outputfile);
    $text = replace($text, $output, '<GENERATE>', '</GENERATE>');
    file_put_contents($outputfile, $text);
    LOG::info("Written " . filesize($outputfile) . " bytes to " . $outputfile);
} catch (\Exception $e) {
    Log::error($e->getMessage());
    exit(1);
}

function parse($definition, $title = '') {
    foreach ($definition as $title => $value) {
        if (is_array($value)) {
            $x = (object) $value;
            if (isset($x->type)) {
                $filename = "types/{$x->type}.php";
                if (is_file($filename)) {
                    $x->title = $title;
                    if (!isset($x->field)) $x->field = preg_replace('/[^a-zA-Z0-9]/', '_', $title);
                    include $filename;
                }
            } else {
                echo "<div>\n";
                echo $title;
                echo "<div style='margin-left:1.2em'>\n";
                parse($value, $title);
                echo "</div>\n";
                echo "</div>\n";
            }
        }
    }
}

function replace($text, $replacement, $start, $stop) {
    $pattern = '/(' . preg_quote($start, '/') . ')(.*?)(' . preg_quote($stop, '/') . ')/s';
    return preg_replace($pattern, '$1' . $replacement . '$3', $text);
}
