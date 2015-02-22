<?php namespace Smartsoftware\Modelworkflow;

/**
 * Tasks
 *
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
class Task {
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
    public function execute($from, $to)
    {
        if ($this->callback) {
            $c = $this->callback;
            $c($from, $to);
        }
    }

    public function __construct($from, $to, $callback = null)
    {
        $this->from     = $from;
        $this->to       = $to;
        $this->callback = $callback;
    }
}