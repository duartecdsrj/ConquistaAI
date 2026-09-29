<?php
declare(strict_types=1);
namespace App\Infrastructure\Persistence\Doctrine\QuestionLearning\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'user_question_interactions')]
class UserQuestionInteractionRecord {
    #[ORM\Id] #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
    #[ORM\Id] #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(type: 'boolean')] public bool $favorite = false;
    #[ORM\Column(name: 'review_later', type: 'boolean')] public bool $reviewLater = false;
    #[ORM\Column(name: 'not_mastered', type: 'boolean')] public bool $notMastered = false;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}

#[ORM\Entity]
#[ORM\Table(name: 'question_notes')]
class QuestionNoteRecord {
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'user_id', type: 'string', length: 36)] public string $userId;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(type: 'text')] public string $content;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}

#[ORM\Entity]
#[ORM\Table(name: 'question_comments')]
class QuestionCommentRecord {
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(name: 'author_user_id', type: 'string', length: 36)] public string $authorUserId;
    #[ORM\Column(name: 'parent_id', type: 'string', length: 36, nullable: true)] public ?string $parentId = null;
    #[ORM\Column(type: 'text')] public string $content;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}

#[ORM\Entity]
#[ORM\Table(name: 'question_problem_reports')]
class QuestionProblemReportRecord {
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'question_id', type: 'string', length: 36)] public string $questionId;
    #[ORM\Column(name: 'reported_by', type: 'string', length: 36)] public string $reportedBy;
    #[ORM\Column(type: 'string', length: 16)] public string $category;
    #[ORM\Column(type: 'text')] public string $description;
    #[ORM\Column(type: 'string', length: 16)] public string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] public DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')] public DateTimeImmutable $updatedAt;
}

#[ORM\Entity]
#[ORM\Table(name: 'question_problem_report_events')]
class QuestionProblemReportEventRecord {
    #[ORM\Id] #[ORM\Column(type: 'string', length: 36)] public string $id;
    #[ORM\Column(name: 'report_id', type: 'string', length: 36)] public string $reportId;
    #[ORM\Column(name: 'actor_user_id', type: 'string', length: 36)] public string $actorUserId;
    #[ORM\Column(name: 'event_type', type: 'string', length: 16)] public string $eventType;
    #[ORM\Column(name: 'previous_status', type: 'string', length: 16, nullable: true)] public ?string $previousStatus = null;
    #[ORM\Column(name: 'next_status', type: 'string', length: 16, nullable: true)] public ?string $nextStatus = null;
    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')] public DateTimeImmutable $occurredAt;
}

#[ORM\Entity]
#[ORM\Table(name: 'learning_events')]
class LearningEventRecord {
 #[ORM\Id] #[ORM\Column(type: 'string',length:36)] public string $id;
 #[ORM\Column(name: 'user_id',type: 'string',length:36)] public string $userId;
 #[ORM\Column(name: 'question_id',type: 'string',length:36)] public string $questionId;
 #[ORM\Column(name: 'attempt_id',type: 'string',length:36,nullable:true)] public ?string $attemptId=null;
 #[ORM\Column(name: 'taxonomy_subject_id',type: 'string',length:36,nullable:true)] public ?string $taxonomySubjectId=null;
 #[ORM\Column(type: 'string',length:32)] public string $type;
 #[ORM\Column(type: 'string',length:16,nullable:true)] public ?string $outcome=null;
 #[ORM\Column(name: 'elapsed_seconds',type: 'integer',nullable:true)] public ?int $elapsedSeconds=null;
 #[ORM\Column(type: 'string',length:64,nullable:true)] public ?string $origin=null;
 #[ORM\Column(type: 'json')] public array $payload=[];
 #[ORM\Column(name: 'occurred_at',type: 'datetime_immutable')] public DateTimeImmutable $occurredAt;
}

#[ORM\Entity]
#[ORM\Table(name: 'question_explanation_executions')]
class QuestionExplanationExecutionRecord {
 #[ORM\Id] #[ORM\Column(type: 'string',length:36)] public string $id;
 #[ORM\Column(name: 'user_id',type: 'string',length:36)] public string $userId;
 #[ORM\Column(name: 'question_id',type: 'string',length:36)] public string $questionId;
 #[ORM\Column(name: 'attempt_id',type: 'string',length:36,nullable:true)] public ?string $attemptId=null;
 #[ORM\Column(name: 'algorithm_version',type: 'string',length:64)] public string $algorithmVersion;
 #[ORM\Column(name: 'safety_mode',type: 'string',length:20)] public string $safetyMode;
 #[ORM\Column(type: 'string',length:16)] public string $status;
 #[ORM\Column(name: 'retry_count',type: 'smallint')] public int $retryCount;
 #[ORM\Column(type: 'string',length:100,nullable:true)] public ?string $provider=null;
 #[ORM\Column(type: 'string',length:190,nullable:true)] public ?string $model=null;
 #[ORM\Column(name: 'token_count',type: 'integer',nullable:true)] public ?int $tokenCount=null;
 #[ORM\Column(name: 'duration_milliseconds',type: 'integer',nullable:true)] public ?int $durationMilliseconds=null;
 #[ORM\Column(type: 'json',nullable:true)] public ?array $result=null;
 #[ORM\Column(name: 'error_code',type: 'string',length:64,nullable:true)] public ?string $errorCode=null;
 #[ORM\Column(name: 'error_message',type: 'string',length:500,nullable:true)] public ?string $errorMessage=null;
 #[ORM\Column(name: 'requested_at',type: 'datetime_immutable')] public DateTimeImmutable $requestedAt;
 #[ORM\Column(name: 'started_at',type: 'datetime_immutable',nullable:true)] public ?DateTimeImmutable $startedAt=null;
 #[ORM\Column(name: 'completed_at',type: 'datetime_immutable',nullable:true)] public ?DateTimeImmutable $completedAt=null;
}
