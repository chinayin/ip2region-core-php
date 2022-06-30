<?php

if (!file_exists(__DIR__ . '/src')) {
    exit(0);
}

$fileHeaderComment = <<<'EOF'
This file is part of the Ip2Region package.

Copyright 2022 The Ip2Region Authors. All rights reserved.
Use of this source code is governed by a Apache2.0-style
license that can be found in the LICENSE file.

@link   https://github.com/lionsoul2014/ip2region

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return (new PhpCsFixer\Config())
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP71Migration' => true,
        '@PHPUnit75Migration:risky' => true,
        '@PSR12' => true,
        'header_comment' => ['header' => $fileHeaderComment],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->ignoreVCSIgnored(true)
            ->files()
            ->name('*.php')
            ->exclude('vendor')
            ->in(__DIR__)
    );
