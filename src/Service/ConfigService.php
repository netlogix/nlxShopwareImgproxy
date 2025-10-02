<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Service;

use Netlogix\NlxSwImgproxy\Enum\ResizeType;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @codeCoverageIgnore
 */
class ConfigService
{
    public const string CONFIG_DOMAIN = 'NlxSwImgproxy.config';

    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        #[Autowire(param: 'shopware.media.remote_thumbnails.enable')]
        private readonly bool $remoteThumbnailsEnable = false
    ) {
    }

    public function isEnabled(?string $salesChannelId = null): bool
    {
        return $this->systemConfigService->getBool($this->getConfigKey('enable'), $salesChannelId)
            && $this->getBaseUrl($salesChannelId) !== null && $this->remoteThumbnailsEnable;
    }

    public function getBaseUrl(?string $salesChannelId = null): ?string
    {
        return $this->systemConfigService->getString($this->getConfigKey('baseUrl'), $salesChannelId);
    }

    public function getImageSource(?string $salesChannelId = null): string
    {
        return $this->systemConfigService->getString($this->getConfigKey('imageSource'), $salesChannelId);
    }

    public function getResizeType(?string $salesChannelId = null): ?ResizeType
    {
        $value = $this->systemConfigService->getString($this->getConfigKey('resizeType'), $salesChannelId);

        return $value !== '' ? ResizeType::from($value) : null;
    }

    public function isSecure(?string $salesChannelId = null): bool
    {
        return $this->getKey($salesChannelId) !== null
            && $this->getSalt($salesChannelId) !== null;
    }

    public function getKey(?string $salesChannelId = null): ?string
    {
        $key = $this->systemConfigService->getString($this->getConfigKey('key'), $salesChannelId);

        return $key !== '' ? pack("H*", $key) : $key;
    }

    public function getSalt(?string $salesChannelId = null): ?string
    {
        $salt = $this->systemConfigService->getString($this->getConfigKey('salt'), $salesChannelId);

        return $salt !== '' ? pack("H*", $salt) : $salt;
    }

    private function getConfigKey(string $key): string
    {
        return sprintf('%s.%s', self::CONFIG_DOMAIN, $key);
    }
}
