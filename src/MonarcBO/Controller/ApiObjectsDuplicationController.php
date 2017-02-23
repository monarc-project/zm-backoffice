<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Duplication Controller
 *
 * Class ApiObjectsDuplicationController
 * @package MonarcBO\Controller
 */
class ApiObjectsDuplicationController extends AbstractController
{

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     * @throws \MonarcCore\Exception\Exception
     */
    public function create($data)
    {
        if (isset($data['id'])) {
            $id = $this->getService()->duplicate($data);

            return new JsonModel(
                array(
                    'status' => 'ok',
                    'id' => $id,
                )
            );
        } else {
            throw new \MonarcCore\Exception\Exception('Object to duplicate is required');
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

