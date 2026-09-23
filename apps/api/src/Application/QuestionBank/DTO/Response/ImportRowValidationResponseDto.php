<?php
declare(strict_types=1);
namespace App\Application\QuestionBank\DTO\Response;
final readonly class ImportRowValidationResponseDto {
    /** @param list<array{field:string,code:string,message:string}> $errors */
    public function __construct(public int $rowNumber,public bool $valid,public array $errors) {}
}
