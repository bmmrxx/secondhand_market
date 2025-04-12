<?php

/**
 * FileUpload class handles file uploads and database updates
 * 
 * This class provides functionality to upload files, validate them, and update the database with the new filename.
 * It also creates necessary directories and sets permissions.
 * 
 * @package FileUpload
 * @version 1.0
 * @author Lythical
 * @license MIT
 */

class FileUpload
{
    private $uploadsDir;
    private $tableName;
    private $columnName;
    private $identifierColumn;
    private $pdo;
    private $maxFileSize;
    private $allowedExtensions = [];
    private $extensionMimeMap = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',


        'svg' => 'image/svg+xml',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',


        'pdf' => 'application/pdf',
        'txt' => 'text/plain',


        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        '7z' => 'application/x-7z-compressed'
    ];


    /**
     * Constructor for the FileUpload class
     * 
     * Initializes the FileUpload class with the necessary parameters, validates inputs, and creates the required directories.
     * 
     * @param PDO $pdo The PDO database connection
     * @param string $uploadsDir The directory to store uploaded files (relative to 'assets/')
     * @param array $allowedExtensions Array of allowed file extensions (e.g., ['jpg', 'png', 'pdf'])
     * @param string $tableName Database table name to update
     * @param string $columnName Column name that stores the file name
     * @param string $identifierColumn Column name that uniquely identifies the record
     * @param int $maxFileSize Maximum file size in megabytes (default is 8MB)
     */
    public function __construct($pdo, $uploadsDir, $allowedExtensions, $tableName, $columnName, $identifierColumn, $maxFileSize = 8)
    {
        switch (true) {
            case empty($pdo):
                throw new Exception("Database connection cannot be empty");
            case empty($uploadsDir):
                throw new Exception("Upload directory cannot be empty");
            case empty($tableName):
                throw new Exception("Table name cannot be empty");
            case empty($columnName):
                throw new Exception("Column name cannot be empty");
            case empty($identifierColumn):
                throw new Exception("Identifier column cannot be empty");
            case !is_array($allowedExtensions):
                throw new Exception("Allowed extensions must be an array");
            case count($allowedExtensions) === 0:
                throw new Exception("Allowed extensions array cannot be empty");
        }

        $this->pdo = $pdo;
        $this->maxFileSize = $maxFileSize * 1024 * 1024;
        $this->allowedExtensions = $allowedExtensions;
        $this->uploadsDir = 'assets/' . $uploadsDir;
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        $this->identifierColumn = $identifierColumn;

        $this->createDirectory();
    }

    private function createDirectory()
    {
        // Create directory if it doesn't exist
        if (!is_dir('assets')) {
            mkdir('assets', 0777, true);
        } else {
            try {
                $currentPerms = fileperms('assets') & 0777;
                if ($currentPerms != 0777) {
                    chmod('assets', 0777);
                }
            } catch (Exception $e) {
                throw new Exception("Error setting permissions for the directory.");
            }
        }
        // Create uploads directory if it doesn't exist
        if (!is_dir($this->uploadsDir)) {
            mkdir($this->uploadsDir, 0777, true);
        } else {
            try {
                $currentPerms = fileperms($this->uploadsDir) & 0777;
                if ($currentPerms != 0777) {
                    chmod($this->uploadsDir, 0777);
                }
            } catch (Exception $e) {
                throw new Exception("Error setting permissions for the directory.");
            }
        }
        return true;
    }

    /**
     * Upload a file and update the database
     * 
     * Handles the file upload process, including validation, renaming, moving the file to the target directory, and updating the database with the new file name.
     * 
     * @param array $file The $_FILES array element containing file upload information
     * @param string $identifierValue The value that identifies the record which the file should be associated with in the database
     * @param string $prefix Optional prefix for the filename (defaults to the identifier value if not provided)
     * @return string The name of the uploaded file
     * @throws Exception If the file upload or database update fails
     */

    public function uploadPicture($file, $identifierValue, $prefix)
    {
        if (empty($prefix)) {
            $prefix = $identifierValue;
        }

        // Validate file
        $this->validateFile($file);

        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid($prefix . '_', true) . '.' . $extension;
        $targetPath = $this->uploadsDir . '/' . $fileName;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to upload file.");
        }

        // Update database
        $this->updateDatabase($fileName, $identifierValue);

        return $fileName;
    }

    /**
     * Validate uploaded file
     * 
     * Ensures the uploaded file meets the requirements, including size, extension, and MIME type. Also performs additional checks for image files.
     * 
     * @param array $file The $_FILES array element containing file upload information
     * @throws Exception If the file is invalid or does not meet the requirements
     */
    private function validateFile($file)
    {
        // Check if file was uploaded without errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => "File exceeds the maximum upload size allowed by the server.",
                UPLOAD_ERR_FORM_SIZE => "File exceeds the maximum upload size allowed by the form.",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded.",
                UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
            ];

            $errorMessage = isset($errorMessages[$file['error']])
                ? $errorMessages[$file['error']]
                : "Unknown upload error.";

            throw new Exception($errorMessage);
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("File is too large. Maximum size is " . ($this->maxFileSize / 1048576) . "MB.");
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new Exception("Invalid file type. Allowed types: " . implode(', ', $this->allowedExtensions));
        }

        // Validate MIME type
        $this->validateMimeType($file);

        // Optional image validation only if file appears to be an image
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        if (in_array($extension, $imageExtensions)) {
            if (!@getimagesize($file['tmp_name'])) {
                throw new Exception("Uploaded file claims to be an image but is not valid.");
            }
        }
    }

    /**
     * Validate MIME type of the uploaded file
     * 
     * Checks the MIME type of the uploaded file against the allowed MIME types based on the file extension.
     * 
     * @param array $file The $_FILES array element containing file upload information
     * @return bool True if the MIME type is valid
     * @throws Exception If the MIME type is invalid
     */

    private function validateMimeType($file)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        // Build allowed MIME types dynamically from allowed extensions
        $allowedMimeTypes = [];
        foreach ($this->allowedExtensions as $ext) {
            if (isset($this->extensionMimeMap[$ext])) {
                $allowedMimeTypes[] = $this->extensionMimeMap[$ext];
            }
        }

        // Remove duplicates (e.g., jpg and jpeg both map to image/jpeg)
        $allowedMimeTypes = array_unique($allowedMimeTypes);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new Exception("Invalid file type. File appears to be " . $mimeType);
        }

        return true;
    }

    /**
     * Update the database with the new filename
     * 
     * Updates the specified database table and column with the new file name for the record identified by the given identifier value.
     * 
     * @param string $fileName The name of the uploaded file
     * @param string $identifierValue The value that identifies the record in the database
     * @throws Exception If the database update fails
     */

    private function updateDatabase($fileName, $identifierValue)
    {
        $sql = "UPDATE {$this->tableName} SET {$this->columnName} = :fileName WHERE {$this->identifierColumn} = :identifierValue";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':fileName', $fileName);
        $stmt->bindParam(':identifierValue', $identifierValue);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update database with file information.");
        }
    }
}
