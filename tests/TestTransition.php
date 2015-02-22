<?php

use Smartsoftware\Modelworkflow\Transition;

class NodeTransition extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testCallback()
    {
        $assert = false;

        $t = new Transition(1,2, function() use (&$assert){
            $assert = true;
        });

        $t->execute();

        $this->assertTrue($assert);
    }
}