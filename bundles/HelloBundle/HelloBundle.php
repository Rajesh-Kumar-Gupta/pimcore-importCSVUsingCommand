<?php

namespace HelloBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class HelloBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/hello/js/pimcore/startup.js'
        ];
    }
}