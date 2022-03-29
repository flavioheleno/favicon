<?php
declare(strict_types = 1);

namespace Favicon\Test\Parser;

use Favicon\Parser\SizeParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SizeParserTest extends TestCase {
  public function testEmpty(): void {
    $sizeCol = SizeParser::parse('');

    $this->assertCount(0, $sizeCol);
  }

  public function testAny(): void {
    $sizeCol = SizeParser::parse('any');

    $this->assertCount(1, $sizeCol);
    $this->assertTrue($sizeCol[0]->isScalable());
    $this->assertSame(PHP_INT_MAX, $sizeCol[0]->getWidth());
    $this->assertSame(PHP_INT_MAX, $sizeCol[0]->getHeight());
  }

  public function testSingleValue(): void {
    $sizeCol = SizeParser::parse('192x192');

    $this->assertCount(1, $sizeCol);
    $this->assertFalse($sizeCol[0]->isScalable());
    $this->assertSame(192, $sizeCol[0]->getWidth());
    $this->assertSame(192, $sizeCol[0]->getHeight());
  }

  public function testMultipleValues(): void {
    $sizeCol = SizeParser::parse('16x16 24x24');

    $this->assertCount(2, $sizeCol);
    $this->assertFalse($sizeCol[0]->isScalable());
    $this->assertSame(16, $sizeCol[0]->getWidth());
    $this->assertSame(16, $sizeCol[0]->getHeight());
    $this->assertFalse($sizeCol[1]->isScalable());
    $this->assertSame(24, $sizeCol[1]->getWidth());
    $this->assertSame(24, $sizeCol[1]->getHeight());
  }

  public function testMixedValues(): void {
    $sizeCol = SizeParser::parse('32x32 any');

    $this->assertCount(2, $sizeCol);
    $this->assertFalse($sizeCol[0]->isScalable());
    $this->assertSame(32, $sizeCol[0]->getWidth());
    $this->assertSame(32, $sizeCol[0]->getHeight());
    $this->assertTrue($sizeCol[1]->isScalable());
    $this->assertSame(PHP_INT_MAX, $sizeCol[1]->getWidth());
    $this->assertSame(PHP_INT_MAX, $sizeCol[1]->getHeight());
  }

  public function testInvalidValue(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid size "xpto"');

    SizeParser::parse('xpto');
  }

  public function testMixedInvalidValue(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid size "xpto"');

    SizeParser::parse('10x10 xpto');
  }
}
