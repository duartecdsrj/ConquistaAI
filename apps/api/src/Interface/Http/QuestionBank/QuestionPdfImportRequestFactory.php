<?php
declare(strict_types=1);
namespace App\Interface\Http\QuestionBank;
use App\Application\QuestionBank\DTO\Request\ListQuestionPdfImportJobsRequestDto;
use App\Application\QuestionBank\DTO\Request\QueueQuestionPdfImportRequestDto;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
final class QuestionPdfImportRequestFactory
{
    /** @return list<QueueQuestionPdfImportRequestDto> */
    public function queue(ServerRequestInterface $request): array
    {
        $files = $request->getUploadedFiles()['documents'] ?? [];
        if ($files instanceof \Psr\Http\Message\UploadedFileInterface) $files = [$files];
        if (!is_array($files) || $files === []) throw new InvalidArgumentException('Selecione ao menos um PDF.');
        $items = [];
        foreach ($files as $file) { if (!$file instanceof \Psr\Http\Message\UploadedFileInterface || $file->getError() !== UPLOAD_ERR_OK) throw new InvalidArgumentException('Um arquivo nao pode ser lido.'); $items[] = new QueueQuestionPdfImportRequestDto('', $file->getClientFilename() ?? 'questoes.pdf', $file->getClientMediaType() ?? '', $file->getStream()->getContents()); }
        return $items;
    }
    public function list(ServerRequestInterface $request): ListQuestionPdfImportJobsRequestDto
    {
        $query = $request->getQueryParams();
        $page = filter_var($query['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $perPage = filter_var($query['per_page'] ?? 25, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]);
        if ($page === false || $perPage === false) throw new InvalidArgumentException('Parametros de paginacao invalidos.');
        return new ListQuestionPdfImportJobsRequestDto($page, $perPage);
    }
}
