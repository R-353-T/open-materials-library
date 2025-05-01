import { readFileSync } from "fs";
import SHELL from "../config/shell.js";
import { shexec } from "../function/shexec.js";
import FILE from "../config/file.js";
import YAML from 'yaml';

const config = YAML.parse(readFileSync(FILE.WPCLI_CONFIG, "utf-8"));

config.url = config.url.replace("http://", "");

if(config.url[config.url.length - 1] === "/") {
    config.url = config.url.slice(0, -1);
}

await shexec({ command: SHELL.SERVE, args: [config.url] });