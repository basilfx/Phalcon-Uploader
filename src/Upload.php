<?php

namespace BasilFX\Uploader;

use Phalcon\Http\Request\FileInterface;

/**
 * Represent an uploaded file.
 */
class Upload
{
    /**
     * @var FileInterface Original FileInterface instance.
     */
    private $file;

    /**
     * @var string Path to uploaded file.
     */
    private $path;

    /**
     * @var string Relative path to uploaded file.
     */
    private $relativePath;

    /**
     * Construct a new Upload instance.
     *
     * @param FileInterface $file Original FileInterface instance.
     * @param string $path Full path to uploaded file.
     * @param string $relativePath Relative path to uploaded file.
     */
    public function __construct(FileInterface $file, $path, $relativePath)
    {
        $this->file = $file;
        $this->path = $path;
        $this->relativePath = $relativePath;
    }

    /**
     * Return the original FileInterface instance that represented the upload.
     *
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the path of the uploaded file, including the path to the globally
     * configured upload folder.
     *
     * @return string Path of uploaded file.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return the relative path of the uploaded file. This path is relative to
     * the globally configured upload folder.
     *
     * @return string Relative path of uploaded file.
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }
}
