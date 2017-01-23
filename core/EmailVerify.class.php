<?php
class EmailVerify {
    public function __construct(){

    }

    public function verify_domain($address_to_verify){
        // an optional sender
        $record = 'MX';
        list($user, $domain) = explode('@', $address_to_verify);
        return checkdnsrr($domain, $record);
    }

    public function verify_formatting($address_to_verify){
        if(strstr($address_to_verify, "@") == FALSE){
            return false;
        }else{
            list($user, $domain) = explode('@', $address_to_verify);

            if(strstr($domain, '.') == FALSE){
                return false;
            }else{
                return true;
            }
        }
    }
}
?>