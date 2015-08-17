<?php
// Backup files and folders stored inside plugin to somewhere outside plugin
// - Backup vendor folder
// - Backup emoji images
// - Backup .lock files

function halo_get_backupable_storge()
{
    $baseDir = dirname(__FILE__);
    $storges = array(
        'halo_vendor_dir' => $baseDir . '/vendor/',
        'halo_emoji_dir' => $baseDir . '/app/views/default/assets/images/emoji/',
        'halo_pkg_lock' => $baseDir . '/pkg.lock',
        'halo_install_index_lock' => $baseDir . '/install/install.lock',
        'halo_install_seed_lock' => $baseDir . '/install/seed.lock'
    );
    return $storges;
}

function halo_copyr($src, $dest) {
    if (!file_exists($src)) {
        return false;
    }
    if (is_link($src)) {
        return symlink(readlink($src), $dest);
    }
    if (is_file($src)) {
        return copy($src, $dest);
    }
    if (!is_dir($dest)) {
        mkdir($dest);
    }
    // Loop through the folder
    $dir = dir($src);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        // Deep copy directories
        halo_copyr("$src/$entry", "$dest/$entry");
    }
    $dir->close();
    return true;
}

function halo_rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        halo_rmdirr("$dirname/$entry");
    }
    $dir->close();
    return rmdir($dirname);
}

function halo_filter_storge_backup($return, $plugin) {
    if (is_wp_error($return)) {
        return $return;
    }
    $plugin = isset($plugin['plugin']) ? $plugin['plugin'] : '';
    if (empty($plugin)) {
        return $return;
    }
    if ($plugin != 'halosocial/halosocial.php') {
        return $return;
    }
    $storges = halo_get_backupable_storge();
    foreach ($storges as  $key => $src) {
        $dest = dirname(__FILE__) . '/../' . md5($key);
        halo_copyr($src, $dest);
    }
    return $return;
}

function halo_filter_storge_recover($return, $plugin, $result) {
    if (is_wp_error($return)) {
        return $return;
    }
    $plugin = isset($plugin['plugin']) ? $plugin['plugin'] : '';
    if (empty($plugin)) {
        return $return;
    }
    if ($plugin != 'halosocial/halosocial.php') {
        return $return;
    }
    $storges = halo_get_backupable_storge();
    foreach ($storges as  $key => $dest) {
        $src = dirname(__FILE__) . '/../' . md5($key);
        halo_copyr($src, $dest);
        if (is_dir($src)) {
            halo_rmdirr($src);
        }
    }
    return $return;
}

add_filter('upgrader_pre_install', 'halo_filter_storge_backup', 10, 2);
add_filter('upgrader_post_install', 'halo_filter_storge_recover', 10, 2);
