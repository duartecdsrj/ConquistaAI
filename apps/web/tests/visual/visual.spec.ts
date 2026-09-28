import { expect, test } from '@playwright/test'

test('login mantém a base visual pública', async ({ page }) => {
  await page.goto('/')
  await expect(page.getByRole('heading', { name: /estude. evolua/i })).toBeVisible()
  await expect(page).toHaveScreenshot('login.png', { fullPage: true, animations: 'disabled' })
})

test('caderno mantém a composição autenticada', async ({ page }, testInfo) => {
  test.skip(!process.env.E2E_EMAIL || !process.env.E2E_PASSWORD, 'Defina E2E_EMAIL e E2E_PASSWORD para a fixture E2E.')
  await page.goto('/')
  await page.getByLabel('E-mail').fill(process.env.E2E_EMAIL!)
  await page.getByLabel('Senha').fill(process.env.E2E_PASSWORD!)
  const loginButton = page.getByRole('button', { name: 'Entrar na plataforma' })
  await loginButton.click()
  await expect(loginButton).toBeHidden()
  if (testInfo.project.name === 'mobile') await page.getByLabel('Abrir navegação').click()
  await page.getByText('Cadernos e plano', { exact: true }).first().click()
  await expect(page.getByText('Caderno visual — Direito Tributário', { exact: true })).toBeVisible()
  const resumeButton = page.locator('.book').filter({ hasText: 'Caderno visual — Direito Tributário' }).getByRole('button', { name: 'Retomar' })
  if (testInfo.project.name === 'mobile') await resumeButton.evaluate((element: HTMLButtonElement) => element.click())
  else await resumeButton.click()
  await expect(page.getByText(/questão/i).first()).toBeVisible()
  await expect(page).toHaveScreenshot('caderno.png', { fullPage: false, animations: 'disabled', mask: [page.locator('.timer')], maxDiffPixels: 1000 })
})



test('caderno mantém controles e vistas responsivas', async ({ page }, testInfo) => {
  test.skip(!process.env.E2E_EMAIL || !process.env.E2E_PASSWORD, 'Defina E2E_EMAIL e E2E_PASSWORD para a fixture E2E.')
  await page.goto('/')
  await page.getByLabel('E-mail').fill(process.env.E2E_EMAIL!)
  await page.getByLabel('Senha').fill(process.env.E2E_PASSWORD!)
  await page.getByRole('button', { name: 'Entrar na plataforma' }).click()
  if (testInfo.project.name === 'mobile') await page.getByLabel('Abrir navegação').click()
  await page.getByText('Cadernos e plano', { exact: true }).first().click()
  const resumeButton = page.locator('.book').filter({ hasText: 'Caderno visual — Direito Tributário' }).getByRole('button', { name: 'Retomar' })
  if (testInfo.project.name === 'mobile') await resumeButton.evaluate((element: HTMLButtonElement) => element.click())
  else await resumeButton.click()

  await expect(page.getByLabel('Finalizar caderno')).toBeVisible()
  await expect(page.getByLabel(/Pausar contador|Retomar contador/)).toBeVisible()

  if (testInfo.project.name === 'mobile') {
    const questionTab = page.getByRole('tab', { name: 'Questão' })
    const navigationTab = page.getByRole('tab', { name: 'Navegação' })
    const progressTab = page.getByRole('tab', { name: 'Progresso' })
    await expect(questionTab).toHaveClass(/q-tab--active/)
    await navigationTab.click()
    await expect(page.locator('.question-nav-panel')).toBeVisible()
    await progressTab.click()
    await expect(page.getByRole('button', { name: 'Finalizar caderno' }).last()).toBeVisible()
    await questionTab.click()

    const footer = page.locator('.execution-actions')
    await expect(footer).toBeVisible()
    const footerBeforeScroll = await footer.boundingBox()
    await page.locator('.question-content-scroll').evaluate((element) => { element.scrollTop = element.scrollHeight })
    expect((await footer.boundingBox())?.y).toBeCloseTo(footerBeforeScroll?.y ?? 0, 0)

    const stage = page.locator('.mobile-stage')
    await stage.dispatchEvent('pointerdown', { pointerType: 'touch', clientX: 320, clientY: 400 })
    await stage.dispatchEvent('pointerup', { pointerType: 'touch', clientX: 240, clientY: 402 })
    await expect(navigationTab).toHaveClass(/q-tab--active/)
    await navigationTab.click()
    await stage.dispatchEvent('pointerdown', { pointerType: 'touch', clientX: 240, clientY: 400 })
    await stage.dispatchEvent('pointerup', { pointerType: 'touch', clientX: 244, clientY: 280 })
    await expect(navigationTab).toHaveClass(/q-tab--active/)
  } else {
    await expect(page.locator('.mobile-execution-tabs')).toBeHidden()
    await expect(page.locator('.question-nav-panel')).toBeVisible()
    await expect(page.locator('.progress-panel')).toBeVisible()
  }
})

test('catálogo apresenta escopo explícito sem acionar processamento', async ({ page }, testInfo) => {
  test.skip(!process.env.E2E_EMAIL || !process.env.E2E_PASSWORD, 'Defina a fixture E2E.')
  await page.goto('/')
  await page.getByLabel('E-mail').fill(process.env.E2E_EMAIL!)
  await page.getByLabel('Senha').fill(process.env.E2E_PASSWORD!)
  await page.getByRole('button', { name: 'Entrar na plataforma' }).click()
  if (testInfo.project.name === 'mobile') await page.getByLabel('Abrir navegação').click()
  await page.getByText('Catálogo', { exact: true }).first().click()
  await expect(page.getByRole('heading', { name: 'Catálogo' })).toBeVisible()
  await page.getByRole('tab', { name: 'Editais' }).click()
  await expect(page.getByText('Selecione um concurso para ver somente os dados relacionados a ele.')).toBeVisible()
  await expect(page).toHaveScreenshot('catalogo.png', { fullPage: true, animations: 'disabled' })
})


test('revisão editorial carrega questões importadas e classificação', async ({ page }, testInfo) => {
  test.skip(!process.env.E2E_EMAIL || !process.env.E2E_PASSWORD, 'Defina a fixture E2E.')
  await page.goto('/')
  await page.getByLabel('E-mail').fill(process.env.E2E_EMAIL!)
  await page.getByLabel('Senha').fill(process.env.E2E_PASSWORD!)
  await page.getByRole('button', { name: 'Entrar na plataforma' }).click()
  if (testInfo.project.name === 'mobile') await page.getByLabel('Abrir navegação').click()
  await page.getByText('Revisar questões', { exact: true }).first().click()
  await expect(page.getByRole('heading', { name: 'Revisar questões' })).toBeVisible()
  const item = page.locator('.q-expansion-item').first()
  await expect(item).toBeVisible()
  await item.locator('.q-item').first().click()
  await expect(item.locator('.question-header')).toBeVisible()
  await expect(item.getByText('Assuntos canônicos', { exact: true })).toBeVisible()
  await expect(item.locator('.q-select')).not.toContainText(/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i)
})


async function authenticateVisualUser(page: import('@playwright/test').Page): Promise<void> {
  await page.goto('/')
  await page.getByLabel('E-mail').fill(process.env.E2E_EMAIL!)
  await page.getByLabel('Senha').fill(process.env.E2E_PASSWORD!)
  await page.getByRole('button', { name: 'Entrar na plataforma' }).click()
}

async function openVisualSection(page: import('@playwright/test').Page, project: string, label: string): Promise<void> {
  if (project === 'mobile') await page.getByLabel('Abrir navegação').click()
  await page.locator('.navigation-drawer').getByText(label, { exact: true }).click()
}

test('demais jornadas mantêm a linguagem editorial', async ({ page }, testInfo) => {
  test.skip(!process.env.E2E_EMAIL || !process.env.E2E_PASSWORD, 'Defina a fixture E2E.')
  test.setTimeout(30000)
  await authenticateVisualUser(page)

  await expect(page.getByRole('heading', { name: /Olá,/ })).toBeVisible()
  await expect(page).toHaveScreenshot('inicio.png', { fullPage: false, animations: 'disabled' })

  const sections: readonly { readonly navigation: string; readonly heading: string; readonly screenshot: string }[] = [
    { navigation: 'Questões', heading: 'Questões', screenshot: 'questoes.png' },
    { navigation: 'Desempenho', heading: 'Seu desempenho', screenshot: 'desempenho.png' },
    { navigation: 'Assistente', heading: 'Consulte seu edital', screenshot: 'assistente.png' },
    { navigation: 'Importar questões', heading: 'Importar questões', screenshot: 'importacao.png' },
    { navigation: 'Taxonomia', heading: 'Taxonomia de assuntos', screenshot: 'taxonomia.png' },
    { navigation: 'Descobertas', heading: 'Provas e gabaritos', screenshot: 'descoberta.png' },
  ]

  for (const section of sections) {
    await openVisualSection(page, testInfo.project.name, section.navigation)
    await expect(page.getByRole('heading', { name: section.heading })).toBeVisible()
    await expect(page).toHaveScreenshot(section.screenshot, { fullPage: false, animations: 'disabled' })
  }
})

test('administração de usuários apresenta dados reais e filtros', async ({ page }, testInfo) => {
  test.skip(!process.env.E2E_EMAIL || !process.env.E2E_PASSWORD, 'Defina a fixture E2E administrativa.')
  await authenticateVisualUser(page)
  await openVisualSection(page, testInfo.project.name, 'Usuários')
  await expect(page.getByRole('heading', { name: 'Usuários e acessos' })).toBeVisible()
  await expect(page.getByLabel('Buscar por nome ou e-mail')).toBeVisible()
  await expect(page.getByRole('button', { name: 'Cadastrar usuário' })).toBeVisible()
})

test('entrada preserva a experiência local sem expor credencial Google', async ({ page }) => {
  await page.goto('/')
  await expect(page.getByLabel('E-mail')).toBeVisible()
  await expect(page.getByLabel('Senha')).toBeVisible()
  await expect(page.getByText('Seu acesso aguarda liberação administrativa.')).toHaveCount(0)
})

test('login Google informa aprovação pendente sem criar sessão', async ({ page }) => {
  await page.addInitScript(() => { (window as Window & { __CONQUISTAAI_GOOGLE_CLIENT_ID__?: string }).__CONQUISTAAI_GOOGLE_CLIENT_ID__ = 'visual-client-id' })
  await page.route('https://accounts.google.com/gsi/client', async (route) => {
    await route.fulfill({ contentType: 'application/javascript', body: `window.google={accounts:{id:{initialize:function(options){window.__gisCallback=options.callback},renderButton:function(element){var button=document.createElement('button');button.textContent='Continuar com Google';button.onclick=function(){window.__gisCallback({credential:'visual-credential'})};element.append(button)}}}};` })
  })
  await page.route('**/api/v1/auth/google', async (route) => {
    await route.fulfill({ contentType: 'application/json', body: JSON.stringify({ data: { status: 'PENDING_APPROVAL', message: 'Seu acesso aguarda liberação administrativa.' }, meta: { request_id: 'visual-request' } }) })
  })
  await page.goto('/')
  await page.getByRole('button', { name: 'Continuar com Google' }).click()
  await expect(page.getByText('Seu acesso aguarda liberação administrativa.')).toBeVisible()
  await expect(page.getByRole('heading', { name: 'Bem-vindo de volta' })).toBeVisible()
})
