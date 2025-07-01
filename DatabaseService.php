<?php

class DatabaseService {
    private $dbFile = 'db.json';
    
    private function pseudonymizeName($name) {
        if (empty($name)) {
            return 'Anonymous';
        }
        // Create a consistent hash-based pseudonym
        $hash = hash('sha256', $name . 'horoscope_salt_2025');
        $prefix = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
        $prefixIndex = hexdec(substr($hash, 0, 1)) % count($prefix);
        $suffix = substr($hash, -4);
        return $prefix[$prefixIndex] . '_' . $suffix;
    }

    public function saveCompatibilityResult($inputData, $results) {
        $record = [
            'timestamp' => date('Y-m-d H:i:s'),
            'id' => uniqid('match_', true),
            'input' => [
                'groom' => [
                    'name_hash' => $this->pseudonymizeName($inputData['groom']['name'] ?? ''),
                    'date' => $inputData['groom']['date'],
                    'time' => $inputData['groom']['time'],
                    'prefecture' => $inputData['groom']['prefecture'],
                    'city' => $inputData['groom']['city']
                ],
                'bride' => [
                    'name_hash' => $this->pseudonymizeName($inputData['bride']['name'] ?? ''),
                    'date' => $inputData['bride']['date'],
                    'time' => $inputData['bride']['time'],
                    'prefecture' => $inputData['bride']['prefecture'],
                    'city' => $inputData['bride']['city']
                ]
            ],
            'results' => [
                'groom_astrology' => [
                    'nakshatra' => $results['groom']['nakshatra'],
                    'rashi' => $results['groom']['rashi'],
                    'moon_longitude' => $results['groom']['moonLon']
                ],
                'bride_astrology' => [
                    'nakshatra' => $results['bride']['nakshatra'],
                    'rashi' => $results['bride']['rashi'],
                    'moon_longitude' => $results['bride']['moonLon']
                ],
                'vedic_compatibility' => [
                    'score' => $results['compatibility']['score'],
                    'max_score' => $results['compatibility']['maxScore'],
                    'percentage' => $results['compatibility']['percentage'],
                    'level' => $results['compatibility']['level'],
                    'gunas' => $results['compatibility']['gunas']
                ],
                'tamil_compatibility' => isset($results['tamil']) ? [
                    'score' => $results['tamil']['totalScore'],
                    'max_score' => $results['tamil']['maxScore'],
                    'percentage' => $results['tamil']['percentage'],
                    'level' => $results['tamil']['level'],
                    'poruthams' => $results['tamil']['poruthams']
                ] : null,
                'doshas' => $results['doshas'] ?? []
            ]
        ];
        
        $this->appendToDatabase($record);
        return $record['id'];
    }
    
    private function appendToDatabase($record) {
        $database = $this->loadDatabase();
        $database[] = $record;
        
        // Keep only last 1000 records
        if (count($database) > 1000) {
            $database = array_slice($database, -1000);
        }
        
        file_put_contents($this->dbFile, json_encode($database, JSON_PRETTY_PRINT));
    }
    
    private function loadDatabase() {
        if (!file_exists($this->dbFile)) {
            return [];
        }
        
        $content = file_get_contents($this->dbFile);
        return json_decode($content, true) ?? [];
    }
    
    public function getRecentMatches($limit = 10) {
        $database = $this->loadDatabase();
        return array_slice(array_reverse($database), 0, $limit);
    }
    
    public function getMatchById($id) {
        $database = $this->loadDatabase();
        foreach ($database as $record) {
            if ($record['id'] === $id) {
                return $record;
            }
        }
        return null;
    }
    
    public function getStatistics() {
        $database = $this->loadDatabase();
        $total = count($database);
        
        if ($total === 0) {
            return [
                'total_matches' => 0,
                'average_vedic_score' => 0,
                'average_tamil_score' => 0,
                'excellent_matches' => 0,
                'popular_prefectures' => []
            ];
        }
        
        $vedicScores = [];
        $tamilScores = [];
        $excellentCount = 0;
        $prefectures = [];
        
        foreach ($database as $record) {
            $vedicPercentage = $record['results']['vedic_compatibility']['percentage'] ?? 0;
            $tamilPercentage = $record['results']['tamil_compatibility']['percentage'] ?? 0;
            
            $vedicScores[] = $vedicPercentage;
            if ($tamilPercentage > 0) {
                $tamilScores[] = $tamilPercentage;
            }
            
            if ($vedicPercentage >= 75 || $tamilPercentage >= 80) {
                $excellentCount++;
            }
            
            $groomPrefecture = $record['input']['groom']['prefecture'];
            $bridePrefecture = $record['input']['bride']['prefecture'];
            $prefectures[] = $groomPrefecture;
            $prefectures[] = $bridePrefecture;
        }
        
        $prefectureCounts = array_count_values($prefectures);
        arsort($prefectureCounts);
        
        return [
            'total_matches' => $total,
            'average_vedic_score' => count($vedicScores) ? round(array_sum($vedicScores) / count($vedicScores), 1) : 0,
            'average_tamil_score' => count($tamilScores) ? round(array_sum($tamilScores) / count($tamilScores), 1) : 0,
            'excellent_matches' => $excellentCount,
            'popular_prefectures' => array_slice($prefectureCounts, 0, 5, true)
        ];
    }
}