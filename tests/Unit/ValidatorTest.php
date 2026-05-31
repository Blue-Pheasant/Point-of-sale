<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Core\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the unified {@see Validator} (roadmap T09).
 *
 * Each rule is exercised on both the passing and the failing branch
 * (negative cases), per the T17 acceptance notes.
 */
final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    // -- required ----------------------------------------------------------

    public function testRequiredPassesWhenValuePresent(): void
    {
        $this->assertTrue($this->validator->validate(
            ['name' => 'Coffee'],
            ['name' => [Validator::RULE_REQUIRED]]
        ));
    }

    /**
     * @dataProvider emptyValueProvider
     */
    public function testRequiredFailsWhenValueEmpty(mixed $value): void
    {
        $this->assertFalse($this->validator->validate(
            ['name' => $value],
            ['name' => [Validator::RULE_REQUIRED]]
        ));
        $this->assertTrue($this->validator->hasError('name'));
    }

    /** @return array<string, array{0: mixed}> */
    public static function emptyValueProvider(): array
    {
        return [
            'null'         => [null],
            'empty string' => [''],
            'false'        => [false],
        ];
    }

    public function testRequiredFailsWhenAttributeMissing(): void
    {
        $this->assertFalse($this->validator->validate(
            [],
            ['name' => [Validator::RULE_REQUIRED]]
        ));
    }

    // -- email -------------------------------------------------------------

    public function testEmailPassesForValidAddress(): void
    {
        $this->assertTrue($this->validator->validate(
            ['email' => 'user@example.com'],
            ['email' => [Validator::RULE_EMAIL]]
        ));
    }

    public function testEmailFailsForInvalidAddress(): void
    {
        $this->assertFalse($this->validator->validate(
            ['email' => 'not-an-email'],
            ['email' => [Validator::RULE_EMAIL]]
        ));
        $this->assertTrue($this->validator->hasError('email'));
    }

    public function testEmailIsSkippedWhenEmpty(): void
    {
        // email is only validated when present; "required" enforces presence.
        $this->assertTrue($this->validator->validate(
            ['email' => ''],
            ['email' => [Validator::RULE_EMAIL]]
        ));
    }

    // -- min / max (string length) ----------------------------------------

    public function testMinPassesAtBoundary(): void
    {
        $this->assertTrue($this->validator->validate(
            ['pwd' => '12345'],
            ['pwd' => ['min:5']]
        ));
    }

    public function testMinFailsWhenTooShort(): void
    {
        $this->assertFalse($this->validator->validate(
            ['pwd' => '1234'],
            ['pwd' => ['min:5']]
        ));
        $this->assertSame('Ít nhất 5 ký tự.', $this->validator->getFirstError('pwd'));
    }

    public function testMaxPassesAtBoundary(): void
    {
        $this->assertTrue($this->validator->validate(
            ['name' => 'abc'],
            ['name' => ['max:3']]
        ));
    }

    public function testMaxFailsWhenTooLong(): void
    {
        $this->assertFalse($this->validator->validate(
            ['name' => 'abcd'],
            ['name' => ['max:3']]
        ));
    }

    public function testMinMaxAcceptArrayDescriptors(): void
    {
        $this->assertTrue($this->validator->validate(
            ['name' => 'abc'],
            ['name' => [[Validator::RULE_MIN, 'min' => 2], [Validator::RULE_MAX, 'max' => 5]]]
        ));
    }

    // -- match -------------------------------------------------------------

    public function testMatchPassesWhenValuesEqual(): void
    {
        $this->assertTrue($this->validator->validate(
            ['password' => 'secret', 'confirm' => 'secret'],
            ['confirm' => ['match:password']]
        ));
    }

    public function testMatchFailsWhenValuesDiffer(): void
    {
        $this->assertFalse($this->validator->validate(
            ['password' => 'secret', 'confirm' => 'other'],
            ['confirm' => ['match:password']]
        ));
        $this->assertTrue($this->validator->hasError('confirm'));
    }

    // -- number / numeric / integer ---------------------------------------

    public function testNumberPassesForNumericValue(): void
    {
        $this->assertTrue($this->validator->validate(
            ['qty' => '42'],
            ['qty' => [Validator::RULE_NUMBER]]
        ));
    }

    public function testNumberFailsForNonNumericValue(): void
    {
        $this->assertFalse($this->validator->validate(
            ['qty' => 'abc'],
            ['qty' => [Validator::RULE_NUMBER]]
        ));
    }

    public function testIntegerFailsForFloatString(): void
    {
        $this->assertFalse($this->validator->validate(
            ['qty' => '4.5'],
            ['qty' => [Validator::RULE_INTEGER]]
        ));
    }

    // -- minint / maxint (numeric value) ----------------------------------

    public function testMinValueFailsBelowFloor(): void
    {
        $this->assertFalse($this->validator->validate(
            ['stock' => '-1'],
            ['stock' => [[Validator::RULE_MIN_VALUE, 'minint' => 0]]]
        ));
    }

    public function testMaxValueFailsAboveCeiling(): void
    {
        $this->assertFalse($this->validator->validate(
            ['stock' => '1000'],
            ['stock' => [[Validator::RULE_MAX_VALUE, 'maxint' => 999]]]
        ));
    }

    public function testValueRangePassesInsideBounds(): void
    {
        $this->assertTrue($this->validator->validate(
            ['stock' => '10'],
            ['stock' => [[Validator::RULE_MIN_VALUE, 'minint' => 0], [Validator::RULE_MAX_VALUE, 'maxint' => 999]]]
        ));
    }

    // -- errors aggregation -----------------------------------------------

    public function testCollectsErrorsForMultipleAttributes(): void
    {
        $this->assertFalse($this->validator->validate(
            ['name' => '', 'email' => 'bad'],
            [
                'name'  => [Validator::RULE_REQUIRED],
                'email' => [Validator::RULE_EMAIL],
            ]
        ));

        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    public function testGetFirstErrorReturnsFalseWhenNoError(): void
    {
        $this->validator->validate(['name' => 'ok'], ['name' => [Validator::RULE_REQUIRED]]);
        $this->assertFalse($this->validator->getFirstError('name'));
    }
}
