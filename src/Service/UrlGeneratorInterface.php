<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Service;

use DateTimeInterface;
use Netlogix\NlxSwImgproxy\Model\Image;

interface UrlGeneratorInterface
{
    /**
     * @param DateTimeInterface|null $imageDate createdAt or updatedAt of the image
     */
    public function generateUrl(
        string $imagePath,
        ?string $width = null,
        ?string $height = null,
        ?DateTimeInterface $imageDate = null,
    ): string;

    public function getImagePath(Image $image): string;

    public function supportMimeType(string $type): bool;
}
