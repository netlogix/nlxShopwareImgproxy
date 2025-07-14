<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Tests\Unit\Service;

use Netlogix\NlxSwImgproxy\Enum\ResizeType;
use Netlogix\NlxSwImgproxy\Model\Image;
use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Netlogix\NlxSwImgproxy\Service\UrlGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlGenerator::class)]#
#[UsesClass(Image::class)]
class UrlGeneratorTest extends TestCase
{
    private UrlGenerator $subject;
    private ConfigService&MockObject $configService;

    protected function setUp(): void
    {
        $this->configService = $this->createMock(ConfigService::class);
        $this->subject = new UrlGenerator(
            $this->configService
        );
    }

    static public function generateUrlDataProvider(): iterable
    {
        yield 'without size' => [
            'imagePath' => 'test/image.jpg',
            'expectedUrl' => '#^https://image-proxy\.example\.com/insecure/[a-zA-Z0-9_-]+$#i',
        ];

        yield 'secure' => [
            'imagePath' => 'test/image.jpg',
            'key' => '16901a13d9b2f42d312122e2069f696c3c32fa9bf130002c8e902e1f09a9b5298ae28bd674272037e94a10e21000fa9748c32434dd2c9ccc32aa1c9ee897138e',
            'salt' => '0c603978ab5316d93df3a3ece0d76e3a7e8a81a0000c1820563f5274dad799e7faffeb8cc95bc999c5c1a000240ec1d89918a21139751bf808f5f77ea4134a47',
            'expectedUrl' => '#^https://image-proxy\.example\.com/([A-Za-z0-9_-]{43})/([a-zA-Z0-9_-]+)$#i',
        ];

        yield 'with resizeType' => [
            'imagePath' => 'test/image.jpg',
            'expectedUrl' => '#^https://image-proxy\.example\.com/insecure/rt:fill-down/[a-zA-Z0-9_-]+$#i',
            'resizeType' => ResizeType::fillDown
        ];

        yield 'with size' => [
            'imagePath' => 'test/image.jpg',
            'width' => '100',
            'height' => '200',
            'expectedUrl' => '#^https://image-proxy\.example\.com/insecure/w:100/h:200/[a-zA-Z0-9_-]+$#i',
        ];
    }

    #[DataProvider('generateUrlDataProvider')]
    public function testGenerateUrl(
        string $imagePath,
        string $expectedUrl,
        ?string $width = null,
        ?string $height = null,
        ?string $key = null,
        ?string $salt = null,
        string $baseUrl = 'https://image-proxy.example.com',
        ?ResizeType $resizeType = null
    ): void {
        $this->configService->expects(self::atLeastOnce())
            ->method('getBaseUrl')
            ->willReturn($baseUrl);
        $this->configService->expects(self::atLeastOnce())
            ->method('getImageSource')
            ->willReturn('');
        $this->configService->expects(self::atLeastOnce())
            ->method('getResizeType')
            ->willReturn($resizeType);

        $this->configService->method('isSecure')
            ->willReturn($key !== null);
        $this->configService
            ->method('getKey')
            ->willReturn($key);
        $this->configService
            ->method('getSalt')
            ->willReturn($salt);

        $generatedUrl = $this->subject->generateUrl($imagePath, $width, $height);

        $this->assertMatchesRegularExpression($expectedUrl, $generatedUrl);
    }
}
