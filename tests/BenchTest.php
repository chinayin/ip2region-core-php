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

class BenchTest extends TestCase
{
    public function testBench()
    {
        $cachePolicy = 'content';
        $searcher = SearchTest::builder($cachePolicy);

        $file = __DIR__ . '/../data/ip.merge.txt';
        if (!file_exists($file)) {
            return;
        }

        $handle = fopen($file, "r");

        $count = 0;
        $costs = 0;
        $ts = XdbSearcher::now();
        while (!feof($handle)) {
            $line = trim(fgets($handle, 1024));
            if (strlen($line) < 1) {
                continue;
            }
            $ps = explode('|', $line, 3);
            $this->assertCount(3, $ps);

            $sip = XdbSearcher::ip2long($ps[0]);
            $eip = XdbSearcher::ip2long($ps[1]);
            $this->assertNotNull($sip);
            $this->assertNotNull($eip);
            $this->assertGreaterThanOrEqual($sip, $eip);

            $mip = ($sip + $eip) >> 1;
            foreach ([$sip, ($sip + $mip) >> 1, $mip, ($mip + $eip) >> 1, $eip] as $ip) {
                try {
                    $cTime = XdbSearcher::now();
                    $region = $searcher->search($ip);
                    $costs += XdbSearcher::now() - $cTime;
                } catch (\Exception $e) {
                    printf("failed to search ip `%s`\n", long2ip($ip));
                    return;
                }

                $this->assertNotNull($region, sprintf("failed to search ip `%s`", long2ip($ip)));

                // check the region info
                $this->assertEquals(
                    $ps[2],
                    $region,
                    sprintf("failed search(%s) with (%s != %s)\n", long2ip($ip), $region, $ps[2])
                );

                $count++;
            }
        }

        fclose($handle);
        $searcher->close();
        printf(
            "Bench finished, {cachePolicy: %s, total: %d, took: %ds, cost: %.3f ms/op}\n",
            $cachePolicy,
            $count,
            (XdbSearcher::now() - $ts) / 1000,
            $count == 0 ? 0 : $costs / $count
        );
    }
}
