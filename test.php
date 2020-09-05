<?php
  # Usage Example

  # Copy the ZipArchive and extract to Folder of your choice
  # Include the PATH-TO-Worker.php file in your script
  require 'PHP-Vefd/worker.php';

  # Please note that the arguments are as stated in the Original Documentation
  # and
  # while testing please not that you don't have to reload this test.php page so as not to waste the invoice range allocated

  # It is important to instantiaze the Worker class like this before performing any operation
  $tpin = '010100001129';
  $tpin = '520404079698';
  $worker = new EFDWorker( $tpin );

  print_r(
    $worker->init([
      "license" => "520404079698",
      "sn" => "187603000010",
      "sw_version" => "1.2",
      "model" => "IP-100",
      "manufacture" => "Inspur",
      "imei" => "359833002198832",
      "os" => "linux2.6.36",
      "hw_sn" => "3458392322" 
    ])
  );

  die('');

  # Show tax information from server
  echo "\n\nTAX INFO APPLY RESPONSE:\n\n";
  print_r( $worker->taxInfoApply([]) );

  # Modify Tax Information
  echo "\n\nTAX INFO MOD RESPONSE:\n\n";
  print_r( $worker->taxInfoMod([]) );

  # Invoice upload: the optional arguments are commented out, Returns the code and number of the new invoice in an array
  echo "\n\nINVOICE UPLOAD RESPONSE:\n\n";
  print_r(
    [] ?? $worker->invoiceUpload(array(
      #"POS-SN" => "092344823532",
      "declaration-info" => array(
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
    )
  );

  # Query for an invoice, you can use this to download a copy of an invoice to server
  echo "\n\nINVOICE QUERY RESPONSE:\n\n";
  print_r(
    $worker->invoiceQuery([
      'code' => '08764738223',
      'number' => 34,
    ])
  );

  # Time Synchronization
  echo "\n\nTIME SYNC RESPONSE:\n\n";
  print_r( $worker->timeSync([]) );

  # Server Ip Ammendment
  echo "\n\nIP UPDATE RESPONSE:\n\n";
  print_r( $worker->ipUpdate([]) );

  # Optional: Get Location to use for heartbeat monitor command
  $worker->location();

  # Monitor the heartbeat, the lat and Lon arguments would be automatically added if you use ($worker->location()) before
  echo "\n\nHEARTBEAT MONITOR RESPONSE:\n\n";
  print_r(
    $worker->hMonitor(array(
      #'lon' => 100.832004,
      #'lat' => 45.832004,
      'sw_version' => '1.2',
      'batch' => '0000000000000001'
    ))
  );

  # Notify the EFD system incase of an error
  echo "\n\nALARM NOTIFY RESPONSE:\n\n";
  print_r(
    $worker->alarmNotify(array(
      "level" => "01",
      "info" => "Something here",
    ))
  );

  # Re-activate the EFD system incase of too much errors
  echo "\n\nRE-ACTIVATE RESPONSE:\n\n";
  print_r( $worker->reactivate($common) );

?>
