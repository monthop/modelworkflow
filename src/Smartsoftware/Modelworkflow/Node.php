<?php namespace Smartsoftware\Modelworkflow;

/**
 * Nodes
 *
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
class Node {

    const TYPE_NORMAL  = 0;
    const TYPE_INITIAL = 1;
    const TYPE_FINAL   = 2;

    /**
     * Node type
     * @var int
     */
    private $type;

    /**
     * Node label
     * @var string
     */
    private $label;

    /**
     * Node id
     * @var mixed
     */
    private $id;

    /**
     * @param mixed $id
     * @param string $label
     */
    public function __construct($id, $label, $type)
    {
        $this->id    = $id;
        $this->label = $label;
        $this->type  = $type;
    }

    /**
     * @return int node type [TYPE_INITIAL|TYPE_NORMAL]
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string Node Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed Node Id
     */
    public function getId()
    {
        return $this->id;
    }
}