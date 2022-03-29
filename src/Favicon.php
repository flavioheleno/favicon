<?php
declare(strict_types = 1);

namespace Favicon;

use Favicon\Icon\Icon;
use Favicon\Icon\IconCollection;

final class Favicon {
  private string $url;
  private IconCollection $iconCollection;

  public function __construct(
    string $url,
    IconCollection $iconCollection
  ) {
    $this->url            = $url;
    $this->iconCollection = $iconCollection;
  }

  public function getUrl(): string {
    return $this->url;
  }

  public function getIconCollection(): IconCollection {
    return $this->iconCollection;
  }

  public function getByRelationship(string $relationship, int $purpose = Icon::PURPOSE_ANY): IconCollection {
    return $this->iconCollection->filter(
      function (Icon $icon) use ($relationship, $purpose): bool {
        return $icon->getRelationship() === $relationship && $icon->checkPurpose($purpose);
      }
    );
  }

  public function getByType(string $type, int $purpose = Icon::PURPOSE_ANY): IconCollection {
    return $this->iconCollection->filter(
      function (Icon $icon) use ($type, $purpose): bool {
        return $icon->getType() === $type && $icon->checkPurpose($purpose);
      }
    );
  }

  public function getHighestResolution(int $purpose = Icon::PURPOSE_ANY): ?Icon {
    if ($this->iconCollection->isEmpty()) {
      return null;
    }

    $filtered = $this->iconCollection->filter(
      function (Icon $icon) use ($purpose): bool {
        return $icon->checkPurpose($purpose);
      }
    );

    $scalable = $filtered->filter(
      function (Icon $icon): bool {
        return $icon->isScalable();
      }
    );

    if ($scalable->isEmpty() === false) {
      return $scalable->first();
    }

    $icon = null;
    $size = null;
    foreach ($filtered as $currIcon) {
      $currSize = $currIcon->getHighestResolution();
      if ($currSize === null) {
        continue;
      }

      if ($size === null || $size->getWidth() < $currSize->getWidth()) {
        $icon = $currIcon;
        $size = $currSize;
      }
    }

    if ($icon === null) {
      $this->iconCollection->first();
    }

    return $icon;
  }

  public function hasIcons(): bool {
    return $this->iconCollection->isEmpty() === false;
  }
}
