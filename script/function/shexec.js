import { spawn } from 'node:child_process';
import { cwd, stdout } from 'node:process';

/**
 * @param {{
 *  command: string,
 *  args?: string[],
 *  additionalStdin?: string,
 *  cwd?: string,
 *  throwError?: boolean
 * }} options
 * @returns {Promise<boolean>}
 */
export function shexec(options) {
    return new Promise((resolve, reject) => {
        const spawnOptions = {
            shell: true,
            stdio: ["pipe", "pipe", "pipe"],
            cwd: options.cwd ?? cwd()
        };

        const spawnArgs = options.args ?? [];

        const p = spawn(options.command, spawnArgs, spawnOptions);

        if (options.additionalStdin) {
            p.stdin.write(options.additionalStdin);
        }

        p.stdout.on("data", (d) => stdout.write(d.toString()));

        p.stderr.on("data", (d) => stdout.write(d.toString()));

        p.on("error", (d) => stdout.write(d.toString()));

        p.on("close", (code) => {
            if (options.throwError && code !== 0) {
                console.error(`Process exited with code ${code}`);
                reject(code);
            } else {
                resolve(code === 0);
            }
        });

        p.stdin.end();
    });
}