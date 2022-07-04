## Custom listener

### Basic listener

Let's create a listener that removes the directory before loading the fixtures.

```php
namespace App\Fixture;

use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Symfony\Component\Filesystem\Filesystem;

final class DirectoryPurgerListener extends AbstractListener implements BeforeSuiteListenerInterface
{
    public function getName(): string
    {
        return 'directory_purger';
    }

    public function beforeSuite(SuiteEvent $suiteEvent, array $options): void
    {
        (new Filesystem())->remove('/hardcoded/path/to/directory');
    }
}
```

If you want to listen to different/more event(s) instead of BeforeSuiteListenerInterface implement one or more of those interfaces:
- AfterFixtureListenerInterface
- AfterSuiteListenerInterface
- BeforeFixtureListenerInterface
- BeforeSuiteListenerInterface

The next step is to register this listener ( you can avoid this step when using autoconfiguration ):

```xml
<service id="app.listener.directory_purger" class="App\Fixture\DirectoryPurgerListener">
    <tag name="sylius_fixtures.listener" />
</service>
```

Listener is now registered and ready to use in your suite:

```yaml
sylius_fixtures:
    suites:
        my_suite:
            listeners:
                directory_purger: ~
```

### Configurable listener

Listener that removes a hardcoded directory isn't very useful. Allowing it to receive an array of directories would make
this listener a lot more reusable.

```php
// ...

final class DirectoryPurgerListener extends AbstractListener implements ListenerInterface
{
    // ...

    public function beforeSuite(SuiteEvent $suiteEvent, array $options): void
    {
        (new Filesystem())->remove($options['directories']);
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNodeBuilder = $optionsNode->children();

        $optionsNodeBuilder
            ->arrayNode('directories')
                ->performNoDeepMerging()
                ->prototype('scalar')
        ;
    }
}
```

The `AbstractListener` implements the `ConfigurationInterface::getConfigTreeBuilder()` and exposes a handy
`configureOptionsNode()` method to reduce the boilerplate. It is possible to test this configuration
using [SymfonyConfigTest] library.

Now, it is possible to remove different directories in each suite:

```yaml
sylius_fixtures:
    suites:
        my_suite:
            listener:
                directory_purger:
                    options:
                        directories:
                            - /custom/directory
                            - /another/custom/directory
        my_another_suite:
            listener:
                directory_purger:
                    options:
                        directories:
                            - /path/per/suite
```

**[Go back to the documentation's index](index.md)**

[SymfonyConfigTest]: https://github.com/matthiasnoback/SymfonyConfigTest
