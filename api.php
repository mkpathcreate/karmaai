<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'LocationService.php';
require_once 'AstrologyService.php';
require_once 'MarriageService.php';


// Helper function to get expected results for known test cases
function getExpectedResults($date, $time, $city) {
    $testCases = [
        '2008-12-25_07:30_Chennai' => [
            'nakshatra' => 'Anuradha',
            'rashi' => 'Vrishchika',
            'moonLon' => 225.0, // Approximate
            'source' => 'Other astrology website'
        ],
        '1996-02-01_14:14_Oita' => [
            'nakshatra' => 'Ardra',
            'rashi' => 'Mithuna',
            'moonLon' => 76.0, // Approximate
            'source' => 'Other astrology website'
        ]
    ];
    
    $key = $date . '_' . $time . '_' . $city;
    return $testCases[$key] ?? null;
}

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $locationService = new LocationService();
    $astrologyService = new AstrologyService();
    $marriageService = new MarriageService();
    
    switch ($action) {
        case 'cities':
            $prefecture = $_GET['prefecture'] ?? '';
            
            if (empty($prefecture)) {
                throw new Exception('Prefecture parameter is required');
            }
            
            $cities = $locationService->getCitiesForPrefecture($prefecture);
            
            echo json_encode([
                'success' => true,
                'prefecture' => $prefecture,
                'cities' => $cities,
                'count' => count($cities)
            ]);
            break;
            
        case 'coordinates':
            $prefecture = $_GET['prefecture'] ?? '';
            $city = $_GET['city'] ?? '';
            
            if (empty($city)) {
                throw new Exception('City parameter is required');
            }
            
            // Handle both prefecture+city and city-only requests
            if (empty($prefecture)) {
                throw new Exception("Prefecture parameter is required for city coordinate lookup");
            }
            
            $coordinates = $locationService->getCityCoordinates($prefecture, $city);
            
            echo json_encode([
                'success' => true,
                'prefecture' => $prefecture,
                'city' => $city,
                'coordinates' => $coordinates,
                'source' => isset($coordinates['source']) ? $coordinates['source'] : 'LocationService'
            ]);
            break;
            
        case 'prefectures':
            $prefectures = $locationService->getAllPrefectures();
            
            echo json_encode([
                'success' => true,
                'prefectures' => $prefectures,
                'count' => count($prefectures)
            ]);
            break;
            
        case 'calculate':
            $date = $_POST['date'] ?? $_GET['date'] ?? '';
            $time = $_POST['time'] ?? $_GET['time'] ?? '';
            $prefecture = $_POST['prefecture'] ?? $_GET['prefecture'] ?? '';
            $city = $_POST['city'] ?? $_GET['city'] ?? '';
            $debug = $_POST['debug'] ?? $_GET['debug'] ?? false;
            
            if (empty($date) || empty($time) || empty($city)) {
                throw new Exception('Date, time, and city are required for calculation');
            }
            
            // Get coordinates from LocationService
            if (empty($prefecture)) {
                throw new Exception('Prefecture parameter is required for coordinate lookup');
            }
            
            $locationService->validateLocation($prefecture, $city);
            $coordinates = $locationService->getCityCoordinates($prefecture, $city);
            $coordinateSource = 'LocationService';
            
            // Calculate astrology
            $astroData = $astrologyService->calculateRashiNakshatra($date, $time, $coordinates);
            
            $response = [
                'success' => true,
                'nakshatra' => $astroData['nakshatra'],
                'rashi' => $astroData['rashi'],
                'moonLon' => round($astroData['moonLon'], 4),
                'coordinates' => $coordinates,
                'coordinateSource' => $coordinateSource,
                'city' => $city,
                'prefecture' => $prefecture
            ];
            
            // Add debug information if requested
            if ($debug && isset($astroData['debug'])) {
                $response['debug'] = $astroData['debug'];
                $response['expectedForTestCase'] = getExpectedResults($date, $time, $city);
            }
            
            echo json_encode($response);
            break;
            
        case 'test_coordinates':
            // Test endpoint to check coordinate lookup
            $city = $_GET['city'] ?? '';
            $prefecture = $_GET['prefecture'] ?? '';
            
            if (empty($city) || empty($prefecture)) {
                throw new Exception('City and prefecture parameters are required');
            }
            
            $results = [];
            
            // Try LocationService
            try {
                $coords = $locationService->getCityCoordinates($prefecture, $city);
                $results['locationService'] = $coords;
            } catch (Exception $e) {
                $results['locationService'] = ['error' => $e->getMessage()];
            }
            
            echo json_encode([
                'success' => true,
                'city' => $city,
                'prefecture' => $prefecture,
                'results' => $results
            ]);
            break;
            
        case 'predict_marriage':
            $name = $_POST['user_name'] ?? $_GET['user_name'] ?? '';
            $date = $_POST['user_date'] ?? $_GET['user_date'] ?? '';
            $time = $_POST['user_time'] ?? $_GET['user_time'] ?? '';
            $gender = $_POST['user_gender'] ?? $_GET['user_gender'] ?? '';
            $prefecture = $_POST['user_prefecture'] ?? $_GET['user_prefecture'] ?? '';
            $city = $_POST['user_city'] ?? $_GET['user_city'] ?? '';
            
            if (empty($date) || empty($time) || empty($gender) || empty($city) || empty($prefecture)) {
                throw new Exception('Date, time, gender, prefecture, and city are required for marriage prediction');
            }
            
            $userData = [
                'name' => $name,
                'date' => $date,
                'time' => $time,
                'gender' => $gender,
                'prefecture' => $prefecture,
                'city' => $city
            ];
            
            $result = $marriageService->predictMarriage($userData, $locationService, $astrologyService);
            
            if ($result['success']) {
                echo json_encode($result);
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        default:
            throw new Exception('Invalid action specified. Available actions: cities, coordinates, prefectures, calculate, test_coordinates, predict_marriage');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
    ]);
}
?>