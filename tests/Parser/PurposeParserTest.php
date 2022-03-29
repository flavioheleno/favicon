<?php
declare(strict_types = 1);

namespace Favicon\Test\Parser;

use Favicon\Icon\Icon;
use Favicon\Parser\PurposeParser;
use PHPUnit\Framework\TestCase;

final class PurposeParserTest extends TestCase {
  public function testEmpty(): void {
    $purpose = PurposeParser::parse('');

    $this->assertSame(Icon::PURPOSE_ANY, $purpose);
  }

  public function testAny(): void {
    $purpose = PurposeParser::parse('any');

    $this->assertSame(Icon::PURPOSE_ANY, $purpose);
  }

  public function testMonochrome(): void {
    $purpose = PurposeParser::parse('monochrome');

    $this->assertSame(Icon::PURPOSE_MONOCHROME, $purpose);
  }

  public function testMaskable(): void {
    $purpose = PurposeParser::parse('maskable');

    $this->assertSame(Icon::PURPOSE_MASKABLE, $purpose);
  }

  public function testMixedValues(): void {
    $purpose = PurposeParser::parse('maskable monochrome');

    $this->assertSame(Icon::PURPOSE_MASKABLE, $purpose & Icon::PURPOSE_MASKABLE);
    $this->assertNotSame(Icon::PURPOSE_MASKABLE, $purpose);
    $this->assertSame(Icon::PURPOSE_MONOCHROME, $purpose & Icon::PURPOSE_MONOCHROME);
    $this->assertNotSame(Icon::PURPOSE_MONOCHROME, $purpose);
  }
}
