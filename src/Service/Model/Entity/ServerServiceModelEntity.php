<?php
namespace Monarc\BackOffice\Service\Model\Entity;

use Monarc\Core\Service\Model\Entity\AbstractServiceModelEntity;

class ServerServiceModelEntity extends AbstractServiceModelEntity
{
	protected $ressources = [
    	'setDbAdapter' => '\MonarcCli\Model\Db',
    ];
}
