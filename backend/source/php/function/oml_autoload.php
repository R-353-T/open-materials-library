<?php

function oml_autoload(string $class)
{
    if (strpos($class, OML_NAMESPACE) === 0) {
        $relativeClass = substr($class, strlen(OML_NAMESPACE));
        $relativePath = str_replace("\\", DIRECTORY_SEPARATOR, $relativeClass);
        $relativePath = str_replace("-", "", $relativePath);
        $file_path = OML_ROOT_DIR . $relativePath . ".php";
        if (file_exists($file_path)) {
            require $file_path;
            return;
        }
    }
}
