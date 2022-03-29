<?php
declare(strict_types = 1);

namespace Favicon\Parser;

use Favicon\Size\Size;
use Favicon\Size\SizeCollection;
use InvalidArgumentException;

final class SizeParser {
  public static function parse(string $value): SizeCollection {
    $sizeCol = new SizeCollection();
    if ($value === '') {
      return $sizeCol;
    }

    $sizes = explode(' ', strtolower($value));
    foreach ($sizes as $size) {
      if ($size === 'any') {
        $sizeCol->add(
          new Size(
            PHP_INT_MAX,
            PHP_INT_MAX,
            true
          )
        );

        continue;
      }

      $matches = [];
      if (preg_match('/^([0-9]+)x([0-9]+)$/', $size, $matches) !== 1) {
        throw new InvalidArgumentException(
          sprintf(
            'Invalid size "%s"',
            $size
          )
        );
      }

      $sizeCol->add(
        new Size(
          (int)$matches[1],
          (int)$matches[2]
        )
      );
    }

    return $sizeCol;
  }
}
