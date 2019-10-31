<?php

namespace Monarc\BackOffice\Service\Model\Entity;

use Monarc\BackOffice\Model\DbCli;
use Monarc\Core\Service\Model\Entity\AbstractServiceModelEntity;

class ClientServiceModelEntity extends AbstractServiceModelEntity
{
    protected $ressources = [
        'setDbAdapter' => DbCli::class,
    ];
}
