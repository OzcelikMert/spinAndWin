<?php
namespace myLibrary\php\post_services;

class XMLPost {
    public static function send(string $url, $xml, bool $ssl_verify_peer = false) : string {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        // For xml, change the content-type.
        curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // ask for results to be returned

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify_peer);

        // Send to remote and return data to caller.
        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}