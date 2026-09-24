<?php
declare(strict_types=1);
namespace App\Infrastructure\Assistant;
use App\Application\Assistant\Port\AssistantProviderInterface;use App\Domain\Assistant\ValueObject\AssistantGeneratedAnswer;
final class DeterministicSyllabusAssistantProvider implements AssistantProviderInterface { public function answer(string $question,array $evidence):AssistantGeneratedAnswer{$pages=implode(', ',array_map(static fn($e)=>(string)$e->pageNumber,$evidence));$summary=implode("\n\n",array_map(static fn($e)=>'Página '.$e->pageNumber.': '.$e->excerpt,$evidence));return new AssistantGeneratedAnswer("Resposta baseada exclusivamente nas páginas {$pages} do edital.\n\n{$summary}",'local-rag','deterministic-v1',$evidence);} }
