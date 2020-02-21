<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Service\GuideService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Api Guides Types Controller
 *
 * Class ApiGuidesTypesController
 * @package Monarc\BackOffice\Controller
 */
class ApiGuidesTypesController extends AbstractRestfulController
{
    /** @var GuideService */
    private $guideService;

    public function __construct(GuideService $guideService)
    {
        $this->guideService = $guideService;
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        return new JsonModel(array(
            'type' => $this->guideService->getTypes()
        ));
    }

    public function deleteList($data)
    {
        if ($this->guideService->deleteList($data)) {
            return new JsonModel(array('status' => 'ok'));
        }

        return new JsonModel(array('status' => 'ko'));
    }
}
