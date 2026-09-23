# Operação externa

## Cloudflare Zero Trust

O túnel Cloudflare é uma configuração local e deliberadamente não versionada. Os arquivos abaixo permanecem ignorados pelo Git:

- `compose.cloudflare-zero-trust.yaml`
- `.env.cloudflare.local`
- `cloudflare-zero-trust-up.sh`

O token do túnel deve existir somente em `.env.cloudflare.local` como `CLOUDFLARE_TUNNEL_TOKEN`. Nunca o adicione ao `.env`, ao Compose principal, à documentação com valores reais ou ao Git.

Para subir localmente:

```bash
./cloudflare-zero-trust-up.sh
```

No painel Cloudflare Zero Trust, o Public Hostname deve apontar `conquistaai.app.br` para o serviço HTTP `nginx:80` pelo túnel. O DNS público é criado/gerenciado pelo Cloudflare; o Vite aceita apenas `conquistaai.app.br` e `www.conquistaai.app.br`.

