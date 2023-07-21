<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\InputValidator\Client;

use Monarc\BackOffice\Table\ClientTable;
use Monarc\BackOffice\Validator\FieldValidator\UniqueClientProxyAlias;
use Monarc\Core\Validator\InputValidator\AbstractInputValidator;
use Monarc\Core\Validator\InputValidator\InputValidationTranslator;

class PostClientInputValidator extends AbstractInputValidator
{
    private ClientTable $clientTable;

    private int $currentClientId = 0;

    public function __construct(ClientTable $clientTable, array $config, InputValidationTranslator $translator)
    {
        $this->clientTable = $clientTable;

        parent::__construct($config, $translator);
    }

    public function setCurrentClientId(int $currentClientId): self
    {
        $this->currentClientId = $currentClientId;

        return $this;
    }

    protected function getRules(): array
    {
        return [
            [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [],
            ],
            [
                'name' => 'proxyAlias',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => UniqueClientProxyAlias::class,
                        'options' => [
                            'clientTable' => $this->clientTable,
                            'getCurrentClientId' => function () {
                                return $this->currentClientId;
                            },
                        ],
                    ],
                ],
            ],
            [
                'name' => 'serverId',
                'required' => true,
                'filters' => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [],
            ],
            [
                'name' => 'firstUserFirstname',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [],
            ],
            [
                'name' => 'firstUserLastname',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [],
            ],
            [
                'name' => 'firstUserEmail',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [],
            ],
            [
                'name' => 'modelId',
                'required' => false,
                'filters' => [],
                'validators' => [
                    [
                        'name' => 'IsCountable',
                    ],
                ],
            ],
            [
                'name' => 'logoId',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ];
    }
}
