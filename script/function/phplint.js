import DIRECTORY from "../config/directory.js";
import SHELL from "../config/shell.js";
import { shexec } from "./shexec.js";

export const PHP_LINT_STATUS = {
    NOT: -1,
    OK: 0,
    ERROR: 1
}

/**
 * @param {string} filename 
 * @returns 
 */
export async function phpLint(filename) {
    if(filename.toLowerCase().endsWith(".php")) {

        await shexec({
            command: SHELL.PHPCBF,
            cwd: DIRECTORY.BACKEND_SOURCE,
            args: ["--standard=PSR12", DIRECTORY.BACKEND_SOURCE],
            throwError: false
        });

        const cs = await shexec({
            command: SHELL.PHPCS,
            cwd: DIRECTORY.BACKEND_SOURCE,
            args: ["--standard=PSR12", DIRECTORY.BACKEND_SOURCE],
            throwError: false
        });

        const row = "#".repeat(process.stdout.columns - 2);
        console.log(row , row, row, "\n");

        return cs ? PHP_LINT_STATUS.OK : PHP_LINT_STATUS.ERROR;
    } else {
        return PHP_LINT_STATUS.NOT;
    }
}