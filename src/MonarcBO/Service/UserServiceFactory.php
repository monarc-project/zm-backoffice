<?php
namespace MonarcBO\Service;

use MonarcCore\Service\AbstractServiceFactory;

class UserServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'userTable'=> '\MonarcBO\Model\Table\UserTable',
        'userEntity'=> '\MonarcBO\Model\Entity\User',
    );
}
