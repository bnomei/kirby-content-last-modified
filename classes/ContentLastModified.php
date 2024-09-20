<?php

namespace Bnomei;

use Kirby\Filesystem\F;
use Kirby\Toolkit\A;

class ContentLastModified
{
    private $options;

    private ?int $timestamp;

    private int $isDirty = 0;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'file' => option('bnomei.content-last-modified.file'),
            'max-dirty' => option('bnomei.content-last-modified.max-dirty'),
            'debug' => option('debug'),
        ], $options);

        foreach (['file'] as $key) {
            $value = A::get($this->options, $key);
            if ($value instanceof \Closure && is_callable($value)) {
                $this->options[$key] = $value();
            }
        }

        $this->timestamp = null;
    }

    public function __destruct()
    {
        if ($this->isDirty) {
            $this->write();
        }
    }

    public function option(?string $key = null)
    {
        if ($key) {
            return A::get($this->options, $key);
        }

        return $this->options;
    }

    public function track(?int $timestamp = null): int
    {
        if (! $timestamp) {
            $timestamp = time();
        }

        if ($this->timestamp != $timestamp) {
            $this->isDirty++;
        }

        $this->timestamp = $timestamp;

        // make sure to write changes to disk at least every N changes
        // complementing the write on destruct as final call
        $maxDirty = $this->option('max-dirty');
        if (! is_null($maxDirty) && $this->isDirty > intval($maxDirty)) {
            $this->write();
        }

        return $this->timestamp;
    }

    public function write(): bool
    {
        if ($this->isDirty === 0) {
            return false;
        }

        $file = $this->option('file');
        $success = F::write($file, $this->timestamp());
        if ($success) {
            $this->isDirty = 0;

        }

        return $success;
    }

    public function timestamp(): int
    {
        if (! is_null($this->timestamp)) {
            return $this->timestamp;
        }

        $file = $this->option('file');
        if (F::exists($file)) {
            $this->timestamp = F::read($file);
        }

        return $this->timestamp;
    }

    private static $singleton;

    public static function singleton()
    {
        if (! self::$singleton) {
            self::$singleton = new self;
        }

        return self::$singleton;
    }
}
