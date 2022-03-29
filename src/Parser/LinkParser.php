<?php
declare(strict_types = 1);

namespace Favicon\Parser;

use DOMNode;
use Favicon\Icon\Icon;
use Favicon\Icon\IconCollection;
use Favicon\Size\SizeCollection;
use Favicon\Utils\UrlResolver;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/link
 */
final class LinkParser {
  private UrlResolver $urlResolver;

  public function __construct(UrlResolver $urlResolver) {
    $this->urlResolver = $urlResolver;
  }

  public function parse(DOMNode $node): IconCollection {
    if ($node->hasAttribute('href') === false) {
      throw new InvalidArgumentException('Link tag has no "href" attribute');
    }

    return new IconCollection(
      [
        new Icon(
          $node->getAttribute('rel'),
          $this->urlResolver->resolveUrl($node->getAttribute('href')),
          match ($node->hasAttribute('sizes')) {
            true  => SizeParser::parse($node->getAttribute('sizes')),
            false => new SizeCollection()
          },
          match ($node->hasAttribute('type')) {
            true  => $node->getAttribute('type'),
            false => ''
          },
          match ($node->hasAttribute('purpose')) {
            true  => PurposeParser::parse($node->getAttribute('purpose')),
            false => Icon::PURPOSE_ANY
          }
        )
      ]
    );
  }
}
