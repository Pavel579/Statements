<?php

namespace App\Service\Admin;

use App\Entity\Statement;
use App\Enum\StatusEnum;
use App\Service\StatementService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AdminStatementService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly StatementService $statementService)
    {
    }

    /**
     * @throws Exception
     */
    public function delete(int $statementId): Statement
    {
        $statement = $this->statementService->findStatementById($statementId);
        if ($statement->getStatus() == StatusEnum::DELETED) {
            throw new Exception("Данная заявка уже в статусе Удалена", 400);
        }
        $statement->setStatus(StatusEnum::DELETED);

        $this->entityManager->persist($statement);
        $this->entityManager->flush();
        return $statement;
    }

    /**
     * @throws Exception
     */
    public function getStatementUnderConsideration(int $statementId): Statement
    {
        $statement = $this->statementService->findStatementById($statementId);
        if ($statement->getStatus() == StatusEnum::SIGNED) {
            $statement->setStatus(StatusEnum::UNDER_CONSIDERATION);

            $this->entityManager->persist($statement);
            $this->entityManager->flush();
            return $statement;
        } else {
            throw new Exception("Данная заявка не в статусе Подписана", 400);
        }
    }

    /**
     * @throws Exception
     */
    public function rejectStatement(int $statementId) : Statement
    {
        $statement = $this->statementService->findStatementById($statementId);
        if (in_array($statement->getStatus(), [StatusEnum::SIGNED, StatusEnum::UNDER_CONSIDERATION])) {
            $statement->setStatus(StatusEnum::REJECTED);

            $this->entityManager->persist($statement);
            $this->entityManager->flush();
            return $statement;
        } else {
            throw new Exception("Данную заявку нельзя отклонить", 400);
        }
    }

    /**
     * @throws Exception
     */
    public function approveStatement(int $statementId): Statement
    {
        $statement = $this->statementService->findStatementById($statementId);
        if ($statement->getStatus() == StatusEnum::UNDER_CONSIDERATION) {
            $statement->setStatus(StatusEnum::APPROVED);

            $this->entityManager->persist($statement);
            $this->entityManager->flush();
            return $statement;
        } else {
            throw new Exception("Данную заявку нельзя утвердить, т.к. она не на рассмотрении", 400);
        }
    }
}