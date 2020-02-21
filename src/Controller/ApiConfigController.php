<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Service\ConfigService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Api Config Controller
 *
 * Class ApiConfigController
 * @package Monarc\BackOffice\Controller
 */
class ApiConfigController extends AbstractRestfulController
{
    /** @var ConfigService */
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function getList()
    {
        return new JsonModel($this->configService->getLanguage());
    }

    public function deleteList($data)
    {
        if ($this->configService->deleteList($data)) {
            return new JsonModel(array('status' => 'ok'));
        }

        return new JsonModel(array('status' => 'ko'));
    }
}
