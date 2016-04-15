<?php
namespace MonarcBO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class ServerServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'serverTable'=> '\MonarcBO\Model\Table\ServerTable',
        'serverEntity'=> '\MonarcBO\Model\Entity\Server',
    );
}
