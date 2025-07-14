<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Decorator;

use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Netlogix\NlxSwImgproxy\Service\UrlGeneratorInterface;
use Shopware\Core\Content\Media\Core\Application\AbstractMediaUrlGenerator;
use Shopware\Core\Content\Media\Core\Params\UrlParams;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

#[AsDecorator(decorates: AbstractMediaUrlGenerator::class)]
class ImgProxyMediaUrlGenerator extends AbstractMediaUrlGenerator
{
    public function __construct(
        #[AutowireDecorated]
        private readonly AbstractMediaUrlGenerator $decorated,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ConfigService $configService,
    ) {
    }

    public function generate(array $paths): array
    {
        if (!$this->configService->isEnabled()) {
            return $this->decorated->generate($paths);
        }

        return array_map(fn (UrlParams $value): string => $this->urlGenerator->generateUrl($value->path), $paths);
    }
}
