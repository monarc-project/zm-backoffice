<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\InputValidator\Server;

use Laminas\Validator\Ip;
use Monarc\Core\Validator\InputValidator\AbstractInputValidator;

class PostServerDataInputValidator extends AbstractInputValidator
{
    protected function getRules(): array
    {
        return [
            [
                'name' => 'label',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            [
                'name' => 'ipAddress',
                'required' => true,
                'filters' => [],
                'validators' => [
                    [
                        'name' => Ip::class
                    ]
                ],
            ],
            [
                'name' => 'fqdn',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            [
                'name' => 'status',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => 'boolean'
                    ],
                ],
                'validators' => [],
            ],
            [
                'name' => 'port',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            [
                'name' => 'login',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            [
                'name' => 'ssh',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => 'boolean'
                    ],
                ],
                'validators' => [],
            ],
        ];
    }
}
