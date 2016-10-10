<?php
namespace MonarcBO\Service\Model\Entity;

use MonarcCore\Service\Model\Entity\AbstractServiceModelEntity;

class ServerServiceModelEntity extends AbstractServiceModelEntity
{
	protected $ressources = [
    	'setDbAdapter' => '\MonarcCli\Model\Db',
    ];
}
