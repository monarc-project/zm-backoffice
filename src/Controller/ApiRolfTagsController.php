<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\RolfTagService;

/**
 * TODO: extend AbstractRestfulController and remove AbstractController.
 *
 * Class ApiRolfTagsController
 * @package Monarc\BackOffice\Controller
 */
class ApiRolfTagsController extends AbstractController
{
    protected $name = 'tags';

    public function __construct(RolfTagService $rolfTagService)
    {
        parent::__construct($rolfTagService);
    }
}
