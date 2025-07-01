<?php

class LocationService {
    private $csvFile = 'latest.csv';
    private $prefectures = [
        "北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県",
        "茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "東京都", "神奈川県",
        "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県", "岐阜県",
        "静岡県", "愛知県", "三重県", "滋賀県", "京都府", "大阪府", "兵庫県",
        "奈良県", "和歌山県", "鳥取県", "島根県", "岡山県", "広島県", "山口県",
        "徳島県", "香川県", "愛媛県", "高知県", "福岡県", "佐賀県", "長崎県",
        "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"
    ];
    
    public function getAllPrefectures() {
        return $this->prefectures;
    }
    
    public function getCitiesForPrefecture($prefecture) {
        if (!file_exists($this->csvFile)) {
            throw new Exception("CSV file not found");
        }
        
        $cities = [];
        $handle = fopen($this->csvFile, 'r');
        
        if ($handle === false) {
            throw new Exception("Cannot read CSV file");
        }
        
        // Skip header
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 14) continue;
            
            $csvPrefecture = trim($data[1], '"');
            $city = trim($data[5], '"');
            $lat = floatval($data[12]);
            $lon = floatval($data[13]);
            
            if ($csvPrefecture === $prefecture && !empty($city) && !is_nan($lat) && !is_nan($lon)) {
                $cities[] = $city;
            }
        }
        
        fclose($handle);
        
        // Remove duplicates and sort
        $cities = array_unique($cities);
        sort($cities);
        
        return array_values($cities);
    }
    
    public function getCityCoordinates($prefecture, $city) {
        if (!file_exists($this->csvFile)) {
            throw new Exception("CSV file not found");
        }
        
        $coordinates = [];
        $handle = fopen($this->csvFile, 'r');
        
        if ($handle === false) {
            throw new Exception("Cannot read CSV file");
        }
        
        // Skip header
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 14) continue;
            
            $csvPrefecture = trim($data[1], '"');
            $csvCity = trim($data[5], '"');
            $lat = floatval($data[12]);
            $lon = floatval($data[13]);
            
            if ($csvPrefecture === $prefecture && $csvCity === $city && !is_nan($lat) && !is_nan($lon)) {
                $coordinates[] = ['lat' => $lat, 'lng' => $lon];
            }
        }
        
        fclose($handle);
        
        if (empty($coordinates)) {
            throw new Exception("Coordinates not found for {$city}, {$prefecture}");
        }
        
        // Average coordinates if multiple entries
        $avgLat = array_sum(array_column($coordinates, 'lat')) / count($coordinates);
        $avgLng = array_sum(array_column($coordinates, 'lng')) / count($coordinates);
        
        return ['lat' => $avgLat, 'lng' => $avgLng];
    }
    
    public function validateLocation($prefecture, $city) {
        if (!in_array($prefecture, $this->prefectures)) {
            throw new Exception("Invalid prefecture: {$prefecture}");
        }
        
        $cities = $this->getCitiesForPrefecture($prefecture);
        if (!in_array($city, $cities)) {
            throw new Exception("Invalid city: {$city} in {$prefecture}");
        }
        
        return true;
    }
}