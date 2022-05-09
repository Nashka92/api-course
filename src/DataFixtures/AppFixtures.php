<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //chrono = n° de facture
        $chrono = 1;


        //créer de faux user
        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $user->setFirstname($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword("password");

            $manager->persist($user);

            //créer de faux profil client
            for ($c = 0; $c < 30; $c++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setCompany($faker->company)
                    ->setEmail($faker->email);

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
