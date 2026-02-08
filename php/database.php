<?php
/**
 * Database & Query Functions
 * Functions for data persistence and retrieval
 */

/**
 * Read data from JSON file
 */
function readData($filename) {
    $file = getDataDir() . '/' . basename($filename);
    
    if (!file_exists($file)) {
        return [];
    }
    
    $content = file_get_contents($file);
    $data = safeJsonDecode($content, true);
    
    return $data ?? [];
}

/**
 * Write data to JSON file
 */
function writeData($filename, $data) {
    $file = getDataDir() . '/' . basename($filename);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    if (file_put_contents($file, $json) === false) {
        throw new Exception("Failed to write to file: $filename");
    }
    
    return true;
}

/**
 * Get all records from a JSON file
 */
function getAllRecords($filename) {
    return readData($filename);
}

/**
 * Add a record to JSON file
 */
function addRecord($filename, $record) {
    $data = readData($filename);
    
    // Add ID and timestamp if not present
    if (!isset($record['id'])) {
        $record['id'] = generateId();
    }
    if (!isset($record['created_at'])) {
        $record['created_at'] = date('Y-m-d H:i:s');
    }
    
    $data[] = $record;
    writeData($filename, $data);
    
    return $record;
}

/**
 * Update a record by ID
 */
function updateRecord($filename, $id, $updates) {
    $data = readData($filename);
    
    foreach ($data as &$record) {
        if (($record['id'] ?? null) === $id) {
            $record = array_merge($record, $updates);
            $record['updated_at'] = date('Y-m-d H:i:s');
            writeData($filename, $data);
            return $record;
        }
    }
    
    return null;
}

/**
 * Delete a record by ID
 */
function deleteRecord($filename, $id) {
    $data = readData($filename);
    $original_count = count($data);
    
    $data = array_filter($data, function($record) use ($id) {
        return ($record['id'] ?? null) !== $id;
    });
    
    if (count($data) < $original_count) {
        writeData($filename, array_values($data));
        return true;
    }
    
    return false;
}

/**
 * Get a single record by ID
 */
function getRecordById($filename, $id) {
    $data = readData($filename);
    
    foreach ($data as $record) {
        if (($record['id'] ?? null) === $id) {
            return $record;
        }
    }
    
    return null;
}

/**
 * Filter records by criteria
 */
function filterRecords($filename, $criteria) {
    $data = readData($filename);
    $results = [];
    
    foreach ($data as $record) {
        $match = true;
        foreach ($criteria as $key => $value) {
            if (($record[$key] ?? null) !== $value) {
                $match = false;
                break;
            }
        }
        if ($match) {
            $results[] = $record;
        }
    }
    
    return $results;
}

/**
 * Get paginated records
 */
function getPaginatedRecords($filename, $page = 1, $per_page = 10) {
    $data = readData($filename);
    $total = count($data);
    $pages = ceil($total / $per_page);
    $start = ($page - 1) * $per_page;
    
    return [
        'data' => array_slice($data, $start, $per_page),
        'pagination' => [
            'page' => $page,
            'per_page' => $per_page,
            'total' => $total,
            'pages' => $pages
        ]
    ];
}

/**
 * Count records
 */
function countRecords($filename) {
    return count(readData($filename));
}

/**
 * Delete all records (with confirmation)
 */
function deleteAllRecords($filename, $confirm = false) {
    if (!$confirm) {
        return false;
    }
    
    return writeData($filename, []);
}

/**
 * Get records by date range
 */
function getRecordsByDateRange($filename, $start_date, $end_date, $date_field = 'created_at') {
    $data = readData($filename);
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    
    $results = [];
    foreach ($data as $record) {
        if (!isset($record[$date_field])) {
            continue;
        }
        
        $record_time = strtotime($record[$date_field]);
        if ($record_time >= $start && $record_time <= $end) {
            $results[] = $record;
        }
    }
    
    return $results;
}

/**
 * Sort records
 */
function sortRecords($records, $field, $direction = 'ASC') {
    usort($records, function($a, $b) use ($field, $direction) {
        $val_a = $a[$field] ?? '';
        $val_b = $b[$field] ?? '';
        
        if ($val_a === $val_b) {
            return 0;
        }
        
        $result = $val_a < $val_b ? -1 : 1;
        return $direction === 'DESC' ? -$result : $result;
    });
    
    return $records;
}

/**
 * Search records (simple text search)
 */
function searchRecords($filename, $query, $search_fields = []) {
    $data = readData($filename);
    $results = [];
    $query = strtolower($query);
    
    foreach ($data as $record) {
        foreach ($search_fields as $field) {
            if (isset($record[$field]) && stripos($record[$field], $query) !== false) {
                $results[] = $record;
                break;
            }
        }
    }
    
    return $results;
}

/**
 * Export data to CSV
 */
function exportToCSV($filename, $output_file = null) {
    $data = readData($filename);
    
    if (empty($data)) {
        return null;
    }
    
    $csv = '';
    $headers = array_keys($data[0]);
    $csv .= implode(',', array_map('escapeCsvField', $headers)) . "\n";
    
    foreach ($data as $record) {
        $row = [];
        foreach ($headers as $header) {
            $row[] = escapeCsvField($record[$header] ?? '');
        }
        $csv .= implode(',', $row) . "\n";
    }
    
    if ($output_file) {
        file_put_contents($output_file, $csv);
    }
    
    return $csv;
}

/**
 * Helper function to escape CSV fields
 */
function escapeCsvField($field) {
    if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
        return '"' . str_replace('"', '""', $field) . '"';
    }
    return $field;
}

/**
 * Get statistics for a file
 */
function getDataStatistics($filename) {
    $data = readData($filename);
    
    if (empty($data)) {
        return [
            'total_records' => 0,
            'first_record' => null,
            'last_record' => null
        ];
    }
    
    return [
        'total_records' => count($data),
        'first_record' => reset($data),
        'last_record' => end($data),
        'sample_fields' => array_keys($data[0])
    ];
}
?>
