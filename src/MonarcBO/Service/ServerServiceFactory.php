<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

namespace MonarcBO\Service;

use MonarcCore\Service\AbstractServiceFactory;

/**
 * Factory class attached to ServerService
 * @package MonarcBO\Service
 */
class ServerServiceFactory extends AbstractServiceFactory
{
    protected $ressources = array(
        'serverTable'=> '\MonarcBO\Model\Table\ServerTable',
        'serverEntity'=> '\MonarcBO\Model\Entity\Server',
    );
}
