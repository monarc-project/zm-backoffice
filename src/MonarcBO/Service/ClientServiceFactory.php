<?php
namespace MonarcBO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class ClientServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'clientTable'=> '\MonarcBO\Model\Table\ClientTable',
        'clientEntity'=> '\MonarcBO\Model\Entity\Client',
    );
}
