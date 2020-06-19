<?php
class RSA {

  public $key = '123';
  public $privkey = '123';

  public function __construct( $key ){
    $this->key = $key;
  }

  public function encrypt($data) {
    if( @openssl_private_encrypt($data, $encrypted, $this->key) ){
      $data = base64_encode($encrypted);
    }

    return $data;
  }

  public function decrypt($data) {
    if( @openssl_private_decrypt(base64_decode($data), $decrypted, $this->key) ){
      $data = $decrypted;
    }

    return $data;
  }
}
