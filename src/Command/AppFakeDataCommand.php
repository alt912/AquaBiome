<?php

namespace App\Command;

use App\Entity\Alerte;
use App\Entity\Aquarium;
use App\Entity\Mesure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fake-data',
    description: 'Génère un historique de 30 jours de test (normal, high, low, random)',
)]
class AppFakeDataCommand extends Command
{
    // ── Seuils normaux (identiques à AjoutDonneeController) ──────────────
    private const SEUILS = [
        'temperature' => ['min' => 24,  'max' => 28,  'unite' => '°C'],
        'ph'          => ['min' => 6.5, 'max' => 7.5, 'unite' => ''],
        'gh'          => ['min' => 6,   'max' => 12,  'unite' => '°dH'],
        'kh'          => ['min' => 3,   'max' => 10,  'unite' => '°dH'],
        'nitrites'    => ['min' => 0,   'max' => 0.5, 'unite' => 'mg/L'],
        'ammonium'    => ['min' => 0,   'max' => 0.1, 'unite' => 'mg/L'],
    ];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('scenario', InputArgument::OPTIONAL, 'Scénario : normal, high, low, random')
            ->addOption('clean', null, null, 'Supprimer toutes les données factices');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // ── Mode nettoyage ────────────────────────────────────────────────
        if ($input->getOption('clean')) {
            $count = $this->entityManager
                ->createQuery('DELETE FROM App\Entity\Mesure m WHERE m.user IS NULL')
                ->execute();
            $io->success("$count mesures factices ont été supprimées.");
            return Command::SUCCESS;
        }

        $scenario = $input->getArgument('scenario') ?? 'normal';

        $aquarium = $this->entityManager->getRepository(Aquarium::class)->findOneBy([]);
        if (!$aquarium) {
            $io->error("Aucun aquarium trouvé !");
            return Command::FAILURE;
        }

        $io->title("Génération d'un historique de 30 jours : $scenario");

        $alertesCreees = 0;

        for ($i = 30; $i >= 0; $i--) {
            $date   = new \DateTime("-$i days");
            $mesure = new Mesure();
            $mesure->setDateSaisie($date);
            $mesure->setAquarium($aquarium);

            // Petite variation aléatoire pour réalisme
            $v = rand(-10, 10) / 100;

            match ($scenario) {
                'high' => $this->fillHigh($mesure, $v),
                'low'  => $this->fillLow($mesure, $v),
                'random' => $this->fillRandom($mesure),
                default  => $this->fillNormal($mesure, $v),
            };

            $this->entityManager->persist($mesure);

            // Génère des alertes uniquement si les valeurs sont hors seuils
            $alerte = $this->checkAndCreateAlerte($mesure, $aquarium);
            if ($alerte) {
                $this->entityManager->persist($alerte);
                $alertesCreees++;
            }
        }

        $this->entityManager->flush();

        $io->success("Historique de 30 jours généré avec succès ! ($alertesCreees alerte(s) créée(s))");

        return Command::SUCCESS;
    }

    // ── Scénarios ─────────────────────────────────────────────────────────

    private function fillNormal(Mesure $mesure, float $v): void
    {
        $mesure->setTemperature(24.5 + $v);
        $mesure->setPh(7.1 + $v);
        $mesure->setGh(8);
        $mesure->setKh(6);
        $mesure->setNitrites(0);
        $mesure->setAmmonium(0);
    }

    private function fillHigh(Mesure $mesure, float $v): void
    {
        $mesure->setTemperature(29.5 + $v);   // > 28 → alerte
        $mesure->setPh(8.0 + $v);              // > 7.5 → alerte
        $mesure->setGh(15);                    // > 12 → alerte
        $mesure->setKh(12);                    // > 10 → alerte
        $mesure->setNitrites(0.8);             // > 0.5 → alerte
        $mesure->setAmmonium(0.3);             // > 0.1 → alerte
    }

    private function fillLow(Mesure $mesure, float $v): void
    {
        $mesure->setTemperature(21.0 + $v);   // < 24 → alerte
        $mesure->setPh(5.9 + $v);             // < 6.5 → alerte
        $mesure->setGh(3);                    // < 6 → alerte
        $mesure->setKh(1);                    // < 3 → alerte
        $mesure->setNitrites(0);
        $mesure->setAmmonium(0);
    }

    private function fillRandom(Mesure $mesure): void
    {
        $mesure->setTemperature(rand(180, 320) / 10);  // 18.0 – 32.0
        $mesure->setPh(rand(50, 95) / 10);             // 5.0 – 9.5
        $mesure->setGh(rand(0, 20));
        $mesure->setKh(rand(0, 15));
        $mesure->setNitrites(rand(0, 15) / 10);        // 0 – 1.5
        $mesure->setAmmonium(rand(0, 5) / 10);         // 0 – 0.5
    }

    // ── Vérification des seuils (même logique que AjoutDonneeController) ─

    private function checkAndCreateAlerte(Mesure $mesure, Aquarium $aquarium): ?Alerte
    {
        $messages = [];

        $valeurs = [
            'temperature' => $mesure->getTemperature(),
            'ph'          => $mesure->getPh(),
            'gh'          => $mesure->getGh(),
            'kh'          => $mesure->getKh(),
            'nitrites'    => $mesure->getNitrites(),
            'ammonium'    => $mesure->getAmmonium(),
        ];

        foreach ($valeurs as $param => $valeur) {
            if ($valeur === null) continue;

            $seuil = self::SEUILS[$param];

            if ($valeur < $seuil['min']) {
                $label = ucfirst($param);
                $messages[] = "$label trop bas ($valeur{$seuil['unite']}, min: {$seuil['min']}{$seuil['unite']})";
            } elseif ($valeur > $seuil['max']) {
                $label = ucfirst($param);
                $messages[] = "$label trop élevé ($valeur{$seuil['unite']}, max: {$seuil['max']}{$seuil['unite']})";
            }
        }

        if (empty($messages)) {
            return null; // Tout est normal → pas d'alerte
        }

        $alerte = new Alerte();
        $alerte->setNom('Alerte Mesure');
        $alerte->setMessageAlerte(implode(' | ', $messages));
        $alerte->setDateAlerte($mesure->getDateSaisie() ?? new \DateTime());
        $alerte->setAquarium($aquarium);

        $mesure->setAlerte($alerte);

        return $alerte;
    }
}
