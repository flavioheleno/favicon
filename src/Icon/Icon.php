<?php
declare(strict_types = 1);

namespace Favicon\Icon;

use Favicon\Size\Size;
use Favicon\Size\SizeCollection;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest/icons
 */
final class Icon {
  /**
   * MONOCHROME: A user agent can present this icon where a monochrome icon with a solid fill is needed. The color
   * information in the icon is discarded and only the alpha data is used. The icon can then be used by the user
   * agent like a mask over any solid fill.
   */
  public const PURPOSE_MONOCHROME = 0x04;
  /**
   * MASKABLE: The image is designed with icon masks and safe zone in mind, such that any part of the image outside the
   * safe zone can safely be ignored and masked away by the user agent.
   */
  public const PURPOSE_MASKABLE = 0x02;
  /**
   * ANY: The user agent is free to display the icon in any context (this is the default value).
   */
  public const PURPOSE_ANY = 0x01;

  /**
   * This attribute names a relationship of the linked document to the current document.
   *
   * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Link_types
   */
  private string $relationship;
  /**
   * The path to the image file. If src is a relative URL, the base URL will be the URL of the manifest.
   */
  private string $url;
  /**
   * A collection of image dimensions.
   */
  private SizeCollection $sizeCollection;
  /**
   * A hint as to the media type of the image.
   * The purpose of this member is to allow a user agent to quickly ignore images with media types it does not support.
   */
  private string $type;
  /**
   * Defines the purpose of the image, for example if the image is intended to serve some special purpose in the
   * context of the host OS (i.e., for better integration).
   */
  private int $purpose;

  public function __construct(
    string $relationship,
    string $url,
    SizeCollection $sizeCollection = new SizeCollection(),
    string $type = '',
    int $purpose = self::PURPOSE_ANY
  ) {
    $this->relationship   = $relationship;
    $this->url            = $url;
    $this->sizeCollection = $sizeCollection;
    $this->type           = $type;
    $this->purpose        = $purpose;
  }

  public function getRelationship(): string {
    return $this->relationship;
  }

  public function getUrl(): string {
    return $this->url;
  }

  public function getSizeCollection(): SizeCollection {
    return $this->sizeCollection;
  }

  public function getType(): string {
    return $this->type;
  }

  public function getHighestResolution(): ?Size {
    if ($this->sizeCollection->isEmpty()) {
      return null;
    }

    $sorted = $this->sizeCollection->sort('getWidth', SizeCollection::SORT_DESC);

    return $sorted->first();
  }

  public function checkPurpose(int $purpose): bool {
    return ($this->purpose & $purpose) === $purpose;
  }

  public function isMonochrome(): bool {
    return ($this->purpose & self::PURPOSE_MONOCHROME) === self::PURPOSE_MONOCHROME;
  }

  public function isMaskable(): bool {
    return ($this->purpose & self::PURPOSE_MASKABLE) === self::PURPOSE_MASKABLE;
  }

  public function isAnyPurpose(): bool {
    return ($this->purpose & self::PURPOSE_ANY) === self::PURPOSE_ANY;
  }

  public function isScalable(): bool {
    $scalable = $this->sizeCollection->filter(
      function (Size $size): bool {
        return $size->isScalable();
      }
    );

    return $scalable->isEmpty() === false;
  }
}
