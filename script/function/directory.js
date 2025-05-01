import fs, { readdirSync, statSync } from 'fs';
import path from 'path';

/**
 * @param {string} dir
 * @param {boolean} recursive
 * @param {string|undefined} origin
 * @returns {{ [key: string]: { rel: string, abs: string, stats: fs.Stats } ] }}
 */
export function walk(dir, recursive = true, origin = undefined) {
    if (origin === undefined) {
        origin = dir;
    }

    const output = {};
    const elementList = readdirSync(dir);

    for (const element of elementList) {
        const abs = path.join(dir, element);
        const rel = path.relative(origin, abs);
        const stats = statSync(abs);

        output[rel] = {
            abs,
            rel,
            stats
        };

        if (stats.isDirectory() && recursive) {
            Object.assign(output, walk(abs, recursive, origin));
        }
    }

    return output;
};

/**
 * @param {string} dirA 
 * @param {string} dirB 
 * @returns {{
 *  createdElements: {abs: string, rel: string, stats: fs.Stats}[],
 *  deletedElements: {abs: string, rel: string, stats: fs.Stats}[],
 *  updatedElements: {abs: string, rel: string, stats: fs.Stats}[]
 * }}
 */
export function compareDirs(dirA, dirB) {
    const walkA = walk(dirA);
    const walkB = walk(dirB);
    const toSkip = {};

    const createdElements = Object
        .entries(walkA)
        .filter(([k,]) => !walkB[k])
        .map(([k, v]) => {
            toSkip[k] = true;
            return v;
        });

    const deletedElements = Object
        .entries(walkB)
        .filter(([k,]) => !walkA[k])
        .map(([,v]) => v);

    const updatedElements = Object
        .entries(walkA)
        .filter(([k,]) => !toSkip[k]
            && walkA[k].stats.isFile()
            && walkB[k].stats.isFile()
            && walkA[k].stats.mtimeMs.toFixed(0) !== walkB[k].stats.mtimeMs.toFixed(0))
        .map(([,v]) => v);

    return {
        createdElements,
        deletedElements,
        updatedElements
    };
}