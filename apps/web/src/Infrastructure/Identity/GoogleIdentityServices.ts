export interface GoogleCredentialResponse {
  readonly credential: string
}

type GoogleAccountsId = {
  initialize(configuration: { readonly client_id: string; readonly callback: (response: GoogleCredentialResponse) => void; readonly auto_select: false }): void
  renderButton(element: HTMLElement, options: { readonly theme: 'outline'; readonly size: 'large'; readonly width?: number; readonly text: 'continue_with' }): void
}

declare global {
  interface Window { google?: { accounts?: { id?: GoogleAccountsId } }; __CONQUISTAAI_GOOGLE_CLIENT_ID__?: string }
}

export class GoogleIdentityServices {
  public constructor(private readonly clientId: string) {}

  public isConfigured(): boolean { return this.configuredClientId() !== '' }

  public async renderButton(element: HTMLElement, onCredential: (credential: string) => void): Promise<void> {
    if (!this.isConfigured()) throw new Error('O login Google não está configurado.')
    const identity = await this.identity()
    element.replaceChildren()
    identity.initialize({ client_id: this.configuredClientId(), auto_select: false, callback: (response) => onCredential(response.credential) })
    identity.renderButton(element, { theme: 'outline', size: 'large', width: Math.max(240, element.clientWidth), text: 'continue_with' })
  }

  private configuredClientId(): string { return (window.__CONQUISTAAI_GOOGLE_CLIENT_ID__ ?? this.clientId).trim() }

  private async identity(): Promise<GoogleAccountsId> {
    const existing = window.google?.accounts?.id
    if (existing) return existing

    await new Promise<void>((resolve, reject) => {
      const present = document.querySelector<HTMLScriptElement>('script[data-google-identity-services]')
      if (present) {
        present.addEventListener('load', () => resolve(), { once: true })
        present.addEventListener('error', () => reject(new Error('Não foi possível carregar o login Google.')), { once: true })
        return
      }
      const script = document.createElement('script')
      script.src = 'https://accounts.google.com/gsi/client'
      script.async = true
      script.defer = true
      script.dataset.googleIdentityServices = 'true'
      script.onload = () => resolve()
      script.onerror = () => reject(new Error('Não foi possível carregar o login Google.'))
      document.head.append(script)
    })

    const identity = window.google?.accounts?.id
    if (!identity) throw new Error('O login Google não foi disponibilizado.')
    return identity
  }
}
