import { mkdir, readFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import AdmZip from 'adm-zip';

const out = resolve('dist');
await mkdir(out, { recursive: true });

function packageWordPress(directory, slug) {
  const zip = new AdmZip();
  zip.addLocalFolder(directory, slug);
  zip.addLocalFile('LICENSE', slug);
  const path = resolve(out, slug + '.zip');
  zip.writeZip(path);
  return path;
}

const theme = packageWordPress('themes/wp-code-starter', 'wp-code-starter');
const plugin = packageWordPress('plugins/wpcs-projects', 'wpcs-projects');
const fixtures = new AdmZip();
fixtures.addLocalFolder('demo', 'demo');

for (const mode of ['base', 'portfolio']) {
  const zip = new AdmZip();
  const bundled = (path) => ({ resource: 'bundled', path: '/' + path });
  zip.addLocalFile(theme);
  zip.addFile('fixtures.zip', fixtures.toBuffer());
  const steps = [
    { step: 'defineWpConfigConsts', consts: { WP_ENVIRONMENT_TYPE: 'local' } },
    { step: 'setSiteOptions', options: { blogname: 'Estúdio Linha de Cedro', blogdescription: 'Arquitetura e reforma', permalink_structure: '/%postname%/' } },
    { step: 'installTheme', themeData: bundled('wp-code-starter.zip'), options: { activate: true } },
  ];
  if (mode === 'portfolio') {
    zip.addLocalFile(plugin);
    steps.push({ step: 'installPlugin', pluginData: bundled('wpcs-projects.zip'), options: { activate: true } });
  }
  steps.push(
    { step: 'unzip', zipFile: bundled('fixtures.zip'), extractToPath: '/tmp/wpcs' },
    {
      step: 'runPHP',
      code: "<?php define('WPCS_PLAYGROUND_SEED', '" + mode + "'); require '/wordpress/wp-load.php'; require '/tmp/wpcs/demo/seed.php'; flush_rewrite_rules(false);",
    },
  );
  const blueprint = {
    $schema: 'https://playground.wordpress.net/blueprint-schema.json',
    preferredVersions: { php: '8.3', wp: '7.1' },
    landingPage: mode === 'portfolio' ? '/galeria/' : '/',
    login: true,
    steps,
  };
  zip.addFile('blueprint.json', Buffer.from(JSON.stringify(blueprint, null, 2) + '\n'));
  zip.writeZip(resolve(out, 'playground-' + mode + '.zip'));
}
