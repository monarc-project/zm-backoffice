<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Exception\Exception;
use Monarc\Core\Service\ObjectService;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Api Objects Duplication Controller
 *
 * Class ApiObjectsDuplicationController
 * @package Monarc\BackOffice\Controller
 */
class ApiObjectsDuplicationController extends AbstractRestfulController
{
    /** @var ObjectService */
    private $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function create($data)
    {
        if (!isset($data['id'])) {
            throw new Exception('Object ID parameter is required');
        }

        $id = $this->objectService->duplicate($data);

        return new JsonModel(
            array(
                'status' => 'ok',
                'id' => $id,
            )
        );
    }

    public function deleteList($data)
    {
        if ($this->objectService->deleteList($data)) {
            return new JsonModel(array('status' => 'ok'));
        }

        return new JsonModel(array('status' => 'ko'));
    }
}
