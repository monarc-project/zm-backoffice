<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\Exception;
use Monarc\Core\Service\ModelService;

class ApiModelsDuplicationController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        if (empty($data['model'])) {
            throw new Exception('"model" param is missing', 412);
        }

        $id = $this->modelService->duplicate((int)$data['model']);

        return $this->getSuccessfulJsonResponse(['id' => $id]);
    }
}
