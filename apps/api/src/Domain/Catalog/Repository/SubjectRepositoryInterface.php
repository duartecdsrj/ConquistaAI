<?php
declare(strict_types=1);
namespace App\Domain\Catalog\Repository;
use App\Domain\Catalog\Entity\Subject;
interface SubjectRepositoryInterface {public function save(Subject $subject):void;/** @return list<Subject> */public function listForSyllabus(string $syllabusId):array;}
