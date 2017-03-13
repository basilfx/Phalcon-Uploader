<?php

namespace BasilFX\Uploader;

use Phalcon\Di\Injectable;

/**
 * Service for validating and managing uploads.
 */
class Uploader extends Injectable
{
    /**
     * @var string Path to upload folder.
     */
    private $path;

    /**
     * @var string File mode to apply to newly uploaded files.
     */
    private $fileMode;

    /**
     * @var string Directory mode to apply to newly created folders.
     */
    private $directoryMode;

    /**
     * @var array List of uploads to process. Each item is an array of options.
     */
    private $uploads;

    /**
     * @var array List of files that will be processed when saving.
     */
    private $queue;

    /**
     * Construct a new Uploader.
     */
    public function __construct()
    {
        $this->path = "";
        $this->fileMode = 0655;
        $this->directoryMode = 0755;
        $this->uploads = [];
        $this->queue = null;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getFileMode()
    {
        return $this->fileMode;
    }

    /**
     * Get the directory mode.
     *
     * @return mixed
     */
    public function getDirectoryMode()
    {
        return $this->directoryMode;
    }

    /**
     * Get the path.
     *
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the file mode.
     *
     * @param $fileMode
     */
    public function setFileMode($fileMode)
    {
        $this->fileMode = $fileMode;
    }

    /**
     * Set the directory mode.
     *
     * @param $directoryMode
     */
    public function setDirectoryMode($directoryMode)
    {
        $this->directoryMode = $directoryMode;
    }

    /**
     * Add an upload to the list of uploads to process. The following options
     * can be specified:
     *
     * - name: string or callable that returns a string.
     * - types: array of accepted mime types.
     * - optional: boolean to indicate whether the upload is optional.
     *
     * @param string $key Name of the upload (as defined in $_FILES).
     * @param array $options Array of options.
     */
    public function add($key, array $options)
    {
        $this->uploads[$key] = $options;
    }

    /**
     * Verify if the added files are valid.
     *
     * @return bool
     */
    public function isValid()
    {
        // Index all files.
        $files = [];

        foreach ($this->request->getUploadedFiles(true) as $file) {
            $files[$file->getKey()] = $file;
        }

        // Verify if all selected uploads are valid.
        $this->queue = [];

        foreach ($this->uploads as $key => $options) {
            // Check if file is present.
            if (!isset($files[$key])) {
                if ($options["required"] ?? true) {
                    return false;
                } else {
                    continue;
                }
            }

            $this->queue[] = [
                "file" => $files[$key],
                "options" => $options
            ];
        }

        return true;
    }

    /**
     * Save the uploads. Returns an array of Upload instances.
     *
     * @return array
     */
    public function save()
    {
        $result = [];

        foreach ($this->queue as $item) {
            $options = $item["options"];
            $file = $item["file"];

            // Determine path and relative path.
            if (is_callable($options["name"])) {
                $relativePath = $options["name"]($file);
            } else {
                $relativePath = $options["name"];
            }

            $path = $this->resolve($relativePath);

            // Ensure target folder exist. Create the folder if needed.
            $pathWithoutFile = dirname($path);

            if (!is_dir($pathWithoutFile)) {
                if (!@mkdir($pathWithoutFile, $this->directoryMode, true)) {
                    throw new Exception(
                        "Unable to create directory: $pathWithoutFile"
                    );
                }
            }

            // Move the file to the new path and change the permissions.
            $file->moveTo($path);

            if (!@chmod($path, $this->fileMode)) {
                throw new Exception("Unable to change mode on file: $path");
            }

            $result[$file->getKey()] = new Upload($file, $path, $relativePath);
        }

        return $result;
    }

    /**
     * Return the resolved path given a relative path. This is useful to
     * determine the absolute path of an uploaded file.
     *
     * @param string $relativePath The relative path of the file.
     * @return string The resolved path of the file.
     */
    public function resolve($relativePath)
    {
        if (!$relativePath) {
            throw new Exception("Cannot resolve path of nothing.");
        }

        return ensure_slash($this->path) . $relativePath;
    }
}
