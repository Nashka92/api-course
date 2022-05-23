<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 

class AppFixtures extends Fixture
{

    /**
     * l'encodeur de mots de passe, ce qui permet de hasher le mdp dans la BDD
     *
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasherInterface;

    //C'est avec le construct que je vais devoir mettre en place le hashage des mdp des users
    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
      $this->userPasswordHasherInterface = $userPasswordHasherInterface;  

    }
  
    public function load(ObjectManager $manager): void
    {
        
        $faker = Factory::create('fr_FR');


        //créer de faux user
        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            //chrono = n° de facture, elle doit repartir à 1 a chaque nouveau client car un client peut avoir 1 ou plusieurs factures
            $chrono = 1;
            $user->setFirstname($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                //setPassword("password");
                //on initialise et on appelle la fonction hashPassword 
                ->setPassword($this->userPasswordHasherInterface->hashPassword($user, "password"));

            $manager->persist($user);
 
            //créer de faux profil client
            for ($c = 0; $c < 30; $c++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setCompany($faker->company)
                    ->setEmail($faker->email)
                    //le customer doit être relié à l'user 
                    ->setUser($user);

                $manager->persist($customer);

                //créer de fausse factures
                for ($i = 0; $i < mt_rand(3, 10); $i++) {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2, 250, 2500))
                        ->setSentAt($faker->dateTimeBetween('-6 month'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono);

                    //a chaque facture le chrono monte
                    $chrono++;

                    $manager->persist($invoice);
                }
            }
        }




        $manager->flush();
    }
}
