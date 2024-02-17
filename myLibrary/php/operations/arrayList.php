<?php
namespace myLibrary\php\operations;

class arrayList {
    /**
     * Finds the desired value index.<br>
     * Returns -1 if it cannot find the desired value.
     * @param array $array
     * @param $search_value
     * @return int
     */
    public static function indexOf(array $array, $search_value){
        for ($i=0; $i < count($array) ; $i++) {
            if($array[$i] == $search_value){
                return $i;
            }
        }

        return -1;
    }

    /**
     * Finds the desired value key.<br>
     * @param array $array
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public static function find(array $array, mixed $value, string $key = ""){
        $key = array_search($value, array_column($array, $key));
        return ($key !== false) ? $array[$key] : null;
    }

    /**
     * Variable value (string) convert to array column name.
     * @param string $key_name
     * @return string
     */
    public static function convertArrayKey(string $key_name){
        $key_name = utf8_encode(strtolower(Variable::clear($key_name)));
        return $key_name;
    }
}