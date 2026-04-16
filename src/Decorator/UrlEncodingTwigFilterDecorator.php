<?php

declare(strict_types=1);

namespace Netlogix\NlxSwImgproxy\Decorator;

use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Shopware\Storefront\Framework\Twig\Extension\UrlEncodingTwigFilter;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: UrlEncodingTwigFilter::class)]
class UrlEncodingTwigFilterDecorator extends UrlEncodingTwigFilter
{
    public function __construct(
        private readonly ConfigService $configService
    ) {
    }

    public function encodeUrl(?string $mediaUrl): ?string
    {
        if ($this->shouldReturnMediaUrl($mediaUrl)) {
            return $mediaUrl;
        }

        return parent::encodeUrl($mediaUrl);
    }

    private function shouldReturnMediaUrl(?string $mediaUrl): bool
    {
        if ($mediaUrl === null) {
            return false;
        }

        if (!$this->configService->isEnabled()) {
            return false;
        }

        $baseUrl = $this->configService->getBaseUrl();

        if (empty($baseUrl)) {
            return false;
        }

        return str_contains($mediaUrl, $baseUrl);
    }
}
