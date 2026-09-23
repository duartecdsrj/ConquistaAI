import type { PublishedQuestion } from '../../Domain/QuestionBank/QuestionRepository'
import type { CreateNotebookCommand, Notebook, NotebookStatistics, StudyRepository } from '../../Domain/Study/StudyRepository'
import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export class ListNotebooksUseCase {
  public constructor(private readonly repository: StudyRepository) {}
  public execute(query?: PageQuery): Promise<PageResult<Notebook>> { return this.repository.list(query) }
}

export class GetNotebookUseCase {
  public constructor(private readonly repository: StudyRepository) {}
  public execute(id: string): Promise<Notebook> { return this.repository.get(id) }
}

export class ListNotebookQuestionsUseCase {
  public constructor(private readonly repository: StudyRepository) {}
  public execute(id: string, query?: PageQuery): Promise<PageResult<PublishedQuestion>> { return this.repository.listQuestions(id, query) }
}

export class StartNotebookUseCase {
  public constructor(private readonly repository: StudyRepository) {}
  public execute(id: string): Promise<Notebook> {
    if (!id) return Promise.reject(new Error('Caderno inválido.'))
    return this.repository.start(id)
  }
}

export class PauseNotebookUseCase { public constructor(private readonly repository: StudyRepository) {} public execute(id: string): Promise<Notebook> { return this.repository.pause(id) } }
export class GetNotebookStatisticsUseCase { public constructor(private readonly repository: StudyRepository) {} public execute(id: string): Promise<NotebookStatistics> { return this.repository.statistics(id) } }

export class FinishNotebookUseCase {
  public constructor(private readonly repository: StudyRepository) {}
  public execute(id: string): Promise<Notebook> {
    if (!id) return Promise.reject(new Error('Caderno inválido.'))
    return this.repository.finish(id)
  }
}

export class CreateNotebookUseCase {
  public constructor(private readonly repository: StudyRepository) {}
  public async execute(command: CreateNotebookCommand): Promise<Notebook> {
    if (command.name.trim().length < 3) throw new Error('Informe um nome com pelo menos 3 caracteres.')
    if (!Number.isInteger(command.quantity) || command.quantity < 1 || command.quantity > 100) throw new Error('A quantidade deve estar entre 1 e 100.')
    return this.repository.create({ ...command, name: command.name.trim() })
  }
}
