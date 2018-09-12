<?php

declare(strict_types=1);

/**
 * @package    Grav\Framework\File
 *
 * @copyright  Copyright (C) 2015 - 2018 Trilby Media, LLC. All rights reserved.
 * @license    MIT License; see LICENSE file for details.
 */

namespace Grav\Framework\File;

use Grav\Framework\File\Formatter\FormatterInterface;

class DataFile extends AbstractFile
{
    /** @var FormatterInterface */
    protected $formatter;

    /**
     * File constructor.
     * @param string $filepath
     * @param FormatterInterface $formatter
     */
    public function __construct($filepath, FormatterInterface $formatter)
    {
        parent::__construct($filepath);

        $this->formatter = $formatter;
    }

    /**
     * (Re)Load a file and return RAW file contents.
     *
     * @return array
     */
    public function load()
    {
        $raw = parent::load();

        try {
            return $this->formatter->decode($raw);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException(sprintf("Failed to load file '%s': %s", $this->getFilePath(), $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * Save file.
     *
     * @param  string|array  $data  Data to be saved.
     * @throws \RuntimeException
     */
    public function save($data)
    {
        if (\is_string($data)) {
            try {
                $this->formatter->decode($data);
            } catch (\RuntimeException $e) {
                throw new \RuntimeException(sprintf("Failed to save file '%s': %s", $this->getFilePath(), $e->getMessage()), $e->getCode(), $e);
            }
            $encoded = $data;
        } else {
            $encoded = $this->formatter->encode($data);
        }

        parent::save($encoded);
    }
}