<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Micro\OpenvpnWebadmin\Core\ServerSettingsService;

$dir = sys_get_temp_dir() . '/webadmin-settings-' . bin2hex(random_bytes(8));
if (!mkdir($dir, 0700)) {
    throw new RuntimeException('Cannot create test directory.');
}

$target = $dir . '/target.conf';
$link = $dir . '/server.conf';
$local = $dir . '/local.conf';
$history = $dir . '/history';

try {
    file_put_contents($target, 'original');
    symlink($target, $link);
    $makeService = static fn(string $source, string $save): ServerSettingsService => new ServerSettingsService([
        'vpn_server_config' => [
            'source_path' => $source,
            'save_path' => $save,
            'history_path' => $history,
        ],
    ]);

    $service = $makeService($link, $link);
    $current = $service->getCurrent();
    if ($current['content'] !== 'original' || $current['read_only'] !== true) {
        throw new RuntimeException('Linked configuration was not read correctly.');
    }
    try {
        $service->save('modified');
        throw new LogicException('Linked configuration was writable.');
    } catch (RuntimeException $e) {
        if ($e->getMessage() !== 'Server configuration is read-only.') {
            throw $e;
        }
    }
    if (file_get_contents($target) !== 'original' || is_dir($history)) {
        throw new RuntimeException('Read-only save changed the target or created history.');
    }
    if (!$makeService($link, $local)->isReadOnly() || !$makeService($local, $link)->isReadOnly()) {
        throw new RuntimeException('Linked source or save path was not protected.');
    }

    file_put_contents($local, 'before');
    $service = $makeService($local, $local);
    if ($service->getCurrent()['read_only'] !== false) {
        throw new RuntimeException('Local configuration unexpectedly read-only.');
    }
    $service->save('after');
    if (file_get_contents($local) !== 'after' || count(glob($history . '/*.conf')) !== 1) {
        throw new RuntimeException('Local save or history failed.');
    }

    unlink($target);
    clearstatcache();
    try {
        $makeService($link, $link)->getCurrent();
        throw new LogicException('Missing symlink target was silently created.');
    } catch (RuntimeException $e) {
        if (file_exists($target)) {
            throw new RuntimeException('Missing symlink target was created.', 0, $e);
        }
    }
    echo "Server settings read-only and local save: OK\n";
} finally {
    foreach (glob($history . '/*.conf') ?: [] as $snapshot) {
        unlink($snapshot);
    }
    if (is_dir($history)) {
        rmdir($history);
    }
    foreach ([$target, $link, $local] as $path) {
        if (is_file($path) || is_link($path)) {
            unlink($path);
        }
    }
    rmdir($dir);
}
