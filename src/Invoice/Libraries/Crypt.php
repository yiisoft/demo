<?php

declare(strict_types=1);

Namespace App\Invoice\Libraries;

class Crypt 
{
    public function salt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    public function generate_password($password, $salt)
    {
        return crypt($password, '$2a$10$' . $salt);
    }

    public function check_password($hash, $password)
    {
        $new_hash = crypt($password, $hash);

        return ($hash == $new_hash);
    }

    public function encode($data)
    {

        $invoice = \Yii::$app->getModule('invoice');
        $key = $invoice->params['invoice_cryptkey'];
        
        if (preg_match("/^base64:(.*)$/", $key, $matches)) {
            $key = base64_decode($matches[1]);
        }

        $encrypted = Cryptor::Encrypt($data, $key);
        return $encrypted;

    }

    public function decode($data)
    {

        if (empty($data)) {
            return '';
        }
        
        $invoice = \Yii::$app->getModule('invoice');
        $key = $invoice->params['invoice_cryptkey'];
        if (preg_match("/^base64:(.*)$/", $key, $matches)) {
            $key = base64_decode($matches[1]);
        }

        $decrypted = Cryptor::Decrypt($data, $key);
        return $decrypted;

    }
}
