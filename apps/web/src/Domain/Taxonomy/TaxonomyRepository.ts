import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'
export interface TaxonomySubject { readonly id:string; readonly parentId:string|null; readonly name:string; readonly slug:string; readonly description:string|null; readonly level:number; readonly active:boolean }
export interface TaxonomySubjectAlias { readonly id:string; readonly subjectId:string; readonly alias:string }
export interface UpdateTaxonomySubject { readonly id:string; readonly name:string; readonly parentId:string|null; readonly description:string|null }
export interface CreateTaxonomySubject { readonly name:string; readonly parentId:string|null; readonly description:string|null }
export interface CreateTaxonomySubjectAlias { readonly subjectId:string; readonly alias:string }
export interface TaxonomyRepository { list(query?:PageQuery):Promise<PageResult<TaxonomySubject>>; create(input:CreateTaxonomySubject):Promise<TaxonomySubject>; update(input:UpdateTaxonomySubject):Promise<TaxonomySubject>; createAlias(input:CreateTaxonomySubjectAlias):Promise<TaxonomySubjectAlias> }
