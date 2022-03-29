<?php
declare(strict_types = 1);

namespace Favicon\Utils;

use Innmind\UrlResolver\UrlResolver as Resolver;
use Innmind\Url\Url;

final class UrlResolver {
  private string $baseUrl;

  public function __construct(string $baseUrl) {
    $this->baseUrl = $baseUrl;
  }

  public function resolveUrl(string $url): string {
    $resolver = new Resolver();

    return $resolver(
      Url::of($this->baseUrl),
      Url::of($url)
    )->toString();
  }
}
