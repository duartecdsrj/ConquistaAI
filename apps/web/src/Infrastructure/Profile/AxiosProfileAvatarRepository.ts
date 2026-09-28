import type { ProfileAvatar, ProfileAvatarRepository } from '../../Domain/Profile/ProfileAvatarRepository'
import { getBlobObjectUrl, getData, postFormData } from '../Http/AxiosApiClient'
type ProfileAvatarApi = { readonly available: boolean; readonly mimeType: string | null; readonly updatedAt: string | null }
export class AxiosProfileAvatarRepository implements ProfileAvatarRepository { public metadata(): Promise<ProfileAvatar> { return getData<ProfileAvatarApi>('/profile/avatar/metadata') } public readUrl(): Promise<string> { return getBlobObjectUrl('/profile/avatar') } public async replace(file: File): Promise<ProfileAvatar> { const form = new FormData(); form.append('avatar', file); return postFormData<ProfileAvatarApi>('/profile/avatar', form) } }
