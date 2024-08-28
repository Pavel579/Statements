<?php

namespace App\Service;

use App\Entity\DTO\StatementDto;
use App\Entity\Statement;
use App\Enum\StatusEnum;
use App\Repository\StatementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class StatementService
{

    public function __construct(
        private readonly StatementRepository    $statementRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function save(StatementDto $statementDto, $user): Statement
    {
        $statement = new Statement();
        $statement->setName($statementDto->getName());
        $statement->setAuthor($user);
        $uniqueId = uniqid();
        $statement->setNumber($uniqueId);
        $statement->setDate(new \DateTime());
        $statement->setStatus(StatusEnum::PENDING);

        $this->entityManager->persist($statement);
        $this->entityManager->flush();
        return $statement;
    }

    public function getAllByCurrentUser(int $userId)
    {
        return $this->statementRepository->findByAuthorId($userId);
    }

    /**
     * @throws Exception
     */
    public function sign(int $statementId, int $userId, LoggerInterface $logger): Statement
    {
        $statement = $this->findStatementById($statementId);
        if ($statement->getAuthor()->getId() != $userId) {
            $logger->error("пользоваетель с id " . $userId . "пытается подписывать заявки пользователя " . $statement->getAuthor()->getId());
            throw new Exception("Доступ запрещен", 400);
        }

        if ($statement->getStatus() == StatusEnum::PENDING) {
            $statement->setStatus(StatusEnum::SIGNED);
            $this->entityManager->persist($statement);
            $this->entityManager->flush();
            return $statement;
        } else {
            throw new Exception("Данная заявка не является черновиком", 400);
        }
    }

    /**
     * @throws Exception
     */
    public function delete(int $statementId, int $userId, LoggerInterface $logger): Statement
    {
        $statement = $this->findStatementById($statementId);
        if ($statement->getAuthor()->getId() != $userId) {
            $logger->error("пользоваетель с id " . $userId . "пытается удалить заявки пользователя " . $statement->getAuthor()->getId());
            throw new Exception("Доступ запрещен", 400);
        }

        if (in_array($statement->getStatus(), [StatusEnum::PENDING, StatusEnum::SIGNED])) {
            $statement->setStatus(StatusEnum::DELETED);
            $this->entityManager->persist($statement);
            $this->entityManager->flush();
            return $statement;
        } else {
            throw new Exception("Данная заявка не в статусе черновик или подписанный документ", 400);
        }
    }

    /**
     * @throws Exception
     */
    public function edit(int $statementId, StatementDto $statementDto, int $userId, LoggerInterface $logger): Statement
    {
        $statement = $this->findStatementById($statementId);
        if ($statement->getAuthor()->getId() != $userId) {
            $logger->error("пользоваетель с id " . $userId . "пытается редактировать заявки пользователя " . $statement->getAuthor()->getId());
            throw new Exception("Доступ запрещен", 400);
        }

        if ($statement->getStatus() == StatusEnum::PENDING) {
            $statement->setName($statementDto->getName());

            $this->entityManager->persist($statement);
            $this->entityManager->flush();
            return $statement;
        } else {
            throw new Exception("Нельзя редактировать заявление не в статусе Черновик", 400);
        }
    }

    /**
     * @throws Exception
     */
    public function findStatementById(int $statementId): Statement
    {
        $statement = $this->statementRepository->find($statementId);
        if (!$statement) {
            throw new Exception("Заявка не найдена", 404);
        }
        return $statement;
    }
}
