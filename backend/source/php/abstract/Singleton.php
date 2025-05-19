<?php

namespace oml\php\abstract;

trait Singleton
{
    private static $INSTANCES = array();

    public static function inject(?array $argument_list = null): object|null
    {
        $class = get_called_class();

        if (!isset(self::$INSTANCES[$class])) {
            if ($argument_list !== null) {
                self::$INSTANCES[$class] = new $class(...$argument_list);
            } else {
                self::$INSTANCES[$class] = new $class();
            }
        }

        return self::$INSTANCES[$class];
    }
}
