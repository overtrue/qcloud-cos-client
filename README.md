<h1 align="center">QCloud COS Client</h1>

<p align="center">Client of QCloud.com COS.</p>


## Installing

```shell
$ composer require overtrue/qcloud-cos-client -vvv
```

## Usage

## Client
```php
use Overtrue\CosClient\Client;

$appId = 1250000000;
$secretId = 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxx';
$secretKey = 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxx';

$client = new Client($appId, $secretId, $secretKey);
```

## Service
```php
$client->service->buckets();
$client->service->buckets('ap-guangzhou');
```

## Bucket

```php
$name = 'example';
$region = 'ap-guangzhou';

$bucket = $client->bucket($name, $region);
```

### API

##### 基本操作

```php
$bucket->put(array $body);
$bucket->head();
$bucket->delete();
$bucket->getObjects(array $query = []);
$bucket->getObjectVersions(array $query = []);

##### Versions
$bucket->putVersions(array $body);
$bucket->getVersions();

##### ACL
$bucket->putACL(array $body, array $headers = [])
$bucket->getACL();

##### CORS
$bucket->putCORS(array $body);
$bucket->getCORS();
$bucket->deleteCORS();

##### Lifecycle
$bucket->putLifecycle(array $body);
$bucket->getLifecycle();
$bucket->deleteLifecycle();

##### Policy
$bucket->putPolicy(array $body);
$bucket->getPolicy();
$bucket->deletePolicy();

##### Referer
$bucket->putReferer(array $body);
$bucket->getReferer();

##### Taging
$bucket->putTaging(array $body);
$bucket->getTaging();
$bucket->deleteTaging();

##### Website
$bucket->putWebsite(array $body);
$bucket->getWebsite();
$bucket->deleteWebsite();

##### Inventory
$bucket->putInventory(string $id, array $body)
$bucket->getInventory(string $id)
$bucket->listInventoryConfigurations(?string $nextContinuationToken = null)
$bucket->deleteInventory(string $id)

##### Versioning
$bucket->putVersioning(array $body);
$bucket->getVersioning();

##### Replication
$bucket->putReplication(array $body);
$bucket->getReplication();
$bucket->deleteReplication();

##### Logging
$bucket->putLogging(array $body);
$bucket->getLogging();

##### Accelerate
$bucket->putAccelerate(array $body);
$bucket->getAccelerate();

##### Encryption
$bucket->putEncryption(array $body);
$bucket->getEncryption();
$bucket->deleteEncryption();

#### Object

$bucket->putObject(string $key, string $contents, array $headers = []);
$bucket->copyObject(string $key, array $headers = []);
$bucket->getObject($key, array $query = [], array $headers = []);
$bucket->headObject($key, array $query = [], array $headers = []);
$bucket->optionsObject($key, array $query = [], array $headers = []);
$bucket->restoreObject($key, array $body, array $query = []);
* $bucket->selectObjectContents($key, array $body);
$bucket->deleteObject($key, array $query = [], array $headers = []);
$bucket->deleteObjects(array $body);

$bucket->putObjectACL($key, array $body, array $headers = []);
$bucket->getObjectACL($key);

$bucket->putObjectTagging($key, array $body, array $query = []);
$bucket->getObjectTagging($key, array $query = []);
$bucket->deleteObjectTagging($key, array $query = []);
```


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/vendor/package/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/vendor/package/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
