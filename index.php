<?php

  require_once('PHP-Vefd/worker.php');

  $methods = [
    "init" => "VEFD Initialization",
    "taxInfoApply" => "Tax Information Application",
    "notifySuccess" => "Success notification",
    "taxInfoMod" => "Tax Information Modification",
    "invoiceUpload" => "Invoice Upload (not completed)",
    "timeSync" => "Time Synchronization",
    "invoiceQuery" => "Invoice Query",
    "ipUpdate" => "Server Ip Ammendment",
    "hMonitor" => "Heartbeat Monitor",
    "alarmNotify" => "Alarm Notification",
    "reactivate" => "VEFD Reactivation",
  ];

  $resp = '';
  $data = '{"id": "010100001129"}';
  $method = '';

  if( isset($_GET['submit']) ){
    $obj = new EFDWorker();

    $obj->Device = '010100001129';
    #$obj->Device = '520404079698';
    $obj->Serial = '000000';

    $obj->loadKey(__DIR__.'/KEY.pem');

    $method = $_GET['api'];
    $data = $_GET['data'];

    try {
      $resp = $obj->$method( json_decode( $data, TRUE ) );
    }
    catch( \Throwable $t ){
      $resp = $t->getMessage();
    }
    catch( \Exception $e ){
      $resp = $e->getMessage();
    }

  }

?>

<em>Still working on the FiscalCode...</em>
<h1>Test Interface</h1>
<form>
  <h4>Api</h4>
  <select style="display: block; width: 100%;" name="api">
    <?php foreach( $methods as $r => $n ){ ?>
      <option value="<?php echo $r; ?>" <?php if($r==$method){ echo "selected"; } ?> ><?php echo $n; ?></option>
    <?php } ?>
  </select>

  <br/>
  <h4>Business Data</h4>
  <textarea style="width: 100%; height: 200px;" name="data"><?php echo $data; ?></textarea>

  <br/>
  <h4>Response</h4>
  <textarea style="width: 100%; height: 200px;"><?php echo json_encode( $resp, JSON_PRETTY_PRINT ); ?></textarea>

  <br/><br/>
  <input type="submit" name="submit" value="DONE" style="width: 100%; height: 40px;" />
</form>
