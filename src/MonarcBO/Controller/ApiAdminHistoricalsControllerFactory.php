<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractControllerFactory;

/**
 * Factory class attached to ApiAdminHistoricalsController
 * @package MonarcFO\Controller
 */
class ApiAdminHistoricalsControllerFactory extends AbstractControllerFactory
{
    protected $serviceName = '\MonarcCore\Service\HistoricalService';
}

