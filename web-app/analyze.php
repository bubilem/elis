<?php

countDir('./app/elis');
countDir('./code-list');
countDir('./www/scss');

function countDir($path)
{
    echo "<h1>$path</h1>";
    $fileCounter = [];
    $totalLines = countLines($path, $fileCounter);
    echo $totalLines . " lines in the current folder<br>";
    $actLines = $totalLines -
        (empty($fileCounter['gen']['commentedLines']) ? 0 : $fileCounter['gen']['commentedLines']) -
        (empty($fileCounter['gen']['blankLines']) ? 0 : $fileCounter['gen']['blankLines']);
    echo $actLines . " actual lines of code (not a comment or blank line)<br><br>";
    foreach ($fileCounter['gen'] as $key => $val) {
        echo ucfirst($key) . ":" . $val . "<br>";
    }
}

function countLines($dir, &$fileCounter)
{
    $allowedFileTypes = "(php|html|xml|json|ini|js|css|scss)";
    $lineCounter = 0;
    $dirHandle = opendir($dir);
    $path = realpath($dir);
    $nextLineIsComment = false;

    if ($dirHandle) {
        while (false !== ($file = readdir($dirHandle))) {
            if (is_dir($path . "/" . $file) && ($file !== '.' && $file !== '..')) {
                $lineCounter += countLines($path . "/" . $file, $fileCounter);
            } elseif ($file !== '.' && $file !== '..') {
                //Check if we have a valid file 
                $ext = findExtension($file);
                if (preg_match("/" . $allowedFileTypes . "$/i", $ext)) {
                    $realFile = realpath($path) . "/" . $file;
                    $fileArray = file($realFile);
                    //Check content of file:
                    for ($i = 0; $i < count($fileArray); $i++) {
                        if ($nextLineIsComment) {
                            add($fileCounter['gen']['commentedLines']);
                            //Look for the end of the comment block
                            if (strpos($fileArray[$i], '*/')) {
                                $nextLineIsComment = false;
                            }
                        } else {
                            //Look for a function
                            if (strpos($fileArray[$i], 'function')) {
                                add($fileCounter['gen']['functions']);
                            }
                            //Look for a commented line
                            if (strpos($fileArray[$i], '//')) {
                                add($fileCounter['gen']['commentedLines']);
                            }
                            //Look for a class
                            if (substr(trim($fileArray[$i]), 0, 5) == 'class') {
                                add($fileCounter['gen']['classes']);
                            }
                            //Look for a comment block
                            if (strpos($fileArray[$i], '/*')) {
                                $nextLineIsComment = true;
                                add($fileCounter['gen']['commentedLines']);
                                add($fileCounter['gen']['commentBlocks']);
                            }
                            //Look for a blank line
                            if (trim($fileArray[$i]) == '') {
                                add($fileCounter['gen']['blankLines']);
                            }
                        }
                    }
                    add($fileCounter['gen'][strtoupper($ext) . ' - lines'], count($fileArray));
                    $lineCounter += count($fileArray);
                }
                //Add to the files counter
                add($fileCounter['gen']['totalFiles']);
                add($fileCounter[strtolower($ext)]);
            }
        }
    } else {
        echo 'Could not enter folder';
    }
    return $lineCounter;
}

function add(&$var, $number = 1)
{
    if (empty($var)) {
        $var = $number;
    } else {
        $var += $number;
    }
}

function findExtension($filename)
{
    $exts = explode(".", strtolower($filename));
    return $exts[count($exts) - 1];
}
