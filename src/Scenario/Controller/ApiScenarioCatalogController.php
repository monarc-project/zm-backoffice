<?php declare(strict_types=1);

namespace Monarc\BackOffice\Scenario\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Monarc\BackOffice\Scenario\Service\CatalogService;
use Monarc\BackOffice\Scenario\Validator\CatalogRequestValidator;
use Monarc\Core\Controller\Handler\ControllerRequestResponseHandlerTrait;
use Monarc\Core\Exception\ActionForbiddenException;
use Monarc\Core\Scenario\Feature\ScenarioCapability;

final class ApiScenarioCatalogController extends AbstractRestfulController
{
    use ControllerRequestResponseHandlerTrait;

    public function __construct(
        private CatalogService $catalog,
        private ScenarioCapability $scenarioCapability
    ) {
    }

    public function getList()
    {
        $this->scenarioCapability->assertEnabled();
        try {
            return $this->getPreparedJsonResponse($this->catalog->list($this->kind(), $this->params()->fromQuery()));
        } catch (ActionForbiddenException $exception) {
            return $this->forbidden($exception);
        }
    }

    public function get($id)
    {
        $this->scenarioCapability->assertEnabled();
        try {
            if ($this->params()->fromRoute('catalogAction') === 'versions') {
                return $this->getPreparedJsonResponse($this->catalog->versions(
                    $this->kind(),
                    (string) $id,
                    (string) $this->params()->fromQuery('locale', 'en')
                ));
            }

            return $this->getPreparedJsonResponse($this->catalog->read(
                $this->kind(),
                (string) $id,
                (string) $this->params()->fromQuery('locale', 'en'),
                $this->version()
            ));
        } catch (ActionForbiddenException $exception) {
            return $this->forbidden($exception);
        } catch (\RuntimeException $exception) {
            $this->getResponse()->setStatusCode(404);
            return $this->getPreparedJsonResponse([
                'error' => ['code' => 'not_found', 'message' => 'Catalog resource not found.'],
            ]);
        }
    }

    public function create($data)
    {
        $this->scenarioCapability->assertEnabled();

        $action = (string) $this->params()->fromRoute('catalogAction', 'create');
        $headers = $this->requestHeaders();
        $correlationId = $headers['x-correlation-id'] ?? '';
        try {
            if ($action === 'create') {
                return $this->getSuccessfulJsonResponse(
                    $this->catalog->createDraft($this->kind(), $data, $correlationId)
                );
            }
            $id = (string) $this->params()->fromRoute('id');
            $result = $this->catalog->transition(
                $this->kind(),
                $id,
                $action,
                CatalogRequestValidator::revision($headers),
                $correlationId
            );

            return $this->getSuccessfulJsonResponse($result);
        } catch (\InvalidArgumentException $exception) {
            $this->getResponse()->setStatusCode(400);
            return $this->getPreparedJsonResponse([
                'error' => [
                    'code' => 'validation_error',
                    'message' => $exception->getMessage(),
                ],
            ]);
        } catch (ActionForbiddenException $exception) {
            return $this->forbidden($exception);
        } catch (\RuntimeException $exception) {
            $isConflict = str_contains($exception->getMessage(), 'revision')
                || str_contains($exception->getMessage(), 'lifecycle');
            $this->getResponse()->setStatusCode($isConflict ? 409 : 404);

            return $this->getPreparedJsonResponse([
                'error' => [
                    'code' => 'conflict',
                    'message' => $exception->getMessage(),
                ],
            ]);
        }
    }

    public function update($id, $data)
    {
        $this->scenarioCapability->assertEnabled();

        try {
            return $this->getSuccessfulJsonResponse($this->catalog->updateDraft(
                $this->kind(),
                (string) $id,
                $data,
                CatalogRequestValidator::revision($this->requestHeaders()),
                $this->requestHeaders()['x-correlation-id'] ?? ''
            ));
        } catch (\InvalidArgumentException $exception) {
            $this->getResponse()->setStatusCode(400);
            return $this->getPreparedJsonResponse([
                'error' => [
                    'code' => 'validation_error',
                    'message' => $exception->getMessage(),
                ],
            ]);
        } catch (ActionForbiddenException $exception) {
            return $this->forbidden($exception);
        } catch (\RuntimeException $exception) {
            $this->getResponse()->setStatusCode(409);

            return $this->getPreparedJsonResponse([
                'error' => [
                    'code' => 'revision_conflict',
                    'message' => $exception->getMessage(),
                ],
            ]);
        }
    }

    private function kind(): string
    {
        return (string) $this->params()->fromRoute('catalogKind') === 'templates' ? 'template' : 'event';
    }

    private function requestHeaders(): array
    {
        $headers = [];
        foreach (['if-match', 'x-correlation-id'] as $name) {
            $header = $this->getRequest()->getHeader($name);
            if ($header !== false) {
                $headers[$name] = $header->getFieldValue();
            }
        }
        return $headers;
    }

    private function version(): ?int
    {
        $version = $this->params()->fromQuery('version');

        return is_numeric($version) && (int) $version > 0 ? (int) $version : null;
    }

    private function forbidden(ActionForbiddenException $exception)
    {
        $this->getResponse()->setStatusCode(403);

        return $this->getPreparedJsonResponse([
            'error' => ['code' => 'forbidden', 'message' => $exception->getMessage()],
        ]);
    }
}
