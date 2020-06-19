<?php
  require 'worker.php';

  $worker = new EFDWorker();

  $worker->Device = '520404079698';
  $worker->Serial = '000000';

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
  $worker->loadKey('KEY.pem');

  $worker->Device = '010100001129';
  $common = array(
    'id' => '010100001129',
  );

  #$worker->taxInfoApply( $common );
  #$worker->notifySuccess( $common );
  #$worker->invoiceApp( $common );
  #$worker->taxInfoMod( $common );
  /*$worker->invoiceUpload(array(
    "id" => $common['id'],
    #"POS-SN" => "092344823532",
    "declaration-info" => array(
      "invoice-code" => "16130010",
      "invoice-number" => "000017210020",
      "buyer-tpin" => "100022473",
      "buyer-vat-acc-name" => "40168862",
      "buyer-name" => "Baidu",
      "buyer-address" => "Beijing, China",
      "buyer-tel" => "400-860-0011",
      "tax-amount" => 1.3,
      "total-amount" => 10,
      "total-discount" => 0,
      "invoice-status" => "01",
      "invoice-issuer" => "Cashier01",
      "invoicing-time" => 1503991003,
      "old-invoice-code" => "",
      "old-invoice-number" => "",
      "memo" => "value",
      "currency-type" => "USD",
      "conversion-rate" => 6.5434,
      "sale-type" => 1,
      "local-purchase-order" => "23423452345342",
      "voucher-PIN" => "0983773823442",
      "items-info" => [
        array(
          "no" => 1,
          "tax-category-code" => "A",
          "tax-category-name" => "Standard Rate",
          "name" =>"apple",
          "barcode" =>"6009706160821",
          "count" => 1.00,
          "amount" => 10.00,
          "tax-amount" => 1.30,
          "discount" => 0,
          "unit-price" => 10.00,
          "tax-rate" => 0.15,
          "rrp" => 12.00
        ),
        array(
          "no" => 2,
          "tax-category-code" => "B",
          "tax-category-name" => "MTV",
          "name" =>"pear",
          "barcode" =>"6009706160821",
          "count" => 1.00,
          "amount" => 10.00,
          "tax-amount" => 1.30,
          "discount" => 0,
          "unit-price" => 10.00,
          "tax-rate" => 0.15,
          "rrp" => 12.00
        ),
        array(
          "no" => 3,
          "tax-category-code" => "A",
          "tax-category-name" => "Standard Rate",
          "name" =>"pear",
          "barcode" => "6009706160821",
          "count" => 1.00,
          "amount" => 10.00,
          "tax-amount" => 1.30,
          "discount" => 0,
          "unit-price" => 10.00,
          "tax-rate" => 0.15,
          "rrp" => 12.00
        )
      ],
      "tax-info" => [
        array(
          "tax-code" => "A",
          "tax-name" => "standard rate",
          "tax-rate" => 0.16,
          "tax-value" => 100
        ),
        array(
          "tax-code" => "C1",
          "tax-name" => "export",
          "tax-rate" => 0,
          "tax-value" => 0
        ),
        array(
          "tax-code" => "T",
          "tax-name" => "Tourism Levy",
          "tax-rate" => 0.015,
          "tax-value" => 30
        )
      ]
    ))
  );*/
  /*$worker->invoiceQuery([
    'id' => $common['id'],
    'code' => '08764738223',
    'number' => 34,
  ]);*/

  #$worker->timeSync($common);
  #$worker->ipUpdate($common);

  /*$worker->location();
  $worker->hMonitor(array(
    'id' => $common['id'],
    #'lon' => 100.832004,
    #'lat' => 45.832004,
    'sw_version' => '1.2',
    'batch' => '0000000000000001'
  ));*/

  /*$worker->alarmNotify(array(
    'id' => $common['id'],
    "level" => "01",
    "info" => "Something here",
  ));
  $worker->reactivate($common);*/

?>
