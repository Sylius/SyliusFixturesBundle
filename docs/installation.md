## Installation

```bash
composer require sylius/fixtures-bundle
```

### Adding required bundles to the kernel

You need to enable the bundle inside the kernel.

```php
# config/bundles.php

return [
    \Sylius\Bundle\ThemeBundle\SyliusFixturesBundle::class => ['all' => true],
];
```

**[Go back to the documentation's index](index.md)**
