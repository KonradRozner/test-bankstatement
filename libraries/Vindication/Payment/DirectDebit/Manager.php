<?php

namespace Vindication\Payment\DirectDebit;


class Manager
{
    /**
     * @return Manager
     */
    public static function getService()
    {
        $path = realpath(__DIR__ . '/../../../../libraries');

        spl_autoload_register(function($className) use ($path) {
            $classPath = $path . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className);
            require_once "{$classPath}.php";
        });

        return new Manager();
    }

    /**
     * @param $type
     * @throws \Exception
     * @return IO\AbstractReader
     */
    public function getReader($type)
    {
        if( !in_array(strtoupper($type), IO\AbstractReader::getAvailableReaderTypes()) ) {
            throw new \Exception('Unknown reader type!');
        }

        $className = __NAMESPACE__ . '\IO\Reader' . strtoupper($type);
        if( class_exists($className) ) {
            return new $className;
        }
    }

    /**
     * @param $type
     * @throws \Exception
     * @return IO\AbstractWriter
     */
    public function getWriter($type)
    {
        if( !in_array(strtoupper($type), IO\AbstractWriter::getAvailableWriterTypes()) ) {
            throw new \Exception('Unknown writer type!');
        }

        $className = __NAMESPACE__ . '\IO\Writer' . strtoupper($type);
        if( class_exists($className) ) {
            return new $className;
        }
    }
}