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
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * @codeCoverageIgnore
 */
#[AsEventListener(ResolveRemoteThumbnailUrlExtension::NAME . '.pre')]
class RemoteThumbnailUrlResolver
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ConfigService $configService,
    ) {
    }

    public function __invoke(ResolveRemoteThumbnailUrlExtension $extension): void
    {
        if (!$this->configService->isEnabled()) {
            return;
        }

        $extension->stopPropagation();

        $extension->result = $this->urlGenerator->generateUrl(
            $extension->mediaPath,
            $extension->width,
            $extension->height
        );
    }
}
