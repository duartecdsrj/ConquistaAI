<?php
declare(strict_types=1);
namespace App\Infrastructure\Assistant;
use App\Application\Assistant\Port\AssistantProviderInterface;
use App\Infrastructure\Database;
final class ConfiguredAssistantProviderFactory {
 public static function create():AssistantProviderInterface {
  $provider=strtolower(Database::env('AI_PROVIDER','local'));
  return match($provider){
   'local','deterministic'=>new DeterministicSyllabusAssistantProvider(),
   'openai'=>new OpenAiAssistantProvider(Database::env('OPENAI_API_KEY'),Database::env('OPENAI_MODEL','gpt-5')),
   'gemini'=>new GeminiAssistantProvider(Database::env('GEMINI_API_KEY'),Database::env('GEMINI_MODEL','gemini-2.5-flash')),
   default=>throw new \DomainException('AI_PROVIDER invalido. Use local, openai ou gemini.')
  };
 }
}
