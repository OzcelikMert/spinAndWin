<?php
namespace myLibrary\php\postServices\sms;
use SoapClient;

class NetGSM{
    private $userName;
    private $password;

    function __construct($userName, $password){
        $this->userName = $userName;
        $this->password = $password;
    }

    function send($header, $message, $phone) {
        try {
            $client = new SoapClient("http://soap.netgsm.com.tr:8080/Sms_webservis/SMS?wsdl");
            $Result = $client -> smsGonder1NV2(array(
                'username'=> $this->userName,
                'password' => $this->password,
                'header' => $header,
                'msg' => $message,
                'gsm' => $phone,
                'filter' => '',
                'startdate'  => '',
                'stopdate'  => '',
                'encoding' => 'utf8'
            ));
        } catch (Exception $exc){}
    }
}

?>
