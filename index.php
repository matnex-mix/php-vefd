<?php

if( !empty($_POST['done']) ){
  include('worker.php');

  $one_conf = explode(",", trim($_POST['one_conf']));
  $one_data = $_POST['one_data'];

  $two_conf = explode(",", trim($_POST['two_conf']));
  $two_data = $_POST['two_data'];

  $worker = new Worker( $_POST['key'], [
    $one_conf,
    $two_conf
  ] );
  $worker->stepOne($one_data);
  $worker->stepThree($two_data);
  print_r( $worker->done() );
  /*$worker = new Worker( '04079698', [
    [ "520404079698", "000000", "R-R-01" ],
    [ "010100001129", "000000", "R-R-02" ]
  ] );
  $worker->stepOne('{"license": "520404079698", "sn": "LAMASAT INTERNATIONAL LTD", "sw_version": "1.2", "model": "IP-100", "manufacture": "Inspur", "imei": "100159197500000", "os": "linux2.6.36", "hw_sn": ""}');
  $worker->stepThree('{"id": "010100001129"}');
  print_r( $worker->done() );*/
  die("<br/><br/><h3>NB: Make sure you have access to INTERNET</h3>");
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Test Php V-EFD</title>
  </head>
  <body>
    <form method="post">
      <input name="key" value="04079698" /><br/>

      <h3>Stage One</h3>
      <input name="one_conf" placeholder="device,serial,bus_id" /><br/>
      <p>
        <b>E.G: </b>
        <b>(</b> 520404079698,000000,R-R-01 <b>)</b>
      </p>
      <textarea name="one_data" rows="10" cols="50" >{"license": "520404079698", "sn": "LAMASAT INTERNATIONAL LTD", "sw_version": "1.2", "model": "IP-100", "manufacture": "Inspur", "imei": "100159197500000", "os": "linux2.6.36", "hw_sn": ""}</textarea><br/>

      <h3>Stage Three</h3>
      <input name="two_conf" placeholder="device,serial,bus_id" /><br/>
      <p>
        <b>E.G: </b>
        <b>(</b> 010100001129,000000,R-R-02 <b>)</b>
      </p>
      <textarea name="two_data" rows="10" cols="50" >{"id": "010100001129"}</textarea><br/>

      <br/>
      <input type="submit" value="SUBMIT" name="done" />
    </form>
  </body>
</html>
