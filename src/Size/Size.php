<?php
declare(strict_types = 1);

namespace Favicon\Size;

final class Size {
  private int $width;
  private int $height;
  private bool $scalable;

  public function __construct(int $width = PHP_INT_MAX, int $height = PHP_INT_MAX, bool $scalable = false) {
    $this->width    = $width;
    $this->height   = $height;
    $this->scalable = $scalable;
  }

  public function getWidth(): int {
    return $this->width;
  }

  public function getHeight(): int {
    return $this->height;
  }

  // sizes="any"
  public function isScalable(): bool {
    return $this->scalable;
  }
}
