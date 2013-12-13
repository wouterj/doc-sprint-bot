<?php

namespace spec\Wj\DocBot\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActivateCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Wj\DocBot\Console\ActivateCommand');
    }

    function it_is_a_command()
    {
        $this->shouldHaveType('Symfony\Component\Console\Command\Command');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('docbot:activate');
    }
}
