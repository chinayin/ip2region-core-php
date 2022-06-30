<?php

/*
 * This file is part of the Ip2Region package.
 *
 * Copyright 2022 The Ip2Region Authors. All rights reserved.
 * Use of this source code is governed by a Apache2.0-style
 * license that can be found in the LICENSE file.
 *
 * @link   https://github.com/lionsoul2014/ip2region
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ip2region\Tests;

use ip2region\XdbSearcher;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function now()
    {
        return microtime(true) * 1000;
    }

    public function test()
    {
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
    }

}
