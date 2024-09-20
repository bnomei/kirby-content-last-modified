# Kirby Content Last Modified

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby-content-last-modified?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby-content-last-modified?color=272822)
[![Coverage](https://flat.badgen.net/codeclimate/coverage/bnomei/kirby-content-last-modified)](https://flat.badgen.net/codeclimate/coverage/bnomei/kirby-content-last-modified)
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby-content-last-modified)](https://codeclimate.com/github/bnomei/kirby-content-last-modified)
[![Discord](https://flat.badgen.net/badge/discord/bnomei?color=7289da)](https://discordapp.com/users/bnomei)

Kirby plugin to track the last timestamp when any content was modified at in the full content folder.

## Commercial Usage

> <br>
> <b>Support open source!</b><br><br>
> This plugin is free but if you use it in a commercial project please consider to sponsor me or make a donation.<br>
> If my work helped you to make some cash it seems fair to me that I might get a little reward as well, right?<br><br>
> Be kind. Share a little. Thanks.<br><br>
> &dash; Bruno<br>
> &nbsp; 

| M | O | N | E | Y |
|---|----|---|---|---|
| [Github sponsor](https://github.com/sponsors/bnomei) | [Patreon](https://patreon.com/bnomei) | [Buy Me a Coffee](https://buymeacoff.ee/bnomei) | [Paypal dontation](https://www.paypal.me/bnomei/15) | [Hire me](mailto:b@bnomei.com?subject=Kirby) |

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

if (site()->contentLastModified()->toInt() > get('last-modified')) {
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
