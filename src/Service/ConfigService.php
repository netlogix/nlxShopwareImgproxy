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

/**
 * @codeCoverageIgnore
 */
class ConfigService
{
    public const string CONFIG_DOMAIN = 'NlxSwImgproxy.config';

    public function __construct(
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public function isEnabled(?string $salesChannelId = null): bool
    {
        return $this->systemConfigService->getBool($this->getConfigKey('enable'), $salesChannelId)
            && $this->getBaseUrl($salesChannelId);
    }

    public function getBaseUrl(?string $salesChannelId = null): ?string
    {
        return $this->systemConfigService->getString($this->getConfigKey('baseUrl'), $salesChannelId)
            ?? null;
    }

    public function getImageSource(?string $salesChannelId = null): string
    {
        return $this->systemConfigService->getString($this->getConfigKey('imageSource'), $salesChannelId)
            ?? '';
    }

    public function getResizeType(?string $salesChannelId = null): ?ResizeType
    {
        $value = $this->systemConfigService->getString($this->getConfigKey('resizeType'), $salesChannelId);

        return $value ? ResizeType::from($value) : null;
    }

    public function isSecure(?string $salesChannelId = null): bool
    {
        return $this->getKey($salesChannelId) !== null
            && $this->getSalt($salesChannelId) !== null;
    }

    public function getKey(?string $salesChannelId = null): ?string
    {
        $key = $this->systemConfigService->getString($this->getConfigKey('key'), $salesChannelId)
            ?? null;

        return $key ? pack("H*", $key) : $key;
    }

    public function getSalt(?string $salesChannelId = null): ?string
    {
        $salt = $this->systemConfigService->getString($this->getConfigKey('salt'), $salesChannelId)
            ?? null;

        return $salt ? pack("H*", $salt) : $salt;
    }

    private function getConfigKey(string $key): string
    {
        return sprintf('%s.%s', self::CONFIG_DOMAIN, $key);
    }
}
