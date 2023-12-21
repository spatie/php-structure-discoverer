# Upgrading

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not cover. We accept PRs to improve this guide.

## From v1 to v2

- `DiscoverWorker` now also takes `DiscoverProfileConfig $config` as an argument when running
- If you've written a custom `DiscoverWorker` then take a look at what's changed in the default ones
- `SynchronousDiscoverWorker` has no `$multiFileResolver` constructor argument anymore
- The `discover` method in `StructuresResolver` is now protected and only takes a config
