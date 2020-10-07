<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * En général, on peut récupérer des services par autowiring
     * dans les constructerus des classes
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        // Génération de 3 administrateurs
        for ($i = 0; $i < 3; $i++) {
            // instanciation
            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'admin' . $i);

            // méthode
            $user
                ->setEmail('admin' . $i . '@blog.fr')
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($hash)
            ;

            $manager->persist($user);
        }

        // Génération de 5 utilisateurs

        for ($i = 0; $i < 5; $i++) {
            // instanciation
            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'user' . $i);

            // méthode
            $user
                ->setEmail('user' . $i . '@mail.org')
                ->setPassword($hash)
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
