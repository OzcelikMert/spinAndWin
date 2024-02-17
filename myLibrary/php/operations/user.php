<?php
namespace myLibrary\php\operations;

abstract class MethodTypes {
    const POST = 0x0001,
    GET = 0x0002,
    SESSION = 0x0003;
}

abstract class CheckTypes {
    const IS_SET = 0x0001,
        EMPTY = 0x0002,
        IS_NULL = 0x0003;
}

/**
 * With this class you can control users.
 */
class User {
    /**
     * Gets the user's ethernet ip.
     * @return string (HTTP_CLIENT_IP | HTTP_X_FORWARDED_FOR | HTTP_X_FORWARDED_FOR)
     */
    public static function getIPAddress() : string{
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    public static function getRealBrowser() : string {
        $kek = $_SERVER['HTTP_USER_AGENT'];
        $browser  = "Unknown Browser";
        $browser_array = array(
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Mozilla Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Google Chrome',
            '/edge/i'      => 'Microsoft Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i'    => 'Handheld Browser'
        );
        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $kek))
                $browser = $value;
        return $browser;
    }

    public static function getRealOS(): string {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform  = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 10/i'      => 'Windows 10',
            '/windows nt 6.3/i'     => 'Windows 8.1',
            '/windows nt 6.2/i'     => 'Windows 8',
            '/windows nt 6.1/i'     => 'Windows 7',
            '/windows nt 6.0/i'     => 'Windows Vista',
            '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     => 'Windows XP',
            '/windows xp/i'         => 'Windows XP',
            '/windows nt 5.0/i'     => 'Windows 2000',
            '/windows me/i'         => 'Windows ME',
            '/win98/i'              => 'Windows 98',
            '/win95/i'              => 'Windows 95',
            '/win16/i'              => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i'        => 'Mac OS 9',
            '/linux/i'              => 'Linux',
            '/ubuntu/i'             => 'Ubuntu',
            '/iphone/i'             => 'iPhone',
            '/ipod/i'               => 'iPod',
            '/ipad/i'               => 'iPad',
            '/android/i'            => 'Android Device',
            '/blackberry/i'         => 'BlackBerry',
            '/webos/i'              => 'Mobile Device'
        );

        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $os_platform = $value;

        return $os_platform;
    }

    /**
     * Checks if there is a session or not
     * @return bool
     */
    public static function checkSessionStart() : bool {
        $value = false;
        if(session_status() == PHP_SESSION_ACTIVE) {
            $value = true;
        }
        return $value;
    }

    /**
     * Checks if the incoming post value is from javascript ajax.
     * @param array $ajax_post
     * @return array
     */
    public static function checkAjaxPost(array $ajax_post) : array{
        $values =  array();
        $values["status"] = false;
        $values["post_name"] = null;

        $HTTP_REFERER = str_replace("https://","",$_SERVER['HTTP_REFERER']);
        $HTTP_REFERER = str_replace("http://","",$HTTP_REFERER);
        $HTTP_REFERER = str_replace("/","",$HTTP_REFERER);

        if(($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && (isset($_SERVER['HTTP_REFERER'])) && $HTTP_REFERER == $_SERVER['SERVER_NAME']) {

            foreach ($ajax_post as $post) {
                $values["status"] = (isset($_POST[$post])) || (isset($_GET[$post]));

                if(!$values["status"]){
                    $values["post_name"] =  $ajax_post;
                    return $values;
                }
            }

        }else {
            $values["post_name"] = "Ip is invalid";
        }

        return $values;
    }

    /**
     * Checks data directed to the page.
     * @param array $keys
     * @param int $method_type
     * @param int $check_type
     * @return bool
     */
    public static function checkSentData(
        array $keys,
        int $method_type = MethodTypes::POST,
        int $check_type = CheckTypes::IS_SET
    ) : bool{
        $method = self::checkMethod($method_type);
        foreach($keys as $key => $value){
            if(!static::checkValue($method, $value, $check_type)) return false;
        }
        return true;
    }

    /**
     * Get $GLOBALS value.
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function global(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $GLOBALS[$key] = $value;
        return $GLOBALS[$key] ?? false;
    }

    /**
     * Get $_POST value.
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function post(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $_POST[$key] = $value;
        return isset($_POST[$key]) ? $_POST[$key] : false;
    }

    /**
     * Get $_GET value
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function get(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $_GET[$key] = $value;
        return isset($_GET[$key]) ? $_GET[$key] : false;
    }

    /**
     * Get $_FILES value
     * @param string $key
     * @return mixed
     */
    public static function files(string $key) : mixed{
        return isset($_FILES[$key]) ? $_FILES[$key] : false;
    }

    /**
     * Get $_SESSION value
     * If u are defining value then it sets the value.
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function session(string $key, mixed $value = null) : mixed{
        if (!is_null($value)) $_SESSION[$key] = $value;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    private static function checkMethod(int $method_type) : array{
        $method = array();
        switch ($method_type){
            case MethodTypes::POST:
                $method = $_POST;
                break;
            case MethodTypes::GET:
                $method = $_GET;
                break;
            case MethodTypes::SESSION:
                if(!self::checkSessionStart()) session_start();
                $method = $_SESSION;
                break;
        }
        return $method;
    }

    private static function checkValue(array $method, string $key, int $check_type) : bool{
        switch ($check_type){
            case CheckTypes::IS_SET:
                if(!isset($method[$key])) return false;
                break;
            case CheckTypes::EMPTY:
                if(!static::checkValue($method, $key, CheckTypes::IS_SET) || empty($method[$key])) return false;
                break;
            case CheckTypes::IS_NULL:
                if(!static::checkValue($method, $key, CheckTypes::IS_SET) || is_null($method[$key])) return false;
                break;
        }
        return true;
    }

    /**
     * Sets the sessions you want to create.
     * @param array $sessions
     */
    public static function sessionCreator(array $sessions) : void{
        if(!self::checkSessionStart()) {
            session_start();
            session_regenerate_id();
        }
        foreach($sessions as $key => $value)
            $_SESSION[$key] = $value;
    }
}