<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to the storage disk.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldFilePath
     * @return string
     */
    public static function uploadFile(UploadedFile $file, string $directory = 'uploads', ?string $oldFilePath = null): string
    {
        // Delete old file if exists
        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        // Store the file
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    /**
     * Delete a file from storage.
     *
     * @param string $filePath
     * @return bool
     */
    public static function deleteFile(string $filePath): bool
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        
        return false;
    }

    /**
     * Get the full URL for a file.
     *
     * @param string $filePath
     * @return string|null
     */
    public static function getFileUrl(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        return Storage::disk('public')->url($filePath);
    }
} 