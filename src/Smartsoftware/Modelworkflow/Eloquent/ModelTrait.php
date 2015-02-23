<?php namespace Smartsoftware\Modelworkflow\Eloquent;

use Smartsoftware\Modelworkflow\Factory\ArrayFactory;
use Smartsoftware\Modelworkflow\InvalidStatusException;

use Illuminate\Support\Facades\Lang;


trait ModelTrait {

    private static $workflows_instances = array();

    public static $current_attribute;

    public static function bootModelTrait()
    {
        // instance workflows
        foreach (static::$workflows as $attr => $definition) {
            self::$workflows_instances[$attr] = ArrayFactory::create($definition);
        }

        static::creating(function($m){
            // init all model workflows
            foreach (array_keys(self::$workflows) as $attribute) {
                $m->getWorkflow($attribute, false);
            }
        });

        static::updating(function($m){
            foreach (array_keys(self::$workflows) as $attribute) {
                // get original value
                $o = $m->getOriginal($attribute);
                // get new value
                $n = $m->getModelStatus($attribute);
                // get workflow
                $wf = $m->getWorkflow($attribute);

                $m::$current_attribute = $attribute;
                // if value change execute workflow transition
                try {
                    if ($o != $n) $wf->next($n);
                } catch (InvalidStatusException $e) {
                    $from = $wf->getNode($o);
                    $to   = $wf->getNode($n);
                    throw new InvalidStatusException(Lang::get('modelworkflow::modeltrait.invalid_status',['to' => $to->getLabel(), 'from' => $from->getLabel()]));
                }
            }
        });
    }

    /**
     * Return the workflow for the model $attribte
     *
     * @param  string  $attribute    Model attribute
     * @param  boolean $check_exists check model exist in database
     * @return Workflow
     */
    public function getWorkflow($attribute, $check_exists = true)
    {
        $workflow = self::$workflows_instances[$attribute];
        $workflow->setObject($this);

        if ($check_exists && !$this->exists) {
            throw new Exception('Model is not saved to database, you cant access workflow');
        }
        self::$current_attribute = $attribute;

        $workflow->init($this->getOriginal($attribute));

        return $workflow;
    }

    //WorkflowPersistence
    public function setObjState($new_status)
    {
        $a = self::$current_attribute;

        $this->$a = $new_status;
    }

    //WorkflowPersistence
    public function getObjState()
    {
        return $this->getModelStatus(self::$current_attribute);
    }

    /**
     * Return the status from model attribute
     * @return string
     */
    public function getModelStatus($attribute)
    {
        return $this->$attribute;
    }

    /**
     * Return current workflow status
     * @param string $attr attribute
     * @return string
     */
    public function getStatus($attr)
    {
        $this->getWorkflow($attr)->getStatus();
    }

    /**
     * Alias for next method
     * @param string $status
     */
    public function setStatus($status)
    {
        return $this->next($status);
    }

    public function next($status)
    {
        $a = static::$attribute;

        $this->$a = $status;
        return $this->save();
    }
}