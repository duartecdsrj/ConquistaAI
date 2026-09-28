<?php
declare(strict_types=1);
namespace App\Infrastructure\Identity;
use App\Application\Identity\Port\GoogleJwksFetcherInterface;
final class GoogleJwksHttpFetcher implements GoogleJwksFetcherInterface
{
    public function fetch(): array
    {
        $raw=@file_get_contents('https://www.googleapis.com/oauth2/v3/certs',false,stream_context_create(['http'=>['timeout'=>5,'ignore_errors'=>true]]));
        $data=is_string($raw)?json_decode($raw,true):null;
        if(!is_array($data)||!is_array($data['keys']??null))throw new \DomainException('Não foi possível validar a credencial Google.');
        return array_values(array_filter($data['keys'],static fn(mixed $key):bool=>is_array($key)&&is_string($key['kid']??null)&&is_string($key['n']??null)&&is_string($key['e']??null)));
    }
}
