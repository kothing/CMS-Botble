<?php

namespace Botble\Backup\Supports\MySql;

use Exception;

class CompressNone extends CompressManagerFactory
{
    protected $fileHandler = null;

    /**
     * @param string $filename
     * @throws Exception
     */
    public function open($filename)
    {
        $this->fileHandler = fopen($filename, 'wb');
        if (false === $this->fileHandler) {
            throw new Exception('Output file is not writable');
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function write($str)
    {
        $bytesWritten = fwrite($this->fileHandler, $str);
        if (false === $bytesWritten) {
            throw new Exception('Writing to file failed! Probably, there is no more free space left?');
        }

        return $bytesWritten;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return fclose($this->fileHandler);
    }
}
