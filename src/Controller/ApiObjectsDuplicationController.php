<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\Handler\AbstractRestfulControllerRequestHandler;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\Exception;
use Monarc\Core\Entity\Anr;
use Monarc\Core\Service\ObjectService;

class ApiObjectsDuplicationController extends AbstractRestfulControllerRequestHandler
{
    use ControllerRequestResponseHandlerTrait;

    private ObjectService $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function create($data)
    {
        /** @var array $data */
        if (!isset($data['id'])) {
            throw new Exception('Object ID parameter is required.', 412);
        }
        /** @var Anr|null $anr */
        $anr = $this->getRequest()->getAttribute('anr');

        $object = $this->objectService->duplicate($data, $anr);

        return $this->getSuccessfulJsonResponse(['id' => $object->getUuid()]);
    }
}
