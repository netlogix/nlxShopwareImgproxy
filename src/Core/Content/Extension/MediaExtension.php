<?php

declare(strict_types=1);

namespace Netlogix\NlxSwImgproxy\Core\Content\Extension;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('shopware.entity.extension')]
class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new JsonField('original_media', 'originalMedia'))
                ->addFlags(new Runtime(), new ApiAware(AdminApiSource::class))
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }

    public function getEntityName(): string
    {
        return MediaDefinition::ENTITY_NAME;
    }
}
