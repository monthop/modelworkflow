Model Workflow for Laravel
==========================
This package provide a workflow for laravel models, like a FSM for model states (defined by a model attribute)

Nodes
-----

A node represent a model state:

```php
<?php
$n1 = new Node(1, 'draft', Node::TYPE_INITIAL); // id, label, type
$n2 = new Node(1, 'aproved', Node::TYPE_NORMAL); // id, label, type
```

Transitions
-----------

A transition represent the change of one state to an other.
```php
<?php
$t1 = new Transition(1,2, function(){echo 'Transition 1-2'.PHP_EOL;}); //transition with a callback
$t2 = new Transition(1,3); //from, to ids
```

Tasks
-----------

A task run after a transition is successfuly completed
```php
<?php
// a task that run after a transition from node 1 to 2 is completed
$task1 = new Task('1','2', function($f,$t) {
    echo "$f -> $t".PHP_EOL;
});

// a task that run after a transition from node 1 to ANY! other node is completed
$task1 = new Task('1','*', function($f,$t) {
    echo "$f -> $t".PHP_EOL;
});
```

Workflow
--------

```php
<?php

use Smartsoftware\Modelworkflow\Interfaces\StatefulInterface;
use Smartsoftware\Modelworkflow\Node;
use Smartsoftware\Modelworkflow\Transition;
use Smartsoftware\Modelworkflow\Workflow;
use Smartsoftware\Modelworkflow\Task;

class Model implements StatefulInterface {
    private $state;
    public function setObjState($new_state){
        $this->state = $new_state;
    }
    public function getObjState() {
        return $this->state;
    }
}

$o = new Model;

$n1 = new Node(1, 'initial', Node::TYPE_INITIAL); // id, label, type
$n2 = new Node(2, 'state1', Node::TYPE_NORMAL);
$n3 = new Node(3, 'state2', Node::TYPE_NORMAL);
$n4 = new Node(4, 'final', Node::TYPE_FINAL);

$t1 = new Transition(1,2, function(){echo 'Transition 1-2'.PHP_EOL;}); //transition with a callback
$t2 = new Transition(1,3); //from, to ids
$t3 = new Transition(3,4);

// a task that run after a transition from node 1 to 2 is completed
$task1 = new Task('1','2', function($f,$t) {
    echo "$f -> $t".PHP_EOL;
});

$w = new Workflow($o);
$w->addNode($n1)
  ->addNode($n2)
  ->addNode($n3)
  ->addNode($n4);

$w->addTransition($t1)
  ->addTransition($t2)
  ->addTransition($t3);

$w->addTask($task1);

$w->init();

print_r($w->getValidNextStatus());
$w->next(2);
```