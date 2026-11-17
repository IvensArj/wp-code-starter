import { mkdir, stat } from 'node:fs/promises';
import AdmZip from 'adm-zip';

const snapshot = 'demo/.local-export';
const database = snapshot + '/database.sql';
const uploads = snapshot + '/uploads';
if (!(await stat(database)).size || !(await stat(uploads)).isDirectory()) {
  throw new Error('The Local WP package needs a database dump and example media.');
}

const zip = new AdmZip();
zip.addLocalFile(database);
zip.addLocalFolder(uploads, 'wp-content/uploads');
zip.addLocalFolder('themes/wp-code-starter', 'wp-content/themes/wp-code-starter');
zip.addLocalFile('LICENSE', 'wp-content/themes/wp-code-starter');
zip.addLocalFolder('plugins/wpcs-projects', 'wp-content/plugins/wpcs-projects');
zip.addLocalFile('LICENSE', 'wp-content/plugins/wpcs-projects');

await mkdir('dist', { recursive: true });
zip.writeZip('dist/wp-code-starter-local.zip');
