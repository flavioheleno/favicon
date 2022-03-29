<?php
declare(strict_types = 1);

namespace Favicon\Parser;

use DOMNode;
use Favicon\Icon\Icon;
use Favicon\Icon\IconCollection;
use Favicon\Utils\UrlResolver;
use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest
 */
final class ManifestParser {
  private ClientInterface $client;
  private RequestFactoryInterface $requestFactory;
  private UrlResolver $urlResolver;

  public function __construct(
    ClientInterface $client,
    RequestFactoryInterface $requestFactory,
    UrlResolver $urlResolver
  ) {
    $this->client         = $client;
    $this->requestFactory = $requestFactory;
    $this->urlResolver    = $urlResolver;
  }

  public function parse(DOMNode $node): IconCollection {
    if ($node->hasAttribute('href') === false) {
      throw new InvalidArgumentException('Manifest link has no "href" attribute');
    }

    $link = $this->urlResolver->resolveUrl($node->getAttribute('href'));

    $request = $this->requestFactory->createRequest('GET', $link);
    $request = $request->withHeader(
      'User-Agent',
      sprintf(
        'favicon/0.1 (php-%s; %s) https://github.com/flavioheleno/favicon',
        PHP_SAPI,
        PHP_VERSION
      )
    );
    $response = $this->client->sendRequest($request);
    if ($response->getStatusCode() !== 200) {
      throw new RuntimeException(
        sprintf(
          'Failed to download manifest from "%s"',
          $link
        )
      );
    }

    $json = json_decode((string)$response->getBody(), true);
    if ($json === false) {
      throw new InvalidArgumentException('Invalid manifest format');
    }

    $iconCol = new IconCollection();
    if (isset($json['icons']) === false) {
      return $iconCol;
    }

    foreach ($json['icons'] as $icon) {
      if (isset($icon['src']) === false) {
        // skip icons that do not set "src"
        continue;
      }

      $iconCol->add(
        new Icon(
          'manifest',
          $this->urlResolver->resolveUrl($icon['src']),
          SizeParser::parse($icon['sizes'] ?? ''),
          $icon['type'] ?? ''
        )
      );
    }

    return $iconCol;
  }
}
