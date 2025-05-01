import { dirname, join } from "path";
import { fileURLToPath } from "url";

const ROOT_DIR = dirname(dirname(dirname(fileURLToPath(import.meta.url))));

const DIRECTORY = {
    ROOT: ROOT_DIR,
    BACKEND: join(ROOT_DIR, "backend"),
    BACKEND_BUILD: join(ROOT_DIR, "backend", "build"),
    BACKEND_SOURCE: join(ROOT_DIR, "backend", "source"),
    BACKEND_DESTINATION: join(ROOT_DIR, "backend", "build", "wp-content", "themes", "api"),
    FRONTEND: join(ROOT_DIR, "frontend"),
    SCRIPT: join(ROOT_DIR, "script"),
    SCRIPT_CONFIG: join(ROOT_DIR, "script", "config"),
    VENDOR: join(ROOT_DIR, "vendor")
};

export default DIRECTORY;