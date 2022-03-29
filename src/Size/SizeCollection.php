<?php
declare(strict_types = 1);

namespace Favicon\Size;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends \Ramsey\Collection\AbstractCollection<\Favicon\Size\Size>
 */
final class SizeCollection extends AbstractCollection {
  public function getType(): string {
    return Size::class;
  }
}
