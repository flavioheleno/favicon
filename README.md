# favicon

A library to parse pages and web manifests to extract favicon images

## Install

The best way to install the library is using the [Composer Package Manager](https://getcomposer.org/).

To install, run the following command:

```shell
php composer.phar require flavioheleno/favicon
```

This package is not tied to any specific library that sends HTTP Messages. Instead, it uses
[PSR-17](https://www.php-fig.org/psr/psr-17/) (HTTP Factories) and [PSR-18](https://www.php-fig.org/psr/psr-18/) (HTTP
Clients) to let developers choose whichever PSR-17 and PSR-18 implementations they want to use.

If you don't use any specific PSR-17 or PSR-18 libraries and want to get started right away you should run the following
command:

```shell
php composer.phar require flavioheleno/favicon kriswallsmith/buzz nyholm/psr7
```

This will install the library itself along with a PSR-17 implementation ([nyholm/psr7](https://github.com/nyholm/psr7))
and a PSR-18 implementation ([kriswallsmith/buzz](https://github.com/kriswallsmith/Buzz)).

You can replace those libraries with any alternative library that provides
[HTTP Message Implementation (PSR-17)](https://packagist.org/providers/psr/http-factory-implementation) or
[HTTP Client Implementantion (PSR-18)](https://packagist.org/providers/psr/http-client-implementation).

## Usage

### Extract all Icons

```php
use Buzz\Client\Curl;
use Favicon\Extractor;
use Nyholm\Psr7\Factory\Psr17Factory;

$extractor = new Extractor(
  new Curl(new Psr17Factory()),
  new Psr17Factory()
);

$favicon = $extractor->from('https://twitter.com');
foreach ($favicon->getIconCollection() as $icon) {
  echo $icon->getRelationship(), ': ', $icon->getUrl(), PHP_EOL;
}
```

```text
manifest: https://abs.twimg.com/responsive-web/client-web-legacy/icon-default.ee534d85.png
manifest: https://abs.twimg.com/responsive-web/client-web-legacy/icon-default-large.8e027b65.png
manifest: https://abs.twimg.com/responsive-web/client-web-legacy/icon-default-maskable.2fd29c85.png
manifest: https://abs.twimg.com/responsive-web/client-web-legacy/icon-default-maskable-large.ee2b7aa5.png
shortcut icon: http://abs.twimg.com/favicons/twitter.2.ico
apple-touch-icon: https://abs.twimg.com/responsive-web/client-web-legacy/icon-ios.b1fc7275.png
```

## License

This library is licensed under the [MIT License](LICENSE).
