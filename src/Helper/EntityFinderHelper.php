<?php

namespace App\Helper;

use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;
use Yiisoft\Aliases\Aliases;

class EntityFinderHelper
{
    /** @var string[] */
    protected $paths = [];
    /** @var Aliases */
    private $aliases;

    public function __construct(Aliases $aliases)
    {
        $this->aliases = $aliases;
    }

    public function addPaths($paths)
    {
        $paths = (array)$paths;
        foreach ($paths as $path) {
            $this->paths[] = $path;
        }
    }

    public function getClassLocator(): ClassLocator
    {
        $list = [];
        foreach ($this->paths as $path) {
            $list[] = $this->aliases->get('@src/Entity');
        }
        $finder = (new Finder())
            ->files()
            ->in($list);

        return new ClassLocator($finder);
    }
}
