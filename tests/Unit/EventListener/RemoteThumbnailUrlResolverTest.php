<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Tests\Unit\EventListener;

use Composer\InstalledVersions;
use Netlogix\NlxSwImgproxy\EventListener\RemoteThumbnailUrlResolver;
use Netlogix\NlxSwImgproxy\Service\ConfigService;
use Netlogix\NlxSwImgproxy\Service\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Shopware\Core\Content\Media\Extension\ResolveRemoteThumbnailUrlExtension;
use Shopware\Core\Framework\DataAbstractionLayer\PartialEntity;
use Shopware\Core\Framework\Feature;

#[CoversClass(RemoteThumbnailUrlResolver::class)]
class  RemoteThumbnailUrlResolverTest extends \PHPUnit\Framework\TestCase
{
    private RemoteThumbnailUrlResolver $subject;
    private UrlGeneratorInterface&MockObject $urlGenerator;
    private ConfigService&MockObject $configService;

    function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->configService = $this->createMock(ConfigService::class);
        parent::setUp();
        $this->subject = new RemoteThumbnailUrlResolver(
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
        $event = $this->createEvent();

        $this->configService
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->urlGenerator
            ->expects(self::never())
        ->method('generateUrl');

        $this->subject->__invoke($event);

        self::assertEquals(null, $event->result);
    }

    function testEvent()
    {
        $event = $this->createEvent();

        $this->configService
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateUrl')
            ->with(
                $event->mediaPath,
                $event->width,
                $event->height
            )
            ->willReturn('http://example.com/test/image.jpg');

        $this->subject->__invoke($event);

        self::assertEquals('http://example.com/test/image.jpg', $event->result);
    }

    private function createEvent(): ResolveRemoteThumbnailUrlExtension
    {
        $shopwareVersion = InstalledVersions::getPrettyVersion('shopware/core') ?? '0.0.0';
        dd($shopwareVersion, version_compare($shopwareVersion, 'v6.7.3.0', '>='));
        return version_compare($shopwareVersion, 'v6.7.3.0', '>=') ? new ResolveRemoteThumbnailUrlExtension(
            mediaUrl: 'http://example.com/test/image.jpg',
            mediaPath: 'test/image.jpg',
            width: '100',
            height: '100',
            pattern: '{width}x{height}',
            mediaUpdatedAt: null,
            mediaEntity: new PartialEntity([])
        ) : new ResolveRemoteThumbnailUrlExtension(
            mediaUrl: 'http://example.com/test/image.jpg',
            mediaPath: 'test/image.jpg',
            width: '100',
            height: '100',
            pattern: '{width}x{height}',
            mediaUpdatedAt: null,
        );
    }
}
