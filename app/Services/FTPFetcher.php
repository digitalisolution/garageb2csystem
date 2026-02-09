<?php

namespace App\Services;

class FTPFetcher
{
    private $ftpConnection;

    public function connect($ftpHost = null, $ftpUser = null, $ftpPassword = null, $ftpPort = 21)
    {
        // Use dynamic credentials if provided, otherwise fallback to fixed credentials from .env
        if ($ftpHost && $ftpUser && $ftpPassword) {
            // Establish FTP connection with dynamic credentials
            $this->ftpConnection = ftp_connect($ftpHost, $ftpPort);
            if (!$this->ftpConnection) {
                throw new \Exception("Could not connect to FTP server at $ftpHost on port $ftpPort");
            }

            // Login with the provided credentials
            if (!ftp_login($this->ftpConnection, $ftpUser, $ftpPassword)) {
                throw new \Exception("FTP login failed for user $ftpUser");
            }
        } else {
            // Fallback to fixed FTP credentials (from config or .env)
            $ftpHost = env('FTP_HOST');
            $ftpUser = env('FTP_USER');
            $ftpPassword = env('FTP_PASSWORD');
            $ftpPort = env('FTP_PORT', 21); // Default to port 21 if not set

            $this->ftpConnection = ftp_connect($ftpHost, $ftpPort);
            if (!$this->ftpConnection) {
                throw new \Exception("Could not connect to FTP server at $ftpHost on port $ftpPort");
            }

            // Login with the fixed credentials
            if (!ftp_login($this->ftpConnection, $ftpUser, $ftpPassword)) {
                throw new \Exception("FTP login failed for user $ftpUser");
            }
        }

        // Enable passive mode (if required)
        ftp_pasv($this->ftpConnection, true);

        return $this;
    }

    public function getDirectoryList($directory)
    {
        // Get the list of files in the given directory
        if (!$this->ftpConnection) {
            throw new \Exception("FTP connection is not established.");
        }

        // List files in the directory
        $fileList = ftp_nlist($this->ftpConnection, $directory);
        if ($fileList === false) {
            throw new \Exception("Failed to list files in directory $directory");
        }

        return $fileList;
    }


    public function fetchFile($remoteFilePath)
    {
        if (!$this->ftpConnection) {
            throw new \Exception("FTP connection is not established.");
        }

        // Fetch file from the fixed FTP server
        ob_start();
        if (!ftp_get($this->ftpConnection, 'php://output', $remoteFilePath, FTP_BINARY)) {
            throw new \Exception("Failed to fetch file from FTP server: $remoteFilePath");
        }
        $fileContent = ob_get_clean();
        return $fileContent;
    }


    public function disconnect()
    {
        if ($this->ftpConnection) {
            ftp_close($this->ftpConnection);
        }
    }
}
