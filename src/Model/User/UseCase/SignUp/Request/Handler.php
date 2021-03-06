<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;


use App\Model\User\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Component\Validator\Constraints\Uuid;

class Handler
{
    private $em;



    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $email = mb_strtolower($command->email);

        if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            throw new DomainException('user already exist.');
        }

        $user = new User(
            strval(Uuid::V4_RANDOM),
            new \DateTimeImmutable(),
            $email,
            password_hash($command->password, PASSWORD_ARGON2I)
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}