<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2023 Luxembourg House of Cybersecurity LHC.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Service\Export\ObjectExportService;
use Laminas\Mvc\Controller\AbstractRestfulController;

class ApiObjectsExportController extends AbstractRestfulController
{
    private ObjectExportService $objectExportService;

    public function __construct(ObjectExportService $objectExportService)
    {
        $this->objectExportService = $objectExportService;
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $result = $this->objectExportService->export($data);

        $contentType = 'application/json; charset=utf-8';
        $extension = '.json';
        if (!empty($data['password'])) {
            $contentType = 'text/plain; charset=utf-8';
            $extension = '.bin';
        }

        $this->getResponse()
            ->getHeaders()
            ->clearHeaders()
            ->addHeaderLine('Content-Type', $contentType)
            ->addHeaderLine('Content-Length', \strlen($result['content']))
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $result['filename'] . $extension . '"');

        $this->getResponse()->setContent($result['content']);

        return $this->getResponse();
    }
}
