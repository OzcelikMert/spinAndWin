<?php
namespace myLibrary\php\operations;

/**
 * Created for variable_operations::clear function
 */
abstract class ClearTypes {
    const STRING = 0x0001,
    EMAIL = 0x0002,
    INT = 0x0003,
    FLOAT = 0x0004,
    SEO_URL = 0x0005,
    BOOL = 0x0006;
}

/**
 * Created for variable_operations::encrypt function
 */
abstract class EncryptTypes {
    const MD5 = 0x0001,
    SHA256 = 0x0002,
    MD5_SHA256 = 0x0003;
}

/**
 * With this class you can control variables
 */
class Variable {
    /**
     * Clears unwanted characters from variable.
     * @param mixed $variable
     * @param int $type
     * @param bool $clear_html_tags
     * @return mixed
     */
    public static function clear(mixed $variable, int $type = ClearTypes::STRING, bool $clear_html_tags = true): mixed {
        // Check is set
        $variable = isset($variable) ? $variable : null;

        if(!is_null($variable)){
            // Check clear html tags
            $variable = ($clear_html_tags) ? strip_tags($variable) : $variable;
            // Make default clear
            $variable = trim($variable);
            $variable = htmlspecialchars($variable, ENT_QUOTES, "UTF-8");
            // Check type
            switch ($type){
                case ClearTypes::INT:
                    $variable = (int)$variable;
                    break;
                case ClearTypes::FLOAT:
                    $variable = (float)$variable;
                    break;
                case ClearTypes::EMAIL:
                    $variable = filter_var($variable, FILTER_SANITIZE_EMAIL);
                    break;
                case ClearTypes::SEO_URL:
                    $variable = self::ConvertSEOUrl($variable);
                    break;
                case ClearTypes::BOOL:
                    $variable = filter_var($variable, FILTER_VALIDATE_BOOLEAN);
                    break;
            }
        }

        return $variable;
    }

    /**
     * Clears unwanted characters from $\_POST or $\_GET value.
     * @param string $key
     * @param int $type
     * @param int $method_type
     * @param bool $clear_html_tags
     * @return mixed
     */
    public static function clearMethod(
        string $key,
        int $type = ClearTypes::STRING,
        int $method_type = MethodTypes::POST,
        bool $clear_html_tags = true
    ) : mixed{
        $variable = ($method_type == MethodTypes::POST)
            ? ((isset($_POST[$key])) ? $_POST[$key] : "")
                : ((isset($_GET[$key])) ? $_GET[$key] : "");
        return static::clear($variable, $type, $clear_html_tags);
    }

    /**
     * Clears unwanted characters from <b>(all)</b> data value.
     * @param array $data
     * @return void
     */
    public static function clearAllData(
        array &$data
    ) : void{
        foreach ($data as $key => $value){
            if(is_array($value)) { self::clearAllData($value); continue;}
            if(is_numeric($value)) $clear_type = ClearTypes::FLOAT;
            else if (is_bool($value) || filter_var($value, FILTER_VALIDATE_BOOL)) $clear_type = ClearTypes::BOOL;
            else $clear_type = ClearTypes::STRING;
            $data[$key] = static::clear($value, $clear_type);
        }
    }

    /**
     * encrypts the variable
     * @param $variable
     * @param int $type
     * @return mixed|string|null
     */
    public static function encrypt(string $variable, $type = EncryptTypes::MD5){
        $variable = static::clear($variable);

        switch($type){
            case EncryptTypes::MD5:
                $variable = hash("md5", $variable);
                break;
            case EncryptTypes::SHA256:
                $variable = hash("sha256", $variable);
                break;
            case EncryptTypes::MD5_SHA256:
                $variable = static::encrypt($variable, EncryptTypes::MD5);
                $variable = static::encrypt($variable, EncryptTypes::SHA256);
                break;
        }

        return $variable;
    }

    /**
     * Check if variable is empty (if u have more variable, enter be parameters).
     * @return bool
     */
    public static function isEmpty() : bool{
        foreach(func_get_args() as $arg) {
            if (!isset($arg) || (empty($arg) && $arg != 0) || $arg == "") return true;
            else continue;
        }

        return false;
    }

    private static function ConvertSEOUrl(string $variable) : string{
        $variable = htmlspecialchars(trim(strip_tags($variable)));
        $variable = str_replace("'", '', $variable);
        $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','(',')','/',':',',','!');
        $eng = array('s','s','i','i','i','g','g','u','u','o','o','c','c','','','-','-','','');
        $variable = str_replace($tr, $eng, $variable);
        $variable = strtolower($variable);
        $variable = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $variable);
        $variable = preg_replace('/\s+/', '-', $variable);
        $variable = preg_replace('|-+|', '-', $variable);
        $variable = preg_replace('/#/', '', $variable);
        $variable = str_replace('.', '', $variable);
        $variable = str_replace("'", '', $variable);
        $variable = trim($variable, '-');
        return $variable;
    }
}