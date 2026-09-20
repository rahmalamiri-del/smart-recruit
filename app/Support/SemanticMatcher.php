<?php

namespace SmartRecruit\Support;

class SemanticMatcher
{
    private array $stopWords = [
        'a', 'au', 'aux', 'avec', 'ce', 'ces', 'dans', 'de', 'des', 'du', 'elle', 'en', 'et', 'eux', 'il',
        'je', 'la', 'le', 'les', 'leur', 'lui', 'ma', 'mais', 'me', 'meme', 'mes', 'moi', 'mon', 'ne',
        'nos', 'notre', 'nous', 'on', 'ou', 'par', 'pas', 'pour', 'qu', 'que', 'qui', 'sa', 'se', 'ses',
        'son', 'sur', 'ta', 'te', 'tes', 'toi', 'ton', 'tu', 'un', 'une', 'vos', 'votre', 'vous',
        'the', 'and', 'or', 'of', 'to', 'in', 'for', 'with', 'is', 'are', 'an',
    ];

    public function __construct(
        private readonly array $skillAliases,
        private readonly array $skillTaxonomy,
    ) {
    }

    public function score(array $offer, array $student): array
    {
        $offerText = $offer['title'].' '.$offer['description'].' '.implode(' ', $offer['required_skills'] ?? []);
        $studentText = $student['headline'].' '.$student['education'].' '.$student['cv_text'].' '.implode(' ', $student['skills'] ?? []);

        $requiredSkills = $this->canonicalSkills(array_merge(
            $offer['required_skills'] ?? [],
            $this->extractSkills($offerText)
        ));

        $candidateSkills = $this->canonicalSkills(array_merge(
            $student['skills'] ?? [],
            $this->extractSkills($studentText)
        ));

        $matchedSkills = array_values(array_intersect($requiredSkills, $candidateSkills));
        $missingSkills = array_values(array_diff($requiredSkills, $candidateSkills));

        $skillCoverage = count($requiredSkills) > 0 ? count($matchedSkills) / count($requiredSkills) : 0.0;
        $semanticCoverage = $this->categoryCoverage($requiredSkills, $candidateSkills);
        $cosine = $this->tfidfCosineSimilarity($this->tokens($offerText), $this->tokens($studentText));

        $experienceBoost = min((float) ($student['experience_years'] ?? 0) / 5, 1.0) * 0.05;
        $score = (0.55 * $skillCoverage) + (0.30 * $cosine) + (0.10 * $semanticCoverage) + $experienceBoost;
        $score = max(0, min(100, (int) round($score * 100)));

        return [
            'score' => $score,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'semantic_coverage' => round($semanticCoverage * 100),
            'text_similarity' => round($cosine * 100),
            'algorithm' => 'tf-idf-cosine+semantic-skill-taxonomy',
            'source' => 'local-php',
            'explanation' => $this->explain($score, $matchedSkills, $missingSkills),
        ];
    }

    public function extractSkills(string $text): array
    {
        $normalized = ' '.$this->normalize($text).' ';
        $skills = [];

        foreach ($this->skillAliases as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                $needle = ' '.$this->normalize($alias).' ';

                if (str_contains($normalized, $needle)) {
                    $skills[] = $canonical;
                    break;
                }
            }
        }

        return array_values(array_unique($skills));
    }

    private function canonicalSkills(array $skills): array
    {
        $canonical = [];

        foreach ($skills as $skill) {
            $skill = trim((string) $skill);

            if ($skill === '') {
                continue;
            }

            $detected = $this->extractSkills($skill);
            $canonical[] = $detected[0] ?? $this->normalize($skill);
        }

        return array_values(array_unique($canonical));
    }

    private function categoryCoverage(array $requiredSkills, array $candidateSkills): float
    {
        $requiredCategories = $this->categories($requiredSkills);
        $candidateCategories = $this->categories($candidateSkills);

        if (count($requiredCategories) === 0) {
            return 0.0;
        }

        return count(array_intersect($requiredCategories, $candidateCategories)) / count($requiredCategories);
    }

    private function categories(array $skills): array
    {
        $categories = [];

        foreach ($skills as $skill) {
            if (isset($this->skillTaxonomy[$skill])) {
                $categories[] = $this->skillTaxonomy[$skill];
            }
        }

        return array_values(array_unique($categories));
    }

    private function tokens(string $text): array
    {
        $text = $this->normalize($text);

        foreach ($this->skillAliases as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                $text = str_replace($this->normalize($alias), str_replace(' ', '_', $canonical), $text);
            }
        }

        $parts = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_filter($parts, function (string $token): bool {
            return mb_strlen($token) > 2 && ! in_array($token, $this->stopWords, true);
        }));
    }

    private function tfidfCosineSimilarity(array $aTokens, array $bTokens): float
    {
        $a = $this->tfidfVector($aTokens, [$aTokens, $bTokens]);
        $b = $this->tfidfVector($bTokens, [$aTokens, $bTokens]);
        $terms = array_unique(array_merge(array_keys($a), array_keys($b)));
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($terms as $term) {
            $av = $a[$term] ?? 0;
            $bv = $b[$term] ?? 0;
            $dot += $av * $bv;
            $normA += $av ** 2;
            $normB += $bv ** 2;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    private function tfidfVector(array $tokens, array $corpus): array
    {
        $counts = array_count_values($tokens);
        $total = max(count($tokens), 1);
        $documents = count($corpus);
        $vector = [];

        foreach ($counts as $term => $count) {
            $documentFrequency = 0;

            foreach ($corpus as $documentTokens) {
                if (in_array($term, $documentTokens, true)) {
                    $documentFrequency++;
                }
            }

            $termFrequency = $count / $total;
            $inverseDocumentFrequency = log((1 + $documents) / (1 + $documentFrequency)) + 1;
            $vector[$term] = $termFrequency * $inverseDocumentFrequency;
        }

        return $vector;
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = $transliterated ?: $text;
        $text = preg_replace('/[^a-z0-9+#.]+/', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    private function explain(int $score, array $matchedSkills, array $missingSkills): string
    {
        if ($score >= 80) {
            return 'Très forte compatibilité, les compétences clés sont bien couvertes.';
        }

        if ($score >= 60) {
            return 'Profil pertinent, avec quelques compétences à vérifier en entretien.';
        }

        if (count($matchedSkills) > 0) {
            return 'Compatibilité partielle: le profil couvre une partie du besoin.';
        }

        return count($missingSkills) > 0
            ? 'Faible compatibilité avec les exigences principales.'
            : 'Score basé surtout sur la similarité textuelle.';
    }
}
