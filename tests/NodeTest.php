<?php

use Smartsoftware\Modelworkflow\Node;

class NodeTest extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function getNode()
    {
        return new Node(1,'node1',Node::TYPE_NORMAL);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Smartsoftware\Modelworkflow\Node', $this->getNode());
    }

    public function testGetters()
    {
        $n = $this->getNode();

        $this->assertTrue( $n->getId()==1 && $n->getLabel()=='node1' && $n->getType() == Node::TYPE_NORMAL);
    }
}