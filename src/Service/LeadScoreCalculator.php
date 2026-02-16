<?php

namespace App\Service;

use App\Entity\Lead;
use App\Entity\Interaction;
use Doctrine\ORM\EntityManagerInterface;

class LeadScoreCalculator
{
    public function calculate(Lead $lead): int
    {
        $score = 0;

        // Email professionnel (domaine non générique)
        if ($lead->getEmail()) {
            $domain = substr(strrchr($lead->getEmail(), "@"), 1);
            if (!in_array($domain, ['gmail.com', 'yahoo.fr', 'hotmail.com', 'outlook.fr', 'live.fr', 'orange.fr', 'free.fr'])) {
                $score += 10;
            }
        }

        // Téléphone renseigné
        if ($lead->getPhone()) {
            $score += 5;
        }

        // Entreprise renseignée
        if ($lead->getCompany()) {
            $score += 10;
        }

        // Interaction récente (<7 jours)
        $latestInteraction = $lead->getInteractions()->last();
        if ($latestInteraction) {
            $daysSince = $latestInteraction->getCreatedAt()->diff(new \DateTimeImmutable())->days;
            if ($daysSince < 7) {
                $score += 15;
            }
        }

        // Réponse positive (vérifier la dernière interaction avec outcome positif)
        if ($latestInteraction && $latestInteraction->getOutcome() === 'positif') {
            $score += 20;
        }

        // Budget confirmé (tag 'budget' présent)
        foreach ($lead->getTags() as $tag) {
            if (stripos($tag->getName(), 'budget') !== false) {
                $score += 25;
                break;
            }
        }

        // Inactivité >30 jours
        if ($latestInteraction) {
            $daysSince = $latestInteraction->getCreatedAt()->diff(new \DateTimeImmutable())->days;
            if ($daysSince > 30) {
                $score -= 10;
            }
        }

        // Clamp between 0 and 100
        return min(100, max(0, $score));
    }
}