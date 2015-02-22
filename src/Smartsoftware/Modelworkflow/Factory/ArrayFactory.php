<?php namespace Smartsoftware\Modelworkflow\Factory;



/**
 * @author Martin Alejandro Santangelo <msantangelo@smartsoftware.com.ar>
 * @copyright SmartSoftware Argentina
 */
class ArrayFactory extends AbstractFactory {

    /**
     * create a complete worflow from definition
     *
     * @param  mixed $definition
     * @return Workflow
     */
    public static function create($definition)
    {
        // return value or null
        $av = function($ar, $at) {
            return (isset($ar[$at]))?$ar[$at]:null;
        };

        $obj = $av($definition, 'obj');

        // we create the workflow
        if (isset($definition['obj'])) {
            $obj = $definition['obj'];
        }

        // create workflow
        $workflow = self::createWorkflow($obj);

        // create each node
        foreach ($definition['nodes'] as $n) {

            $node = self::createNode($n[0],$n[1],$av($n, 2));

            $workflow->addNode($node);
        }

        // create transitions
        foreach ($definition['transitions'] as $t) {
            $transition = self::createTransition($t[0],$t[1],$av($t, 2));

            $workflow->addTransition($transition);
        }

        // create tasks
        $tasks = $av($definition, 'tasks');

        if ($tasks) {
            foreach ($tasks as $t) {
                $task = self::createTask($t[0],$t[1],$av($t, 2));

                $workflow->addTask($task);
            }
        }

        return $workflow;
    }
}