<?php declare(strict_types=1);
/**
 * @link      https://github.com/monarc-project for the canonical source repository
 * @copyright Copyright (c) 2016-2022  SMILE GIE Securitymadein.lu - Licensed under GNU Affero GPL v3
 * @license   MONARC is licensed under GNU Affero General Public License version 3
 */

namespace Monarc\BackOffice\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\InputFormatter\Theme\GetThemesInputFormatter;
use Monarc\Core\Service\ThemeService;
use Monarc\Core\Validator\InputValidator\Theme\PostThemeDataInputValidator;

class ApiThemesController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    private GetThemesInputFormatter $getThemesInputFormatter;

    private PostThemeDataInputValidator $postThemeDataInputValidator;

    private ThemeService $themeService;

    public function __construct(
        GetThemesInputFormatter $getThemesInputFormatter,
        PostThemeDataInputValidator $postThemeDataInputValidator,
        ThemeService $themeService
    ) {
        $this->getThemesInputFormatter = $getThemesInputFormatter;
        $this->postThemeDataInputValidator = $postThemeDataInputValidator;
        $this->themeService = $themeService;
    }

    public function getList()
    {
        $formatterParams = $this->getFormattedInputParams($this->getThemesInputFormatter);

        return $this->getPreparedJsonResponse([
            'themes' => $this->themeService->getList($formatterParams),
        ]);
    }

    public function get($id)
    {
        return $this->getPreparedJsonResponse($this->themeService->getThemeData((int)$id));
    }

    public function create($data)
    {
        $this->validatePostParams($this->postThemeDataInputValidator, $data);

        $theme = $this->themeService->create($data);

        return $this->getPreparedJsonResponse([
            'status' => 'ok',
            'id' => $theme->getId(),
        ]);
    }

    public function update($id, $data)
    {
        $this->validatePostParams($this->postThemeDataInputValidator, $data);

        $this->themeService->update((int)$id, $data);

        return $this->getPreparedJsonResponse([
            'status' => 'ok',
        ]);
    }

    public function delete($id)
    {
        $this->themeService->delete((int)$id);

        return $this->getPreparedJsonResponse([
            'status' => 'ok',
        ]);
    }
}
