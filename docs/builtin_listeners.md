## Built-in listeners

**SyliusFixturesBundle** comes with a few useful listeners.

## Suite Loader (`suite_loader`)
This allows a fixture suite to define other suites which will be loaded **before** the current suite.

```yaml
sylius_fixtures:
    suites:
        my_suite:
            listeners:
                suite_loader:
                    options:
                        suites:
                            - "other_suite"
```

This will load the `other_suite` before loading the fixtures from the `my_suite`.

### Logger (`logger`)

Provides output while running `sylius:fixtures:load` command.

```
# Without logger

$ bin/console sylius:fixtures:load my_suite
$ _

# With logger

$ bin/console sylius:fixtures:load my_suite
Running suite "my_suite"...
Running fixture "country"...
Running fixture "locale"...
Running fixture "currency"...
$ _
```

The logger does not have any configuration options. It can be enabled in such a way:

```yaml
sylius_fixtures:
    suites:
        my_suite:
            listeners:
                logger: ~
```

### ORM Purger (`orm_purger`)

Purges the relational database. Uses `delete` purge mode and the default entity manager if not configured otherwise.

Configuration options:

 * `mode` - sets how database is purged, available values: `delete` (default), `truncate`
 * `managers` - an array of entity managers' names used to purge the database, `[null]` by default
 * `exclude` - an array of table/view names to be excluded from purge, `[]` by default

Example configuration:

```yaml
sylius_fixtures:
    suites:
        my_suite:
            listeners:
                orm_purger:
                    options:
                        mode: truncate
                        managers:
                            - custom_manager
                        exclude:
                            - custom_entity_table_name
```

### PHPCR / MongoDB Purger (`phpcr_purger` / `mongodb_purger`)

Purges the document database. Uses the default document manager if not configured otherwise.

Configuration options:

 * `managers` - an array of document managers' names used to purge the database, `[null]` by default

Example configuration:

```yaml
sylius_fixtures:
    suites:
        my_suite:
            listeners:
                phpcr_purger:
                    options:
                        managers:
                            - custom_manager # Uses custom document manager
                mongodb_purger: ~ # Uses default document manager
```

**[Go back to the documentation's index](index.md)**
