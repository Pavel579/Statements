<?php

namespace App\Controller\Admin;

use App\Service\Admin\AdminStatementService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/admin/statements')]
class AdminStatementController extends AbstractController
{
    public function __construct(private readonly AdminStatementService $adminStatementService)
    {
    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{statementId}', methods: ['DELETE'])]
    public function delete(int $statementId): JsonResponse
    {
        try {
            $statement = $this->adminStatementService->delete($statementId);
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/under-consideration/{statementId}', methods: ['PATCH'])]
    public function getStatementUnderConsideration(int $statementId): JsonResponse
    {
        try {
            $statement = $this->adminStatementService->getStatementUnderConsideration($statementId);
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/under-consideration/{statementId}', methods: ['PATCH'])]
    public function rejectStatement(int $statementId): JsonResponse
    {
        try {
            $statement = $this->adminStatementService->rejectStatement($statementId);
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/approve/{statementId}', methods: ['PATCH'])]
    public function approveStatement(int $statementId): JsonResponse
    {
        try {
            $statement = $this->adminStatementService->approveStatement($statementId);
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}