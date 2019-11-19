<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Service\ObjectService;
use Zend\Mvc\Controller\AbstractRestfulController;

/**
 * Api Objects Export Controller
 *
 * Class ApiObjectsExportController
 * @package Monarc\BackOffice\Controller
 */
class ApiObjectsExportController extends AbstractRestfulController
{
    /** @var ObjectService */
    private $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function create($data)
    {
        $output = $this->objectService->export($data);

        if (empty($data['password'])) {
            $contentType = 'application/json; charset=utf-8';
            $extension = '.json';
        } else {
            $contentType = 'text/plain; charset=utf-8';
            $extension = '.bin';
        }

        $this->getResponse()
            ->getHeaders()
            ->clearHeaders()
            ->addHeaderLine('Content-Type', $contentType)
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' .
                (empty($data['filename']) ? $data['id'] : $data['filename']) . $extension . '"');

        $this->getResponse()
            ->setContent($output);

        return $this->getResponse();
    }
}
