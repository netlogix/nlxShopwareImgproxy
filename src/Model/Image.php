<?php

declare(strict_types=1);

namespace Netlogix\NlxSwImgproxy\Model;

use Netlogix\NlxSwImgproxy\Enum\ResizeType;
use Stringable;

class Image implements Stringable
{
    public function __construct(
        protected string $imageSource,
        protected string $sourceUrl
    ) {
    }

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#preset
     */
    public ?array $preset = [];

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#filename
     */
    public ?string $filename = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#quality
     */
    public ?int $quality = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#format-quality
     */
    public array $formatQuality = [];

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#cache-buster
     */
    public ?string $cacheBuster = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#enlarge
     */
    public bool $enlarge = false;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#width
     */
    public ?int $width = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#height
     */
    public ?int $height = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#gravity
     */
    public ?string $gravity = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#auto-rotate
     */
    public bool $autoRotate = false;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#background
     */
    public ?string $background = null;

    /**
     * @link https://docs.imgproxy.net/latest/usage/processing#resizing-type
     */
    public ?ResizeType $resizeType = null;

    public function __toString(): string
    {
        $parts = [];

        if ($this->preset !== []) {
            $parts[] = 'pr:' . implode(':', $this->preset);
        }

        if ($this->filename !== null) {
            $parts[] = 'fn:' . $this->filename;
        }

        if ($this->quality !== null) {
            $parts[] = 'q:' . $this->quality;
        }

        if ($this->formatQuality !== []) {
            $parts[] = 'fq:' . implode(':', $this->formatQuality);
        }

        if ($this->cacheBuster !== null) {
            $parts[] = 'cb:' . $this->cacheBuster;
        }

        if ($this->enlarge) {
            $parts[] = 'e:1';
        }

        if ($this->gravity !== null) {
            $parts[] = 'g:' . $this->gravity;
        }

        if ($this->autoRotate) {
            $parts[] = 'ar:1';
        }

        if ($this->background !== null) {
            $parts[] = 'bg:' . $this->background;
        }

        if ($this->resizeType instanceof ResizeType) {
            $parts[] = 'rt:' . $this->resizeType->value;
        }

        if ($this->width !== null) {
            $parts[] = 'w:' . $this->width;
        }

        if ($this->height !== null) {
            $parts[] = 'h:' . $this->height;
        }

        $parts[] = $this->encodeUrl($this->sourceUrl);

        return implode('/', $parts);
    }

    protected function encodeUrl(string $url): string
    {
        return rtrim(strtr(base64_encode($this->imageSource . $url), '+/', '-_'), '=');
    }
}
