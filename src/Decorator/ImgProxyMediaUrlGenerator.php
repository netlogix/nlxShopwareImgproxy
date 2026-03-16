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
use Shopware\Core\Framework\Feature;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

#[AsDecorator(decorates: AbstractMediaUrlGenerator::class)]
class ImgProxyMediaUrlGenerator extends AbstractMediaUrlGenerator
{
    private static bool $bypass = false;

    public function __construct(
        #[AutowireDecorated]
        private readonly AbstractMediaUrlGenerator $decorated,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ConfigService $configService,
    ) {
    }

    public function generate(array $paths): array
    {
        if (!$this->configService->isEnabled() || self::$bypass) {
            return $this->decorated->generate($paths);
        }

        $images = [];
        $default = [];
        if (
            (Feature::has('v6.8.0.0') && Feature::isActive('v6.8.0.0'))
            || Feature::isActive('UrlParams_has_mimeType')
        ) {
            /** @var UrlParams $path */
            foreach ($paths as $key => $path) {
                $mimeType = $path->mimeType ?? '';
                if ($this->urlGenerator->supportMimeType($mimeType)) {
                    $images[$key] = $path;
                } else {
                    $default[$key] = $path;
                }
            }
        } else {
            $images = $paths;
        }

        return [
            ...($images === [] ? [] : array_map(
                fn (UrlParams $path): string => $this->urlGenerator->generateUrl($path->path),
                $images
            )),
            ...($default === [] ? [] : $this->decorated->generate($default)),
        ];
    }

    public static function bypass(callable $callable): void
    {
        self::$bypass = true;
        $callable();
        self::$bypass = false;
    }
}
