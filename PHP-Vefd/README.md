# Documentation For Php V-EFD Implementation

### Usage
Create an object then set the `Device`, `Terminal Id` & `Serial` attribute

#### Example
```
$worker = new EFDWorker();

$worker->Device = '520404079698';
$worker->Serial = '000000';
$worker->TerminalId = '010100001129';
```
you can now proceed to use the API methods as you wish

### Initialization
Initialization is required the first time the script is run so as to be able to download the private key to be used. The returned object can be used to download the key as a backup

- **Caller**: `...->init()`
- **Parameter**: `array`
- **Return**: `EFDWorker object`

#### Example
```
$worker->init(array(
  "license" => "520404079698",
  "sn" => "LAMASAT INTERNATIONAL LTD",
  "sw_version" => "1.2",
  "model" => "IP-100",
  "manufacture" => "Inspur",
  "imei" => "100159197500000",
  "os" => "linux2.6.36",
  "hw_sn" => ""
))
  ->downloadKey( %FILE_PATH% );
```

### Request Tax Information
To get the taxpayer's and tax information form efd server

- **Caller**: `...->taxInfoApply()`
- **Parameter**: `array`
- **Return**: `array`

#### Example

```
$common = array(
  'id' => '010100001129',
);
$info = $worker->taxInfoApply( $common );
print_r( $info );
```

### Modify Tax Information
To send a modification request for tax information

- **Caller**: `...->taxInfoMod()`
- **Parameter**: `array`
- **Return**: `array`

#### Example

```
$common = array(
  'id' => '010100001129',
);
$info = $worker->taxInfoMod( $common );
print_r( $info );
```
