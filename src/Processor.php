<?php

namespace Bitov8\LuaParameterHandler;

use Composer\IO\IOInterface;

class Processor
{
    private $io;

    /**
     * Processor constructor.
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    public function processFile(array $config)
    {
        $config = $this->processConfig($config);
        $realFile = $config['file'];
        $exists = is_file($realFile);

        $luaParser = new LuaConfigParser();

        $action = $exists ? 'Updating' : 'Creating';
        $this->io->write(sprintf('<info>%s the "%s" file</info>', $action, $realFile));

        // Find the expected params
        $expectedValues = $luaParser->parse(file_get_contents($config['dist-file']));
        $expectedParams = $expectedValues;

        $actualValues = [];
        if ($exists) {
            $existingValues = $luaParser->parse(file_get_contents($realFile));
            if ($existingValues === null) {
                $existingValues = [];
            }
            if (!is_array($existingValues)) {
                throw new \InvalidArgumentException(sprintf('The existing "%s" file does not contain an array', $realFile));
            }

            $actualValues = $existingValues;
        }
        
        $actualValues = $this->processParams($config, $expectedParams, $actualValues);
        
        if (!is_dir($dir = dirname($realFile))) {
            mkdir($dir, 0755, true);
        }

        
        file_put_contents($realFile, "# This file is auto-generated during the composer install\n" . $this->dumpToString($actualValues));
    }

    /**
     * @param array $config
     * @return array
     */
    private function processConfig(array $config)
    {
        if (empty($config['file'])) {
            throw new \InvalidArgumentException('The extra.8bit-lua-parameters.file setting is required to use this script handler.');
        }

        if (empty($config['dist-file'])) {
            $config['dist-file'] = $config['file'] . '.dist';
        }

        if (!is_file($config['dist-file'])) {
            throw new \InvalidArgumentException(sprintf('The dist file "%s" does not exist. Check your dist-file config or create it.', $config['dist-file']));
        }


        return $config;
    }

    /**
     * @param array $config
     * @param array $expectedParams
     * @param array $actualParams
     * @return array
     */
    private function processParams(array $config, array $expectedParams, array $actualParams)
    {
        return $this->getParams($expectedParams, $actualParams);
    }


    /**
     * @param array $expectedParams
     * @param array $actualParams
     * @return array
     */
    private function getParams(array $expectedParams, array $actualParams)
    {
        // Simply use the expectedParams value as default for the missing params.
        if (!$this->io->isInteractive()) {
            return array_replace($expectedParams, $actualParams);
        }

        $isStarted = false;

        foreach ($expectedParams as $key => $message) {
            if (array_key_exists($key, $actualParams)) {
                continue;
            }

            if (!$isStarted) {
                $isStarted = true;
                $this->io->write('<comment>Some parameters are missing. Please provide them.</comment>');
            }

            $default = $message;
            $value = $this->io->ask(sprintf('<question>%s</question> (<comment>%s</comment>): ', $key, $default), $default);

            $actualParams[$key] = $value;
        }

        return $actualParams;
    }

    /**
     * @param $actualValues
     *
     * @return string
     */
    private function dumpToString($actualValues)
    {
        $str = '';

        foreach ($actualValues as $key => $val) {
            $str .= 'set '.$key.'   "'.$val.'";'."\n";
        }

        return $str;
    }


}
