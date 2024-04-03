<?php
require './lib/.php';

$yamlfile = "contoh.yaml";
$targetfile = "contoh.html";

try {
    if (isset($argv)) {
        //executed from command line
        if (isset($argv[1])) $yamlfile = $argv[1];
        $EOL = "\n";
    } else {
        $EOL = "<br>\n";
    }

    //read yaml
    echo $yamlfile . " " . filesize($yamlfile) . " bytes{$EOL}";
    $x = (new Yaml())->load($yamlfile);
    if (isset($x['file'])) {
        $targetfile = $x['file'];
    }

    //parse
    ob_start();
    parse($x);
    $output = "\n" . ob_get_clean();
    echo "generated " . strlen($output) . " bytes{$EOL}";

    //replace
    $text = file_get_contents($targetfile);
    $text = replace($text, $output, '<GENERATE>', '</GENERATE>');
    file_put_contents($targetfile, $text);
    echo $targetfile . " " . filesize($targetfile) . " bytes{$EOL}";
} catch (\Exception $e) {
    echo $e;
}

function parse($definition, $title = '') {
    foreach ($definition as $title => $value) {
        if (is_array($value)) {
            $x = (object) $value;
            if (isset($x->field, $x->type)) {
                $filename = "types/{$x->type}.php";
                if (is_file($filename)) {
                    $x->title = $title;
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
