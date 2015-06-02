# ShiftpiMetascanApi
Client for [Metascan Online Public API](https://www.metascan-online.com/).

## Installation
### Using Composer
Enable module by adding 
```php
// ...
'modules' => [
    // ...
    'ShiftpiMetascanApi',
    // ...
],
// ...
```
after you have required `shiftpi/metascan-api` in your `composer.json`.

## Configuration
Add 
```php
'metascan' => [
    'key' => 'yourapikey'
]
```
to your application's config.

## Usage
### Scan data
If you want to scan the whole content of a file, you should use the ScanService class. Retrieve an instance from the
service manager by using the key `ShiftpiMetascanApi\Service\Scan`.
```php
$service = $sm->get(\ShiftpiMetascanApi\Service\Scan::class);
$result = $service->scan(file_get_contents('/path/to/myfile.zip', 'myfile.zip', 'secretpassword'));
```
The second and third parameters are optional. If you upload an encrypted archive, Metascan Online can decrypt it if you provide the password as third parameter.

### Lookup hash
A faster way checking files is looking up its hash. For that you can use the HashLookupService by retrieving `ShiftpiMetascanApi\Service\HashLookup` from the service manager.
```php
$service = $sm->get(\ShiftpiMetascanApi\Service\HashLookup::class);
$result = $service->lookup(hash('sha256', file_get_contents('/path/to/myfile.zip')));
```
The parameter should be a SHA256, SHA1 or MD5 hash. If the parameter has an other string length, than a supported algorithm produces, an exception will be thrown.
Please note: If you lookup a hash Metascan does not know, a "Not Found" state will be returned. This does not mean, that the file is probably not malicious.

## Testing
There are some test files for the services present. You have to copy the file `test/config/config.local.php.dist` to `test/config/config.local.php` and paste your API key in order to run those.  
These are tests against the real API, which will produce real API requests (no mocks!). Therefore be careful running them and keep yor API limits in mind. ;-)


## License
Licensed under the MIT license. See license file.