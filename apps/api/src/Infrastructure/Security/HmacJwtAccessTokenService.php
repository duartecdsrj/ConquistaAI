<?php
declare(strict_types=1);
namespace App\Infrastructure\Security;
use App\Application\Identity\Port\AccessTokenClaims;
use App\Application\Identity\Port\AccessTokenIssuerInterface;
use App\Application\Identity\Port\AccessTokenVerifierInterface;
use App\Domain\Identity\Entity\User;
use App\Infrastructure\Database;
final class HmacJwtAccessTokenService implements AccessTokenIssuerInterface, AccessTokenVerifierInterface {
 public function issue(User $user):string{$now=time();$head=self::b64(json_encode(['alg'=>'HS256','typ'=>'JWT'],JSON_THROW_ON_ERROR));$body=self::b64(json_encode(['iss'=>Database::env('JWT_ISSUER','concursos-api'),'sub'=>$user->id,'roles'=>$user->roles,'iat'=>$now,'exp'=>$now+(int)Database::env('ACCESS_TOKEN_TTL','900')],JSON_THROW_ON_ERROR));$sig=self::b64(hash_hmac('sha256',$head.'.'.$body,Database::env('JWT_SECRET'),true));return $head.'.'.$body.'.'.$sig;}
 public function verify(string $accessToken):AccessTokenClaims{$parts=explode('.',$accessToken);if(count($parts)!==3){throw new \DomainException('Invalid access token.');}$expected=self::b64(hash_hmac('sha256',$parts[0].'.'.$parts[1],Database::env('JWT_SECRET'),true));if(!hash_equals($expected,$parts[2])){throw new \DomainException('Invalid access token.');}$claims=json_decode(self::unb64($parts[1]),true,512,JSON_THROW_ON_ERROR);if(($claims['iss']??null)!==Database::env('JWT_ISSUER','concursos-api')||!is_string($claims['sub']??null)||!is_int($claims['exp']??null)||$claims['exp']<time()){throw new \DomainException('Invalid access token.');}return new AccessTokenClaims($claims['sub']);}
 private static function b64(string $value):string{return rtrim(strtr(base64_encode($value),'+/','-_'),'=');} private static function unb64(string $value):string{return base64_decode(strtr($value,'-_','+/').str_repeat('=',(4-strlen($value)%4)%4),true)?:throw new \DomainException('Invalid access token.');}
}
