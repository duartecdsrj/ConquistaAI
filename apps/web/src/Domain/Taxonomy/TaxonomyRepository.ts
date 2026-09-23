import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'
export interface TaxonomySubject { readonly id:string; readonly parentId:string|null; readonly name:string; readonly slug:string; readonly description:string|null; readonly level:number; readonly active:boolean }
export interface CreateTaxonomySubject { readonly name:string; readonly parentId:string|null; readonly description:string|null }
export interface TaxonomyRepository { list(query?:PageQuery):Promise<PageResult<TaxonomySubject>>; create(input:CreateTaxonomySubject):Promise<TaxonomySubject> }
