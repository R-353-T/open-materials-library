import { join } from "path";
import DIRECTORY from "./directory.js";

const FILE = {
    WPCLI_CONFIG: join(DIRECTORY.BACKEND, "wp-cli.yml"),
    VENDOR: {
        WPCLI: join(DIRECTORY.VENDOR, "wpcli-2.11.0.phar"),
        PHPCBF: join(DIRECTORY.VENDOR, "phpcbf-3.11.1.phar"),
        PHPCS: join(DIRECTORY.VENDOR, "phpcs-3.11.1.phar")
    }
};

export default FILE;