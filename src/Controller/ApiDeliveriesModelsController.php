<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Diactoros\Response;
use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\Exception;
use Monarc\Core\Service\DeliveriesModelsService;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 */
class ApiDeliveriesModelsController extends AbstractController
{
    use ControllerRequestResponseHandlerTrait;

    protected $name = "deliveriesmodels";

    public function __construct(DeliveriesModelsService $deliveriesModelsService)
    {
        parent::__construct($deliveriesModelsService);
    }

    public function create($data)
    {
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        for ($i = 1; $i <= 4; ++$i) {
            unset($data['path' . $i]);
            if (!empty($file['file'][$i])) {
                $file['file'][$i]['name'] = $data['category'] . ".docx";
                $data['path' . $i] = $file['file'][$i];
            }
        }

        $service->create($data);

        return $this->getSuccessfulJsonResponse();
    }

    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter);

        foreach ($entities as $k => $v) {
            for ($i = 1; $i <= 4; $i++) {
                $entities[$k]['filename' . $i] = '';
                if (!empty($entities[$k]['path' . $i]) && file_exists($entities[$k]['path' . $i])) {
                    $entities[$k]['filename' . $i] = pathinfo($entities[$k]['path' . $i], PATHINFO_BASENAME);
                    $entities[$k]['path' . $i] = './api/deliveriesmodels/' . $v['id'] . '?lang=' . $i;
                }
            }
        }

        return $this->getPreparedJsonResponse([
            'count' => \count($entities),
            $this->name => $entities
        ]);
    }

    public function get($id)
    {
        $entity = $this->getService()->getEntity($id);
        if (!empty($entity)) {
            $lang = $this->params()->fromQuery('lang', 1);
            $pathModel = getenv('APP_CONF_DIR') ?: '';
            $currentPath = $pathModel . $entity['path' . $lang];
            if (isset($entity['path' . $lang]) && file_exists($currentPath)) {
                $filename = pathinfo($currentPath)['basename'];

                $fileContents = file_get_contents($currentPath);
                if ($fileContents !== false) {
                    $stream = fopen('php://memory', 'rb+');
                    fwrite($stream, $fileContents);
                    rewind($stream);

                    return new Response($stream, 200, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'Content-Length' => strlen($fileContents),
                        'Content-Disposition' => 'attachment; filename="' . utf8_decode($filename) . '"',
                    ]);
                }
            }
        }

        throw new Exception('Document template not found');
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();

        for ($i = 1; $i <= 4; ++$i) {
            unset($data['path' . $i]);
            if (!empty($file['file'][$i])) {
                $data['path' . $i] = $file['file'][$i];
            }
        }
        $service->update($id, $data);

        return $this->getSuccessfulJsonResponse();
    }

    /**
     * @inheritdoc
     */
    public function patch($id, $data)
    {
        $service = $this->getService();
        $file = $this->request->getFiles()->toArray();
        for ($i = 1; $i <= 4; ++$i) {
            unset($data['path' . $i]);
            if (!empty($file['file'][$i])) {
                $data['path' . $i] = $file['file'][$i];
            }
        }
        $service->patch($id, $data);

        return $this->getSuccessfulJsonResponse();
    }
}
