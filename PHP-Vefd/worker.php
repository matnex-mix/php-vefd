<?php

  date_default_timezone_set("Africa/Lusaka");

  require('rsa.php');
  require('des.php');
  require('curl.php');

  class EFDWorker {

    const DEBUG = TRUE;

    public $Device;
    public $Serial;

    private $Key;
    private $AutoKey;
    private $des;
    private $http;
    private $TaxpayerInfo;
    private $invoiceCache;

    public function __construct(){

      $this->loadKey( __DIR__.'/cache/.key' );

      $this->http = new cURL("http://41.72.108.82:8097/iface/index");
      $this->http->setopt(CURLOPT_HEADER, 0);
      $this->http->setopt(CURLOPT_RETURNTRANSFER, 1);
      $this->http->setopt(CURLOPT_POST, 1);
      $this->http->setopt(CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json;Charset=utf-8',
        'Host: 211.90.56.2'
      ));

      $this->Device = @file_get_contents( __DIR__.'/cache/.tid' );
      $this->Serial = '000000';

      $this->ensureReady();
      $this->loadInfo( __DIR__.'/cache/.info' );

    }

    /*
     * Built-in Functions
     */

    # Make sure all variables are set before starting the application
    private function ensureReady( $strict=TRUE ){

      if( $strict && empty($this->Key) ){
        return $this->error( 'Empty value for Key, load the certificate file please.' );
      }

      if( empty($this->Device) ){
        return $this->error( 'Null value for Device Id' );
      }

      if( empty($this->Serial) ){
        return $this->error( 'Null value for Serial' );
      }

      $this->setAutoKey();
    }

    # Set Key
    private function setAutoKey(){
      if( empty($this->AutoKey) ){
        $this->AutoKey = substr( $this->Device, -8 );

        $this->rsa = new RSA($this->Key);
        $this->QueryKey = $this->rsa->encrypt( $this->AutoKey );

        $this->des = new DES($this->AutoKey, 'DES-ECB', DES::OUTPUT_BASE64);
        $this->RealKey = preg_replace( '/(-----BEGIN PRIVATE KEY-----|-----END PRIVATE KEY-----|\n)/', '', $this->Key );
      }
    }

    # Download key
    public function downloadKey( $filename ){
      $this->ensureReady();
      return @file_put_contents( $filename, $this->Key );
    }

    # Load Key
    public function loadKey( $filename ){
      $file = @file_get_contents( $filename );
      if( $file ){
        $this->Key = $file;
      } else {
        return $this->error( "Could not open ($filename), do you have permission to view it." );
      }
    }

    # Load Tax Information
    private function loadInfo( $filename ){
      $data = $this->des->decrypt( @file_get_contents( $filename ) );
      $this->TaxpayerInfo = @json_decode( $data );
    }

    # Set Body
    private function setBody_Run( $body, $print=FALSE ){
      $this->http->setopt(CURLOPT_POSTFIELDS, json_encode(array(
          "message" => array(
            "body" => array(
              "data" => array_merge( array(
                "device" => $this->Device,
                "serial" => $this->Serial,
                # "bus_id" => "",
                # "content" => "",
                # "sign" => "",
                "key" => $this->QueryKey,
              ), $body ),
            )
          )
        ))
      );

      $output = json_decode( $this->http->exec(), TRUE );
      if( $print )
        #print_r( $output );

      if( !empty($output = $output['message']['body']['data']) ){
        if( $output['bus_id']=='unknown' ){
          return $this->error( 'API Response Error: '.$output['content'] );
        }

        return $output;
      } else {
        return $this->error( 'Error encountered, Invalid response from API.' );
      }
    }

    private function prepare( $data ){
      $data['id'] ?? ($data['id'] = $this->Device);
      $format = preg_replace( '/(:|,)/', '$1 ', str_replace( '"', '\'', json_encode( $data ) ) );
      return $format;
    }

    private function finalDecoder( $data ){
      $_key = $this->rsa->decrypt( $data['key'] );
      $_e = new DES( $_key, 'DES-ECB', DES::OUTPUT_BASE64 );

      $output = json_decode( $_e->decrypt( $data['content'] ), TRUE );
      if( $output['code']!=200 ){
        return $this->error( 'API Response Error: ('.$output['code'].') '.$output['desc'] );
      }

      #print_r( $output );
      return $output;
    }

    private function error( $message ){
      throw new Exception( $message );
    }

    private function checkRequirements( $req, $data ){
        # Requirement Checker
        # Format: [ "id", "business.type{1,2}" ]
        foreach( $req as $rule ){
          $bool = preg_match( '/(\S+){(\S+(?:,(?:[^,]+|"[^"]+"|\'[^\']+\'))*)}/', $rule, $match );
          $values = [];

          if( !empty($match) ){
            $rule = $match[1];
            $values = explode( ',', $match[2] );

            foreach ($values as $key => $value) {
              $values[$key] = eval("return $value;");
            }
          }

          $rule = "['".preg_replace( '/(\w)\.(\w)/', '$1\'][\'$2', $rule )."']";
          if( eval("return empty(\$data$rule);") ){
            return $this->error('ArgumentError: Missing a required argument '.$rule);
          } else if( sizeof($values) && !in_array( eval("return \$data$rule;"), $values ) ) {
            return $this->error('ArgumentError: '.$rule.' value is not valid.');
          }
        }
    }

    private function generateFiscal( $data ){

      if( empty($this->TaxpayerInfo) ){
        $this->error('Could not find a valid taxpayer details');
      }

      $tpin = str_pad( $this->TaxpayerInfo->taxpayer->tpin, 18, "0", STR_PAD_LEFT );
      $inv_code = $data['declaration-info']['invoice-code'];
      $inv_num = str_pad( $data['declaration-info']['invoice-number'], 8, "0", STR_PAD_LEFT );
      $inv_time = Date( 'YmdHis', $data['declaration-info']['invoicing-time'] );
      $tid = $this->Device;
      $amount = str_pad( $data['declaration-info']['total-amount'], 20, "0", STR_PAD_LEFT );
      $pri_key = $this->RealKey;

      ("
TPIN (".strlen($tpin)."): $tpin
INVOICE_CODE (".strlen($inv_code)."): $inv_code
INVOICE_NUMBER (".strlen($inv_num)."): $inv_num
INVOICING_TIME (".strlen($inv_time)."): $inv_time
TERMINAL_ID (".strlen($tid)."): $this->Device
AMOUNT (".strlen($amount)."): $amount
PRIVATE_KEY (".strlen($pri_key)."): $pri_key
\n");

      $file = realpath( __DIR__."/../fiscalcode/fiscal_code.py" );
      $code = "python $file -t \"$tpin\" -c \"$inv_code\" -n \"$inv_num\" -u \"$inv_time\" -i \"$tid\" -a \"$amount\" -k \"$pri_key\"";
      $output = trim( shell_exec($code) );

      if( !$output ){
        $this->error('Could not generate fiscal code');
      }

      return $output;

    }

    public function location(){
      if( !empty($this->LocationDetails) )
        return;

      $file = @file_get_contents("http://api.ipstack.com/check?access_key=c8a109707e32a7ed1e3a1f20cc0f2870&format=1");
      if( $file ){
        $this->LocationDetails = json_decode( $file, TRUE );
      }
      else {
        $this->error('An Error Ocurred!');
      }
    }

    private function invoiceInfo( $enforce=false ){
      $inv = ($this->invoiceCache ?? @$this->invoiceApp(array())['invoice']);

      if( !sizeof($inv) ){
        $this->error('You have no invoice space');
      } else {
        $_inv = [];
        foreach( $inv as $v ){
          for ($i=0; $i < $v['number-end']-$v['number-begin']+1; $i++) {
          $_inv[] = array(
            'code' => $v['code'],
            'number-begin' => $v['number-begin']+$i,
            'number-end' => $v['number-begin']+$i,
          );
          }
        }

        $this->invoiceCache = $_inv;
        $inv = current($inv);
        if( $inv ){
          return array(
            'code' => $inv['code'],
            'number' => $inv['number-begin'],
          );
        }
      }

      $this->error('Invoice range exhausted');
    }

    public function test(){
      $this->ensureReady();
      return $this->invoiceInfo(true);
    }

    # ------------------------------------------------------------------------ #
    #  API METHODS
    # ------------------------------------------------------------------------ #


    /*
     * Alarm Notification
     */

    public function alarmNotify( $bus_data ){
      $this->checkRequirements( [ /*"id",*/ "level", "info" ], $bus_data );
      $this->ensureReady();
      $bus_data['time'] = strval(time());
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "ALARM-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * Heartbeat Monitor
     */

    public function hMonitor( $bus_data ){
      $this->checkRequirements( [ /*"id",*/ "sw_version", "batch" ], $bus_data );
      $this->ensureReady();
      if( !empty($this->LocationDetails) ){
        $bus_data['lat'] = round( $this->LocationDetails['latitude'], 6 );
        $bus_data['lon'] = round( $this->LocationDetails['longitude'], 6 );
      }

      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "MONITOR-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * V-EFD Initialization
     */

    public function init( $bus_data ){
      $this->checkRequirements( [ "license", "sw_version", "manufacture" ], $bus_data );
      $this->ensureReady( $strict=FALSE );
      $bus_data = $this->prepare($bus_data);

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5($content, TRUE) );

      $this->stepOneResult = $this->setBody_Run(array(
        "bus_id" => "R-R-01",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $message = $this->stepOneResult['content'];
      $stepTwoResult = json_decode( $this->des->decrypt($message), TRUE );

      $this->Tid = $stepTwoResult['id'];
      file_put_contents( __DIR__.'/cache/.tid', $this->Tid );

      $this->Key = "-----BEGIN PRIVATE KEY-----\n".wordwrap( $stepTwoResult['secret'], 64 )."\n-----END PRIVATE KEY-----";
      $this->downloadKey( __DIR__.'/cache/.key' );

      return $this;
    }

    /*
     * Invoice Application
     */

    public function invoiceApp( $bus_data ){
      #$this->checkRequirements( [ "id" ], $bus_data );
      $this->ensureReady();
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "INVOICE-APP-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * Invoice Query
     */

    public function invoiceQuery( $bus_data ){
      $this->checkRequirements( [ /*"id",*/ "code", "number" ], $bus_data );
      $bus_data['number'] = strval($bus_data['number']);
      $bus_data['number'] = str_repeat( "0", 8-strlen($bus_data['number']) ).$bus_data['number'];

      $this->ensureReady();
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "INVOICE-RETRIEVE-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * Invoice Upload
     */

   public function invoiceUpload( $bus_data ){
     $this->ensureReady();
     $this->checkRequirements( [
       "declaration-info.tax-amount",
       "declaration-info.total-amount",
       "declaration-info.invoice-status{'01','02','03'}",
       "declaration-info.invoice-issuer",
       "declaration-info.invoicing-time",
       #"declaration-info.fiscal-code",
       "declaration-info.sale-type{0,1}",
       "declaration-info.currency-type",
       "declaration-info.conversion-rate",
     ], $bus_data );

     $data = $this->invoiceInfo(true);

     $bus_data['declaration-info']['invoice-code'] = $data['code'];
     $bus_data['declaration-info']['invoice-number'] = str_pad( $data['number'], 8, "0", STR_PAD_LEFT );
     $bus_data['declaration-info']['invoicing-time'] = strval(time());
     $bus_data['declaration-info']['tax-amount'] = number_format(doubleval($bus_data['declaration-info']['tax-amount']), 2, '.', '');
     $bus_data['declaration-info']['total-amount'] = number_format(doubleval($bus_data['declaration-info']['total-amount']), 2, '.', '');
     $bus_data['declaration-info']['fiscal-code'] = $this->generateFiscal( $bus_data );
     #$bus_data['declaration-info']['total-discount'] = ;

     #print_r( json_encode($bus_data, JSON_PRETTY_PRINT) );
     $bus_data = $this->prepare( $bus_data );

     $content = $this->des->encrypt( $bus_data );
     $sign = base64_encode( md5( $content, TRUE ));

     /*$result = $this->setBody_Run(array(
       "bus_id" => "INVOICE-REPORT-R",
       "content" => $content,
       "sign" => $sign,
     ), self::DEBUG );

     $result = $this->finalDecoder( $result );*/
     if( true || @$result['desc']=='success' ){
       @array_shift( $this->invoiceCache );
       return $data;
     }

     return $result;
   }

   /*
    * Server Ip Ammendment
    */

   public function ipUpdate( $bus_data ){
     #$this->checkRequirements( [ "id" ], $bus_data );
     $this->ensureReady();
     $bus_data = $this->prepare( $bus_data );

     $content = $this->des->encrypt( $bus_data );
     $sign = base64_encode( md5( $content, TRUE ));

     $result = $this->setBody_Run(array(
       "bus_id" => "UPDATE-IP-R",
       "content" => $content,
       "sign" => $sign,
     ), self::DEBUG );

     $result = $this->finalDecoder( $result );
     return $result;
   }

    /*
     * Initialization Success Notification
     */

    public function notifySuccess( $bus_data ){
      #$this->checkRequirements( [ "id" ], $bus_data );
      $this->ensureReady();
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "R-R-03",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * Reactivation
     */

    public function reactivate( $bus_data ){
      #$this->checkRequirements( [ "id" ], $bus_data );
      $this->ensureReady();
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "RECOVER-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * Tax Information Application
     */

    public function taxInfoApply( $bus_data ){
      #$this->checkRequirements( [ "id" ], $bus_data );
      $this->ensureReady( $strict=FALSE );
      $bus_data = $this->prepare($bus_data);

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "R-R-02",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $finalResult = $this->finalDecoder( $result );
      file_put_contents( __DIR__.'/cache/.info', $this->des->encrypt(json_encode($finalResult)) );
      return $finalResult;
    }

    /*
     * Tax Information Modification
     */

    public function taxInfoMod( $bus_data ){
      #$this->checkRequirements( [ "id" ], $bus_data );
      $this->ensureReady();
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "INFO-MODI-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

    /*
     * Time Synchronization
     */

    public function timeSync( $bus_data ){
      #$this->checkRequirements( [ "id" ], $bus_data );
      $this->ensureReady();
      $bus_data = $this->prepare( $bus_data );

      $content = $this->des->encrypt( $bus_data );
      $sign = base64_encode( md5( $content, TRUE ));

      $result = $this->setBody_Run(array(
        "bus_id" => "SYS-TIME-R",
        "content" => $content,
        "sign" => $sign,
      ), self::DEBUG );

      $result = $this->finalDecoder( $result );
      return $result;
    }

  }
