<?php

function parse($text)
{
    // Декодируем c-string.
    $text = preg_replace_callback('/("(\\\x[a-f0-9]{2}|[a-zA-Z]|_)+")/u', function ($matches) {
        $temp = '';
        eval("\$temp = $matches[0];");
        return "\"$temp\"";
    }, $text);

    // Переносим все переменные определяемые в глобальном массиве file_manager в глобальный массив нашего парсера для
    // их дальнейшего использования и удаляем их определения из файла.
    $text = preg_replace_callback('/\${"GLOBALS"}\["[a-z_]*"\]="[a-z_]*";/', function ($matches){
        eval("$matches[0]");
        return '';
    }, $text);

    // Разыменовываем все переменные использующие значения глобального массива подставляя вместо них соответствующие
    // значения из глобального массива парсера.
    $text = preg_replace_callback('/\${"GLOBALS"}["[a-z_]*"]/', function ($matches){
        $temp = '';
        eval("\$temp = $matches[0];");
        return '"' . $temp . '"';
    }, $text);

    // Разыменовываем переменные
    $text = preg_replace_callback('/\${"([a-z_]*)"}/', function ($matches){
        return '$'.$matches[1];
    }, $text);

    // Собираем значения переменных, используемых только для хранения названий переменных
    $variables = [];
    $text = preg_replace_callback('/\$([a-z_]*)="([a-z_]*)";/', function ($matches) use (&$variables){
        $variables[$matches[1]] = $matches[2];
        return '';
    }, $text);

    // Разыменовываем оставшиеся переменные подставляя ранее собранные значения
    $text = preg_replace_callback('/\${\$([a-z]*)}/', function ($matches) use ($variables){
        return '$' . $variables[$matches[1]];
    }, $text);

    return $text;
}

$text = file_get_contents('file_manager.php');
file_put_contents('WorkingFileManager.php', parse($text));