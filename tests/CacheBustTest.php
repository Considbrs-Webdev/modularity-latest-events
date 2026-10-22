<?php

declare(strict_types=1);

namespace ModularityLatestEvents\Tests;

use ModularityLatestEvents\Helper\CacheBust;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class CacheBustTest extends TestCase
{
    private string $manifestPath;

    protected function setUp(): void
    {
        $this->manifestPath = MODULARITYLATESTEVENTS_PATH . 'assets/dist/manifest.json';
        $this->resetManifestCache();
        $this->removeManifest();
    }

    protected function tearDown(): void
    {
        $this->removeManifest();
        $this->resetManifestCache();
    }

    public function testNameReturnsFalseWhenTheManifestIsMissing(): void
    {
        $this->assertFalse(CacheBust::name('css/modularity-latest-events.css'));
    }

    public function testNameReturnsTheHashedFileFromTheManifest(): void
    {
        $this->writeManifest([
            'css/modularity-latest-events.css' => 'css/modularity-latest-events.example.css',
        ]);

        $this->assertSame(
            'css/modularity-latest-events.example.css',
            CacheBust::name('css/modularity-latest-events.css'),
        );
    }

    public function testNameReturnsFalseForAnUnknownEntry(): void
    {
        $this->writeManifest([
            'css/modularity-latest-events.css' => 'css/modularity-latest-events.example.css',
        ]);

        $this->assertFalse(CacheBust::name('js/missing.js'));
    }

    /**
     * @param array<string, string> $manifest
     */
    private function writeManifest(array $manifest): void
    {
        $directory = dirname($this->manifestPath);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            $this->fail('Could not create manifest directory');
        }

        file_put_contents($this->manifestPath, json_encode($manifest, JSON_THROW_ON_ERROR));
        $this->resetManifestCache();
    }

    private function removeManifest(): void
    {
        if (is_file($this->manifestPath)) {
            unlink($this->manifestPath);
        }
    }

    private function resetManifestCache(): void
    {
        $property = new ReflectionProperty(CacheBust::class, 'manifest');
        $property->setValue(null, null);
    }
}
