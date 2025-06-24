<?php

namespace App\Services;

use App\Libraries\AlephAPI;
use CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class CmdbService
{
    protected AlephAPI $api;

    public function __construct()
    {
        $this->api = new AlephAPI();
        helper('text');
    }

    /**
     * Prepares data for exporting CMDB records to an Excel file.
     *
     * @param int $categoryId The ID of the category to export.
     * @return array An array containing file details.
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportRecords(int $categoryId): array
    {
        $recordsResponse = $this->api->getCmdbByCategoria($categoryId);
        $records = $recordsResponse['cmdb'] ?? [];

        // Find category name more efficiently
        $categoryName = $this->findCategoryName($categoryId);

        // Create Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($categoryName);

        // Header row
        $sheet->setCellValue('A1', 'Nombre');
        $sheet->setCellValue('B1', 'Identificador');
        $sheet->setCellValue('C1', 'Activado');
        $sheet->setCellValue('D1', 'Fecha Creación');

        // Data rows
        $rowNum = 2;
        foreach ($records as $item) {
            $sheet->setCellValue('A' . $rowNum, $item['nombre'] ?? '');
            $sheet->setCellValue('B' . $rowNum, $item['identificador'] ?? '');
            $sheet->setCellValue('C' . $rowNum, ($item['activado'] ?? 0) == 1 ? 'Sí' : 'No');
            $sheet->setCellValue('D' . $rowNum, $item['fecha_creacion'] ?? '');
            $rowNum++;
        }

        // Generate file name and path
        $fileName = slugify($categoryName) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $relativePath = 'reports/' . $fileName;
        $fullPath = FCPATH . $relativePath;

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0775, true);
        }

        // Save Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        return [
            'file_name' => $fileName,
            'file_url' => base_url($relativePath),
        ];
    }

    /**
     * Processes an uploaded file to import CMDB records.
     *
     * @param File $file The uploaded file object.
     * @param int $categoryId The ID of the category to import into.
     * @return array An array with import results (successes and errors).
     */

    public function importRecords(File $file, int $categoryId): array
    {
        // --- 1. Read Spreadsheet Data ---
        try {
            // Automatically detect the file type (Xlsx, Csv, etc.) and create the appropriate reader.
            $reader = IOFactory::createReaderForFile($file->getTempName());
            // Set to read data only, which improves performance by ignoring cell formatting.
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getTempName());
            // Get all data from the active sheet as a zero-indexed array.
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (Throwable $e) {
            // If the file is corrupt or cannot be read, return a generic error.
            return ['errors' => ['Could not read the file: ' . $e->getMessage()], 'inserted' => 0];
        }

        $errors = [];
        $inserted = 0;
        $usedIdentifiers = []; // Tracks identifiers within this file to prevent duplicates.

        // --- 2. Process Headers ---
        // Extract the first row, which we assume contains the column headers.
        $headerRow = array_shift($sheetData);
        // Create a map from header names to column letters (e.g., ['Nombre' => 'A', 'Identificador' => 'B']).
        // This makes the import independent of the column order.
        $headerMap = array_flip(array_map('trim', $headerRow));

        // Validate that the required columns 'Nombre' and 'Identificador' exist in the header.
        if (!isset($headerMap['Nombre']) || !isset($headerMap['Identificador'])) {
            $errors[] = "The file must contain the columns 'Nombre' and 'Identificador'.";
            return ['errors' => $errors, 'inserted' => 0];
        }

        // Get the column letters for our required fields.
        $nameCol = $headerMap['Nombre'];
        $identifierCol = $headerMap['Identificador'];

        // --- 3. Iterate and Validate Data Rows ---
        foreach ($sheetData as $i => $row) {
            // Get the data from the correct columns using the header map.
            $name = trim((string)($row[$nameCol] ?? ''));
            $identifier = trim((string)($row[$identifierCol] ?? ''));

            // Skip rows that are completely empty to avoid unnecessary validation errors.
            if (empty($name) && empty($identifier)) {
                continue;
            }

            // --- Data Validation Logic ---
            if (empty($name) || empty($identifier)) {
                $errors[] = "Row " . ($i + 1) . ": 'nombre' or 'identificador' is empty.";
                continue; // Move to the next row.
            }
            if (in_array($identifier, $usedIdentifiers)) {
                $errors[] = "Row " . ($i + 1) . ": The identifier '$identifier' is duplicated in the file.";
                continue;
            }
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $identifier)) {
                $errors[] = "Row " . ($i + 1) . ": The identifier '$identifier' contains invalid characters.";
                continue;
            }

            // If validation passes, add the identifier to our list of used ones for this session.
            $usedIdentifiers[] = $identifier;

            // --- 4. API Call ---
            // Prepare the data and send it to the API for insertion or update.
            $response = $this->api->insertOrUpdateCmdb([
                'categoria_id'  => $categoryId,
                'nombre'        => $name,
                'identificador' => $identifier,
                'activado'      => 1, // Default to active.
            ]);

            // Process the API response.
            if (isset($response['insert_id']) || isset($response['mensaje'])) {
                $inserted++;
            } else {
                // If the API returned an error, capture it.
                $errorMsg = $response['error'] ?? 'Unknown error from API';
                $errors[] = "Row " . ($i + 1) . " (Identifier: $identifier): $errorMsg";
            }
        }

        // Return the final counts and any errors that occurred.
        return ['errors' => $errors, 'inserted' => $inserted];
    }


    /**
     * Finds the name of a category by its ID.
     *
     * @param int $categoryId
     * @return string
     */
    private function findCategoryName(int $categoryId): string
    {
        $categoriesResponse = $this->api->getCategorias();
        $categories = $categoriesResponse['categorias'] ?? [];

        // Use array_column for efficient searching
        $categoryNames = array_column($categories, 'nombre', 'id');

        return $categoryNames[$categoryId] ?? 'categoria_' . $categoryId;
    }
}
