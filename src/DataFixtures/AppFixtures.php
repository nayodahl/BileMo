<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Entity\Reseller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $faker;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        // create 7 phones
        $product = new Phone();
        $product->setModel('S10')
        ->setBrand('Samsung')
        ->setDescription('Samsung Galaxy S10')
        ->setPrice(759)
        ->setInternalReference('S10-G981BLBDEUB');
        $manager->persist($product);

        $product = new Phone();
        $product->setModel('S10+')
        ->setBrand('Samsung')
        ->setDescription('Samsung Galaxy S10+')
        ->setPrice(859)
        ->setInternalReference('S10+-G981BLBDEUA');
        $manager->persist($product);

        $product = new Phone();
        $product->setModel('S10e')
        ->setBrand('Samsung')
        ->setDescription('Galaxy S10e')
        ->setPrice(759)
        ->setInternalReference('S10e-G981BLADEUB');
        $manager->persist($product);

        $product = new Phone();
        $product->setModel('S20')
        ->setBrand('Samsung')
        ->setDescription('Samsung Galaxy S20')
        ->setPrice(909)
        ->setInternalReference('S20-G451BLBDEUB');
        $manager->persist($product);

        $product = new Phone();
        $product->setModel('Note10')
        ->setBrand('Samsung')
        ->setDescription('Samsung Galaxy Note10')
        ->setPrice(959)
        ->setInternalReference('N10-G451ARBDEUB');
        $manager->persist($product);

        $product = new Phone();
        $product->setModel('iPhone 11')
        ->setBrand('Apple')
        ->setDescription('Apple iPhone 11')
        ->setPrice(809)
        ->setInternalReference('I11-A451ARBDEUS');
        $manager->persist($product);

        $product = new Phone();
        $product->setModel('iPhone XR')
        ->setBrand('Apple')
        ->setDescription('Apple iPhone XR')
        ->setPrice(709)
        ->setInternalReference('IXR-A887ARBDEUR');
        $manager->persist($product);

        // create 1 reseller
        $reseller = new Reseller();
        $reseller->setEmail('dev@phonecompany.com')
        ->setPassword($this->passwordEncoder->encodePassword(
            $reseller, '@dmIn123'
        ));
        $manager->persist($reseller);

        // create 20 customers for this reseller
        for ($i = 1; $i <= 20; ++$i) {
            $customer = new Customer();
            $customer->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName)
                ->setEmail($this->faker->freeEmail)
                ->setReseller($reseller);
            $manager->persist($customer);
        }

        // create 1 reseller
        $reseller = new Reseller();
        $reseller->setEmail('dev@phonevendor.com')
        ->setPassword($this->passwordEncoder->encodePassword(
            $reseller, '@dmIn123'
        ));
        $manager->persist($reseller);

        // create 20 customers for this reseller
        for ($i = 1; $i <= 20; ++$i) {
            $customer = new Customer();
            $customer->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName)
                ->setEmail($this->faker->freeEmail)
                ->setReseller($reseller);
            $manager->persist($customer);
        }

        // create 1 admin account
        $admin = new Reseller();
        $admin->setEmail('admin@bilemo.com')
        ->setPassword($this->passwordEncoder->encodePassword(
            $admin, '@dmIn123'
        ));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        

        $manager->flush();
    }
}
