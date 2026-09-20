import { mkdtempSync, copyFileSync, rmSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const root = dirname(dirname(fileURLToPath(import.meta.url)));
const temporary = mkdtempSync(join(root, 'tests/.generated-'));

function run(command, args) {
  const result = spawnSync(command, args, { cwd: root, stdio: 'inherit' });
  if (result.error) throw result.error;
  if (result.status !== 0) throw new Error(`${command} exited with ${result.status}`);
}

try {
  run('php', ['tests/generate.php', temporary]);
  for (const name of ['matching', 'values']) {
    const directory = join(temporary, name);
    copyFileSync(join(root, 'tests/fixtures', `${name}.ts`), join(directory, 'consumer.ts'));
    run(join(root, 'node_modules/.bin/tsc'), [
      '--strict', '--noEmitOnError', '--pretty', 'false',
      '--target', 'ES2020', '--module', 'commonjs',
      '--outDir', join(directory, 'compiled'), join(directory, 'consumer.ts'),
    ]);
    run('node', [join(directory, 'compiled/consumer.js')]);
    console.log(`PASS: ${name} generated TypeScript compiles and executes`);
  }
} finally {
  rmSync(temporary, { recursive: true, force: true });
}
