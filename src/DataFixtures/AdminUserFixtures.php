<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin user (create or update)
        $adminEmail = 'admin@gmail.com';
        $existingAdmin = $manager->getRepository(User::class)->findOneBy(['email' => $adminEmail]);
        if ($existingAdmin) {
            $adminUser = $existingAdmin;
        } else {
            $adminUser = new User();
            $adminUser->setEmail($adminEmail);
        }
        $adminUser->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, '12345');
        $adminUser->setPassword($hashedPassword);
        $manager->persist($adminUser);

        // Staff user (create or update)
        $staffEmail = 'staff@gmail.com';
        $existingStaff = $manager->getRepository(User::class)->findOneBy(['email' => $staffEmail]);
        if ($existingStaff) {
            $staffUser = $existingStaff;
        } else {
            $staffUser = new User();
            $staffUser->setEmail($staffEmail);
        }
        $staffUser->setRoles(['ROLE_STAFF']);
        if (method_exists($staffUser, 'setName') && !$staffUser->getName()) {
            $staffUser->setName('Staff User');
        }
        $hashedStaffPassword = $this->passwordHasher->hashPassword($staffUser, '12345');
        $staffUser->setPassword($hashedStaffPassword);
        $manager->persist($staffUser);

        $manager->flush();
    }
}