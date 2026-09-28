export interface ProfileAvatar { readonly available: boolean; readonly mimeType: string | null; readonly updatedAt: string | null }
export interface ProfileAvatarRepository { metadata(): Promise<ProfileAvatar>; readUrl(): Promise<string>; replace(file: File): Promise<ProfileAvatar> }
