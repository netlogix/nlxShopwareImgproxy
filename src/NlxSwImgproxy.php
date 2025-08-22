<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy;

use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Plugin;

class NlxSwImgproxy extends Plugin
{
    public function boot(): void
    {
        parent::boot();

        Feature::registerFeature('UrlParams_has_mimeType');
    }
}
