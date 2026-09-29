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
            $name = $image->pageNumber.'-'.$image->assetIndex.'.png';
            if (!is_file($image->assetPath) || !copy($image->assetPath, $imageDirectory.'/'.$name)) throw new \RuntimeException('Imagem candidata indisponível.');
            $images[] = ['page_number' => $image->pageNumber, 'asset_index' => $image->assetIndex, 'file' => 'images/'.$name];
        }
        return ['schema_version' => $candidate->schemaVersion, 'candidate_fingerprint' => $candidate->candidateFingerprint, 'evidence_pages' => array_map(static fn ($page): array => ['page_number' => $page->pageNumber, 'content' => $page->content], $candidate->evidencePages), 'images' => $images, 'allowed_taxonomy_paths' => $candidate->taxonomyPaths];
    }

    private function run(string $workspace): void
    {
        $network = Database::env('QUESTION_IMPORT_CODEX_NETWORK', Database::env('CORRECTION_CODEX_NETWORK', 'conquistaai_public'));
        $volume = Database::env('CORRECTION_WORKSPACE_VOLUME', 'conquistaai_correction_workspaces');
        $auth = Database::env('CODEX_AUTH_VOLUME', 'conquistaai_codex_auth');
        $command = ['docker', 'run', '--rm', '--network', $network, '--mount', 'type=volume,src='.$volume.',dst=/correction-workspaces', '--mount', 'type=volume,src='.$auth.',dst=/root/.codex', '-w', $workspace, 'concursos-codex-runner', 'exec', '--ephemeral', '--ignore-user-config', '--ignore-rules', '--sandbox', 'workspace-write', '--skip-git-repo-check', '--cd', $workspace, '--output-schema', $workspace.'/schema.json', '--output-last-message', $workspace.'/result.json', 'Use exclusivamente input.json e os arquivos relativos de images. Analise uma única questão candidata, preserve integralmente enunciado e alternativas, use metadados somente quando houver evidência explícita nas páginas, nunca invente gabarito e retorne somente JSON conforme schema.json. Em safe_summary, não informe alternativa ou gabarito.'];
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $workspace);
        if (!is_resource($process)) throw new \RuntimeException('Executor Codex indisponível.');
        fclose($pipes[0]); stream_set_blocking($pipes[1], false); stream_set_blocking($pipes[2], false);
        $timeout = max(30, (int) Database::env('QUESTION_IMPORT_CODEX_TIMEOUT', '180'));
        $started = microtime(true);
        while ((proc_get_status($process))['running']) {
            if (microtime(true) - $started > $timeout) { proc_terminate($process, 9); throw new \RuntimeException('Tempo limite da análise de importação excedido.'); }
            stream_get_contents($pipes[1]); stream_get_contents($pipes[2]); usleep(200000);
        }
        foreach ([1, 2] as $pipe) { stream_get_contents($pipes[$pipe]); fclose($pipes[$pipe]); }
        if (proc_close($process) !== 0 || !is_file($workspace.'/result.json')) throw new \RuntimeException('Executor Codex não concluiu a análise.');
    }

    private function deleteWorkspace(string $workspace): void
    {
        foreach (glob($workspace.'/images/*') ?: [] as $file) if (is_file($file)) unlink($file);
        if (is_dir($workspace.'/images')) @rmdir($workspace.'/images');
        foreach (['input.json', 'schema.json', 'result.json'] as $file) if (is_file($workspace.'/'.$file)) unlink($workspace.'/'.$file);
        @rmdir($workspace);
    }
}
