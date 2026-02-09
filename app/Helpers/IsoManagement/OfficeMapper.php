<?php

namespace App\Helpers\IsoManagement;

use Illuminate\Validation\ValidationException;

class OfficeMapper{

    // Map office code to full office name
    public static function map(string $code) : string{
        if(empty($code)){
            throw new \Exception("Originating section code is required but was empty or missing.");
        }
        $code = strtoupper(trim($code));
        $mappings = config('offices.mappings');

        // Check if code exists in mappings
        if(array_key_exists($code, $mappings)){
            return $mappings[$code];
        }

        // If not found, throw exception
    throw ValidationException::withMessages([
        'code' => ["Invalid Originating Code: '{$code}'. Please use valid office codes (e.g., AAC-BED, OOP-ITC)"]
    ]);
    }

    // Check if office code is valid
    public static function isValid(string $code) : bool {
        $code = strtoupper(trim($code));
        return array_key_exists($code, config('offices.mappings'));
    }

    // Get all of the valid office codes
    public static function getAllCodes() : array{
        return array_keys(config('offices.mappings'));
    }

    // ==========================
    // Source Type
    // ==========================
    public static function normalizeSourceType(string $type): string{
        if(empty($type)){
            throw new \Exception("Source type is required but was empty or missing.");
        }

        // Convert to lowercase for storage
        $normalized = strtolower(trim($type));

        // Get valid source Types from config
        $validTypes = config('offices.source_types');

        if(in_array($normalized, $validTypes)){
            return $normalized;
        }

        // If not found
        throw ValidationException::withMessages([
            'source_type' => ["Invalid source type: '{$type}'. Please use: eoms, procedures, forms, records, policies or others"]
        ]);
    }

    public static function isValidSourceType(string $type): bool{
        $normalized = strtolower(trim($type));
        return in_array($normalized, config('offices.source_types'));
    }

    public static function getAllSourceTypes(): array{
        return config('offices.source_types');
    }
}