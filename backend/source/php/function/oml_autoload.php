<?php

function oml_autoload(string $class)
{
    if (strpos($class, ___NAMESPACE___) === 0) {
        $relativeClass = substr($class, strlen(___NAMESPACE___));
        $relativePath = str_replace("\\", DIRECTORY_SEPARATOR, $relativeClass);
        $relativePath = str_replace("-", "", $relativePath);
        $file_path = ___ROOT_DIRECTORY___ . $relativePath . ".php";
        if (file_exists($file_path)) {
            require $file_path;
            return;
        }
    }
}
