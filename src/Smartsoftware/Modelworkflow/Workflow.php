<?php namespace Smartsoftware\Modelworkflow;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

use Smartsoftware\Modelworkflow\Interfaces\StatefulInterface;
use Smartsoftware\Modelworkflow\Node;
use Smartsoftware\Modelworkflow\Transition;
use Smartsoftware\Modelworkflow\Task;

/**
 * Workflow
 *
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
class Workflow {

    /**
     * Workflow nodes
     * @var array
     */
    private $nodes = [];

    /**
     * @var Collection
     */
    private $tasks;

    /**
     * Transitions
     * @var Collection
     */
    private $transitions;

    /**
     * @var StatefulInterface
     */
    private $statefull_obj;

    /**
     * Current status
     *
     * @var mixed
     */
    private $current_status;

    /**
     * Initial status
     *
     * @var mixed
     */
    private $initial_status;

    /**
     * Constructor
     * @param string|null current status string
     */
    public function __construct(StatefulInterface $statefull_obj=null)
    {
        $this->transitions = new Collection();
        $this->tasks       = new Collection();

        $this->statefull_obj = $statefull_obj;
    }

    public function setObject(StatefulInterface $statefull_obj)
    {
        $this->statefull_obj = $statefull_obj;
    }

    /**
     * Add a node to workflow
     * @param Node $node
     */
    public function addNode(Node $node)
    {
        if ($node->getType() == Node::TYPE_INITIAL) {
            if ($this->initial_status) {
                throw new Exception('The workflow cant have more than one initial state');
            }
            $this->initial_status = $node->getId();
        }

        $this->nodes[$node->getId()] = $node;

        return $this;
    }

    /**
     * Add a transition to workflow
     *
     * @param Transition $transition
     */
    public function addTransition(Transition $transition)
    {
        if (!$this->statusExists($transition->to)) {
            throw new Exception("Transition destination $transition->to status do not exist in workflow");
        }
        if (!$this->statusExists($transition->from)) {
            throw new Exception("Transition source $transition->from status do not exist in workflow");
        }

        $this->transitions->push($transition);

        return $this;
    }


    /**
     * Add a transition tasks to workflow
     *
     * @param Task $transition
     */
    public function addTask(Task $task)
    {
        if ($task->to != '*' && !$this->statusExists($task->to)) {
            throw new Exception('Task destination status do not exist in workflow');
        }
        if ($task->from != '*' && !$this->statusExists($task->from)) {
            throw new Exception('Task source status do not exist in workflow');
        }

        $this->tasks->push($task);

        return $this;
    }

    /**
     * Init Workflow
     *
     * @param  string $status status
     * @return boolean        true on success, false on contranint for status fail
     */
    public function init($status = null)
    {
        if (!$this->statefull_obj) {
            throw new Exception('Statefull object is not defined in workflow');
        }

        if($status) {
            return $this->setStatus($status);
        } else {                      // if it's null set initial status
            return $this->setStatus($this->initial_status);
        }
    }

    public function getStatus()
    {
        return $this->current_status;
    }

    public function getValidNextStatus()
    {
        $transitions = $this->getValidTransitions();

        return $transitions->lists('to');
    }

    /**
     * Get current valid transitions
     *
     * @return array transitions
     */
    public function getValidTransitions($to = null)
    {
        $node    = $this->getNode();

        $current = $this->current_status;

        if ($to) {
            return $this->transitions->filter(function($t) use ($current, $to)
            {
                if ($t->from == $current && $t->to == $to) return true;
            })->first();
        } else {
            return $this->transitions->filter(function($t) use ($current)
            {
                if ($t->from == $current) return true;
            });

        }

    }

    /**
     * Return if a next status is valid for the current status;
     * @param  mixed  $next_status
     * @return boolean
     */
    public function isValidNextStatus($next_status)
    {
        $node = $this->getNode();

        return in_array($next_status, $node['transition']);
    }

    /**
     * set the current status of the workflow
     * @param  $status new status
     */
    private function setStatus($status)
    {
        if ($this->statusExists($status)) {

            $this->statefull_obj->setObjState($status);

            $this->current_status = $status;

            return true;
        } else {
            throw new Exception('status do not exists in the workflow');
        }
    }

    /**
     * Transition to new status
     *
     * @param  [type]   $status [description]
     * @return function         [description]
     */
    public function next($status)
    {
        $transition = $this->getValidTransitions($status);

        if (empty($transition)) {
            throw new InvalidStatusException('Invalid next Status');
        }

        $current = $this->current_status;

        // if event handler return false we cancel the transition
        $continue = Event::until('smartsoftware.workflow.beforetransition',[$current, $status, $this]);

        if ($continue !== false) {

            // execute transition callback (if set)
            $transition->execute();

            // set status
            $this->setStatus($status);

            Event::fire('smartsoftware.workflow.transition',[$current, $status, $this]);

            $this->executeTasks($current, $status);
        }
    }

    /**
     * Execute all the task to a given transition
     *
     * @param  mixed $old_status
     * @param  mixed $current_status
     */
    private function executeTasks($old_status, $current_status)
    {
        $tasks   = $this->tasks->filter(function($t) use ($old_status, $current_status)
        {
            $condition = ($t->from == $old_status || $t->from == '*') && ($t->to == $current_status || $t->to == '*');
            if ( $condition ) return true;
        });

        foreach ($tasks as $task) {
            $task->execute($old_status, $current_status);
        }
    }

    /**
     * Verify if an status exists in workflow
     * @param  mixed $status
     * @return boolean
     */
    public function statusExists($status)
    {
        return array_key_exists($status, $this->nodes);
    }

    /**
     * Get a node
     *
     * @param  mixed $status to get current node must be null
     * @return array node
     */
    public function getNode($status = null)
    {
        if (!$status) {
            return $this->nodes[$this->current_status];
        } else {
            return $this->nodes[$status];
        }
    }
}