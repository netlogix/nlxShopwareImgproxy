<?php

namespace Netlogix\NlxSwImgproxy\Test\Helper;

use Composer\InstalledVersions;

class ShopwareVersionHelper
{
    public static function getShopwareVersion(): string
    {
        return InstalledVersions::isInstalled('shopware/platform') ?
            InstalledVersions::getPrettyVersion('shopware/platform') :
            InstalledVersions::getPrettyVersion('shopware/core');
    }
}
