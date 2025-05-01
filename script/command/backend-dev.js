import fs from "fs";
import { existsSync, mkdirSync } from "fs";
import { sleep } from "../function/sleep.js";
import DIRECTORY from "../config/directory.js";
import { compareDirs } from "../function/directory.js";
import { PHP_LINT_STATUS, phpLint } from "../function/phplint.js";
import { join } from "path";

if (existsSync(DIRECTORY.BACKEND_DESTINATION) === false) {
    mkdirSync(DIRECTORY.BACKEND_DESTINATION);
}

let lintFlag = PHP_LINT_STATUS.NOT;
let elementPath = "";
let elementStats = null;

while (true) {
    console.clear();

    const {
        createdElements,
        deletedElements,
        updatedElements
    } = compareDirs(DIRECTORY.BACKEND_SOURCE, DIRECTORY.BACKEND_DESTINATION);

    lintFlag = PHP_LINT_STATUS.NOT;

    for (const deletedElement of deletedElements) {
        if (deletedElement.stats.isDirectory()) {
            fs.rmdirSync(join(DIRECTORY.BACKEND_DESTINATION, deletedElement.rel), { recursive: true });
        } else {
            fs.rmSync(join(DIRECTORY.BACKEND_DESTINATION, deletedElement.rel));
        }
        console.log(`- ${deletedElement.rel}`);
    }

    for (const createdElement of createdElements) {
        if(createdElement.stats.isDirectory()) {
            fs.mkdirSync(join(DIRECTORY.BACKEND_DESTINATION, createdElement.rel), { recursive: true });
        } else {
            if (lintFlag === PHP_LINT_STATUS.NOT) {
                lintFlag = await phpLint(createdElement.rel);
            }

            if (lintFlag === PHP_LINT_STATUS.NOT || lintFlag === PHP_LINT_STATUS.OK) {
                elementPath = join(DIRECTORY.BACKEND_DESTINATION, createdElement.rel);
                fs.copyFileSync(createdElement.abs, elementPath);
                elementStats = fs.statSync(createdElement.abs);
                fs.utimesSync(elementPath, elementStats.mtime, elementStats.mtime);
            }
        }
        
        console.log(`+ ${createdElement.rel}`);
    }

    for (const updatedElement of updatedElements) {
        if(updatedElement.stats.isFile()) {
            if (lintFlag === PHP_LINT_STATUS.NOT) {
                lintFlag = await phpLint(updatedElement.rel);
            }
    
            if (lintFlag === PHP_LINT_STATUS.NOT || lintFlag === PHP_LINT_STATUS.OK) {
                elementPath = join(DIRECTORY.BACKEND_DESTINATION, updatedElement.rel);
                fs.copyFileSync(updatedElement.abs, elementPath);
                elementStats = fs.statSync(updatedElement.abs);
                fs.utimesSync(elementPath, elementStats.mtime, elementStats.mtime);
                console.log(`~ ${updatedElement.rel}`);
            }
        }
    }

    await sleep((lintFlag === PHP_LINT_STATUS.ERROR ? 15000 : 3000));
}