<?php
namespace myLibrary\php\operations;


class Cryption {

    public function encryption($phone, $verify_code): string{
        $token = ($phone."-".$verify_code);
        return base64_encode($token);
    }

    public function decryption($data): array{
        $data = base64_decode($data);
        $array = explode("-",$data);
        $token = [];
        //$token["array"] = $array;
        //$token["data"] = $data;

        if(strlen($array[0]) == 10 && strlen($array[1]) == 4){
            $token["status"] = true;
            $token["phone"] = $array[0];
            $token["verify_code"] = $array[1];
        }else {
            $token["status"] = false;
        }
        return $token;
    }
}


?>