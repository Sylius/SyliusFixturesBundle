## UPGRADE FROM `1.5.x` TO `1.6`

- Commands `FixturesListCommand` and `FixturesLoadCommand` are not container aware anymore, they have dependencies
  injected through the constructor. Adjust your code if you've modified them or their DI definitions.
