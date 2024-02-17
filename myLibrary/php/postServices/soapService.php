<?php
namespace myLibrary\php\post_services;

use SoapClient;
use SoapHeader;

class soap_service{
    function __construct(string $url){
        try {
            $this->client = new SoapClient($url, array('exceptions' => 1, 'cache_wsdl' => WSDL_CACHE_NONE));
        } catch (\SoapFault $e) {}
    }

    public SoapHeader $header;
    public SoapClient $client;

    protected function set_header(string $namespace, string $auth_name, array $auth, bool $must_understand = false) : void{
        $this->header = new SoapHeader($namespace, $auth_name, $auth, $must_understand);
        $this->client->__setSoapHeaders($this->header);
    }

    public function get_functions() : array { return $this->client->__getFunctions(); }
    public function get_types() : array { return $this->client->__getTypes(); }
    public static function xml_to_array(string $xml) : array { return json_decode(json_encode(simplexml_load_string($xml)), true); }
}

?>