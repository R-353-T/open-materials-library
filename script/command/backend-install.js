import DIRECTORY from "../config/directory.js";
import SHELL from "../config/shell.js";
import { shexec } from "../function/shexec.js";

const commandList = [
    ["core", "download"],
    ["core", "verify-checksums"],
    ["config", "create"],
    ["core", "install"],
    ["plugin", "install", "jwt-authentication-for-wp-rest-api"],
    ["option", "update", "permalink_structure", "\"/index.php/%postname%/\""]
];

for(const command of commandList) {
    await shexec({
        command: SHELL.WPCLI,
        cwd: DIRECTORY.BACKEND,
        args: command,
        throwError: true
    });
}

console.log("\n\nWORDPRESS INSTALLED SUCCESSFULLY\n\n");