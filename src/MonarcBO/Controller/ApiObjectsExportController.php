<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Export Controller
 *
 * Class ApiObjectsExportController
 * @package MonarcBO\Controller
 */
class ApiObjectsExportController extends AbstractController
{

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     * @throws \Exception
     */
    public function create($data)
    {
        if (isset($data['id'])) {
            $output = $this->getService()->export($data);

            $response = $this->getResponse();
            $response->setContent($output);

            $headers = $response->getHeaders();
            $headers->clearHeaders()
                ->addHeaderLine('Content-Type', 'application/binary')
                ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $data['id'] . '.bin"');

            return $this->response;
        } else {
            throw new \Exception('Object to export is required');
        }
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

