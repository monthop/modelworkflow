<?php namespace Smartsoftware\Modelworkflow;

/**
 * Transitions
 *
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
class Transition {
    /**
     * Id from source node
     * @var mixed
     */
    public $from;
    /**
     * Id destination node
     * @var mixed
     */
    public $to;

    /**
     * @var Closure
     */
    private $callback;

    /**
     * execute transition
     */
    public function execute()
    {
        if ($this->callback) {
            $c = $this->callback;
            $c();
        }
    }

    public function __construct($from, $to, $callback = null)
    {
        $this->from     = $from;
        $this->to       = $to;
        $this->callback = $callback;
    }
}