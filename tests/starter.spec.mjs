import { test, expect } from '@playwright/test';

const homeHeading = 'Arquitetura em escala humana.';
const homeUrl = '/';
const portfolio = process.env.WPCS_TEST_PORTFOLIO === '1';

async function login(page) {
  await page.goto('/wp-login.php');
  await page.locator('#user_login').fill('admin');
  await page.locator('#user_pass').fill('password');
  await Promise.all([
    page.waitForURL('**/wp-admin/**'),
    page.locator('#wp-submit').click(),
  ]);
}

async function openHomeEditor(page) {
  await page.goto(homeUrl);
  await page.locator('#wp-admin-bar-edit a').click();
  await page.waitForFunction(() => {
    const editor = window.wp?.data?.select('core/editor');
    return editor?.getCurrentPostType() === 'page' && Boolean(editor.getCurrentPostId());
  });
  const welcome = page.getByRole('button', { name: /^(Close|Fechar)$/ });
  if (await page.getByRole('dialog', { name: /Welcome|Boas-vindas/i }).isVisible()) {
    await welcome.first().click();
  }
}

test('a home, os artigos e a navegação funcionam em desktop e celular', async ({ page }) => {
  const errors = [];
  page.on('pageerror', (error) => errors.push(error.message));
  await page.goto(homeUrl);
  await expect(page.getByRole('heading', { name: homeHeading, level: 1 })).toBeVisible();
  await expect(page.locator('h1')).toHaveCount(1);
  await expect(page.locator('.wpcs-hero img')).toHaveJSProperty('complete', true);
  await expect.poll(() => page.locator('.wpcs-hero img').evaluate((image) => image.naturalWidth)).toBeGreaterThan(0);
  await expect(page.locator('.wpcs-hero')).toHaveCSS('display', 'grid');
  await expect(page.locator('.wpcs-site-header')).toHaveAttribute('data-wpcs-sticky', 'true');
  await page.keyboard.press('Tab');
  await expect(page.locator('.skip-link')).toBeFocused();
  await page.keyboard.press('Enter');
  await expect(page.locator('main')).toBeFocused();
  await page.getByRole('link', { name: 'Conheça o estúdio' }).click();
  await expect(page.getByRole('heading', { name: 'Sobre', level: 1 })).toBeVisible();
  await page.getByRole('link', { name: 'Blog', exact: true }).click();
  await expect(page.getByRole('link', { name: 'Como dimensionamos a sombra da varanda' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Antes de erguer a parede, uma amostra' })).toBeVisible();
  await page.getByRole('link', { name: 'Como dimensionamos a sombra da varanda' }).click();
  await expect(page.getByRole('heading', { name: 'Como dimensionamos a sombra da varanda', level: 1 })).toBeVisible();

  for (const width of [390, 320]) {
    await page.setViewportSize({ width, height: 840 });
    await page.goto(homeUrl);
    await expect(page.locator('.wpcs-hero img')).toHaveCSS('height', '280px');
    await expect.poll(() => page.evaluate(() => document.documentElement.scrollWidth)).toBe(width);
    const toggle = page.locator('button.wp-block-navigation__responsive-container-open');
    await expect(toggle).toBeVisible();
    await toggle.click();
    await expect(page.getByRole('link', { name: 'Blog', exact: true })).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(toggle).toBeFocused();
  }
  expect(errors).toEqual([]);
});

test('a página reabre após salvar metadados de composição pelo Gutenberg', async ({ page }) => {
  const errors = [];
  page.on('pageerror', (error) => errors.push(error.message));
  await login(page);
  await openHomeEditor(page);
  const canvas = page.frameLocator('iframe[name="editor-canvas"]');
  await expect(canvas.getByRole('heading', { name: homeHeading })).toBeVisible();
  await expect(canvas.locator('.wpcs-site-header')).toBeVisible();
  await expect(canvas.locator('.wpcs-site-footer')).toBeVisible();

  const original = await page.evaluate(() => {
    const editor = wp.data.select('core/block-editor');
    const blocks = editor.getBlocks();
    const flatten = (items) => items.flatMap((block) => [block, ...flatten(block.innerBlocks)]);
    const all = flatten(blocks);
    const hero = all.find((block) => (block.attributes.className || '').split(' ').includes('wpcs-hero'));
    const paragraph = flatten(hero.innerBlocks).find((block) => block.name === 'core/paragraph');
    const image = flatten(hero.innerBlocks).find((block) => block.name === 'core/image');
    return {
      invalid: all.filter((block) => block.isValid === false).map((block) => block.name),
      values: [hero, paragraph, image].map((block) => block.attributes.metadata.wpcsVisual),
    };
  });
  expect(original.invalid).toEqual([]);

  const setVisuals = (values) => page.evaluate(async (next) => {
    const store = wp.data.select('core/block-editor');
    const flatten = (items) => items.flatMap((block) => [block, ...flatten(block.innerBlocks)]);
    const hero = flatten(store.getBlocks()).find((block) => (block.attributes.className || '').split(' ').includes('wpcs-hero'));
    const inner = flatten(hero.innerBlocks);
    const affected = [hero, inner.find((block) => block.name === 'core/paragraph'), inner.find((block) => block.name === 'core/image')];
    affected.forEach((block, index) => wp.data.dispatch('core/block-editor').updateBlockAttributes(block.clientId, {
      metadata: { ...block.attributes.metadata, wpcsVisual: next[index] },
    }));
    await wp.data.dispatch('core/editor').savePost();
  }, values);

  const updated = [
    { ...original.values[0], first: 60, gap: 29 },
    { ...original.values[1], width: 75, position: 'center' },
    { ...original.values[2], mobileHeight: 310, focus: { x: 25, y: 60 } },
  ];
  try {
    await setVisuals(updated);
    await page.reload();
    await expect(canvas.locator('.wpcs-hero').first()).toBeVisible();
    await expect(canvas.locator('.wpcs-visual-paragraph').first()).toHaveCSS('--wpcs-width', '75%');
    await expect(canvas.locator('.wpcs-visual-crop').first()).toHaveCSS('--wpcs-focus', '25% 60%');

    const publicPage = await page.context().newPage();
    await publicPage.goto(homeUrl);
    await expect(publicPage.locator('.wpcs-hero')).toHaveCSS('gap', '29px');
    await expect(publicPage.locator('.wpcs-hero p.wpcs-visual-paragraph')).toHaveCSS('--wpcs-width', '75%');
    await expect(publicPage.locator('.wpcs-hero img')).toHaveCSS('object-position', '25% 60%');
    await publicPage.close();
  } finally {
    await setVisuals(original.values);
  }
  expect(errors).toEqual([]);
});

test('a galeria filtra projetos e navega para fichas', async ({ page }) => {
  test.skip(!portfolio, 'Execute npm run demo:portfolio e WPCS_TEST_PORTFOLIO=1 para testar o exemplo.');
  await page.goto('/galeria/');
  await expect(page.getByRole('heading', { name: 'Galeria', level: 1 })).toBeVisible();
  const filters = page.getByRole('navigation', { name: 'Filtrar projetos por área' });
  await expect(filters.getByRole('link', { name: 'Todas as áreas' })).toHaveAttribute('aria-current', 'true');
  await expect(page.getByRole('heading', { name: 'Casa do Pátio' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Oficina do Bairro' })).toBeVisible();
  await filters.getByRole('link', { name: 'Residencial', exact: true }).click();
  await expect(filters.getByRole('link', { name: 'Residencial', exact: true })).toHaveAttribute('aria-current', 'true');
  await expect(page.getByRole('heading', { name: 'Oficina do Bairro' })).toHaveCount(0);
  await page.getByRole('link', { name: 'Casa do Pátio', exact: true }).last().click();
  await expect(page.getByRole('heading', { name: 'Casa do Pátio', level: 1 })).toBeVisible();
  await expect(page.locator('main img')).toBeVisible();
});
