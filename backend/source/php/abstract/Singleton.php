<?php

namespace oml\php\abstract;

trait Singleton
{
    private static $INSTANCES = array();

    public static function inject(?array $arguments = null): self|null
    {
        $class = get_called_class();

        if (!isset(self::$INSTANCES[$class])) {
            if ($arguments !== null) {
                self::$INSTANCES[$class] = new $class(...$arguments);
            } else {
                self::$INSTANCES[$class] = new $class();
            }
        }

        return self::$INSTANCES[$class];
    }
}
