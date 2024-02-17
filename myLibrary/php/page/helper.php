<?php
namespace myLibrary\php\page;

abstract class Helper {
    /**
     * Use when u want to be include other php files in web page
     * @param array $includes
     * @return string
     */
    protected function setInclude(array $includes) : string{
        ob_start();
        foreach ($includes as $include){
            include "{$include}";
        }
        $values = ob_get_clean();
        if (ob_get_contents()) ob_clean();

        return $values;
    }
}