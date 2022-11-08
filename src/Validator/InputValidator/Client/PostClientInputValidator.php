<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022 SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\InputValidator\Client;

use Monarc\BackOffice\Table\ClientTable;
use Monarc\BackOffice\Validator\FieldValidator\UniqueClientProxyAlias;
use Monarc\Core\Validator\InputValidator\AbstractInputValidator;

class PostClientInputValidator extends AbstractInputValidator
{
    private ClientTable $clientTable;

    private int $currentClientId = 0;

    public function __construct(ClientTable $clientTable, array $config)
    {
        $this->clientTable = $clientTable;

        parent::__construct($config);
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
                            'currentClientId' => $this->currentClientId,
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
                'name' => 'modelIds',
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
