<?php

declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace Netlogix\NlxSwImgproxy\Enum;

/**
 * @codeCoverageIgnore
 */
enum ResizeType: string
{
    case fit = 'fit'; # resizes the image while keeping aspect ratio to fit a given size.
    case fill = 'fill'; # resizes the image while keeping aspect ratio to fill a given size and crops projecting parts.
    case fillDown = 'fill-down'; # the same as fill, but if the resized image is smaller than the requested size, imgproxy will crop the result to keep the requested aspect ratio.
    case force = 'force'; # resizes the image without keeping the aspect ratio.
    case auto = 'auto'; # if both source and resulting dimensions have the same orientation (portrait or landscape), imgproxy will use fill. Otherwise, it will use fit.
}
