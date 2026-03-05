<?php

namespace Netlogix\NlxSwImgproxy\EventListener;

use Netlogix\NlxSwImgproxy\Core\Content\Entity\OriginalMediaEntity;
use Netlogix\NlxSwImgproxy\Decorator\ImgProxyMediaUrlGenerator;
use Shopware\Core\Content\Media\Core\Application\AbstractMediaUrlGenerator;
use Shopware\Core\Content\Media\Core\Params\UrlParams;
use Shopware\Core\Content\Media\Infrastructure\Path\MediaUrlGenerator;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaEvents;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(MediaEvents::MEDIA_LOADED_EVENT)]
class OriginalMediaUrlHydrator
{
    public function __construct(
        private readonly AbstractMediaUrlGenerator $mediaUrlGenerator,
    ) {
    }

    public function __invoke(EntityLoadedEvent $event)
    {
        /** @var MediaEntity $mediaEntity */
        foreach ($event->getEntities() as $mediaEntity) {
            $url = '';
            if ($mediaEntity->hasPath() && $this->mediaUrlGenerator instanceof ImgProxyMediaUrlGenerator) {
                $url = $this->mediaUrlGenerator->generate([UrlParams::fromMedia($mediaEntity)], true);
            }

            $mediaEntity->addArrayExtension('originalMedia', [
                'originalUrl' => $url,
            ]);
        }
    }
}