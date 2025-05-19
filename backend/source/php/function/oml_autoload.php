<?php

function oml_autoload(string $class)
{
    if (strpos($class, ___NAMESPACE___) === 0) {
        $relative_class = substr($class, strlen(___NAMESPACE___));
        $relative_path = str_replace("\\", DIRECTORY_SEPARATOR, $relative_class);
        $relative_path = str_replace("-", "", $relative_path);
        $file_path = ___ROOT_DIRECTORY___ . $relative_path . ".php";
        if (file_exists($file_path)) {
            require $file_path;
            return;
        }
    }
}
