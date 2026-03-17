<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Tests\Unit\Decorator;

use Composer\InstalledVersions;
use Netlogix\NlxSwImgproxy\Decorator\ImgProxyMediaUrlGenerator;
use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Netlogix\NlxSwImgproxy\Service\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Core\Application\AbstractMediaUrlGenerator;
use Shopware\Core\Content\Media\Core\Params\UrlParams;
use Shopware\Core\Content\Media\Core\Params\UrlParamsSource;
use Shopware\Core\Framework\Feature;

#[CoversClass(ImgProxyMediaUrlGenerator::class)]
class ImgProxyMediaUrlGeneratorTest extends TestCase
{
    private ImgProxyMediaUrlGenerator $subject;

    private AbstractMediaUrlGenerator&MockObject $decorated;

    private UrlGeneratorInterface&MockObject $urlGenerator;

    private ConfigService&MockObject $configService;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(AbstractMediaUrlGenerator::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->configService = $this->createMock(ConfigService::class);

        $this->subject = new ImgProxyMediaUrlGenerator(
            $this->decorated,
            $this->urlGenerator,
            $this->configService
        );

        Feature::registerFeature('UrlParams_has_mimeType');
        Feature::setActive('UrlParams_has_mimeType', false);
    }

    protected function tearDown(): void
    {
        Feature::setActive('UrlParams_has_mimeType', false);
    }

    public function testIfDisabled(): void
    {
        $paths = [
            new UrlParams('1', UrlParamsSource::MEDIA, 'test/image.jpg'),
            new UrlParams('2', UrlParamsSource::MEDIA, 'test/image2.jpg'),
        ];

        $this->configService->method('isEnabled')->willReturn(false);
        $this->decorated->expects($this->once())
            ->method('generate')
            ->with($paths)
            ->willReturn(['url1', 'url2']);
        $result = $this->subject->generate($paths);

        self::assertSame(['url1', 'url2'], $result);
    }

    public static function SW6Version(): iterable
    {
        $shopwareVersion = InstalledVersions::getPrettyVersion('shopware/core') ?? '0.0.0';

        yield 'v6.7' => [
            'version' => 'v6.7',
            'paths' => [
                new UrlParams('1', UrlParamsSource::MEDIA, 'test/image.jpg'),
                new UrlParams('2', UrlParamsSource::MEDIA, 'test/image2.jpg'),
            ],
        ];

        if (version_compare($shopwareVersion, 'v6.8.0.0', '>=')) {
            yield 'v6.8' => [
                'version' => 'v6.8',
                'paths' => [
                    new UrlParams('1', UrlParamsSource::MEDIA, 'test/image.jpg', mimeType: 'image/jpeg'),
                    new UrlParams('2', UrlParamsSource::MEDIA, 'test/image2.jpg', mimeType: 'image/jpeg'),
                ],
            ];
        }
    }

    #[DataProvider('SW6Version')]
    public function testGenerate(string $version, array $paths): void
    {
        $this->configService->method('isEnabled')->willReturn(true);
        $this->decorated->expects($this->never())
            ->method('generate');

        if ($version === 'v6.8') {
            Feature::setActive('UrlParams_has_mimeType', true);
            $this->urlGenerator->expects($this->atLeastOnce())
                ->method('supportMimeType')
                ->with('image/jpeg')
                ->willReturn(true);
        }

        $this->urlGenerator->expects($this->atLeastOnce())
            ->method('generateUrl')
            ->willReturnCallback(fn ($path): string => match ($path) {
                $paths[0]->path => 'imgproxyUrl1',
                $paths[1]->path => 'imgproxyUrl2',
            });

        $result = $this->subject->generate($paths);

        self::assertSame(['imgproxyUrl1', 'imgproxyUrl2'], $result);
    }

    public function testGenerateSkip(): void
    {
        $shopwareVersion = InstalledVersions::getPrettyVersion('shopware/core') ?? '0.0.0';

        if (version_compare($shopwareVersion, 'v6.8.0.0', '<')) {
            $this->markTestSkipped('This test is only relevant for Shopware versions < 6.8.9');
        }

        Feature::setActive('UrlParams_has_mimeType', true);

        $path1 = new UrlParams('1', UrlParamsSource::MEDIA, 'test/image.jpg', mimeType: 'foo/bar');

        $paths = [$path1];

        $this->configService->method('isEnabled')->willReturn(true);

        $this->urlGenerator->expects($this->atLeastOnce())
            ->method('supportMimeType')
            ->with('foo/bar')
            ->willReturn(false);

        $this->decorated->expects($this->once())
            ->method('generate')
            ->with($paths)
            ->willReturn(['imgproxyUrl1']);

        $this->urlGenerator->expects($this->never())
            ->method('generateUrl');

        $result = $this->subject->generate($paths);

        self::assertSame(['imgproxyUrl1'], $result);
    }
}
