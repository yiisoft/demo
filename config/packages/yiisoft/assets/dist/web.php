<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetConverter;
use Yiisoft\Assets\AssetConverterInterface;
use Yiisoft\Assets\AssetLoader;
use Yiisoft\Assets\AssetLoaderInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Assets\AssetPublisher;
use Yiisoft\Assets\AssetPublisherInterface;
use Yiisoft\Factory\Definitions\Reference;

/** @var array $params */

return [
    AssetConverterInterface::class => [
        'class' => AssetConverter::class,
        'callMethods' => [
            'setCommand' => [
                $params['yiisoft/assets']['assetConverter']['command']['from'],
                $params['yiisoft/assets']['assetConverter']['command']['to'],
                $params['yiisoft/assets']['assetConverter']['command']['command'],
            ],
            'setForceConvert' => [$params['yiisoft/assets']['assetConverter']['forceConvert']],
        ],
    ],

    AssetLoaderInterface::class => [
        'class' => AssetLoader::class,
        'callMethods' => [
            'setAppendTimestamp' => [$params['yiisoft/assets']['assetLoader']['appendTimestamp']],
            'setAssetMap' => [$params['yiisoft/assets']['assetLoader']['assetMap']],
            'setBasePath' => [$params['yiisoft/assets']['assetLoader']['basePath']],
            'setBaseUrl' => [$params['yiisoft/assets']['assetLoader']['baseUrl']],
        ],
    ],

    AssetPublisherInterface::class => [
        'class' => AssetPublisher::class,
        'callMethods' => [
            'setForceCopy' => [$params['yiisoft/assets']['assetPublisher']['forceCopy']],
            'setLinkAssets' => [$params['yiisoft/assets']['assetPublisher']['linkAssets']],
        ],
    ],

    AssetManager::class => [
        'class' => AssetManager::class,
        'constructor' => [
            Reference::to(Aliases::class),
            Reference::to(AssetLoaderInterface::class),
            $params['yiisoft/assets']['assetManager']['allowedBundleNames'],
            $params['yiisoft/assets']['assetManager']['customizedBundles'],
        ],
        'callMethods' => [
            'setPublisher' => [Reference::to(AssetPublisherInterface::class)],
            'setConverter' => [Reference::to(AssetConverterInterface::class)],
            'register' => [$params['yiisoft/assets']['assetManager']['register']],
        ],
    ],
];
