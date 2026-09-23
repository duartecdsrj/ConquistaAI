import type { CreateTaxonomySubject, TaxonomyRepository, TaxonomySubject } from '../../Domain/Taxonomy/TaxonomyRepository'
import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'
export class ListTaxonomySubjectsUseCase { public constructor(private readonly repository:TaxonomyRepository){} public execute(query?:PageQuery):Promise<PageResult<TaxonomySubject>> { return this.repository.list(query) } }
export class CreateTaxonomySubjectUseCase { public constructor(private readonly repository:TaxonomyRepository){} public execute(input:CreateTaxonomySubject):Promise<TaxonomySubject>{const name=input.name.trim();if(!name) return Promise.reject(new Error('Informe o nome do assunto.'));return this.repository.create({...input,name});} }
