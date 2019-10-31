<?php
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2019  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Monarc\Core\Controller\AbstractController;
use Monarc\Core\Service\ThemeService;

/**
 * Api Themes Controller
 *
 * Class ApiThemesController
 * @package Monarc\BackOffice\Controller
 */
class ApiThemesController extends AbstractController
{
    protected $name = 'themes';

    public function __construct(ThemeService $themeService)
    {
        parent::__construct($themeService);
    }
}
