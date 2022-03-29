<?php
declare(strict_types = 1);

namespace Favicon\Parser;

use Favicon\Icon\Icon;
use InvalidArgumentException;

final class PurposeParser {
  public static function parse(string $value): int {
    if ($value === '') {
      return Icon::PURPOSE_ANY;
    }

    $purpose  = 0x00;
    $purposes = explode(' ', strtolower($value));
    if (in_array('monochrome', $purposes)) {
      $purpose |= Icon::PURPOSE_MONOCHROME;
    }

    if (in_array('maskable', $purposes)) {
      $purpose |= Icon::PURPOSE_MASKABLE;
    }

    if (in_array('any', $purposes)) {
      $purpose |= Icon::PURPOSE_ANY;
    }

    return $purpose;
  }
}
