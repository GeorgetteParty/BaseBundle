<?php

namespace GeorgetteParty\BaseBundle\Utils;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ClassGuesser
 * Find some info from a class full path
 * @package GeorgetteParty\BaseBundle\Utils
 */
class ClassGuesser
{
    /**
     * @var
     */
    protected $namespace;

    /**
     * @var
     */
    protected $bundle;

    /**
     * @var
     */
    protected $directory;

    /**
     * @var
     */
    protected $class;

    /**
     * @var string
     */
    // TODO test pattern
    protected $classPattern = '/([a-z]*)\\\\([a-z]*)\\\\([a-z]*)\\\\([a-z]*)/i';
    //protected $classPattern = '\\([a-z]*)';

    /**
     * @param $mixed
     * @throws \Symfony\Component\Config\Definition\Exception\Exception
     */
    public function __construct($mixed)
    {
        if (!$mixed) {
            throw new Exception('Unable to guess class on an empty object.');
        }
        $matches = array();
        $className = is_object($mixed) ? get_class($mixed) : $mixed;
        preg_match($this->classPattern, $className, $matches);

        $this->namespace = $matches[1];
        $this->bundle = $matches[2];
        $this->directory = $matches[3];
        $this->class = $matches[4];
    }

    /**
     * Return class namespace
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Return class bundle name
     * @param array $excludes
     * @return string
     */
    public function getBundle($excludes = array())
    {
        $bundle = $this->bundle;

        if (count($excludes)) {
            $bundle = str_replace($excludes, '', $bundle);
        }
        return $bundle;
    }

    /**
     * Return class parent directory name
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Return class name
     * @param array $excludes
     * @return string
     */
    public function getClass($excludes = array())
    {
        $class = $this->class;

        if (count($excludes)) {
            $class = str_replace($excludes, '', $class);
        }
        return $class;
    }
}