<?php
namespace myLibrary\php\page;

abstract class Creator extends Elements{
    protected function __construct() {
        parent::__construct();
        self::pageLaunch();
    }

    /**
     * Calling all set functions for html page.<br>
     * @return void
     */
    private function pageLaunch() : void{
        parent::definer();
        parent::pageSkeleton();
    }
}