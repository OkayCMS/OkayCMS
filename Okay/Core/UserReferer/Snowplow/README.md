# Vendored `snowplow/referer-parser` (PHP)

Source: [snowplow/referer-parser](https://github.com/snowplow/referer-parser) **0.2.0** (MIT). The Composer package is unmaintained; the subtree under `RefererParser/` mirrors `php/src/Snowplow/RefererParser/` with:

- explicit nullable `?ConfigReaderInterface` on `Parser::__construct` (PHP 8.4+);
- **no** `YamlConfigReader` (unused in OkayCMS — only `JsonConfigReader` is referenced).

Referer definitions: `../data/referers.json` (same data the app already shipped for `UserReferer::createConfigReader()`).
