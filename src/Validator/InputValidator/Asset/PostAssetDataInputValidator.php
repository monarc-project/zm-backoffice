<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\InputValidator\Asset;

use Laminas\Validator\InArray;
use Monarc\Core\Model\Entity\AssetSuperClass;
use Monarc\Core\Validator\InputValidator\Asset\PostAssetDataInputValidator as CorePostAssetDataInputValidator;

class PostAssetDataInputValidator extends CorePostAssetDataInputValidator
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
                            'haystack' => [AssetSuperClass::MODE_GENERIC, AssetSuperClass::MODE_SPECIFIC],
                        ]
                    ],
                ],
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
