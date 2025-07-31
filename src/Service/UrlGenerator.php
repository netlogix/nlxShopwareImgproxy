<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Service;

use Netlogix\NlxSwImgproxy\Model\Image;

class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private readonly ConfigService $configService
    ) {
    }

    public function generateUrl(string $imagePath, ?string $width = null, ?string $height = null): string
    {
        $image = new Image($this->configService->getImageSource(), $imagePath);

        if ($width) {
            $image->width = (int) $width;
        }
        if ($height) {
            $image->height = (int) $height;
        }

        $image->resizeType = $this->configService->getResizeType();

        $parts = [rtrim($this->configService->getBaseUrl(), '/')];

        $path = $this->getImagePath($image);
        $parts[] = $this->configService->isSecure() === false
            ? 'insecure'
            : $this->generateSignature($path);
        $parts[] = $path;

        return implode('/', $parts);
    }

    public function getImagePath(Image $image): string
    {
        return (string) $image;
    }

    public function generateSignature(string $path): string
    {
        $sha256 = hash_hmac(
            'sha256',
            $this->configService->getSalt() . '/' . $path,
            $this->configService->getKey(),
            true
        );
        $sha256Encoded = base64_encode($sha256);

        return str_replace(["+", "/", "="], ["-", "_", ""], $sha256Encoded);
    }

    public function supportMimeType(string $type): bool
    {
        return str_starts_with($type, 'image/');
    }
}
