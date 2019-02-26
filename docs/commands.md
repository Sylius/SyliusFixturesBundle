## Commands

### Listing fixtures

To list all available suites and fixtures, use the `sylius:fixtures:list` command.

```
$ bin/console sylius:fixtures:list

Available suites:
 - default
 - dev
 - test
Available fixtures:
 - country
 - locale
 - currency
```

### Loading fixtures

To load a suite, use the `sylius:fixtures:load [suite]` command.

```
$ bin/console sylius:fixtures:load default

Running suite "default"...
Running fixture "country"...
Running fixture "locale"...
Running fixture "currency"...
```

**[Go back to the documentation's index](index.md)**
