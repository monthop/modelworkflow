<?php namespace Smartsoftware\Modelworkflow\Interfaces;

interface StatefulInterface {

    public function setObjState($new_status);

    public function getObjState();
}