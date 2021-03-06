<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Validator;

use Monarc\BackOffice\Model\Entity\Client;
use Laminas\Validator\AbstractValidator;

/**
 * Class UniqueClientProxyAlias is an implementation of AbstractValidator that ensures the uniqueness or a client.
 * proxy alias (which may only exist once ever)
 * @package Monarc\BackOffice\Validator
 */
class UniqueClientProxyAlias extends AbstractValidator
{
    protected $options = array(
        'adapter' => null,
        'id' => 0,
    );

    const ALREADYUSED = "ALREADYUSED";

    protected $messageTemplates = array(
        self::ALREADYUSED => 'This proxy alias is already used',
    );

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        if (empty($this->options['adapter'])) {
            return false;
        }

        $res = $this->options['adapter']->getRepository(Client::class)->findOneByProxyAlias($value);
        if (!empty($res) && $this->options['id'] != $res->get('id')) {
            $this->error(self::ALREADYUSED);

            return false;
        }

        return true;
    }
}
