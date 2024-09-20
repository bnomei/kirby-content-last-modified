<?php

use Kirby\Content\Field;

@include_once __DIR__.'/vendor/autoload.php';

Kirby::plugin('bnomei/content-last-modified', [
    'options' => [
        'file' => function (): string {
            return kirby()->roots()->content().'/.content-last-modified';
        },
        'max-dirty' => 100, // write to disk every N changes
    ],
    'hooks' => [
        'page.*:after' => function ($event, $page) {
            if ($event->action() !== 'render') {
                \Bnomei\ContentLastModified::singleton()->track();
            }
        },
        'file.*:after' => function ($event, $file) {
            if ($event->action() !== 'render') {
                \Bnomei\ContentLastModified::singleton()->track();
            }
        },
        'site.*:after' => function ($event, $site) {
            if ($event->action() !== 'render') {
                \Bnomei\ContentLastModified::singleton()->track();
            }
        },
    ],
    'siteMethods' => [
        'contentLastModified' => function (): Field {
            return new Field($this, 'contentLastModified', [
                \Bnomei\ContentLastModified::singleton()->timestamp(),
            ]);
        },
        'removeFromCacheIfCreatedIsAfterModified' => function (string $cache, string $key, ?int $timestamp): bool {
            /* @var $cache \Kirby\Cache\Cache */
            $cache = kirby()->cache($cache);
            if (! $cache) {
                return false;
            }

            /* @var $rawValue \Kirby\Cache\Value */
            $rawValue = $cache->retrieve('some-cache-key');
            if (! $rawValue) {
                return false;
            }

            if (! $timestamp) {
                if (site()->contentLastModified()->isEmpty()) {
                    return false;
                }
                $timestamp = \Bnomei\ContentLastModified::singleton()->timestamp();
            }

            if ($rawValue->created() < $timestamp) {
                return $cache->remove('some-cache-key');
            }

            return false;
        },
    ],
]);
