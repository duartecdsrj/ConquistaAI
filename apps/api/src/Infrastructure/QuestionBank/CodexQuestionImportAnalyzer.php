<?php
declare(strict_types=1);

namespace App\Infrastructure\QuestionBank;

use App\Application\QuestionBank\AI\QuestionImportAnalysisResponseValidator;
use App\Application\QuestionBank\AI\QuestionImportAnalysisSchema;
use App\Application\QuestionBank\DTO\Request\AnalyzeQuestionImportCandidateRequestDto;
use App\Application\QuestionBank\DTO\Response\QuestionImportAnalyzerResponseDto;
use App\Application\QuestionBank\Port\QuestionImportAnalyzerInterface;
use App\Domain\QuestionBank\ValueObject\QuestionImportTokenUsage;
use App\Infrastructure\Database;

final class CodexQuestionImportAnalyzer implements QuestionImportAnalyzerInterface
{
    public function __construct(private readonly QuestionImportAnalysisResponseValidator $validator = new QuestionImportAnalysisResponseValidator()) {}

    public function analyze(AnalyzeQuestionImportCandidateRequestDto $candidate): QuestionImportAnalyzerResponseDto
    {
        $workspace = '/correction-workspaces/question-import-analysis-'.bin2hex(random_bytes(8));
        if (!mkdir($workspace, 0700, true) && !is_dir($workspace)) throw new \RuntimeException('Workspace de importação indisponível.');
        try {
            $input = $this->writeInput($workspace, $candidate);
            file_put_contents($workspace.'/input.json', json_encode($input, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
            file_put_contents($workspace.'/schema.json', json_encode(QuestionImportAnalysisSchema::jsonSchema(), JSON_THROW_ON_ERROR));
            $started = microtime(true);
            $this->run($workspace);
            $payload = json_decode((string) file_get_contents($workspace.'/result.json'), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($payload)) throw new \RuntimeException('Resposta Codex inválida.');
            return new QuestionImportAnalyzerResponseDto($this->validator->validate($payload, $candidate), 'codex', Database::env('QUESTION_IMPORT_CODEX_MODEL', 'configured'), QuestionImportTokenUsage::unavailable(), (int) ((microtime(true) - $started) * 1000));
        } finally { $this->deleteWorkspace($workspace); }
    }

    /** @return array<string,mixed> */
    private function writeInput(string $workspace, AnalyzeQuestionImportCandidateRequestDto $candidate): array
    {
        $images = [];
        $imageDirectory = $workspace.'/images';
        foreach ($candidate->images as $image) {
            if (!is_dir($imageDirectory) && !mkdir($imageDirectory, 0700, true) && !is_dir($imageDirectory)) throw new \RuntimeException('Diretório de imagens indisponível.');
            $name = $image->pageNumber.'-'.$image->assetIndex.(str_ends_with($image->assetPath, '-render.png') ? '-render' : '').'.png';
            if (!is_file($image->assetPath) || !copy($image->assetPath, $imageDirectory.'/'.$name)) throw new \RuntimeException('Imagem candidata indisponível.');
            $images[] = ['page_number' => $image->pageNumber, 'asset_index' => $image->assetIndex, 'file' => 'images/'.$name];
        }
        return ['schema_version' => $candidate->schemaVersion, 'candidate_fingerprint' => $candidate->candidateFingerprint, 'evidence_pages' => array_map(static fn ($page): array => ['page_number' => $page->pageNumber, 'content' => $page->content], $candidate->evidencePages), 'images' => $images, 'allowed_taxonomy_paths' => $candidate->taxonomyPaths];
    }

    public function analyzeBatch(array $candidates): \App\Application\QuestionBank\DTO\Response\QuestionImportBatchAnalyzerResponseDto
    {
        if ($candidates === []) throw new \InvalidArgumentException("Lote de candidatos vazio.");
        $workspace = "/correction-workspaces/question-import-batch-".bin2hex(random_bytes(8));
        if (!mkdir($workspace, 0700, true) && !is_dir($workspace)) throw new \RuntimeException("Workspace de importação indisponível.");
        try {
            $input = ["schema_version" => "question-import-analysis-batch-v1", "candidates" => array_map(fn (AnalyzeQuestionImportCandidateRequestDto $candidate): array => $this->writeBatchInput($workspace, $candidate), $candidates)];
            file_put_contents($workspace."/input.json", json_encode($input, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
            file_put_contents($workspace."/schema.json", json_encode(QuestionImportAnalysisSchema::batchJsonSchema(), JSON_THROW_ON_ERROR));
            $started = microtime(true); $this->run($workspace);
            $payload = json_decode((string) file_get_contents($workspace."/result.json"), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($payload) || ($payload["schema_version"] ?? null) !== "question-import-analysis-batch-v1" || !is_array($payload["items"] ?? null)) throw new \RuntimeException("Resposta Codex em lote inválida.");
            $byFingerprint = []; foreach ($candidates as $candidate) $byFingerprint[$candidate->candidateFingerprint] = $candidate; $responses = []; $invalid = [];
            foreach ($payload["items"] as $item) { if (!is_array($item) || !is_string($item["candidate_fingerprint"] ?? null) || !is_array($item["analysis"] ?? null) || !isset($byFingerprint[$item["candidate_fingerprint"]])) continue; $fingerprint = $item["candidate_fingerprint"]; if (isset($responses[$fingerprint]) || in_array($fingerprint, $invalid, true)) continue; try { $responses[$fingerprint] = new QuestionImportAnalyzerResponseDto($this->validator->validate($item["analysis"], $byFingerprint[$fingerprint]), "codex", Database::env("QUESTION_IMPORT_CODEX_MODEL", "configured"), QuestionImportTokenUsage::unavailable(), 0); } catch (\DomainException $exception) { error_log("Question import candidate ".$fingerprint.": ".$exception->getMessage()); $invalid[] = $fingerprint; } }
            $duration = (int) ((microtime(true) - $started) * 1000);
            $perCandidateDuration = max(1, intdiv($duration, max(1, count($responses))));
            foreach ($responses as $fingerprint => $response) $responses[$fingerprint] = new QuestionImportAnalyzerResponseDto($response->analysis, $response->provider, $response->model, $response->tokenUsage, $perCandidateDuration);
            return new \App\Application\QuestionBank\DTO\Response\QuestionImportBatchAnalyzerResponseDto($responses, "codex", Database::env("QUESTION_IMPORT_CODEX_MODEL", "configured"), QuestionImportTokenUsage::unavailable(), $duration, $invalid);
        } finally { $this->deleteWorkspace($workspace); }
    }

    private function writeBatchInput(string $workspace, AnalyzeQuestionImportCandidateRequestDto $candidate): array
    {
        $images = [];
        foreach ($candidate->images as $image) {
            $directory = $workspace."/images/".$candidate->candidateFingerprint;
            if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) throw new \RuntimeException("Diretório de imagens indisponível.");
            $name = $image->pageNumber."-".$image->assetIndex.(str_ends_with($image->assetPath, '-render.png') ? '-render' : '').".png";
            if (!is_file($image->assetPath) || !copy($image->assetPath, $directory."/".$name)) throw new \RuntimeException("Imagem candidata indisponível.");
            $images[] = ["page_number" => $image->pageNumber, "asset_index" => $image->assetIndex, "file" => "images/".$candidate->candidateFingerprint."/".$name];
        }
        return ["schema_version" => $candidate->schemaVersion, "candidate_fingerprint" => $candidate->candidateFingerprint, "evidence_pages" => array_map(static fn ($page): array => ["page_number" => $page->pageNumber, "content" => $page->content], $candidate->evidencePages), "images" => $images, "allowed_taxonomy_paths" => $candidate->taxonomyPaths];
    }

    private function run(string $workspace): void
    {
        $network = Database::env('QUESTION_IMPORT_CODEX_NETWORK', Database::env('CORRECTION_CODEX_NETWORK', 'conquistaai_public'));
        $volume = Database::env('CORRECTION_WORKSPACE_VOLUME', 'conquistaai_correction_workspaces');
        $auth = Database::env('CODEX_AUTH_VOLUME', 'conquistaai_codex_auth');
        // O runner já é um contêiner efêmero isolado, sem socket Docker e com somente workspace/auth montados.
        // `workspace-write` requer namespaces de usuário via Bubblewrap, indisponíveis no kernel do host.
        $command = ['docker', 'run', '--rm', '--network', $network, '--mount', 'type=volume,src='.$volume.',dst=/correction-workspaces', '--mount', 'type=volume,src='.$auth.',dst=/root/.codex', '-w', $workspace, 'concursos-codex-runner', 'exec', '--ephemeral', '--ignore-user-config', '--ignore-rules', '--sandbox', 'danger-full-access', '--skip-git-repo-check', '--cd', $workspace, '--output-schema', $workspace.'/schema.json', '--output-last-message', $workspace.'/result.json', 'Use exclusivamente input.json e os arquivos relativos de images. Analise cada candidato de input.json de forma independente, preserve integralmente enunciado e alternativas, use metadados somente quando houver evidência explícita nas páginas, nunca invente gabarito e retorne um item por candidate_fingerprint somente no JSON conforme schema.json. Em todos os campos de página (evidence_pages, metadata.evidence_pages, findings.evidence_pages e image_anchors.source_page), use somente page_number declarado no evidence_pages do MESMO candidato; nunca cite página adjacente ou número inferido. Se image_assessment for DISCREPANCY, inclua obrigatoriamente um finding com code IMAGE_DISCREPANCY; caso contrário use NONE ou CONSISTENT conforme a evidência. Em taxonomy_path, devolva exatamente uma das allowed_taxonomy_paths do candidato, segmentada por " > "; escolha sempre uma folha, jamais categoria-pai ou nome inventado. Em safe_summary, não informe alternativa ou gabarito. Extraia banca, concurso, cargo e ano quando estiverem explícitos no cabeçalho; remova esse cabeçalho do enunciado. Quando images não estiver vazio, abra e inspecione visualmente cada arquivo listado antes de montar a resposta. Quando a camada textual tiver rótulos de alternativas sem conteúdo, leia as imagens da mesma página para transcrever cada alternativa; nunca repita uma alternativa para preencher lacuna. Para cada imagem ancorada, insira um marcador sequencial [[FIGURA:n]] exatamente uma vez no enunciado ou alternativa correspondente, imediatamente após a referência textual.'];
        $prompt = array_pop($command);
        $allImages = array_merge(glob($workspace.'/images/*.png') ?: [], glob($workspace.'/images/*/*.png') ?: []);
        $renderedPages = array_values(array_filter($allImages, static fn (string $path): bool => str_ends_with($path, '-render.png')));
        $attachedImages = array_slice($renderedPages !== [] ? $renderedPages : $allImages, 0, 12);
        foreach ($attachedImages as $image) { $command[] = '--image'; $command[] = $image; }
        $command[] = '--';
        $command[] = $prompt;
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $workspace);
        if (!is_resource($process)) throw new \RuntimeException('Executor Codex indisponível.');
        fclose($pipes[0]); stream_set_blocking($pipes[1], false); stream_set_blocking($pipes[2], false);
        $idleTimeout = max(60, (int) Database::env("QUESTION_IMPORT_CODEX_IDLE_TIMEOUT", "900"));
        $lastActivityAt = microtime(true);
        $exitCode = null;
        $stderr = '';
        while (true) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }
            if (microtime(true) - $lastActivityAt > $idleTimeout) { proc_terminate($process, 9); throw new \RuntimeException("Analisador sem atividade pelo período configurado."); }
            $stdoutChunk = stream_get_contents($pipes[1]); $stderrChunk = stream_get_contents($pipes[2]); if ($stdoutChunk !== "" || $stderrChunk !== "") $lastActivityAt = microtime(true); $stderr .= $stderrChunk; usleep(200000);
        }
        stream_get_contents($pipes[1]); fclose($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]); fclose($pipes[2]);
        $closeExitCode = proc_close($process);
        $exitCode = is_int($exitCode) && $exitCode >= 0 ? $exitCode : $closeExitCode;
        if ($exitCode !== 0 || !is_file($workspace.'/result.json')) {
            $diagnostic = trim(preg_replace('/\s+/', ' ', $stderr) ?? '');
            error_log(sprintf('Codex question import executor failed: exit=%d result=%s stderr=%s', $exitCode, is_file($workspace.'/result.json') ? 'present' : 'missing', mb_substr($diagnostic, 0, 1000)));
            throw new \RuntimeException('Executor Codex não concluiu a análise.');
        }
    }

    private function deleteWorkspace(string $workspace): void
    {
        foreach (glob($workspace.'/images/*') ?: [] as $file) if (is_file($file)) unlink($file);
        if (is_dir($workspace.'/images')) @rmdir($workspace.'/images');
        foreach (['input.json', 'schema.json', 'result.json'] as $file) if (is_file($workspace.'/'.$file)) unlink($workspace.'/'.$file);
        @rmdir($workspace);
    }
}
