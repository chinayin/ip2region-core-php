# ip2region SDK for PHP

[![Author](https://img.shields.io/badge/author-@chinayin-blue.svg)](https://github.com/chinayin)
[![Software License](https://img.shields.io/badge/license-Apache--2.0-brightgreen.svg)](LICENSE)
[![Latest Version](https://img.shields.io/packagist/v/chinayin/ip2region-core.svg)](https://packagist.org/packages/chinayin/ip2region-core)
[![Total Downloads](https://img.shields.io/packagist/dt/chinayin/ip2region-core.svg)](https://packagist.org/packages/chinayin/ip2region-core)
![php 7.1+](https://img.shields.io/badge/php-min%207.1-red.svg)

### Installation

运行环境要求 PHP 7.1 及以上版本，以及[cURL](http://php.net/manual/zh/book.curl.php)。

#### 官方原生查询包

特点：包更小，数据路径自定义

> composer require chinayin/ip2region-core

#### 包含数据查询包

特点：`xdb数据`封装在composer包内，数据会不定期更新

使用方法：[github.com/chinayin/ip2region](https://github.com/chinayin/ip2region-sdk-php)

> composer require chinayin/ip2region

### Quick Examples

#### 完全基于文件的查询

```php
use ip2region\XdbSearcher;

$ip = '1.2.3.4';
$xdb = './ip2region.xdb';
try {
    $region = XdbSearcher::newWithFileOnly($xdb)->search($ip);
    var_dump($region);
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
```

> 备注：并发使用，每个线程或者协程需要创建一个独立的 searcher 对象。

#### 缓存 VectorIndex 索引

如果你的 php 母环境支持，可以预先加载 vectorIndex 缓存，然后做成全局变量，每次创建 Searcher 的时候使用全局的
vectorIndex，可以减少一次固定的 IO 操作从而加速查询，减少 io 压力。

```php
use ip2region\XdbSearcher;

$ip = '1.2.3.4';
$xdb = './ip2region.xdb';
try {
    // 1、加载 VectorIndex 缓存，把下述的 vIndex 变量缓存到内存里面。
    $vIndex = XdbSearcher::loadVectorFromFile($xdb);
    if (null === $vIndex) {
throw new \RuntimeException("failed to load vector index from '$xdb'.");
    }
    // 2、使用全局的 vIndex 创建带 VectorIndex 缓存的查询对象。
    $searcher = XdbSearcher::newWithVectorIndex($xdb, $vIndex);
    // 3、查询
    $region = $searcher->search($ip);
    var_dump($region);
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
```

> 备注：并发使用，每个线程或者协程需要创建一个独立的 searcher 对象，但是都共享统一的只读 vectorIndex。

#### 缓存整个 xdb 数据

如果你的 PHP 母环境支持，可以预先加载整个 xdb 的数据到内存，这样可以实现完全基于内存的查询，类似之前的 memory search 查询。

```php
use ip2region\XdbSearcher;

$ip = '1.2.3.4';
$xdb = './ip2region.xdb';
try {
    // 1、加载整个 xdb 到内存。
    $cBuff = XdbSearcher::loadContentFromFile($xdb);
    if (null === $cBuff) {
        throw new \RuntimeException("failed to load content buffer from '$xdb'");
    }
    // 2、使用全局的 cBuff 创建带完全基于内存的查询对象。
    $searcher = XdbSearcher::newWithBuffer($cBuff);
    // 3、查询
    $region = $searcher->search($ip);
    var_dump($region);
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
```

> 备注：并发使用，用整个 xdb 缓存创建的 searcher 对象可以安全用于并发。
