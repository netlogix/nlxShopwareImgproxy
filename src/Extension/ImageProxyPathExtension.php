<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Extension;

use Netlogix\NlxSwImgproxy\Model\Image;
use Shopware\Core\Framework\Extensions\Extension;

/**
 * @extends Extension<Image>
 * @codeCoverageIgnore
 */
class ImageProxyPathExtension extends Extension
{
    public const NAME = 'nlx.sw_imgproxy_path';

    public function __construct(
        public readonly string $imagePath,
        public readonly ?string $width,
        public readonly ?string $height
    ) {
    }
}
