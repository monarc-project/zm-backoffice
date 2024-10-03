<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2024 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Referential\GetReferentialInputFormatter;
use Monarc\Core\Service\ReferentialService;
use Monarc\Core\Validator\InputValidator\Referential\PostReferentialDataInputValidator;

class ApiReferentialsController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private ReferentialService $referentialService,
        private GetReferentialInputFormatter $getReferentialInputFormatter,
        private PostReferentialDataInputValidator $postReferentialDataInputValidator,
    ) {
    }

    public function getList()
    {
        $formatterParams = $this->getFormattedInputParams($this->getReferentialInputFormatter);

        return $this->getPreparedJsonResponse([
            'referentials' => $this->referentialService->getList($formatterParams),
        ]);
    }

    /**
     * @param string $id
     */
    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->referentialService->getReferentialData($id));
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->validatePostParams($this->postReferentialDataInputValidator, $data);

        return $this->getSuccessfulJsonResponse([
            'id' => $this->referentialService->create(
                $this->postReferentialDataInputValidator->getValidData()
            )->getUuid(),
        ]);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update($id, $data)
    {
        $this->validatePostParams($this->postReferentialDataInputValidator, $data);

        $this->referentialService->update($id, $this->postReferentialDataInputValidator->getValidData());

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @param string $id
     */
    public function delete($id)
    {
        $this->referentialService->delete($id);

        return $this->getSuccessfulJsonResponse();
    }
}
