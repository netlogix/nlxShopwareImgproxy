<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Tests\Unit\Decorator;

use Netlogix\NlxSwImgproxy\Decorator\ImgProxyMediaUrlGenerator;
use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Netlogix\NlxSwImgproxy\Service\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Core\Application\AbstractMediaUrlGenerator;
use Shopware\Core\Content\Media\Core\Params\UrlParams;
use Shopware\Core\Content\Media\Core\Params\UrlParamsSource;

#[CoversClass(ImgProxyMediaUrlGenerator::class)]
class ImgProxyMediaUrlGeneratorTest extends TestCase
{
    private ImgProxyMediaUrlGenerator $subject;

    private AbstractMediaUrlGenerator&MockObject $decorated;
    private  UrlGeneratorInterface&MockObject $urlGenerator;
    private  ConfigService&MockObject $configService;

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

    public function testGenerate(): void
    {
        $path1 = new UrlParams('1', UrlParamsSource::MEDIA, 'test/image.jpg');
        $path2 = new UrlParams('2', UrlParamsSource::MEDIA, 'test/image2.jpg');

        $paths = [$path1, $path2];

        $this->configService->method('isEnabled')->willReturn(true);
        $this->decorated->expects($this->never())
            ->method('generate');

        $this->urlGenerator->expects($this->atLeastOnce())
            ->method('generateUrl')
            ->willReturnCallback(fn ($path): string =>
                match ($path){
                    $path1->path => 'imgproxyUrl1',
                    $path2->path => 'imgproxyUrl2',
                }
            );


        $result = $this->subject->generate($paths);

        self::assertSame(['imgproxyUrl1', 'imgproxyUrl2'], $result);
    }
}
