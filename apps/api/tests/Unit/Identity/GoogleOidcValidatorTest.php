<?php
declare(strict_types=1);
namespace Tests\Unit\Identity;
use App\Application\Identity\Port\GoogleJwksFetcherInterface;use App\Infrastructure\Identity\GoogleOidcValidator;use PHPUnit\Framework\TestCase;
final class GoogleOidcValidatorTest extends TestCase
{
 public function testItValidatesSignedGoogleCredential():void{$key=openssl_pkey_new(['private_key_bits'=>2048,'private_key_type'=>OPENSSL_KEYTYPE_RSA]);openssl_pkey_export($key,$private);$details=openssl_pkey_get_details($key);$header=$this->part(['alg'=>'RS256','kid'=>'test']);$payload=$this->part(['iss'=>'https://accounts.google.com','aud'=>'client-id','exp'=>time()+300,'email_verified'=>true,'email'=>'USER@EXAMPLE.TEST','name'=>'Usuário']);$input=$header.'.'.$payload;openssl_sign($input,$signature,$private,OPENSSL_ALGO_SHA256);$validator=new GoogleOidcValidator('client-id',60,new ValidatorJwksFetcher([['kid'=>'test','n'=>$this->b64($details['rsa']['n']),'e'=>$this->b64($details['rsa']['e'])]]));$identity=$validator->validate($input.'.'.$this->b64($signature));self::assertSame('user@example.test',$identity->email);}
 public function testItRejectsUnverifiedEmail():void{$this->expectException(\DomainException::class);(new GoogleOidcValidator('client',60,new ValidatorJwksFetcher([])))->validate('x.y.z');}
 private function part(array $value):string{return $this->b64(json_encode($value,JSON_THROW_ON_ERROR));}private function b64(string $value):string{return rtrim(strtr(base64_encode($value),'+/','-_'),'=');}
}
final class ValidatorJwksFetcher implements GoogleJwksFetcherInterface { public function __construct(private array $keys){} public function fetch():array{return $this->keys;} }
