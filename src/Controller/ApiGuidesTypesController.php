<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Service\GuideService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

class ApiGuidesTypesController extends AbstractRestfulController
{
    private GuideService $guideService;

    public function __construct(GuideService $guideService)
    {
        $this->guideService = $guideService;
    }

    public function getList()
    {
        return new JsonModel([
            'type' => $this->guideService->getTypes(),
        ]);
    }

    public function deleteList($data)
    {
        if ($this->guideService->deleteList($data)) {
            return new JsonModel(['status' => 'ok']);
        }

        return new JsonModel(['status' => 'ko']);
    }
}
