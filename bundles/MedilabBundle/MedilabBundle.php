<?php

namespace MedilabBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class MedilabBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/medilab/js/pimcore/startup.js'
        ];
    }
}