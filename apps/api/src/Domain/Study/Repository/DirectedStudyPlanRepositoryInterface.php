<?php
declare(strict_types=1);
namespace App\Domain\Study\Repository;
use App\Domain\Study\Entity\DirectedStudyPlan;
interface DirectedStudyPlanRepositoryInterface { public function save(DirectedStudyPlan $plan):void; /** @return list<DirectedStudyPlan> */ public function listForUser(string $userId):array; public function findForUser(string $id,string $userId):?DirectedStudyPlan; }
