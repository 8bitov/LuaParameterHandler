<?php

namespace Bitov8\LuaParameterHandler;

use Composer\Script\Event;

class ScriptHandler
{
    public static function buildParameters(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['8bit-lua-parameters'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.8bit-lua-parameters setting.');
        }

        $configs = $extras['8bit-lua-parameters'];

        if (!is_array($configs)) {
            throw new \InvalidArgumentException('The extra.8bit-lua-parameters setting must be an array or a configuration object.');
        }

        if (array_keys($configs) !== range(0, count($configs) - 1)) {
            $configs = array($configs);
        }

        $processor = new Processor($event->getIO());

        foreach ($configs as $config) {
            if (!is_array($config)) {
                throw new \InvalidArgumentException('The extra.8bit-lua-parameters setting must be an array of configuration objects.');
            }

            $processor->processFile($config);
        }
    }
}
