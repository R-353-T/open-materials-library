import DIRECTORY from "./directory.js";
import FILE from "./file.js";

const SHELL = {
    PHPCS: `php ${FILE.VENDOR.PHPCS}`,
    PHPCBF: `php ${FILE.VENDOR.PHPCBF}`,
    WPCLI: `php -d memory_limit=256M ${FILE.VENDOR.WPCLI}`,
    SERVE: `php -t ${DIRECTORY.BACKEND_BUILD} -S`
};

export default SHELL;