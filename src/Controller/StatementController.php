<?php

namespace App\Controller;

use App\Entity\DTO\StatementDto;
use App\Service\StatementService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/client/statements')]
class StatementController extends AbstractController
{

    public function __construct(private readonly StatementService $statementService, private readonly Security $security, private readonly ValidatorInterface $validator)
    {
    }

    #[Route('/save', methods: ['POST'])]
    public function save(StatementDto $statementDto): JsonResponse
    {
        try {
            $user = $this->security->getUser();
            $errors = $this->validator->validate($statementDto);

            if (count($errors) > 1) {
                $errorsString = '';
                foreach ($errors as $error) {
                    $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
                }

                return new JsonResponse($errorsString);
            }
            $statement = $this->statementService->save($statementDto, $user);

            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }

    /**
     * @throws Exception
     */
    #[Route('/sign/{statementId}', methods: ['POST'])]
    public function sign(int $statementId): JsonResponse
    {
        try {
            $user = $this->security->getUser();
            $statement = $this->statementService->sign($statementId, $user->getId());
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }

    /**
     * @throws Exception
     */
    #[Route('/delete/{statementId}', methods: ['DELETE'])]
    public function delete(int $statementId): JsonResponse
    {
        try {
            $user = $this->security->getUser();
            $statement = $this->statementService->delete($statementId, $user->getId());
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{statementId}', methods: ['PATCH'])]
    public function edit(int $statementId, StatementDto $statementDto): JsonResponse
    {
        try{
            $user = $this->security->getUser();
            $errors = $this->validator->validate($statementDto);

            if (count($errors) > 1) {
                $errorsString = '';
                foreach ($errors as $error) {
                    $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
                }

                return new JsonResponse($errorsString);
            }
            $statement = $this->statementService->edit($statementId, $statementDto, $user->getId());
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/get-all', methods: ['GET'])]
    public function getAllByCurrentUser(): JsonResponse
    {
        try {
            $user = $this->security->getUser();
            $statements = $this->statementService->getAllByCurrentUser($user->getId());
            return $this->json($statements);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
