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
  await page.getByRole('button', { name: 'Retomar' }).click()
  await expect(page.getByText(/questão/i).first()).toBeVisible()
  await expect(page).toHaveScreenshot('caderno.png', { fullPage: false, animations: 'disabled', mask: [page.locator('.timer')] })
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
