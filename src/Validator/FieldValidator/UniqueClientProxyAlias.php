<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator\FieldValidator;

use Doctrine\ORM\EntityRepository;
use Monarc\BackOffice\Model\Entity\Client;
use Laminas\Validator\AbstractValidator;

/**
 * Class UniqueClientProxyAlias is an implementation of AbstractValidator that ensures the uniqueness or a client's
 * proxy alias (which may only exist once ever).
 */
class UniqueClientProxyAlias extends AbstractValidator
{
    protected $options = [
        'clintRepository' => null,
        'id' => 0,
    ];

    const ALREADY_USED = "ALREADY_USED";

    protected $messageTemplates = [
        self::ALREADY_USED => 'This proxy alias is already used',
    ];

    public function isValid($value)
    {
        if ($this->options['clintRepository'] === null) {
            throw new \LogicException('"clientRepository" option is mandatory for the Proxy Alias validation.', 412);
        }

        /** @var EntityRepository $clientRepository */
        $clientRepository = $this->options['clintRepository'];
        /** @var Client|null $client */
        $client = $clientRepository->findOneByProxyAlias($value);
        if ($client !== null && (int)$this->options['id'] !== $client->getId()) {
            $this->error(self::ALREADY_USED);

            return false;
        }

        return true;
    }
}
