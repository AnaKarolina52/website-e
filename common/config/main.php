<?php
require_once __DIR__ . '/../../common/helpers.php';

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'class'=> \common\i18n\Formatter::class,
            'currencyCode' => 'EUR',
            'datetimeFormat' => 'php: d/m/y H:i'

        ]
    ],
];
