import axios, { AxiosError, type AxiosRequestConfig, type AxiosResponse, type InternalAxiosRequestConfig } from 'axios'

export interface ApiMeta { readonly request_id: string; readonly pagination?: PaginationMeta }
export interface PaginationMeta { readonly page: number; readonly per_page: number; readonly total: number; readonly total_pages: number }
export interface ApiEnvelope<T> { readonly data: T; readonly meta: ApiMeta }
export interface ApiErrorDetail { readonly field: string; readonly code: string; readonly message: string }
interface ApiFailure { readonly error: { readonly code: string; readonly message: string; readonly details: readonly ApiErrorDetail[] }; readonly meta?: ApiMeta }

export interface PageQuery { readonly page?: number; readonly perPage?: number }
export type QueryParameters = Readonly<Record<string, string | number | boolean | undefined>>
export interface PageResult<T> { readonly items: readonly T[]; readonly pagination: PaginationMeta; readonly requestId: string }

export class ApiRequestError extends Error {
  public constructor(public readonly code: string, message: string, public readonly status: number, public readonly details: readonly ApiErrorDetail[] = [], public readonly requestId?: string) { super(message) }
}

const client = axios.create({ baseURL: '/api/v1', withCredentials: true, headers: { Accept: 'application/json', 'Content-Type': 'application/json' } })
let accessTokenProvider: () => string | null = () => null
let refreshHandler: (() => Promise<string | null>) | null = null
let refreshInFlight: Promise<string | null> | null = null
export function configureAccessTokenProvider(provider: () => string | null): void { accessTokenProvider = provider }
export function configureRefreshHandler(handler: () => Promise<string | null>): void { refreshHandler = handler }

client.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = accessTokenProvider()
  if (token) config.headers.Authorization = 'Bearer ' + token
  return config
})
client.interceptors.response.use((response: AxiosResponse) => response, async (error: AxiosError<ApiFailure> & { config?: InternalAxiosRequestConfig & { _retried?: boolean } }) => { const config = error.config; const url = config?.url ?? ''; if (error.response?.status === 401 && config && !config._retried && !url.includes('/auth/refresh') && !url.includes('/auth/login') && refreshHandler) { config._retried = true; refreshInFlight ??= refreshHandler().finally(() => { refreshInFlight = null }); const token = await refreshInFlight; if (token) { config.headers.Authorization = 'Bearer ' + token; return client.request(config) } } return Promise.reject(toApiRequestError(error)) }) 

export async function getData<T>(url: string, config?: AxiosRequestConfig): Promise<T> { return (await client.get<ApiEnvelope<T>>(url, config)).data.data }
export async function postData<TResponse, TRequest>(url: string, body?: TRequest, config?: AxiosRequestConfig): Promise<TResponse> { return (await client.post<ApiEnvelope<TResponse>>(url, body, config)).data.data }
export async function postFormData<TResponse>(url: string, body: FormData, config?: AxiosRequestConfig): Promise<TResponse> { return (await client.post<ApiEnvelope<TResponse>>(url, body, { ...config, headers: { ...config?.headers, 'Content-Type': undefined } })).data.data }
export async function patchData<TResponse, TRequest>(url: string, body: TRequest, config?: AxiosRequestConfig): Promise<TResponse> { return (await client.patch<ApiEnvelope<TResponse>>(url, body, config)).data.data }
export async function putData<TResponse, TRequest>(url: string, body: TRequest, config?: AxiosRequestConfig): Promise<TResponse> { return (await client.put<ApiEnvelope<TResponse>>(url, body, config)).data.data }

export async function getPage<T>(url: string, query: PageQuery = {}, filters: QueryParameters = {}): Promise<PageResult<T>> {
  const response = await client.get<ApiEnvelope<readonly T[]>>(url, { params: { ...paginationParams(query), ...withoutUndefined(filters) } })
  const pagination = response.data.meta.pagination
  if (!pagination) throw new ApiRequestError('INVALID_API_CONTRACT', 'A API nao retornou paginacao.', 500)
  return { items: response.data.data, pagination, requestId: response.data.meta.request_id }
}
export function paginationParams(query: PageQuery): Record<string, number> { return { page: Math.max(1, Math.trunc(query.page ?? 1)), per_page: Math.min(100, Math.max(1, Math.trunc(query.perPage ?? 25))) } }
function withoutUndefined(values: QueryParameters): Record<string, string | number | boolean> {
  return Object.fromEntries(Object.entries(values).filter((entry): entry is [string, string | number | boolean] => entry[1] !== undefined))
}
function toApiRequestError(error: AxiosError<ApiFailure>): ApiRequestError {
  const failure = error.response?.data
  return new ApiRequestError(failure?.error?.code ?? 'NETWORK_ERROR', failure?.error?.message ?? 'Nao foi possivel comunicar com a API.', error.response?.status ?? 0, failure?.error?.details ?? [], failure?.meta?.request_id)
}
