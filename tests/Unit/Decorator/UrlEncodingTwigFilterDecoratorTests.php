<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Test\Unit\Decorator;

use Netlogix\NlxSwImgproxy\Decorator\UrlEncodingTwigFilterDecorator;
use Netlogix\NlxSwImgproxy\Service\ConfigService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlEncodingTwigFilterDecorator::class)]
class UrlEncodingTwigFilterDecoratorTests extends TestCase
{
    private ConfigService $configService;
    private UrlEncodingTwigFilterDecorator $subject;

    protected function setUp(): void
    {
        $this->configService = $this->createMock(ConfigService::class);
        $this->subject = new UrlEncodingTwigFilterDecorator($this->configService);
    }

    #[DataProvider('provideEncodeUrlData')]
    public function testEncodeUrl(
        bool $isEnabled,
        string $baseUrl,
        ?string $mediaUrl,
        ?string $expectedResult
    ): void {

        $this->configService->method('isEnabled')->willReturn($isEnabled);
        $this->configService->method('getBaseUrl')->willReturn($baseUrl);

        $result = $this->subject->encodeUrl($mediaUrl);

        $this->assertSame($expectedResult, $result);
    }

    public static function provideEncodeUrlData(): array
    {
        return [
            'feature disabled' => [
                'isEnabled' => false,
                'baseUrl' => 'https://imgproxy.example.test',
                'mediaUrl' => 'https://test.test/width:200&height:200',
                'expectedResult' => 'https://test.test/width%3A200%26height%3A200',
            ],
            'empty baseUrl' => [
                'isEnabled' => true,
                'baseUrl' => '',
                'mediaUrl' => 'https://test.test/width:200&height:200',
                'expectedResult' => 'https://test.test/width%3A200%26height%3A200',
            ],
            'URL contains baseUrl' => [
                'isEnabled' => true,
                'baseUrl' => 'https://imgproxy.example.test',
                'mediaUrl' => 'https://imgproxy.example.test/width:200&height:200',
                'expectedResult' => 'https://imgproxy.example.test/width:200&height:200',
            ],
            'URL does not contain baseUrl' => [
                'isEnabled' => true,
                'baseUrl' => 'https://imgproxy.example.test',
                'mediaUrl' => 'https://test.test/width:200&height:200',
                'expectedResult' => 'https://test.test/width%3A200%26height%3A200',
            ],
            'null mediaUrl' => [
                'isEnabled' => true,
                'baseUrl' => 'https://imgproxy.example.test',
                'mediaUrl' => null,
                'expectedResult' => null,
            ],
        ];
    }
}
