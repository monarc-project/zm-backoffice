<?php
/**
 * @link      https://github.com/CASES-LU for the canonical source repository
 * @copyright Copyright (c) Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   MyCases is licensed under the GNU Affero GPL v3 - See license.txt for more information
 */

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
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

