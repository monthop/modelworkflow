Model Workflow for Laravel 4
==========================
This package provide a workflow for laravel models, like a FSM for model states (defined by a model attribute)

Install
-----
This package can be installed via [composer](https://getcomposer.org/)
Add requirement to composer.json
```
"require": {
     ...
     "smartsoftware/modelworkflow":"0.1.*"
}
```
Then just add the service provider to app/config/app.php
```php
<?php
'providers' => array(
    ...
   'Smartsoftware\Modelworkflow\ModelworkflowServiceProvider'
```

Geting Started
===========
The most simple way of using this package with eloquent it's via the ModelTrait
```
<?php
use Smartsoftware\Modelworkflow\Interfaces\StatefulInterface;
use Smartsoftware\Modelworkflow\Eloquent\ModelTrait;
use Smartsoftware\Modelworkflow\Node;

class Ticket extends Eloquent implements StatefulInterface {
    use ModelTrait; // <- use the trait

     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tickets';

    protected $fillable = ['detail','date','status_id','user_id'];


    protected static $workflows;

    public static function boot()
    {
        static::$workflows = [
            'status_id' => [
                'nodes' => [
                    [1, 'pending', Node::TYPE_INITIAL],
                    [2, 'in process', Node::TYPE_NORMAL],
                    [3, 'closed', Node::TYPE_FINAL],
                ],
                'transitions' => [
                    [1,2, function(){
                        /* 
                        Some logic here when change 
                        from 'pending' to 'in process'
                        */
                    }],
                    [2,3]
                ],
                'taks' => [
                    [1,2, function($from, $to){
                       /* 
                           Some logic here when transition 
                           from 'pending' (1) to 'in process' (2) 
                           it's finished successfuly
                       */
                    }],
                    [2,'*', function($from, $to) {
                            /*
                            Run after all transitions from 'in process' (2)
                            to any other status (*).
                            */
                    }]
                ]
            ]
        ];

        parent::boot();
    }
```

Then the package track changes on the 'status' attribute
```
<?php
   
   $ticket = new Ticket;
   $ticket->detail = 'Some bug here!';
   $ticket->date  = date('Y-m-d');
   $ticket->save(); 
   // will automaticaly set status_id to initial state of 1

    $ticket = Ticket::find(1);
    $ticket->status_id = 2; // change from  1 to 2
    $ticket->save(); 
    /*
    workflow will execute the transition to 2 if it's posible
    or throw a InvalidStatusException
    */
```

Using workflow without Trait
=====================

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