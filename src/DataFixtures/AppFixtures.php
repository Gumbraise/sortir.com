<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(
        ObjectManager $manager,
    ): void
    {
        $faker = Factory::create('fr_FR');

        // Etats
        foreach (['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'Passée', 'Annulée'] as $libelle) {
            $etat = new Etat();
            $etat->setLibelle($libelle);

            $manager->persist($etat);
            $allEtats[] = $etat;
        }

        // Campus
        for ($i = 0; $i < 5; $i++) {
            $campus = new Campus();
            $campus->setNom($faker->city());

            $manager->persist($campus);
            $allCampus[] = $campus;
        }

        // Villes
        for ($i = 0; $i < 5; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city());
            $ville->setCodePostal($faker->postcode());

            $manager->persist($ville);
            $allVilles[] = $ville;
        }

        // Lieux
        for ($i = 0; $i < 20; $i++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->sentence(3));
            $lieu->setRue($faker->streetAddress());
            $lieu->setLatitude($faker->latitude());
            $lieu->setLongitude($faker->longitude());
            $lieu->setVille($faker->randomElement($allVilles));

            $manager->persist($lieu);
            $allLieux[] = $lieu;
        }

        // Administrateur
        $admin = new Participant();
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPseudo('admin');
        $admin->setEmail('admin@sortir.com');
        $admin->setPassword("admin123");
        $admin->setNom($faker->lastName());
        $admin->setPrenom($faker->firstName());
        $admin->setTelephone($faker->phoneNumber());
        $manager->persist($admin);

        // Participants
        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setPseudo($faker->userName());
            $participant->setEmail($faker->email());
            $participant->setPassword("password");
            $participant->setNom($faker->lastName());
            $participant->setPrenom($faker->firstName());
            $participant->setTelephone($faker->phoneNumber());
            $participant->setCampus($faker->randomElement($allCampus));

            $manager->persist($participant);
            $allParticipants[] = $participant;
        }

        // Sorties
        for ($i = 0; $i < 10; $i++) {
            $todayBetween6Months = $faker->dateTimeBetween('now', '+1 month');
            $sortie = new Sortie();
            $sortie->setNom($faker->sentence(3));
            $sortie->setDateHeureDebut(DateTimeImmutable::createFromMutable($todayBetween6Months));
            $sortie->setDuree($faker->numberBetween(10, 300));
            $sortie->setDateLimiteInscription(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', $todayBetween6Months)));
            $sortie->setNbInscriptionMax($faker->numberBetween(1, 7));
            $sortie->setInfosSortie($faker->text());
            $sortie->setInfosSortie($faker->text());
            $sortie->setEtat($faker->randomElement($allEtats));
            $sortie->setOrganisateur($faker->randomElement($allParticipants));
            for ($j = 0; $j < $faker->numberBetween(0, $sortie->getNbInscriptionMax()); $j++) {
                $sortie->addParticipant($faker->randomElement($allParticipants));
            }
            $sortie->setLieu($faker->randomElement($allLieux));
            $sortie->setCampus($faker->randomElement($allCampus));

            $manager->persist($sortie);
            $allSorties[] = $sortie;
        }

        $manager->flush();
    }
}