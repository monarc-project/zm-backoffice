<?php
namespace MonarcBO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class ClientServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'clientTable'=> '\MonarcBO\Model\Table\ClientTable',
        'clientEntity'=> '\MonarcBO\Model\Entity\Client',
        'countryTable'=> '\MonarcCore\Model\Table\CountryTable',
        'countryEntity'=> '\MonarcCore\Model\Entity\Country',
    );
}
