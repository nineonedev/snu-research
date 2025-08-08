<?php 

namespace app\facades;

use app\core\Disk;
use app\core\File;

class Storage {
    public static Disk $disk;
    
    public static function ensureDisk(): void
    {
        if (!isset(self::$disk)) {
            throw new \RuntimeException('Disk not set. Use Storage::setDisk() first.');
        }
    } 
    
    public static function setDisk(Disk $disk)
    {
        self::$disk = $disk;
    }

    public static function save($key): ?array
    {
        self::ensureDisk();

        if (!File::has($key)) return null; 
        
        $file = File::make($key);
    
        $success = self::$disk->put($file);
    
        if (!$success) return null; 

        return self::$disk->lastPutData();
    }

    public static function saveAndReturnUrl($key): ?string
    {
        self::ensureDisk();

        if (!File::has($key)) return null; 
        
        $file = File::make($key);
    
        $success = self::$disk->put($file);
    
        if (!$success) return null; 

        return self::$disk->url($file);
    }

    public static function url(File $file): string
    {
        self::ensureDisk();
        return self::$disk->url($file);
    }

    public static function delete(string $filename): string 
    {
        self::ensureDisk();
        return self::$disk->delete($filename);
    }

    public static function exists(string $filename): bool 
    {
        self::ensureDisk(); 
        return self::$disk->exists($filename);
    }
}