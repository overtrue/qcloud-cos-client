<h1 align="center">QCloud COS Client</h1>

对象存储（Cloud Object Storage，COS）是腾讯云提供的一种存储海量文件的分布式存储服务，具有高扩展性、低成本、可靠安全等优点。通过控制台、API、SDK 和工具等多样化方式，用户可简单、快速地接入 COS，进行多格式文件的上传、下载和管理，实现海量数据存储和管理。

> :star: 官方文档：https://cloud.tencent.com/document/product/436

![Test](https://github.com/overtrue/qcloud-cos-client/workflows/Test/badge.svg)

## 安装

环境要求：

- PHP >= 7.4
- ext-libxml
- ext-simplexml
- ext-json
- ext-dom

```shell
$ composer require overtrue/qcloud-cos-client -vvv
```

## 配置

配置前请了解官方名词解释：[文档中心 > 对象存储 > API 文档 > 简介：术语信息](https://cloud.tencent.com/document/product/436/7751#.E6.9C.AF.E8.AF.AD.E4.BF.A1.E6.81.AF)

```php
use Overtrue\CosClient\Config;

$config = new Config([
    // 必填，app_id、secret_id、secret_key 可在个人秘钥管理页查看：https://console.cloud.tencent.com/capi
    'app_id' => 10020201024, 
    'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx', 
    'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
    
    // 可选(批量处理接口必填)，腾讯云账号 ID，可在腾讯云控制台账号信息中查看：https://console.cloud.tencent.com/developer
    'uin' => '10000*******', 
    
    // 可选，地域列表请查看 https://cloud.tencent.com/document/product/436/6224
    'region' => 'ap-guangzhou', 

    // 可选，仅在调用不同的接口时按场景必填
    'bucket' => 'example', // 使用 Bucket 接口时必填
    
    // 可选，签名有效期，默认 60 分钟
    'signature_expires' => '+60 minutes', 
]);
```

## 使用

您可以分两种方式使用此 SDK：

- **ServiceClient、BucketClient、JobClient** - 封装了具体 API 的类调用指定业务的 API。
- **Client** - 基于最基础的 HTTP 类封装调用 COS 全部 API。

在使用前我们强烈建议您仔细阅读[官方 API 文档](https://cloud.tencent.com/document/product/436)，以减少不必要的时间浪费。

## ServiceClient

```php
use Overtrue\CosClient\Config;
use Overtrue\CosClient\ServiceClient;

$config = new Config([
    // 请参考配置说明
]);
$service = new ServiceClient($config);

$service->listBuckets();
$service->listBuckets('ap-guangzhou');
```

## JobClient

```php
use Overtrue\CosClient\Config;
use Overtrue\CosClient\JobClient;

$config = new Config([
    // 请参考配置说明
]);

$job = new JobClient($config);

$job->lists(array $query = []);
$job->create(array $body);
$job->describe(string $id, array $query);
$job->updatePriority(string $id, int $priority);
$job->updateStatus(string $id, array $query);
```

## BucketClient

```php
use Overtrue\CosClient\Config;
use Overtrue\CosClient\BucketClient;

$config = new Config([
    // 请参考配置说明
    'bucket' => 'example',
    'region' => 'ap-guangzhou',
]);

$bucket = new BucketClient($config);
```

### API

##### 基本操作

```php
$bucket->create(array $body); // put bucket
$bucket->ping(); // head bucket
$bucket->delete();
$bucket->getObjects(array $query = []);
$bucket->getObjectVersions(array $query = []);

// Versions
$bucket->putVersions(array $body);
$bucket->getVersions();

// ACL
$bucket->putACL(array $body, array $headers = [])
$bucket->getACL();

// CORS
$bucket->putCORS(array $body);
$bucket->getCORS();
$bucket->deleteCORS();

// Lifecycle
$bucket->putLifecycle(array $body);
$bucket->getLifecycle();
$bucket->deleteLifecycle();

// Policy
$bucket->putPolicy(array $body);
$bucket->getPolicy();
$bucket->deletePolicy();

// Referer
$bucket->putReferer(array $body);
$bucket->getReferer();

// Taging
$bucket->putTaging(array $body);
$bucket->getTaging();
$bucket->deleteTaging();

// Website
$bucket->putWebsite(array $body);
$bucket->getWebsite();
$bucket->deleteWebsite();

// Inventory
$bucket->putInventory(string $id, array $body)
$bucket->getInventory(string $id)
$bucket->listInventoryConfigurations(?string $nextContinuationToken = null)
$bucket->deleteInventory(string $id)

// Versioning
$bucket->putVersioning(array $body);
$bucket->getVersioning();

// Replication
$bucket->putReplication(array $body);
$bucket->getReplication();
$bucket->deleteReplication();

// Logging
$bucket->putLogging(array $body);
$bucket->getLogging();

// Accelerate
$bucket->putAccelerate(array $body);
$bucket->getAccelerate();

// Encryption
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
$bucket->selectObjectContents($key, array $body);
$bucket->deleteObject($key, array $query = [], array $headers = []);
$bucket->deleteObjects(array $body);

$bucket->putObjectACL($key, array $body, array $headers = []);
$bucket->getObjectACL($key);

$bucket->putObjectTagging($key, array $body, array $query = []);
$bucket->getObjectTagging($key, array $query = []);
$bucket->deleteObjectTagging($key, array $query = []);
```

## 测试

你可以使用类提供的 `spy` 方法来创建一个测试对象：

```php
use Overtrue\CosClient\ServiceClient;

$service = ServiceClient::spy();

$service->shouldReceive('get')
        ->with('https://service.cos.myqcloud.com')
        ->once()
        ->andReturn('all region buckets');

$this->assertSame('all region buckets', $service->listBuckets());
```

更多测试写法请阅读：[Mockery 官方文档](http://docs.mockery.io/en/latest/index.html)

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/vendor/package/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/vendor/package/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
