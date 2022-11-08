<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\FieldValidator;

use Laminas\Validator\AbstractValidator;
use Monarc\BackOffice\Table\ClientTable;

class UniqueClientProxyAlias extends AbstractValidator
{
    public const ALREADY_USED = "ALREADY_USED";

    protected $messageTemplates = [
        self::ALREADY_USED => 'This proxy alias is already used',
    ];

    public function isValid($value)
    {
        /** @var ClientTable $clientTable */
        $clientTable = $this->getOption('clintTable');
        $client = $clientTable->findOneByProxyAlias($value);
        if ($client === null || (int)$this->getOption('currentClintId') === $client->getId()) {
            return true;
        }

        $this->error(self::ALREADY_USED);

        return false;
    }
}
