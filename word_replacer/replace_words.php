<?php

if ($argc !== 2) {
    echo 'File name must be passed as first argument to call this program.' . PHP_EOL;
    return 1;
}

$fileName = $argv[1];

if (!file_exists($fileName)) {
    echo 'File "' . $fileName . '" not found' . PHP_EOL;
    return 2;
}

$text = file_get_contents($fileName);

$count = 0;
$result = preg_replace_callback('/(\w+)/u', function ($matches) use (&$count) {
    $count++;
    if ($count % 15 === 0) {
        return 'ПЯТНАДЦАТЬ';
    }
    if ($count % 5 === 0) {
        return 'ПЯТЬ';
    }
    if ($count % 3 === 0) {
        return 'ТРИ';
    }
    return $matches[0];
}, $text);

$pathInfo = pathinfo($fileName);
$newFileName = (isset($pathInfo['filename']) ? $pathInfo['filename'] : '')
    . '_with_replacement_words'
    . (isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '');

file_put_contents($newFileName, $result);
echo 'Prepared text from file ' . $fileName . '  write to "' . $newFileName . '".' . PHP_EOL;
return 0;