<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2026 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace MonarcBackOfficeTests\Unit\Validator;

use Monarc\BackOffice\Validator\InputValidator\Server\PostServerDataInputValidator;
use Monarc\Core\Service\ConnectedUserService;
use Monarc\Core\Validator\InputValidator\InputValidationTranslator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Monarc\BackOffice\Validator\InputValidator\Server\PostServerDataInputValidator
 */
class PostServerDataInputValidatorTest extends TestCase
{
    private function createValidator(): PostServerDataInputValidator
    {
        return new PostServerDataInputValidator(
            [],
            new InputValidationTranslator($this->createMock(ConnectedUserService::class), [])
        );
    }

    public function testValidServerPreservesExplicitFalseFlagsAndFiltersUnknownFields(): void
    {
        $validator = $this->createValidator();
        self::assertTrue($validator->isValid([
            'label' => 'Local server',
            'ipAddress' => '127.0.0.1',
            'fqdn' => 'localhost',
            'status' => '0',
            'ssh' => false,
            'unexpected' => 'discarded',
        ]));
        $data = $validator->getValidData();
        self::assertSame('127.0.0.1', $data['ipAddress']);
        self::assertFalse($data['status']);
        self::assertFalse($data['ssh']);
        self::assertArrayNotHasKey('unexpected', $data);
    }

    public function testInvalidAddressAndMissingRequiredFieldsAreRejected(): void
    {
        $validator = $this->createValidator();
        self::assertFalse($validator->isValid(['ipAddress' => 'not-an-ip']));
        self::assertEqualsCanonicalizing(['label', 'ipAddress', 'fqdn'], array_keys($validator->getErrorMessages()));
        self::assertSame([], $validator->getValidDataSets());
    }
}
