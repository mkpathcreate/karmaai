<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'LocationService.php';
require_once 'AstrologyService.php';
require_once 'TamilAstrologyService.php';
require_once 'DatabaseService.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $locationService = new LocationService();
    $astrologyService = new AstrologyService();
    $tamilAstrologyService = new TamilAstrologyService();
    $databaseService = new DatabaseService();

    // Validate required fields (names are optional)
    $requiredFields = [
        'groom_date', 'groom_time', 'groom_prefecture', 'groom_city',
        'bride_date', 'bride_time', 'bride_prefecture', 'bride_city'
    ];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field {$field} is required");
        }
    }

    $groomData = [
        'name' => $_POST['groom_name'] ?? '',
        'date' => $_POST['groom_date'],
        'time' => $_POST['groom_time'],
        'prefecture' => $_POST['groom_prefecture'],
        'city' => $_POST['groom_city']
    ];
    
    $brideData = [
        'name' => $_POST['bride_name'] ?? '',
        'date' => $_POST['bride_date'],
        'time' => $_POST['bride_time'],
        'prefecture' => $_POST['bride_prefecture'],
        'city' => $_POST['bride_city']
    ];
    
    // Calculate compatibility
    $results = $astrologyService->calculateCompatibility($groomData, $brideData, $locationService);
    
    // Calculate Tamil 10 Porutham
    $tamilResults = $tamilAstrologyService->calculate10Porutham(
        $results['groom']['nakshatra'], 
        $results['bride']['nakshatra'],
        $results['groom']['rashi'], 
        $results['bride']['rashi']
    );
    $results['tamil'] = $tamilResults;
    
    // Save to database
    $matchId = $databaseService->saveCompatibilityResult(['groom' => $groomData, 'bride' => $brideData], $results);
    $results['matchId'] = $matchId;

    echo json_encode([
        'success' => true,
        'results' => $results,
        'groom' => $groomData,
        'bride' => $brideData
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>