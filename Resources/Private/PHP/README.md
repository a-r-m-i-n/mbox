# PHP dependencies for TER release of EXT:mbox

From the repository root, update the lock file and install the bundled dependencies
using DDEV:

```bash
ddev composer --working-dir=/var/www/mbox/Resources/Private/PHP update --no-dev --optimize-autoloader
```

The lock file targets PHP 8.2, the minimum supported by the extension. To build a
TER release from the committed lock file, use:

```bash
ddev composer --working-dir=/var/www/mbox/Resources/Private/PHP install --no-dev --optimize-autoloader
```

Include `Resources/Private/PHP/vendor/` in the TER package for installations
without Composer.
