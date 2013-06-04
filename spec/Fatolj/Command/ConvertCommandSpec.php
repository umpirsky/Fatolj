<?php

namespace spec\Fatolj\Command;

use PhpSpec\ObjectBehavior;

class ConvertCommandSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('convert');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Fatolj\Command\ConvertCommand');
    }
}
