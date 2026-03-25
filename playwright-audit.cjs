const { chromium } = require('playwright');

const BASE_URL = 'http://linceb.local';

async function audit() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 }
  });

  const issues = [];
  const screenshots = [];

  async function visitPage(page, url, name) {
    const consoleErrors = [];
    const networkErrors = [];

    page.on('console', msg => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    page.on('requestfailed', req => {
      networkErrors.push(`${req.method()} ${req.url()} — ${req.failure()?.errorText}`);
    });

    try {
      const response = await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
      const status = response?.status();
      const finalUrl = page.url();

      // Screenshot
      const screenshotPath = `storage/app/playwright-audit-${name.replace(/\s/g, '_')}.png`;
      await page.screenshot({ path: screenshotPath, fullPage: true });
      screenshots.push(screenshotPath);

      // HTTP status
      if (status && status >= 400) {
        issues.push({ page: name, url, type: 'HTTP Error', detail: `Status ${status}` });
      }

      // Redirect check
      if (finalUrl !== url && !finalUrl.startsWith(BASE_URL + '/login')) {
        issues.push({ page: name, url, type: 'Redirect', detail: `→ ${finalUrl}` });
      }

      // Console errors
      for (const err of consoleErrors) {
        issues.push({ page: name, url, type: 'Console Error', detail: err });
      }

      // Network failures
      for (const err of networkErrors) {
        issues.push({ page: name, url, type: 'Network Error', detail: err });
      }

      // Broken images
      const brokenImages = await page.evaluate(() => {
        return Array.from(document.images)
          .filter(img => !img.complete || img.naturalWidth === 0)
          .map(img => img.src);
      });
      for (const src of brokenImages) {
        issues.push({ page: name, url, type: 'Broken Image', detail: src });
      }

      // Missing alt text
      const missingAlt = await page.evaluate(() => {
        return Array.from(document.images)
          .filter(img => !img.alt || img.alt.trim() === '')
          .map(img => img.src);
      });
      for (const src of missingAlt) {
        issues.push({ page: name, url, type: 'Accesibilidad: img sin alt', detail: src });
      }

      // Buttons without text
      const badButtons = await page.evaluate(() => {
        return Array.from(document.querySelectorAll('button'))
          .filter(btn => !btn.textContent.trim() && !btn.getAttribute('aria-label') && !btn.title)
          .map(btn => btn.outerHTML.substring(0, 120));
      });
      for (const btn of badButtons) {
        issues.push({ page: name, url, type: 'Accesibilidad: botón sin texto/aria-label', detail: btn });
      }

      // Forms without labels
      const unlabeledInputs = await page.evaluate(() => {
        return Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="submit"])'))
          .filter(input => {
            const id = input.id;
            const hasLabel = id && document.querySelector(`label[for="${id}"]`);
            const hasAriaLabel = input.getAttribute('aria-label') || input.getAttribute('aria-labelledby');
            const hasPlaceholder = input.placeholder;
            return !hasLabel && !hasAriaLabel && !hasPlaceholder;
          })
          .map(input => input.outerHTML.substring(0, 120));
      });
      for (const input of unlabeledInputs) {
        issues.push({ page: name, url, type: 'Accesibilidad: input sin label', detail: input });
      }

      // Check page title
      const title = await page.title();
      if (!title || title.trim() === '') {
        issues.push({ page: name, url, type: 'SEO: Sin título de página', detail: 'El <title> está vacío' });
      }

      return { name, url, status, title, consoleErrors: consoleErrors.length, networkErrors: networkErrors.length };
    } catch (e) {
      issues.push({ page: name, url, type: 'Timeout / Error de carga', detail: e.message });
      return { name, url, status: null, error: e.message };
    }
  }

  // --- Login first ---
  const page = await context.newPage();
  console.log('🔐 Iniciando sesión...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle', timeout: 15000 });

  // Try to fill login form
  try {
    await page.fill('input[name="email"]', 'lolo@lolo.es');
    await page.fill('input[name="password"]', '54585458');
    await page.click('button[type="submit"]');
    await page.waitForURL(url => url !== `${BASE_URL}/login`, { timeout: 10000 });
    console.log('✅ Login exitoso, URL actual:', page.url());
  } catch (e) {
    console.log('⚠️  Login automático falló:', e.message);
    console.log('   Intentando continuar sin sesión...');
  }

  // --- Pages to audit ---
  const pages = [
    { url: `${BASE_URL}/dashboard`, name: 'Dashboard' },
    { url: `${BASE_URL}/`, name: 'Proyectos (lista)' },
    { url: `${BASE_URL}/team`, name: 'Equipo' },
    { url: `${BASE_URL}/settings/profile`, name: 'Settings - Perfil' },
    { url: `${BASE_URL}/sadmin`, name: 'Super Admin' },
    { url: `${BASE_URL}/files`, name: 'Subida de archivos' },
  ];

  const results = [];
  for (const p of pages) {
    console.log(`📄 Auditando: ${p.name} (${p.url})`);
    const result = await visitPage(page, p.url, p.name);
    results.push(result);
  }

  // --- Check broken links on homepage ---
  console.log('🔗 Verificando links internos...');
  await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle', timeout: 15000 });
  const links = await page.evaluate((base) => {
    return Array.from(document.querySelectorAll('a[href]'))
      .map(a => a.href)
      .filter(href => href.startsWith(base) && !href.includes('#'))
      .filter((v, i, arr) => arr.indexOf(v) === i)
      .slice(0, 20);
  }, BASE_URL);

  for (const link of links) {
    try {
      const resp = await page.goto(link, { waitUntil: 'domcontentloaded', timeout: 10000 });
      const status = resp?.status();
      if (status && status >= 400) {
        issues.push({ page: 'Link check', url: link, type: 'Link Roto', detail: `Status ${status}` });
      }
    } catch (e) {
      issues.push({ page: 'Link check', url: link, type: 'Link Error', detail: e.message });
    }
  }

  await browser.close();

  // --- Report ---
  console.log('\n' + '='.repeat(70));
  console.log('REPORTE DE AUDITORÍA — LinceB');
  console.log('='.repeat(70));
  console.log(`Páginas auditadas: ${results.length}`);
  console.log(`Problemas encontrados: ${issues.length}`);
  console.log('');

  if (issues.length === 0) {
    console.log('✅ No se encontraron problemas.\n');
  } else {
    // Group by type
    const grouped = {};
    for (const issue of issues) {
      if (!grouped[issue.type]) grouped[issue.type] = [];
      grouped[issue.type].push(issue);
    }
    for (const [type, list] of Object.entries(grouped)) {
      console.log(`\n🔴 ${type} (${list.length})`);
      for (const item of list) {
        console.log(`   [${item.page}] ${item.detail}`);
      }
    }
  }

  console.log('\n📸 Screenshots guardados en:');
  for (const s of screenshots) console.log(`   ${s}`);
  console.log('');
}

audit().catch(err => {
  console.error('Error fatal en auditoría:', err);
  process.exit(1);
});
