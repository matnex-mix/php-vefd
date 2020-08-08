<?php
  # Usage Example

  # Copy the ZipArchive and extract to Folder of your choice
  # Include the PATH-TO-Worker.php file in your script
  require 'PHP-Vefd/worker.php';

  # It is important to instantiaze the Worker class like this before performing any operation
  $worker = new EFDWorker();

  #$worker->Device = '520404079698';
  /*$worker->init(array(
    "license" => "520404079698",
    "sn" => "LAMASAT INTERNATIONAL LTD",
    "sw_version" => "1.2",
    "model" => "IP-100",
    "manufacture" => "Inspur",
    "imei" => "100159197500000",
    "os" => "linux2.6.36",
    "hw_sn" => ""
  ))
    ->downloadKey('KEY.pem');*/
  #$worker->loadKey('KEY.pem');

  //$worker->Device = '010100001129';
  $common = array(
    #'id' => '010100001129',
  );

  #$worker->taxInfoApply( $common );
  #$worker->notifySuccess( $common );
  #$worker->taxInfoMod( $common );
  /*array(
    "declaration-info" => array(
      "invoice-code" => "000200110000",
      "invoice-number" => "00018112",
      "buyer-tpin" => "",
      "buyer-vat-acc-name" => "",
      "buyer-name" => "",
      "buyer-address" => "",
      "buyer-tel" => "",
      "tax-amount" => 4.16,
      "total-amount" => 30.14,
      "total-discount" => 0,
      "invoice-status" => "01",
      "invoice-issuer" => "Administrator",
      "invoicing-time" => 1592818992,
      "old-invoice-code" => "",
      "old-invoice-number" => "",
      "fiscal-code" => "60353941232210040508",
      "memo" => "",
      "sale-type" => 0,
      "currency-type" => "USD",
      "conversion-rate" => 14.93,
      "local-purchase-order" => "",
      "voucher-PIN" => "",
      "items-info" => [
        array(
          "no" => "1",
          "tax-category-code" => "A",
          "tax-category-name" => "STANDARD RATED",
          "name" => "ACCOMODATION",
          "barcode" => "",
          "count" => 1,
          "amount" => 30.14,
          "tax-amount" => 4.16,
          "discount" => 0,
          "unit-price" => 30.14,
          "tax-rate" => 0.16,
          "rrp" => 0
        )
      ],
      "tax-info" => [
        array(
          "tax-code" => "A",
          "tax-name" => "STANDARD RATED",
          "tax-rate" => 0.16,
          "tax-value" => 4.16
        )
      ],
    )
  );*/

  print_r(
    $worker->invoiceUpload(array(
    #"POS-SN" => "092344823532",
    "declaration-info" => array(
      //"invoice-code" => "16130010",
      //"invoice-number" => "000017210020",
      /*"buyer-tpin" => "100022473",
      "buyer-vat-acc-name" => "40168862",
      "buyer-name" => "Baidu",
      "buyer-address" => "Beijing, China",
      "buyer-tel" => "400-860-0011",*/
      "tax-amount" => 1.3,
      "total-amount" => 10,
      #"total-discount" => 0,
      "invoice-status" => "01",
      "invoice-issuer" => "Cashier01",
      "invoicing-time" => 1503991003,
      /*"old-invoice-code" => "",
      "old-invoice-number" => "",
      "memo" => "value",*/
      "currency-type" => "USD",
      "conversion-rate" => 6.5434,
      "sale-type" => 1,
      #"local-purchase-order" => "23423452345342",
      #"voucher-PIN" => "0983773823442",
      "items-info" => [
        array(
          "no" => 1,
          "tax-category-code" => "A",
          "tax-category-name" => "Standard Rate",
          "name" =>"apple",
          #"barcode" =>"6009706160821",
          "count" => 1.00,
          "amount" => 10.00,
          "tax-amount" => 1.30,
          #"discount" => 0,
          "unit-price" => 10.00,
          "tax-rate" => 0.15,
          #"rrp" => 12.00
        ),
      ],
      "tax-info" => [
        array(
          "tax-code" => "A",
          "tax-name" => "standard rate",
          "tax-rate" => 0.16,
          "tax-value" => 100
        ),
      ]
    ))
  ));

  /*$worker->invoiceQuery([
    'code' => '08764738223',
    'number' => 34,
  ]);*/

  #$worker->timeSync([]);
  #$worker->ipUpdate([]);

  /*$worker->location();
  $worker->hMonitor(array(
    #'lon' => 100.832004,
    #'lat' => 45.832004,
    'sw_version' => '1.2',
    'batch' => '0000000000000001'
  ));*/

  /*$worker->alarmNotify(array(
    "level" => "01",
    "info" => "Something here",
  ));
  $worker->reactivate($common);*/

?>