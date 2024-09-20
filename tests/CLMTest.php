<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Bnomei\ContentLastModified;
use Kirby\Filesystem\F;

test('singleton', function () {
    // create
    $clm = ContentLastModified::singleton();
    expect($clm)->toBeInstanceOf(ContentLastModified::class);

    // from static cached
    $clm = ContentLastModified::singleton();
    expect($clm)->toBeInstanceOf(ContentLastModified::class);
});

test('option', function () {
    $clm = new ContentLastModified([
        'debug' => true,
        'file' => function () {
            return 'hello';
        },
    ]);

    expect($clm->option('debug'))->toBeTrue();
    expect($clm->option('file') === 'hello')->toBeTrue();

    expect($clm->option())->toBeArray();
});

test('construct', function () {
    $clm = new ContentLastModified;
    expect($clm)->toBeInstanceOf(ContentLastModified::class);
});

test('track and write', function () {
    $clm = ContentLastModified::singleton();
    $file = $clm->option('file');
    F::remove($file);

    $timestamp = time();
    sleep(1);
    expect($clm->track($timestamp))->toBeInt()
        ->and($clm->timestamp())->toBe($timestamp)
        ->and(file_exists($file))->toBeFalse();

    $clm->write();
    expect(file_exists($file))->toBeTrue()
        ->and(file_get_contents($file))->toBe((string) $timestamp);

    sleep(1);
    kirby()->impersonate('kirby');
    $page = page('home')->update(['title' => time()]);
    $clm->write(); // destruct
    expect(file_exists($file))->toBeTrue()
        ->and(file_get_contents($file))->toBe((string) $page->title()->value());
});
