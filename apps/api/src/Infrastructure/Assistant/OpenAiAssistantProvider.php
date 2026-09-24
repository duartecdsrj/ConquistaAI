<?php
declare(strict_types=1);
namespace App\Infrastructure\Assistant;
use App\Application\Assistant\Port\AssistantProviderInterface;
use App\Domain\Assistant\ValueObject\AssistantGeneratedAnswer;
use App\Infrastructure\Database;
final class OpenAiAssistantProvider implements AssistantProviderInterface {
 public function __construct(private readonly string $apiKey,private readonly string $model) {}
 public function answer(string $question,array $evidence):AssistantGeneratedAnswer {
  if($this->apiKey==='') throw new \DomainException('OPENAI_API_KEY nao foi configurada.');
  $sources=implode("\n\n",array_map(static fn($e)=>"[Página {$e->pageNumber}]\n{$e->excerpt}",$evidence));
  $payload=['model'=>$this->model,'store'=>false,'instructions'=>'Você é um assistente de estudos. Responda em português do Brasil usando exclusivamente as fontes fornecidas. Se elas não sustentarem a resposta, diga isso claramente. Cite as páginas relevantes e não invente regras, datas ou links.','input'=>"Pergunta do estudante:\n{$question}\n\nFontes recuperadas do edital:\n{$sources}"];
  $context=stream_context_create(['http'=>['method'=>'POST','timeout'=>30,'ignore_errors'=>true,'header'=>"Content-Type: application/json\r\nAuthorization: Bearer {$this->apiKey}\r\n",'content'=>json_encode($payload,JSON_THROW_ON_ERROR)]]);
  $body=@file_get_contents('https://api.openai.com/v1/responses',false,$context);
  if($body===false) throw new \DomainException('Nao foi possivel consultar a OpenAI.');
  try{$response=json_decode($body,true,512,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new \DomainException('A OpenAI retornou uma resposta invalida.');}
  $text=is_string($response['output_text']??null)?trim($response['output_text']):'';
  if($text===''){ $message=$response['error']['message']??null; throw new \DomainException(is_string($message)?'OpenAI: '.$message:'A OpenAI nao retornou texto.'); }
  return new AssistantGeneratedAnswer($text,'openai',$this->model,$evidence);
 }
}
