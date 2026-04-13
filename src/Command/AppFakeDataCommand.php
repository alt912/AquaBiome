<?php

namespace App\Command;

use App\Entity\Mesure;
use App\Entity\Alerte;
use App\Entity\Aquarium;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fake-data',
    description: 'Genere un historique de 30 jours de test (normal, high, low)',
)]
class AppFakeDataCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('scenario', InputArgument::OPTIONAL, 'Le scenario souhaité : normal, high, low')
            ->addOption('clean', null, null, 'Supprimer toutes les données factices');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        if ($input->getOption('clean')) {
            $count = $this->entityManager->createQuery('DELETE FROM App\Entity\Mesure m WHERE m.user IS NULL')->execute();
            $io->success("$count mesures factices ont été supprimées.");
            return Command::SUCCESS;
        }

        $scenario = $input->getArgument('scenario');

        $aquarium = $this->entityManager->getRepository(Aquarium::class)->findOneBy([]);
        if (!$aquarium) {
            $io->error("Aucun aquarium trouvé !");
            return Command::FAILURE;
        }

        $io->title("Génération d'un historique de 30 jours : $scenario");

        for ($i = 30; $i >= 0; $i--) {
            $date = new \DateTime("-$i days");
            
            $mesure = new Mesure();
            $mesure->setDateSaisie($date);
            $mesure->setAquarium($aquarium);

            // On ajoute une petite variation aléatoire pour que ce soit réaliste
            $v = rand(-10, 10) / 100; 

            switch ($scenario) {
                case 'high':
                    $mesure->setTemperature(28 + $v);
                    $mesure->setPh(8.2 + $v);
                    $mesure->setGh(14);
                    $mesure->setKh(11);
                    $mesure->setNitrites(0.4);
                    if ($i === 0) $this->createAlerte("ALERTE HAUTE", "Historique de test haut", $aquarium, $mesure);
                    break;

                case 'low':
                    $mesure->setTemperature(19 + $v);
                    $mesure->setPh(5.8 + $v);
                    $mesure->setGh(4);
                    $mesure->setKh(3);
                    $mesure->setNitrites(0);
                    if ($i === 0) $this->createAlerte("ALERTE BASSE", "Historique de test bas", $aquarium, $mesure);
                    break;

                case 'random':
                    $mesure->setTemperature(rand(180, 320) / 10); // 18.0 à 32.0
                    $mesure->setPh(rand(50, 95) / 10); // 5.0 à 9.5
                    $mesure->setGh(rand(0, 25));
                    $mesure->setKh(rand(0, 15));
                    $mesure->setNitrites(rand(0, 20) / 10); // 0 à 2.0
                    if (rand(0, 10) > 8) $this->createAlerte("ALERTE ALÉATOIRE", "Généré par le chaos", $aquarium, $mesure);
                    break;

                case 'normal':
                default:
                    $mesure->setTemperature(24.5 + $v);
                    $mesure->setPh(7.1 + $v);
                    $mesure->setGh(8);
                    $mesure->setKh(6);
                    $mesure->setNitrites(0);
                    break;
            }

            $this->entityManager->persist($mesure);
        }

        $this->entityManager->flush();
        $io->success("Historique de 30 jours généré avec succès !");

        return Command::SUCCESS;
    }

    private function createAlerte(string $nom, string $message, Aquarium $aquarium, Mesure $mesure): void
    {
        $alerte = new Alerte();
        $alerte->setNom($nom);
        $alerte->setMessageAlerte($message);
        $alerte->setDateAlerte(new \DateTime());
        $alerte->setAquarium($aquarium);
        $this->entityManager->persist($alerte);
        $mesure->setAlerte($alerte);
    }
}
