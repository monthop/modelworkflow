<?php namespace Smartsoftware\Modelworkflow\Factory;

use Smartsoftware\Modelworkflow\Workflow;
use Smartsoftware\Modelworkflow\Node;
use Smartsoftware\Modelworkflow\Transition;
use Smartsoftware\Modelworkflow\Interfaces\StatefulInterface;
use Smartsoftware\Modelworkflow\Task;
/**
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
abstract class  AbstractFactory {

    /**
     * Create a workflow with $definition
     * @param  mixed $definition
     * @return Workflow
     */
    public static abstract function create($definition);

    /**
     * Create a new Workflow
     *
     * @param  StatefulInterface $obj Statefull Object instance
     * @return Workflow
     */
    protected static function createWorkflow(StatefulInterface $obj=null)
    {
        return new Workflow($obj);
    }

    /**
     * Create a transition
     *
     * @param  mixed   $from
     * @param  mixed   $to
     * @param  Closure $callback optional
     * @return Transition
     */
    protected static function createTransition($from, $to, $callback = null)
    {
        return new Transition($from, $to, $callback);
    }

    /**
     * Create a Node
     *
     * @param  mixed   $id    Node id
     * @param  string  $label Label
     * @param  integer $type  Node type (optional)
     * @return Node
     */
    protected static function createNode($id, $label, $type=Node::TYPE_NORMAL)
    {
        return new Node($id, $label, $type);
    }

    /**
     * Create a Task
     *
     * @param  mixed $from
     * @param  mixed $to
     * @param  Closure $callback
     * @return Task
     */
    protected static function createTask($from, $to, $callback)
    {
        return new Task($from, $to, $callback);
    }
}