<?php
declare(strict_types = 1);

namespace Favicon\Icon;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends \Ramsey\Collection\AbstractCollection<\Favicon\Icon\Icon>
 */
final class IconCollection extends AbstractCollection {
  public function getType(): string {
    return Icon::class;
  }
}
