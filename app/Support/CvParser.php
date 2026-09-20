<?php

namespace SmartRecruit\Support;

use Illuminate\Http\UploadedFile;

class CvParser
{
    // En dessous de ce nombre de caractères extraits d'un fichier, on considère que
    // l'extraction a échoué (PDF scanné/image, flux compressé illisible en local...).
    private const MIN_RELIABLE_LENGTH = 40;

    public function __construct(
        private readonly SemanticMatcher $matcher,
        private readonly ?AiClient $aiClient = null,
    ) {
    }

    public function parse(?UploadedFile $file, string $fallbackText = ''): array
    {
        $manualText = trim($fallbackText);
        $extractedFromFile = '';
        $fileName = null;

        if ($file) {
            $fileName = $this->safeFileName($file);
            $extractedFromFile = trim($this->extractText($file));
        }

        $text = trim($extractedFromFile.' '.$manualText);

        return [
            'file_name' => $fileName,
            'text' => $text,
            'skills' => $this->matcher->extractSkills($text),
            'experience_years' => $this->extractExperience($text),
            'education' => $this->extractEducation($text),
            'metadata' => [
                'emails' => $this->extractEmails($text),
                'phones' => $this->extractPhones($text),
                'detected_skills' => $this->matcher->extractSkills($text),
            ],
            // true si un fichier a été fourni mais que son contenu n'a pas pu être
            // extrait de façon exploitable, et qu'aucun texte manuel ne compense.
            'extraction_unreliable' => $file !== null
                && mb_strlen($extractedFromFile) < self::MIN_RELIABLE_LENGTH
                && $manualText === '',
        ];
    }

    private function extractText(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'pdf' && $this->aiClient) {
            $parsed = $this->aiClient->parseCv($file);
            $text = trim((string) ($parsed['text'] ?? ''));

            if ($text !== '') {
                return $text;
            }
        }

        return $this->extractTextLocally($file, $extension);
    }

    /**
     * Repli local si le microservice IA est hors-ligne ou n'a rien pu extraire.
     * Fiable pour du texte brut ; pour un PDF réel (flux compressés), ce hack
     * regex ne récupère souvent rien d'exploitable — d'où le flag `extraction_unreliable`.
     */
    private function extractTextLocally(UploadedFile $file, string $extension): string
    {
        $contents = @file_get_contents($file->getRealPath());

        if (! is_string($contents)) {
            return '';
        }

        if (in_array($extension, ['txt', 'md', 'csv'], true)) {
            return $contents;
        }

        if ($extension === 'pdf') {
            $contents = preg_replace('/\(([^()]*)\)/', ' $1 ', $contents) ?? $contents;
        }

        $text = preg_replace('/[^\pL\pN@+.#:\/\-_ ]+/u', ' ', $contents) ?? '';

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    private function extractExperience(string $text): int
    {
        if (preg_match_all('/(\d+)\s*(?:ans|années|years?)\s*(?:d[’\']?)?\s*(?:expérience|experience)?/iu', $text, $matches)) {
            return max(array_map('intval', $matches[1]));
        }

        return 0;
    }

    private function extractEducation(string $text): string
    {
        $levels = ['doctorat', 'master', 'mastère', 'ingénieur', 'licence', 'bachelor', 'bts'];
        $lower = mb_strtolower($text, 'UTF-8');

        foreach ($levels as $level) {
            if (str_contains($lower, $level)) {
                return ucfirst($level);
            }
        }

        return 'Non renseigné';
    }

    private function extractEmails(string $text): array
    {
        preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $matches);

        return array_values(array_unique($matches[0] ?? []));
    }

    private function extractPhones(string $text): array
    {
        preg_match_all('/(?:\+?\d[\d\s().-]{7,}\d)/', $text, $matches);

        return array_values(array_unique(array_map('trim', $matches[0] ?? [])));
    }

    private function safeFileName(UploadedFile $file): string
    {
        $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());
        $base = preg_replace('/[^a-z0-9_-]+/i', '-', $base) ?: 'cv';

        return strtolower(trim($base, '-')).'-'.date('YmdHis').'.'.$extension;
    }
}
