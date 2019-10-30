<?php
namespace Monarc\BackOffice\Service\Model\Entity;

use Monarc\Core\Service\Model\Entity\AbstractServiceModelEntity;

class ClientServiceModelEntity extends AbstractServiceModelEntity
{
	protected $ressources = [
    	'setDbAdapter' => '\MonarcCli\Model\Db',
    ];
}
