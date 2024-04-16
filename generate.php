<?php
require 'lib/.php';
error_reporting(E_ERROR);

$yamlfile = "contoh.yaml";

try {
    if (isset($argv)) {
        if (isset($argv[1])) $yamlfile = $argv[1];
        Log::$EOL = "\n";
    } else {
        Log::$EOL = "<br>\n";
    }
    process_filename($yamlfile);
} catch (\Exception $e) {
    Log::error($e->getMessage());
    exit(1);
}

class Title {
    public $title;
    public $value;
    public function __construct($title, $value) {
        $this->title = $title;
        $this->value = $value;
        $filename = __DIR__ . "/types/title.php";
        if (is_file($filename)) {
            $x = $this;
            include $filename;
        }
    }
    public function __get($name) {
        if ($name == 'children') {
            parse($this->value);
        }
    }
}

function parse($definition, $title = '') {
    foreach ($definition as $title => $value) {
        if (is_array($value)) {
            $x = (object) $value;
            if (isset($x->type)) {
                $filename = __DIR__ . "/types/{$x->type}.php";
                if (is_file($filename)) {
                    $x->title = $title;
                    if (!isset($x->field)) $x->field = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $title));
                    include $filename;
                }
            } else {
                new Title($title, $value);
            }
        }
    }
}

function replace($text, $replacement, $start, $stop) {
    $pattern = '/(' . preg_quote($start, '/') . ')(.*?)(' . preg_quote($stop, '/') . ')/s';
    return preg_replace($pattern, '$1' . $replacement . '$3', $text);
}

function process_project($x) {
    if (!isset($x->project['name'])) {
        Log::error("`project.name` tidak ada");
        exit(1);
    }
    Log::info("Project {$x->project['name']}");
    if (!isset($x->forms)) {
        Log::error("`forms` tidak ada");
        exit(1);
    }
    if (!is_array($x->forms)) {
        Log::error("`forms` harus berupa array");
        exit(1);
    }
    foreach ($x->forms as $filename) {
        // process_form_filename($filename);
        processForm($filename);
    }
}

function process_filename($yamlfile) {
    if (!is_file($yamlfile)) {
        Log::error("File {$yamlfile} tidak ada");
        exit(1);
    }
    Log::info("Reading " . $yamlfile . " " . filesize($yamlfile) . " bytes");
    $x = (new Yaml())->load($yamlfile);

    $cwd = getcwd();
    chdir(dirname($yamlfile));
    if (isset($x['project'])) {
        process_project((object) $x);
    } else {
        // process_form((object) $x);
        processForm($yamlfile, (object) $x);
    }
    chdir($cwd);
}

function processForm($filename, $x = null) {
    $size = null;
    if (empty($x)) {
        //cek filename
        if (!is_file($filename)) {
            Log::error("File {$filename} tidak ada");
            exit(1);
        }
        $size = filesize($filename);

        //baca yaml
        $x = (object) (new Yaml())->load($filename);
    }

    //pindah directory
    $cwd = getcwd();
    chdir(dirname($filename));

    // output
    if (!isset($x->output)) {
        Log::error('"output" tidak ada');
        exit(1);
    }

    //cek file output
    if (!is_file($x->output)) {
        Log::error("File {$x->output} tidak ada");
        exit(1);
    }

    //generate
    ob_start();
    parse($x);
    $output = "\n" . ob_get_clean();
    $generated = strlen($output);

    //replace
    $text = file_get_contents($x->output);
    $text = replace($text, $output, '<GENERATE>', '</GENERATE>');
    file_put_contents($x->output, $text);
    $written = strlen($text);
    LOG::info("Form {$filename} -> generated {$generated} bytes -> written {$written} bytes");

    //kembalikan directory
    chdir($cwd);
}

// function process_form_filename($yamlfile) {
//     if (!is_file($yamlfile)) {
//         Log::error("File {$yamlfile} tidak ada");
//         exit(1);
//     }
//     Log::info("Reading " . $yamlfile . " " . filesize($yamlfile) . " bytes");
//     $x = (new Yaml())->load($yamlfile);
    
//     // Log::info("process_form_filename " . getcwd());
//     $cwd = getcwd();
//     chdir(dirname($yamlfile));
//     process_form((object) $x);
//     chdir($cwd);
// }

// function process_form($x) {
//     if (!isset($x->output)) {
//         Log::error("`output` tidak ada");
//         exit(1);
//     }
//     $outputfile = $x->output;

//     //check outputfile
//     if (!is_file($outputfile)) {
//         Log::error("File {$outputfile} tidak ada");
//         exit(1);
//     }

//     //generate
//     ob_start();
//     parse($x);
//     $output = "\n" . ob_get_clean();
//     $generated = strlen($output);
//     // LOG::info("Generated " . strlen($output) . " bytes");

//     //replace
//     $text = file_get_contents($outputfile);
//     $text = replace($text, $output, '<GENERATE>', '</GENERATE>');
//     file_put_contents($outputfile, $text);
//     $written = strlen($text);
//     // LOG::info("Written " . filesize($outputfile) . " bytes");
//     LOG::info("Generated {$generated} bytes, written {$written} bytes");
// }
