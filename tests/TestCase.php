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

class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function now()
    {
        return microtime(true) * 1000;
    }

}
