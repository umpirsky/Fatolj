<?php

namespace Fatolj;

use Symfony\Component\Console\Application;

class Fatolj extends Application
{
    public function __construct()
    {
        parent::__construct('Welcome to Fåtölj', '1.0');

        $this->addCommands(array(
            new Command\ConvertCommand('convert')
        ));
    }
}
