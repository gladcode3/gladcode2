<?php
    $dir = 'script/functions/';
    $contents = array();
    
    // get file contents
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && !is_dir($entry)) {
                $contents[explode(".", $entry)[0]] = json_decode(file_get_contents($dir . $entry), true);
            }
        }
        closedir($handle);
    }

    $str = json_encode($contents);
    file_put_contents('script/functions.json', $str);

    // get samples and explanation and put in the same file
    $dir = 'script/functions/samples/';
    $fileList = array();
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && !is_dir($entry)) {
                $filename = explode(".", $entry);
                if (!in_array($filename[0], $fileList))
                    array_push($fileList, $filename[0]);
            }
        }
        closedir($handle);
    }

    $codes = array();
    foreach($fileList as $file){
        $codes[$file] = array();
    }
    
    foreach($contents as $func => $data){
        foreach($data['sample'] as $lang => $filename){
            $file = explode(".", $filename)[0];

            if (!isset($codes[$file][$lang]))
                $codes[$file][$lang] = file_get_contents("$dir$filename");
        }
    }

    foreach($codes as $file => $lang){
        $str = json_encode($codes[$file]);
        file_put_contents("$dir$file.json", $str);
    }
?>