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
     * @throws \Exception
     */
    public function create($data)
    {
        if (array_key_exists('id', $data)) {
            $id = $this->getService()->duplicate($data);

            return new JsonModel(
                array(
                    'status' => 'ok',
                    'id' => $id,
                )
            );
        } else {
            throw new \Exception('Object to duplicate is required');
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
