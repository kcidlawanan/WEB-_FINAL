<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(name: 'app:fix-plain-passwords')]
class FixPlainPasswordsCommand extends Command
{
    protected static $defaultName = 'app:fix-plain-passwords';

    public function __construct(private UserRepository $userRepo, private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Find users with unhashed passwords and set a temporary hashed password.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepo->findAll();
        $changed = [];

        foreach ($users as $user) {
            $pw = $user->getPassword();
            if (!$pw) {
                continue;
            }
            // Heuristic: hashed passwords usually start with '$'
            if (is_string($pw) && str_starts_with($pw, '$')) {
                continue;
            }

            // Reset to temporary password
            $temp = 'TempPass123!';
            $hashed = $this->passwordHasher->hashPassword($user, $temp);
            $user->setPassword($hashed);
            $this->em->persist($user);
            $changed[] = [$user->getId(), $user->getEmail(), $temp];
        }

        if (count($changed) > 0) {
            $this->em->flush();
            $io->success('Updated ' . count($changed) . ' user(s).');
            $io->table(['ID','Email','Temporary Password'], $changed);
            $io->warning('Users listed above now have the temporary password shown; require them to change it on next login.');
        } else {
            $io->success('No plain-text passwords found.');
        }

        return Command::SUCCESS;
    }
}
