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

    /**
     * Сохранение заявки
     * @param StatementDto $statementDto
     * @return JsonResponse
     */
    #[Route('/save', methods: ['POST'])]
    public function save(StatementDto $statementDto): JsonResponse
    {
        try {
            $user = $this->security->getUser();
            $validationResponse = $this->validateDto($statementDto);

            if ($validationResponse) {
                return $validationResponse;
            }
            $statement = $this->statementService->save($statementDto, $user);

            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }

    /**
     * Подписание заявки
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
     * Удаление заявки
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
     * Редактирование заявки
     * @throws Exception
     */
    #[Route('/edit/{statementId}', methods: ['PATCH'])]
    public function edit(int $statementId, StatementDto $statementDto): JsonResponse
    {
        try{
            $user = $this->security->getUser();
            $validationResponse = $this->validateDto($statementDto);

            if ($validationResponse) {
                return $validationResponse;
            }
            $statement = $this->statementService->edit($statementId, $statementDto, $user->getId());
            return $this->json($statement);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Получить все заявки текущего пользователя
     * @return JsonResponse
     */
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

    /**
     * Валидация StatementDto
     * @param StatementDto $dto
     * @return JsonResponse|null
     */
    private function validateDto(StatementDto $dto): ?JsonResponse
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 1) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }
            return new JsonResponse($errorsString);
        }
        return null;
    }
}
