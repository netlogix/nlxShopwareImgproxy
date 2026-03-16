<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\EventListener;

use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Netlogix\NlxSwImgproxy\Service\UrlGeneratorInterface;
use Shopware\Core\Content\Media\Extension\ResolveRemoteThumbnailUrlExtension;
use Shopware\Core\Framework\Feature;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * @codeCoverageIgnore
 */
#[AsEventListener(ResolveRemoteThumbnailUrlExtension::NAME . '.pre')]
class RemoteThumbnailUrlResolver
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ConfigService $configService
    ) {
    }

    public function __invoke(ResolveRemoteThumbnailUrlExtension $extension): void
    {
        if (!$this->configService->isEnabled()) {
            return;
        }

        $extension->stopPropagation();

        if (
            (Feature::has('v6.8.0.0') && Feature::isActive('v6.8.0.0'))
            || Feature::isActive('UrlParams_has_mimeType')
        ) {
            $mimeType = $extension->mediaEntity?->get('mimeType') ?? '';

            if (!$this->urlGenerator->supportMimeType($mimeType)) {
                return;
            }
        }

        $extension->result = $this->urlGenerator->generateUrl(
            $extension->mediaPath,
            $extension->width,
            $extension->height
        );
    }
}
