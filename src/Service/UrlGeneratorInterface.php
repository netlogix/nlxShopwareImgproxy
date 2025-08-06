<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Service;

use Netlogix\NlxSwImgproxy\Model\Image;

interface UrlGeneratorInterface
{
    public function generateUrl(string $imagePath, ?string $width = null, ?string $height = null): string;

    public function getImagePath(Image $image): string;

    public function supportMimeType(string $type): bool;
}
