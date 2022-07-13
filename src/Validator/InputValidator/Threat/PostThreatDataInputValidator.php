<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\InputValidator\Threat;

use Laminas\Validator\InArray;
use Monarc\Core\Model\Entity\ThreatSuperClass;
use Monarc\Core\Validator\InputValidator\Threat\PostThreatDataInputValidator as CorePostThreatDataInputValidator;

class PostThreatDataInputValidator extends CorePostThreatDataInputValidator
{
    protected function getRules(): array
    {
        return array_merge(parent::getRules(), [
            [
                'name' => 'mode',
                'required' => true,
                'filters' => [
                    [
                        'name' => 'ToInt'
                    ],
                ],
                'validators' => [
                    [
                        'name' => InArray::class,
                        'options' => [
                            'haystack' => [ThreatSuperClass::MODE_GENERIC, ThreatSuperClass::MODE_SPECIFIC],
                        ]
                    ],
                ],
            ],
            [
                'name' => 'comment',
                'required' => false,
                'filters' => [
                ],
                'validators' => [],
            ],
            [
                'name' => 'qualification',
                'required' => false,
                'filters' => [
                    [
                        'name' => 'ToInt'
                    ],
                ],
                'validators' => [],
            ],
            [
                'name' => 'trend',
                'required' => false,
                'filters' => [
                    [
                        'name' => 'ToInt'
                    ],
                ],
                'validators' => [],
            ],
            [
                'name' => 'models',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            [
                'name' => 'follow',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ]);
    }
}
