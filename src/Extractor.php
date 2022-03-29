<?php
declare(strict_types = 1);

namespace Favicon;

use DOMDocument;
use Favicon\Icon\IconCollection;
use Favicon\Parser\LinkParser;
use Favicon\Parser\ManifestParser;
use Favicon\Utils\UrlResolver;
use Masterminds\HTML5;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;

final class Extractor {
  private ClientInterface $client;
  private RequestFactoryInterface $requestFactory;
  private bool $followRedirects = true;

  public function __construct(
    ClientInterface $client,
    RequestFactoryInterface $requestFactory,
  ) {
    $this->client         = $client;
    $this->requestFactory = $requestFactory;
  }

  public function enableFollowRedirects(): self {
    $this->followRedirects = true;

    return $this;
  }

  public function disableFollowRedirects(): self {
    $this->followRedirects = false;

    return $this;
  }

  public function from(string $url): Favicon {
    $urlResolver = new UrlResolver($url);
    do {
      $request = $this->requestFactory->createRequest('GET', $url);
      $request = $request->withHeader(
        'User-Agent',
        sprintf(
          'favicon/0.1 (php-%s; %s) https://github.com/flavioheleno/favicon',
          PHP_SAPI,
          PHP_VERSION
        )
      );

      $response = $this->client->sendRequest($request);
      if ($this->followRedirects && $response->hasHeader('location')) {
        $url = $urlResolver->resolveUrl($response->getHeaderLine('location'));

        // keep up with url changes
        $urlResolver = new UrlResolver($url);
      }
    } while ($this->followRedirects && in_array($response->getStatusCode(), [301, 302]));

    if ($response->getStatusCode() !== 200) {
      throw new RuntimeException(
        sprintf(
          'Failed to retrieve page contents for "%s": %s',
          $url,
          $response->getReasonPhrase()
        )
      );
    }

    $html5 = new HTML5();
    $dom = $html5->loadHTML((string)$response->getBody());
    if ($dom === false) {
      throw new RuntimeException(
        sprintf(
          'Failed to parse page contents for "%s"',
          $url
        )
      );
    }

    $iconCollection = new IconCollection();

    $linkParser = new LinkParser($urlResolver);
    $manifestParser = new ManifestParser(
      $this->client,
      $this->requestFactory,
      $urlResolver
    );

    $nodes = $dom->getElementsByTagName('link');
    foreach ($nodes as $node) {
      $relationship = $node->getAttribute('rel');
      if (preg_match('/icon$/', $relationship) === 1) {
        $iconCollection = $iconCollection->merge(
          $linkParser->parse($node)
        );

        continue;
      }

      if ($relationship === 'manifest') {
        $iconCollection = $iconCollection->merge(
          $manifestParser->parse($node)
        );
      }
    }

    return new Favicon($url, $iconCollection);
  }
}
