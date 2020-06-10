<?php

  require('rsa.php');
  require('des.php');
  require('curl.php');

  class Worker {

    public function __construct( $key, $conf ){

      $this->key = $key;
      $this->c = $conf;
      $this->e = new DES($key, 'DES-ECB', DES::OUTPUT_BASE64);

      $this->http = new cURL("http://41.72.108.82:8097/iface/index");
      $this->http->setopt(CURLOPT_HEADER, 0);
      $this->http->setopt(CURLOPT_RETURNTRANSFER, 1);
      $this->http->setopt(CURLOPT_POST, 1);
      $this->http->setopt(CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json;Charset=utf-8',
        'Host: 211.90.56.2'
      ));

    }

    public function stepOne( $bus_data ){
      $bus_data = str_replace( '"', '\'', $bus_data );
      $content = $this->e->encrypt( $bus_data );
      $sign = base64_encode( md5($content, TRUE) );

      $this->http->setopt(CURLOPT_POSTFIELDS, json_encode(array(
        "message" => array(
          "body" => array(
            "data" => array(
              "device" => $this->c[0][0],
              "serial" => $this->c[0][1],
              "bus_id" => $this->c[0][2],
              "content" => $content,
              "sign" => $sign,
              "key" => "",
            )
          )
        )
      )));

      $this->stepOneResult = json_decode( $this->http->exec(), TRUE );
      $this->stepTwo( $this->stepOneResult['message']['body']['data']['content'] );
    }

    public function stepTwo( $message ){
      $this->stepTwoResult = json_decode( $this->e->decrypt($message), TRUE );
    }

    public function stepThree( $bus_data ){
      $bus_data = str_replace( '"', '\'', $bus_data );
      $content = $this->e->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));
      $this->r = new RSA("-----BEGIN PRIVATE KEY-----\n".wordwrap( $this->stepTwoResult['secret'], 64 )."\n-----END PRIVATE KEY-----");
      $key = $this->r->encrypt( $this->key );

      $this->http->setopt(CURLOPT_POSTFIELDS, json_encode(array(
        "message" => array(
          "body" => array(
            "data" => array(
              "device" => $this->c[1][0],
              "serial" => $this->c[1][1],
              "bus_id" => $this->c[1][2],
              "content" => $content,
              "sign" => $sign,
              "key" => $key,
            )
          )
        )
      )));

      $this->stepThreeResult = json_decode( $this->http->exec(), TRUE );

      $dna_key = $this->r->decrypt( $this->stepThreeResult['message']['body']['data']['key'] );
      $e = new DES($dna_key, 'DES-ECB', DES::OUTPUT_BASE64);
      $this->finalResult = $e->decrypt( $this->stepThreeResult['message']['body']['data']['content'] );
    }

    public function done(){
      $this->http->close();
      return $this->finalResult;
    }

  }
