<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Filesystem_Native implements IRessio_Filesystem
{
    /**
     * Check file exists
     * @param string $filename
     * @return bool
     */
    public function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * Check directory exists
     * @param string $path
     * @return bool
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * @param string $filename
     * @return integer|bool
     */
    public function size($filename)
    {
        return filesize($filename);
    }

    /**
     * Load content from file
     * @param string $filename
     * @return string
     */
    public function getContents($filename)
    {
        return @file_get_contents($filename);
    }

    /**
     * Save content to file
     * @param string $filename
     * @param string $content
     * @return bool
     */
    public function putContents($filename, $content)
    {
        $result = false;
        $fp = fopen($filename, 'wb+');
        if (flock($fp, LOCK_EX)) {
            $result = (fwrite($fp, $content) === strlen($content));
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return $result;
    }

    /**
     * Make directory
     * @param string $path
     * @param int $chmod
     * @return bool
     */
    public function makeDir($path, $chmod = 0777)
    {
        return is_dir($path) || @mkdir($path, $chmod, true) || is_dir($path);
    }

    /**
     * Get file timestamp
     * @param string $path
     * @return int
     */
    public function getModificationTime($path)
    {
        $time = @filemtime($path);
        if (strncasecmp(PHP_OS, 'win', 3) !== 0) {
            return $time;
        }
        // fix mtime on Windows
        return $time + 3600 * (date('I') - date('I', $time));
    }

    /**
     * Update file timestamp
     * @param string $filename
     * @param int $time
     * @return bool
     */
    public function touch($filename, $time = null)
    {
        if ($time === null) {
            $time = time();
        }
        return touch($filename, $time);
    }

    /**
     * Delete file or empty directory
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        return unlink($path);
    }

    /**
     * Copy file
     * @param string $src
     * @param string $target
     * @return bool
     */
    public function copy($src, $target)
    {
        return copy($src, $target);
    }
}