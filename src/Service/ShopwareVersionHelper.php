<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Service;

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
