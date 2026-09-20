<?php

namespace SmartRecruit\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Throwable;

class AiClient
{
    public function __construct(private readonly string $baseUrl)
    {
    }

    public function health(): array
    {
        return $this->get('/health') ?? [
            'status' => 'offline',
            'message' => 'Service d\'analyse non joignable',
        ];
    }

    public function match(string $offerText, string $cvText, array $requiredSkills = [], array $candidateSkills = []): ?array
    {
        return $this->post('/match', [
            'offer_text' => $offerText,
            'cv_text' => $cvText,
            'required_skills' => $requiredSkills,
            'candidate_skills' => $candidateSkills,
        ]);
    }

    /**
     * Extraction robuste du texte d'un CV (PDF réel, flux compressés inclus) via le
     * parseur Python (pdfminer). Retourne null si le service est injoignable ou si
     * l'extraction échoue, pour que l'appelant retombe sur le parseur local.
     */
    public function parseCv(UploadedFile $file): ?array
    {
        try {
            $response = Http::timeout(6)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(rtrim($this->baseUrl, '/').'/parse-cv');
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $decoded = $response->json();

        return is_array($decoded) && isset($decoded['text']) ? $decoded : null;
    }

    private function get(string $path): ?array
    {
        return $this->request('GET', $path);
    }

    private function post(string $path, array $payload): ?array
    {
        return $this->request('POST', $path, $payload);
    }

    private function request(string $method, string $path, array $payload = []): ?array
    {
        $url = rtrim($this->baseUrl, '/').$path;
        $options = [
            'http' => [
                'method' => $method,
                'timeout' => 0.4,
                'ignore_errors' => true,
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
            ],
        ];

        if ($method === 'POST') {
            $options['http']['content'] = json_encode($payload);
        }

        $response = @file_get_contents($url, false, stream_context_create($options));

        if (! is_string($response)) {
            return null;
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : null;
    }
}
