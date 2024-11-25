# Kirby Content Last Modified

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby-content-last-modified?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby-content-last-modified?color=272822)
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby-content-last-modified)](https://codeclimate.com/github/bnomei/kirby-content-last-modified)
[![Discord](https://flat.badgen.net/badge/discord/bnomei?color=7289da)](https://discordapp.com/users/bnomei)

Kirby plugin to track the last timestamp when any content was modified at in the full content folder.

## Installation

- unzip [master.zip](https://github.com/bnomei/kirby-content-last-modified/archive/master.zip) as folder `site/plugins/kirby-content-last-modified` or
- `git submodule add https://github.com/bnomei/kirby-content-last-modified.git site/plugins/kirby-content-last-modified` or
- `composer require bnomei/kirby-content-last-modified`

## Usage

When you install this plugin it will create a file in the `content` folder of your Kirby installation. If any content (site, pages, files) in the `content` folder changes, this file will store the timestamp when the modification occurred. This might be useful to invalidate caches or trigger other actions.

### Output the last modified timestamp
```php
<?= site()->contentLastModified()->toDate('c') ?>
```

### Flush a cache if the content was modified after a given timestamp

```php
<?php
// http://example.com/some-page?last-modified=1726822135

if (site()->contentLastModified()->toInt() > intval(get('last-modified'))) {
    kirby()->cache('my-cache')->flush();
}
```

### Invalidate individual cache values based on the last modified timestamp
```php
<?php
$lastModifiedTimestamp = site()->contentLastModified()->toInt();

/* @var $cache \Kirby\Cache\Cache */
$cache = kirby()->cache('your-cache-name');

/* @var $rawValue \Kirby\Cache\Value */
$rawValue = $cache->retrieve('some-cache-key');
if ($rawValue && $rawValue->created() < $lastModifiedTimestamp) {
    $cache->remove('some-cache-key');
}

// or with the helper
site()->removeFromCacheIfCreatedIsAfterModified('your-cache-name', 'some-cache-key', $lastModifiedTimestamp);

// defaults to site()->contentLastModified()->toInt()
site()->removeFromCacheIfCreatedIsAfterModified('your-cache-name', 'some-cache-key');
```

### Performance

The core method `site()->modified()` will give you the same timestamp but it will crawl all content files, which might not be the most performant thing to do depending on how much content you have.

## Settings

| bnomei.content-last-modified. | Default    | Description                                                   |            
|-------------------------------|------------|---------------------------------------------------------------|
| file                          | `callback` | returning the absolute path to the file storing the timestamp |
| max-dirty                     | `100`      | write every N changes, if `null` it only writes on destruct   |

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby-content-last-modified/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
