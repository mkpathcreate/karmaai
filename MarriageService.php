<?php

class MarriageService {
    
    private $nakshatras = [
        'Ashwini', 'Bharani', 'Krittika', 'Rohini', 'Mrigashira', 'Ardra', 
        'Punarvasu', 'Pushya', 'Ashlesha', 'Magha', 'Purva Phalguni', 'Uttara Phalguni',
        'Hasta', 'Chitra', 'Swati', 'Vishakha', 'Anuradha', 'Jyeshtha',
        'Mula', 'Purva Ashadha', 'Uttara Ashadha', 'Shravana', 'Dhanishta', 'Shatabhisha',
        'Purva Bhadrapada', 'Uttara Bhadrapada', 'Revati'
    ];
    
    private $rashis = [
        'Mesha', 'Vrishabha', 'Mithuna', 'Karka', 'Simha', 'Kanya',
        'Tula', 'Vrishchika', 'Dhanu', 'Makara', 'Kumbha', 'Meena'
    ];
    
    private $marriageTimingRules = [
        // Early marriage indicators (18-25 years)
        'early' => [
            'venus_strong_7th' => ['Rohini', 'Bharani', 'Purva Phalguni', 'Purva Ashadha'],
            'benefic_7th_lord' => ['Pushya', 'Swati', 'Hasta', 'Revati'],
            'jupiter_aspect_7th' => ['Punarvasu', 'Vishakha', 'Purva Bhadrapada']
        ],
        // Moderate marriage timing (25-30 years)
        'moderate' => [
            'neutral_influences' => ['Krittika', 'Mrigashira', 'Chitra', 'Uttara Phalguni', 'Uttara Ashadha', 'Shravana'],
            'mixed_aspects' => ['Ashwini', 'Ardra', 'Magha', 'Dhanishta']
        ],
        // Late marriage indicators (30+ years)
        'late' => [
            'saturn_influence' => ['Ashlesha', 'Jyeshtha', 'Mula', 'Shatabhisha', 'Uttara Bhadrapada'],
            'malefic_7th_house' => ['Anuradha']
        ]
    ];
    
    private $compatibilityMatrix = [
        // Best compatible nakshatras for each nakshatra
        'Ashwini' => ['Bharani', 'Pushya', 'Ashlesha'],
        'Bharani' => ['Ashwini', 'Rohini', 'Magha'],
        'Krittika' => ['Rohini', 'Hasta', 'Swati'],
        'Rohini' => ['Krittika', 'Mrigashira', 'Chitra'],
        'Mrigashira' => ['Rohini', 'Ardra', 'Vishakha'],
        'Ardra' => ['Mrigashira', 'Punarvasu', 'Anuradha'],
        'Punarvasu' => ['Ardra', 'Pushya', 'Jyeshtha'],
        'Pushya' => ['Punarvasu', 'Ashlesha', 'Mula'],
        'Ashlesha' => ['Pushya', 'Magha', 'Purva Ashadha'],
        'Magha' => ['Ashlesha', 'Purva Phalguni', 'Uttara Ashadha'],
        'Purva Phalguni' => ['Magha', 'Uttara Phalguni', 'Shravana'],
        'Uttara Phalguni' => ['Purva Phalguni', 'Hasta', 'Dhanishta'],
        'Hasta' => ['Uttara Phalguni', 'Chitra', 'Shatabhisha'],
        'Chitra' => ['Hasta', 'Swati', 'Purva Bhadrapada'],
        'Swati' => ['Chitra', 'Vishakha', 'Uttara Bhadrapada'],
        'Vishakha' => ['Swati', 'Anuradha', 'Revati'],
        'Anuradha' => ['Vishakha', 'Jyeshtha', 'Ashwini'],
        'Jyeshtha' => ['Anuradha', 'Mula', 'Bharani'],
        'Mula' => ['Jyeshtha', 'Purva Ashadha', 'Krittika'],
        'Purva Ashadha' => ['Mula', 'Uttara Ashadha', 'Rohini'],
        'Uttara Ashadha' => ['Purva Ashadha', 'Shravana', 'Mrigashira'],
        'Shravana' => ['Uttara Ashadha', 'Dhanishta', 'Ardra'],
        'Dhanishta' => ['Shravana', 'Shatabhisha', 'Punarvasu'],
        'Shatabhisha' => ['Dhanishta', 'Purva Bhadrapada', 'Pushya'],
        'Purva Bhadrapada' => ['Shatabhisha', 'Uttara Bhadrapada', 'Ashlesha'],
        'Uttara Bhadrapada' => ['Purva Bhadrapada', 'Revati', 'Magha'],
        'Revati' => ['Uttara Bhadrapada', 'Ashwini', 'Purva Phalguni']
    ];
    
    private $rashiCompatibility = [
        'Mesha' => ['Simha', 'Dhanu', 'Mithuna'],
        'Vrishabha' => ['Kanya', 'Makara', 'Karka'],
        'Mithuna' => ['Tula', 'Kumbha', 'Mesha'],
        'Karka' => ['Vrishchika', 'Meena', 'Vrishabha'],
        'Simha' => ['Mesha', 'Dhanu', 'Mithuna'],
        'Kanya' => ['Vrishabha', 'Makara', 'Karka'],
        'Tula' => ['Mithuna', 'Kumbha', 'Simha'],
        'Vrishchika' => ['Karka', 'Meena', 'Kanya'],
        'Dhanu' => ['Mesha', 'Simha', 'Tula'],
        'Makara' => ['Vrishabha', 'Kanya', 'Vrishchika'],
        'Kumbha' => ['Mithuna', 'Tula', 'Dhanu'],
        'Meena' => ['Karka', 'Vrishchika', 'Makara']
    ];
    
    public function predictMarriage($userData, $locationService, $astrologyService) {
        try {
            // Get coordinates
            $coordinates = $locationService->getCityCoordinates($userData['prefecture'], $userData['city']);
            
            // Calculate astrology data
            $astroData = $astrologyService->calculateRashiNakshatra(
                $userData['date'], 
                $userData['time'], 
                $coordinates
            );
            
            $nakshatra = $astroData['nakshatra'];
            $rashi = $astroData['rashi'];
            $moonLon = $astroData['moonLon'];
            $gender = $userData['gender'] ?? 'male'; // Default to male if not provided
            $isOtherGender = ($gender === 'other');
            $effectiveGender = ($gender === 'other') ? 'male' : $gender;
            $birthYear = date('Y', strtotime($userData['date']));
            $currentYear = date('Y');
            $currentAge = $currentYear - $birthYear;
            
            // Calculate Mars position for Kuja Dosha analysis
            $marsData = $this->calculateMarsPosition($userData['date'], $userData['time'], $coordinates);
            $kujaDosha = $this->checkKujaDosha($marsData, $moonLon);
            
            // Calculate 7th house analysis
            $seventhHouseData = $this->analyze7thHouse($userData['date'], $userData['time'], $coordinates, $moonLon, $marsData);
            
            // Enhanced Venus/Jupiter karaka analysis
            $karakaAnalysis = $this->analyzeMarriageKarakas($userData['date'], $userData['time'], $coordinates, $effectiveGender);
            
            // Marriage possibility prediction (gender-specific)
            $marriageProbability = $this->calculateMarriageProbability($nakshatra, $rashi, $currentAge, $gender, $kujaDosha);
            
            // Marriage timing prediction (gender-specific)
            $marriageTiming = $this->predictMarriageTiming($nakshatra, $rashi, $currentAge, $gender);
            
            // Partner compatibility suggestions
            $partnerSuggestions = $this->getPartnerSuggestions($nakshatra, $rashi);
            
            // Delay predictions (gender-specific)
            $delayPrediction = $this->predictMarriageDelays($nakshatra, $rashi, $currentAge, $gender);
            
            return [
                'success' => true,
                'user_data' => [
                    'name' => $userData['name'],
                    'nakshatra' => $nakshatra,
                    'rashi' => $rashi,
                    'current_age' => $currentAge,
                    'moonLon' => $astroData['moonLon']
                ],
                'marriage_prediction' => [
                    'will_marry' => $marriageProbability['will_marry'],
                    'probability_percentage' => $marriageProbability['percentage'],
                    'reasoning' => $marriageProbability['reasoning']
                ],
                'timing_prediction' => $marriageTiming,
                'partner_suggestions' => $partnerSuggestions,
                'delay_prediction' => $delayPrediction,
                'kuja_dosha' => $kujaDosha,
                'seventh_house' => $seventhHouseData,
                'karaka_analysis' => $karakaAnalysis,
                'gender_note' => $isOtherGender ? 'その他の性別を選択されたため、結果は近似値となります。伝統的なヴェーダ占星術では男性・女性の二元論に基づいています。' : null,
                'coordinates' => $coordinates
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function calculateMarriageProbability($nakshatra, $rashi, $currentAge, $gender, $kujaDosha = null) {
        $probability = 85; // Base probability
        $reasoning = [];
        
        // For "other" gender, use male logic as default
        $effectiveGender = ($gender === 'other') ? 'male' : $gender;
        
        // Gender-specific age factors
        if ($effectiveGender === 'female') {
            if ($currentAge < 18) {
                $probability -= 15;
                $reasoning[] = "年齢が若いため確率が下がります";
            } elseif ($currentAge > 32) {
                $probability -= 20;
                $reasoning[] = "年齢的に結婚が遅くなる傾向があります";
            } elseif ($currentAge >= 22 && $currentAge <= 28) {
                $probability += 10;
                $reasoning[] = "女性として結婚に最適な年齢です";
            }
        } else { // male
            if ($currentAge < 21) {
                $probability -= 10;
                $reasoning[] = "年齢が若いため確率がやや下がります";
            } elseif ($currentAge > 35) {
                $probability -= 15;
                $reasoning[] = "年齢的に結婚が遅くなる傾向があります";
            } elseif ($currentAge >= 25 && $currentAge <= 32) {
                $probability += 8;
                $reasoning[] = "男性として結婚に適した年齢です";
            }
        }
        
        // Gender-specific nakshatra adjustments
        if ($effectiveGender === 'female') {
            // Jupiter-influenced nakshatras are more favorable for women
            $favorableNakshatras = ['Pushya', 'Vishakha', 'Punarvasu', 'Rohini', 'Hasta', 'Revati'];
            $challengingNakshatras = ['Ashlesha', 'Jyeshtha', 'Mula', 'Shatabhisha', 'Ardra'];
        } else {
            // Venus-influenced nakshatras are more favorable for men
            $favorableNakshatras = ['Bharani', 'Rohini', 'Purva Phalguni', 'Purva Ashadha', 'Swati', 'Revati'];
            $challengingNakshatras = ['Ashlesha', 'Jyeshtha', 'Mula', 'Shatabhisha'];
        }
        
        if (in_array($nakshatra, $favorableNakshatras)) {
            $probability += ($effectiveGender === 'female') ? 12 : 10;
            $reasoning[] = "あなたのナクシャトラ（{$nakshatra}）は{$this->getGenderText($gender)}として結婚に非常に有利です";
        } elseif (in_array($nakshatra, $challengingNakshatras)) {
            $probability -= ($effectiveGender === 'female') ? 8 : 5;
            $reasoning[] = "あなたのナクシャトラ（{$nakshatra}）は結婚に慎重さが必要です";
        }
        
        // Rashi-based adjustments
        $relationshipFriendlyRashis = ['Vrishabha', 'Karka', 'Tula', 'Meena'];
        if (in_array($rashi, $relationshipFriendlyRashis)) {
            $probability += 5;
            $reasoning[] = "あなたのラーシ（{$rashi}）は人間関係に恵まれています";
        }
        
        // Kuja Dosha considerations
        if ($kujaDosha && $kujaDosha['present']) {
            switch ($kujaDosha['severity']) {
                case 'high':
                    $probability -= 15;
                    $reasoning[] = "マンガリク・ドーシャにより慎重な結婚選択が必要です";
                    break;
                case 'moderate':
                    $probability -= 8;
                    $reasoning[] = "軽度のマンガリク・ドーシャがあります";
                    break;
                case 'mild':
                    $probability -= 3;
                    $reasoning[] = "火星の軽微な影響があります";
                    break;
            }
        } else {
            $reasoning[] = "マンガリク・ドーシャは検出されません";
        }
        
        $probability = max(60, min(95, $probability)); // Keep between 60-95%
        
        return [
            'will_marry' => $probability > 70 ? 'はい' : '可能性があります',
            'percentage' => $probability,
            'reasoning' => $reasoning
        ];
    }
    
    private function predictMarriageTiming($nakshatra, $rashi, $currentAge, $gender) {
        // For "other" gender, use male logic as default
        $effectiveGender = ($gender === 'other') ? 'male' : $gender;
        
        // Gender-specific default ages
        $baseAge = ($effectiveGender === 'female') ? 26 : 28;
        $timing = [];
        
        // Gender-specific early marriage indicators
        if ($effectiveGender === 'female') {
            // For women: Jupiter aspects and Venus-Moon combinations
            if (in_array($nakshatra, ['Pushya', 'Vishakha', 'Punarvasu', 'Rohini', 'Hasta', 'Revati'])) {
                $baseAge = 23;
                $timing['category'] = 'early';
                $timing['description'] = '女性として早期結婚の可能性が高い';
            }
        } else {
            // For men: Venus-strong positions
            if (in_array($nakshatra, $this->marriageTimingRules['early']['venus_strong_7th']) ||
                in_array($nakshatra, $this->marriageTimingRules['early']['benefic_7th_lord']) ||
                in_array($nakshatra, $this->marriageTimingRules['early']['jupiter_aspect_7th'])) {
                $baseAge = 25;
                $timing['category'] = 'early';
                $timing['description'] = '男性として早期結婚の可能性が高い';
            }
        }
        
        // Gender-specific late marriage indicators
        if (!isset($timing['category'])) {
            if (in_array($nakshatra, $this->marriageTimingRules['late']['saturn_influence']) ||
                in_array($nakshatra, $this->marriageTimingRules['late']['malefic_7th_house'])) {
                $baseAge = ($effectiveGender === 'female') ? 30 : 32;
                $timing['category'] = 'late';
                $timing['description'] = '慎重な結婚時期';
            }
            // Moderate timing
            else {
                $baseAge = ($effectiveGender === 'female') ? 26 : 28;
                $timing['category'] = 'moderate';
                $timing['description'] = '適度な結婚時期';
            }
        }
        
        if ($currentAge < $baseAge) {
            $timing['predicted_year'] = date('Y') + ($baseAge - $currentAge);
            $timing['age_range'] = ($baseAge - 1) . "-" . ($baseAge + 2) . "歳";
            $timing['message'] = "あなたの結婚は" . $timing['predicted_year'] . "年頃（" . $timing['age_range'] . "）に起こる可能性が高いです";
        } else {
            $timing['predicted_year'] = date('Y') + 2;
            $timing['age_range'] = ($currentAge + 1) . "-" . ($currentAge + 3) . "歳";
            $timing['message'] = "あなたの結婚は近い将来（" . $timing['predicted_year'] . "年頃）に起こる可能性があります";
        }
        
        return $timing;
    }
    
    private function getPartnerSuggestions($nakshatra, $rashi) {
        $suggestions = [];
        
        // Get compatible nakshatras
        $compatibleNakshatras = $this->compatibilityMatrix[$nakshatra] ?? [];
        $compatibleRashis = $this->rashiCompatibility[$rashi] ?? [];
        
        $suggestions['best_nakshatra_matches'] = array_slice($compatibleNakshatras, 0, 3);
        $suggestions['best_rashi_matches'] = $compatibleRashis;
        
        // Provide detailed recommendations
        $suggestions['recommendations'] = [
            'primary' => "最も相性の良いナクシャトラ: " . implode(", ", $suggestions['best_nakshatra_matches']),
            'secondary' => "相性の良いラーシ: " . implode(", ", $suggestions['best_rashi_matches']),
            'advice' => $this->getPersonalizedAdvice($nakshatra, $rashi)
        ];
        
        return $suggestions;
    }
    
    private function getPersonalizedAdvice($nakshatra, $rashi) {
        $advice = [];
        
        // Nakshatra-specific advice
        $nakshatraAdvice = [
            'Ashwini' => 'エネルギッシュで冒険好きなパートナーを探してください',
            'Bharani' => '情熱的で創造的なパートナーとの相性が良いでしょう',
            'Krittika' => '誠実で責任感のある人との関係を築いてください',
            'Rohini' => '美しさと芸術を愛する人との絆が深まります',
            'Mrigashira' => '知的で好奇心旺盛なパートナーを見つけてください',
            'Ardra' => '感情的に安定したパートナーがバランスを保ちます',
            'Punarvasu' => '楽観的で精神的に成熟した人を探してください',
            'Pushya' => '家族を大切にする温和なパートナーが理想的です',
            'Ashlesha' => '深い理解力を持つ賢明なパートナーを求めてください',
            'Magha' => '威厳と伝統を重んじる人との結婚が幸せをもたらします',
            'Purva Phalguni' => '社交的で楽しい人生を共有できるパートナーを見つけてください',
            'Uttara Phalguni' => '親切で寛大な心を持つ人との関係を築いてください',
            'Hasta' => '器用で実用的なパートナーとの相性が抜群です',
            'Chitra' => '美的センスと創造性を持つ人を探してください',
            'Swati' => '独立心があり、自由を尊重してくれるパートナーが理想的です',
            'Vishakha' => '目標志向で野心的なパートナーとの結婚が成功します',
            'Anuradha' => '忠実で献身的な愛を示してくれる人を見つけてください',
            'Jyeshtha' => '強いリーダーシップを持つパートナーとの関係が良いでしょう',
            'Mula' => '哲学的で精神的な深さを持つ人を探してください',
            'Purva Ashadha' => '楽観的で冒険心のあるパートナーが最適です',
            'Uttara Ashadha' => '勝利と成功を共に目指せるパートナーを見つけてください',
            'Shravana' => '聞き上手で理解力のある人との関係を築いてください',
            'Dhanishta' => '音楽や芸術を愛する文化的なパートナーが理想的です',
            'Shatabhisha' => '独創的で未来志向のパートナーを探してください',
            'Purva Bhadrapada' => '精神性の高い哲学的なパートナーとの結婚が幸せをもたらします',
            'Uttara Bhadrapada' => '慈悲深く平和を愛するパートナーを見つけてください',
            'Revati' => '優しく思いやりのあるパートナーとの関係が理想的です'
        ];
        
        $advice[] = $nakshatraAdvice[$nakshatra] ?? '相性の良いパートナーを見つけるために時間をかけてください';
        
        return implode('. ', $advice);
    }
    
    private function predictMarriageDelays($nakshatra, $rashi, $currentAge, $gender) {
        $delay = [];
        
        // For "other" gender, use male logic as default
        $effectiveGender = ($gender === 'other') ? 'male' : $gender;
        
        // Gender-specific delay thresholds
        $criticalAge = ($effectiveGender === 'female') ? 30 : 32;
        
        // Adjust critical age based on nakshatra
        if (in_array($nakshatra, $this->marriageTimingRules['late']['saturn_influence'])) {
            $criticalAge = ($effectiveGender === 'female') ? 32 : 35;
        }
        
        if ($currentAge < $criticalAge) {
            $delay['warning_age'] = $criticalAge;
            $delay['delay_years'] = 3;
            $delay['message'] = "もし{$criticalAge}歳までに結婚されない場合、結婚は{$delay['delay_years']}年程度遅れる可能性があります";
        } else {
            $delay['warning_age'] = $currentAge + 2;
            $delay['delay_years'] = 2;
            $delay['message'] = "現在の状況を考慮すると、結婚は{$delay['delay_years']}年以内に実現する可能性が高いです";
        }
        
        // Add remedial suggestions
        $delay['remedies'] = [
            '金曜日に白い花をお供えしてヴィーナス（金星）の恩恵を受けてください',
            '毎週木曜日に黄色い服を着てジュピター（木星）の加護を求めてください',
            '適切な時期に家族や友人の紹介を受け入れてください'
        ];
        
        return $delay;
    }
    
    private function getGenderText($gender) {
        switch ($gender) {
            case 'female':
                return '女性';
            case 'other':
                return 'その他';
            default:
                return '男性';
        }
    }
    
    private function calculateMarsPosition($date, $time, $coordinates) {
        // Simplified Mars calculation based on existing astronomical methods
        // This uses approximate orbital calculations for Mars position
        
        $jd = $this->getJulianDay($date, $time, $coordinates);
        
        // Mars orbital elements (simplified)
        $marsOrbitalPeriod = 686.98; // days
        $marsEccentricity = 0.0934;
        $marsLongitudeAtEpoch = 355.45; // degrees at J2000.0
        $marsMeanMotion = 360.0 / $marsOrbitalPeriod; // degrees per day
        
        // Calculate days since J2000.0
        $daysSinceJ2000 = $jd - 2451545.0;
        
        // Calculate mean longitude
        $meanLongitude = $marsLongitudeAtEpoch + ($marsMeanMotion * $daysSinceJ2000);
        $meanLongitude = fmod($meanLongitude, 360);
        if ($meanLongitude < 0) $meanLongitude += 360;
        
        // Simple correction for eccentricity (basic elliptical orbit)
        $eccentricityCorrection = 2 * $marsEccentricity * sin(deg2rad($meanLongitude));
        $trueLongitude = $meanLongitude + rad2deg($eccentricityCorrection);
        
        // Normalize to 0-360 range
        $trueLongitude = fmod($trueLongitude, 360);
        if ($trueLongitude < 0) $trueLongitude += 360;
        
        // Apply Lahiri Ayanamsa for sidereal position
        $ayanamsa = $this->getLahiriAyanamsa($jd);
        $sideLongitude = $trueLongitude - $ayanamsa;
        if ($sideLongitude < 0) $sideLongitude += 360;
        
        return [
            'longitude' => $sideLongitude,
            'rashi' => $this->getRashiFromLongitude($sideLongitude),
            'rashi_number' => floor($sideLongitude / 30) + 1
        ];
    }
    
    private function checkKujaDosha($marsData, $moonLon) {
        // Simplified Kuja Dosha detection based on Mars rashi position
        // In proper Vedic astrology, this would use house positions from ascendant
        // For now, using rashi-based approximation
        
        $marsRashi = $marsData['rashi_number'];
        $moonRashi = floor($moonLon / 30) + 1;
        
        // Traditional Manglik positions (houses 1, 2, 4, 7, 8, 12)
        // Using rashi positions as approximation
        $manglikRashis = [1, 2, 4, 7, 8, 12]; // Aries, Taurus, Cancer, Libra, Scorpio, Pisces
        
        $isKujaDoshic = false;
        $severity = 'none';
        $description = '';
        $remedies = [];
        
        // Check if Mars is in traditionally challenging rashis for marriage
        $challengingRashis = [1, 8, 12]; // Aries (own), Scorpio (own), Pisces (debilitated area)
        
        if (in_array($marsRashi, $challengingRashis)) {
            $isKujaDoshic = true;
            
            if ($marsRashi == 1 || $marsRashi == 8) { // Aries or Scorpio
                $severity = 'high';
                $description = 'マルス（火星）が強い位置にあり、結婚に影響を与える可能性があります';
            } else {
                $severity = 'moderate';
                $description = 'マルス（火星）の位置により、結婚に軽微な影響があります';
            }
            
            $remedies = [
                '毎週火曜日にハヌマーン寺院を参拝してください',
                '赤珊瑚（コーラル）の着用を検討してください',
                '同じくマンガリクの方とのマッチングが理想的です',
                'マルス・シャンティ（火星の平和儀式）を行ってください'
            ];
        }
        
        // Check Mars-Moon relationship for additional insights
        $rashiDistance = abs($marsRashi - $moonRashi);
        if ($rashiDistance == 0 || $rashiDistance == 6) { // Same rashi or opposition
            if (!$isKujaDoshic) {
                $isKujaDoshic = true;
                $severity = 'mild';
                $description = 'マルス（火星）とチャンドラ（月）の配置により、軽微な注意が必要です';
                $remedies = [
                    '月曜日に白い花をお供えしてください',
                    '火曜日には辛い食べ物を避けてください'
                ];
            }
        }
        
        return [
            'present' => $isKujaDoshic,
            'severity' => $severity,
            'description' => $description,
            'mars_rashi' => $this->getRashiName($marsRashi),
            'mars_longitude' => $marsData['longitude'],
            'remedies' => $remedies,
            'marriage_impact' => $isKujaDoshic ? 
                '結婚相手もマンガリクであることが推奨されます' : 
                '結婚に特別な障害は見られません'
        ];
    }
    
    private function getJulianDay($date, $time, $coordinates) {
        // Reuse the Julian Day calculation method from AstrologyService pattern
        $datetime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        $timestamp = $datetime->getTimestamp();
        
        // Convert to Julian Day
        $jd = $timestamp / 86400.0 + 2440587.5;
        
        return $jd;
    }
    
    private function getLahiriAyanamsa($jd) {
        // Simplified Lahiri Ayanamsa calculation
        // Based on the formula from existing AstrologyService
        $t = ($jd - 2451545.0) / 36525.0;
        $ayanamsa = 23.85 + (0.0029951 * ($jd - 2451545.0));
        return $ayanamsa;
    }
    
    private function getRashiFromLongitude($longitude) {
        $rashis = [
            'Mesha', 'Vrishabha', 'Mithuna', 'Karka', 'Simha', 'Kanya',
            'Tula', 'Vrishchika', 'Dhanu', 'Makara', 'Kumbha', 'Meena'
        ];
        
        $rashiIndex = floor($longitude / 30);
        return $rashis[$rashiIndex] ?? 'Mesha';
    }
    
    private function getRashiName($rashiNumber) {
        $rashis = [
            1 => 'メーシャ（牡羊座）', 2 => 'ヴリシャーバ（牡牛座）', 3 => 'ミトゥナ（双子座）',
            4 => 'カルカ（蟹座）', 5 => 'シンハ（獅子座）', 6 => 'カンニャー（乙女座）',
            7 => 'トゥラー（天秤座）', 8 => 'ヴリシュチカ（蠍座）', 9 => 'ダヌ（射手座）',
            10 => 'マカラ（山羊座）', 11 => 'クンバ（水瓶座）', 12 => 'ミーナ（魚座）'
        ];
        
        return $rashis[$rashiNumber] ?? 'メーシャ（牡羊座）';
    }
    
    private function analyze7thHouse($date, $time, $coordinates, $moonLon, $marsData) {
        // Calculate approximate ascendant (Lagna)
        $jd = $this->getJulianDay($date, $time, $coordinates);
        $ascendant = $this->calculateApproximateAscendant($jd, $coordinates);
        
        // Determine 7th house sign and lord
        $seventhHouseSign = ($ascendant['rashi_number'] + 6) % 12;
        if ($seventhHouseSign == 0) $seventhHouseSign = 12;
        
        $seventhHouseLord = $this->get7thHouseLord($seventhHouseSign);
        
        // Analyze 7th house strength
        $houseStrength = $this->assess7thHouseStrength($seventhHouseSign, $moonLon, $marsData);
        
        // Generate marriage insights based on 7th house
        $insights = $this->generate7thHouseInsights($seventhHouseSign, $seventhHouseLord, $houseStrength);
        
        return [
            'ascendant_rashi' => $this->getRashiName($ascendant['rashi_number']),
            'ascendant_degree' => round($ascendant['longitude'], 2),
            'seventh_house_sign' => $this->getRashiName($seventhHouseSign),
            'seventh_house_lord' => $seventhHouseLord,
            'house_strength' => $houseStrength,
            'marriage_insights' => $insights,
            'spouse_characteristics' => $this->getSpouseCharacteristics($seventhHouseSign)
        ];
    }
    
    private function calculateApproximateAscendant($jd, $coordinates) {
        // Simplified ascendant calculation
        // This is an approximation - proper calculation requires complex astronomical formulas
        
        $lat = $coordinates['latitude'] ?? 35.6762; // Default to Tokyo
        $lon = $coordinates['longitude'] ?? 139.6503;
        
        // Calculate Local Sidereal Time (LST)
        $t = ($jd - 2451545.0) / 36525.0;
        $gmst = 280.46061837 + 360.98564736629 * ($jd - 2451545.0) + 0.000387933 * $t * $t;
        $lst = $gmst + $lon;
        $lst = fmod($lst, 360);
        if ($lst < 0) $lst += 360;
        
        // Simplified ascendant calculation using LST and latitude
        // This is a basic approximation
        $ascendantDegree = $lst + (0.25 * sin(deg2rad($lat))); // Very simplified
        $ascendantDegree = fmod($ascendantDegree, 360);
        if ($ascendantDegree < 0) $ascendantDegree += 360;
        
        // Apply Lahiri Ayanamsa
        $ayanamsa = $this->getLahiriAyanamsa($jd);
        $siderealAscendant = $ascendantDegree - $ayanamsa;
        if ($siderealAscendant < 0) $siderealAscendant += 360;
        
        return [
            'longitude' => $siderealAscendant,
            'rashi_number' => floor($siderealAscendant / 30) + 1
        ];
    }
    
    private function get7thHouseLord($seventhHouseSign) {
        $houseLords = [
            1 => 'マルス（火星）',      // Aries - Mars
            2 => 'シュクラ（金星）',    // Taurus - Venus  
            3 => 'ブダ（水星）',       // Gemini - Mercury
            4 => 'チャンドラ（月）',    // Cancer - Moon
            5 => 'スーリヤ（太陽）',    // Leo - Sun
            6 => 'ブダ（水星）',       // Virgo - Mercury
            7 => 'シュクラ（金星）',    // Libra - Venus
            8 => 'マルス（火星）',      // Scorpio - Mars
            9 => 'グル（木星）',       // Sagittarius - Jupiter
            10 => 'シャニ（土星）',     // Capricorn - Saturn
            11 => 'シャニ（土星）',     // Aquarius - Saturn
            12 => 'グル（木星）'       // Pisces - Jupiter
        ];
        
        return $houseLords[$seventhHouseSign] ?? 'マルス（火星）';
    }
    
    private function assess7thHouseStrength($seventhHouseSign, $moonLon, $marsData) {
        $strength = 70; // Base strength
        $factors = [];
        
        // Benefic signs for 7th house
        $beneficSigns = [2, 4, 6, 7, 12]; // Taurus, Cancer, Virgo, Libra, Pisces
        if (in_array($seventhHouseSign, $beneficSigns)) {
            $strength += 15;
            $factors[] = '7室に吉星座が配置されています';
        }
        
        // Malefic signs
        $maleficSigns = [1, 8, 12]; // Aries, Scorpio, Pisces (partial)
        if (in_array($seventhHouseSign, $maleficSigns)) {
            $strength -= 10;
            $factors[] = '7室に火星の影響があります';
        }
        
        // Moon influence on 7th house (approximation)
        $moonRashi = floor($moonLon / 30) + 1;
        $rashiDistance = abs($moonRashi - $seventhHouseSign);
        
        if ($rashiDistance == 0) { // Moon in 7th house
            $strength += 20;
            $factors[] = '月が7室を強化しています';
        } elseif ($rashiDistance == 6) { // Moon opposite 7th house
            $strength += 10;
            $factors[] = '月が7室にアスペクトしています';
        }
        
        // Mars influence (from Kuja Dosha analysis)
        $marsRashi = $marsData['rashi_number'];
        if (abs($marsRashi - $seventhHouseSign) == 0) {
            $strength -= 15;
            $factors[] = '火星が7室に配置され、注意が必要です';
        }
        
        $strength = max(30, min(95, $strength));
        
        return [
            'score' => $strength,
            'rating' => $strength >= 80 ? 'excellent' : ($strength >= 65 ? 'good' : ($strength >= 50 ? 'fair' : 'challenging')),
            'factors' => $factors
        ];
    }
    
    private function generate7thHouseInsights($seventhHouseSign, $seventhHouseLord, $houseStrength) {
        $insights = [];
        
        // Strength-based insights
        switch ($houseStrength['rating']) {
            case 'excellent':
                $insights[] = '7室の配置が非常に良好で、結婚運が強いです';
                break;
            case 'good':
                $insights[] = '7室の配置が良好で、結婚に有利な条件が揃っています';
                break;
            case 'fair':
                $insights[] = '7室の配置は平均的で、努力により良い結果が期待できます';
                break;
            case 'challenging':
                $insights[] = '7室に課題があり、慎重な結婚選択が推奨されます';
                break;
        }
        
        // Lord-specific insights
        $lordInsights = [
            'マルス（火星）' => '情熱的で活動的なパートナーとの縁があります',
            'シュクラ（金星）' => '美しく愛情深いパートナーとの結婚が期待できます',
            'ブダ（水星）' => '知的で会話が楽しいパートナーとの相性が良いです',
            'チャンドラ（月）' => '思いやりがあり家庭的なパートナーとの縁があります',
            'スーリヤ（太陽）' => '自信に満ちたリーダーシップのあるパートナーと出会えます',
            'グル（木星）' => '賢明で精神的に成熟したパートナーとの結婚が幸運をもたらします',
            'シャニ（土星）' => '真面目で責任感の強いパートナーとの安定した結婚が期待できます'
        ];
        
        $insights[] = $lordInsights[$seventhHouseLord] ?? 'パートナーとの良い縁が期待できます';
        
        return $insights;
    }
    
    private function getSpouseCharacteristics($seventhHouseSign) {
        $characteristics = [
            1 => ['積極的', '勇敢', '独立心旺盛'], // Aries
            2 => ['安定志向', '美的感覚', '忍耐強い'], // Taurus
            3 => ['知的', '社交的', '適応力がある'], // Gemini
            4 => ['家庭的', '感情豊か', '保護本能'], // Cancer
            5 => ['自信がある', 'リーダーシップ', '創造的'], // Leo
            6 => ['几帳面', '分析的', 'サービス精神'], // Virgo
            7 => ['調和を重視', '外交的', 'パートナーシップ重視'], // Libra
            8 => ['神秘的', '変容力', '深い洞察力'], // Scorpio
            9 => ['哲学的', '楽観的', '冒険的'], // Sagittarius
            10 => ['野心的', '責任感', '社会的地位'], // Capricorn
            11 => ['革新的', '独創的', '人道主義'], // Aquarius
            12 => ['直感的', '芸術的', '霊性が高い'] // Pisces
        ];
        
        return $characteristics[$seventhHouseSign] ?? ['バランスの取れた', '理解ある', '支援的な'];
    }
    
    private function analyzeMarriageKarakas($date, $time, $coordinates, $gender) {
        $jd = $this->getJulianDay($date, $time, $coordinates);
        
        // Calculate Venus and Jupiter positions
        $venusData = $this->calculateVenusPosition($jd, $coordinates);
        $jupiterData = $this->calculateJupiterPosition($jd, $coordinates);
        
        // Determine primary karaka based on gender
        $primaryKaraka = ($gender === 'female') ? 'jupiter' : 'venus';
        $primaryData = ($gender === 'female') ? $jupiterData : $venusData;
        $secondaryData = ($gender === 'female') ? $venusData : $jupiterData;
        
        // Analyze karaka strength
        $primaryStrength = $this->assessKarakaStrength($primaryData, $primaryKaraka);
        $secondaryStrength = $this->assessKarakaStrength($secondaryData, $primaryKaraka === 'venus' ? 'jupiter' : 'venus');
        
        return [
            'primary_karaka' => [
                'name' => $primaryKaraka === 'venus' ? 'シュクラ（金星）' : 'グル（木星）',
                'type' => $primaryKaraka,
                'position' => $primaryData,
                'strength' => $primaryStrength,
                'role' => $gender === 'female' ? '夫の表示体' : '妻の表示体'
            ],
            'secondary_karaka' => [
                'name' => $primaryKaraka === 'venus' ? 'グル（木星）' : 'シュクラ（金星）',
                'type' => $primaryKaraka === 'venus' ? 'jupiter' : 'venus',
                'position' => $secondaryData,
                'strength' => $secondaryStrength,
                'role' => '結婚運の補助'
            ],
            'overall_assessment' => $this->generateKarakaAssessment($primaryStrength, $secondaryStrength, $gender)
        ];
    }
    
    private function calculateVenusPosition($jd, $coordinates) {
        // Simplified Venus orbital calculation
        $venusOrbitalPeriod = 224.7; // days
        $venusEccentricity = 0.0067;
        $venusLongitudeAtEpoch = 181.98; // degrees at J2000.0
        $venusMeanMotion = 360.0 / $venusOrbitalPeriod; // degrees per day
        
        // Calculate days since J2000.0
        $daysSinceJ2000 = $jd - 2451545.0;
        
        // Calculate mean longitude
        $meanLongitude = $venusLongitudeAtEpoch + ($venusMeanMotion * $daysSinceJ2000);
        $meanLongitude = fmod($meanLongitude, 360);
        if ($meanLongitude < 0) $meanLongitude += 360;
        
        // Simple correction for eccentricity
        $eccentricityCorrection = 2 * $venusEccentricity * sin(deg2rad($meanLongitude));
        $trueLongitude = $meanLongitude + rad2deg($eccentricityCorrection);
        
        // Normalize and apply Ayanamsa
        $trueLongitude = fmod($trueLongitude, 360);
        if ($trueLongitude < 0) $trueLongitude += 360;
        
        $ayanamsa = $this->getLahiriAyanamsa($jd);
        $sideLongitude = $trueLongitude - $ayanamsa;
        if ($sideLongitude < 0) $sideLongitude += 360;
        
        return [
            'longitude' => $sideLongitude,
            'rashi' => $this->getRashiFromLongitude($sideLongitude),
            'rashi_number' => floor($sideLongitude / 30) + 1
        ];
    }
    
    private function calculateJupiterPosition($jd, $coordinates) {
        // Simplified Jupiter orbital calculation
        $jupiterOrbitalPeriod = 4332.59; // days (about 11.86 years)
        $jupiterEccentricity = 0.0489;
        $jupiterLongitudeAtEpoch = 34.35; // degrees at J2000.0
        $jupiterMeanMotion = 360.0 / $jupiterOrbitalPeriod; // degrees per day
        
        // Calculate days since J2000.0
        $daysSinceJ2000 = $jd - 2451545.0;
        
        // Calculate mean longitude
        $meanLongitude = $jupiterLongitudeAtEpoch + ($jupiterMeanMotion * $daysSinceJ2000);
        $meanLongitude = fmod($meanLongitude, 360);
        if ($meanLongitude < 0) $meanLongitude += 360;
        
        // Simple correction for eccentricity
        $eccentricityCorrection = 2 * $jupiterEccentricity * sin(deg2rad($meanLongitude));
        $trueLongitude = $meanLongitude + rad2deg($eccentricityCorrection);
        
        // Normalize and apply Ayanamsa
        $trueLongitude = fmod($trueLongitude, 360);
        if ($trueLongitude < 0) $trueLongitude += 360;
        
        $ayanamsa = $this->getLahiriAyanamsa($jd);
        $sideLongitude = $trueLongitude - $ayanamsa;
        if ($sideLongitude < 0) $sideLongitude += 360;
        
        return [
            'longitude' => $sideLongitude,
            'rashi' => $this->getRashiFromLongitude($sideLongitude),
            'rashi_number' => floor($sideLongitude / 30) + 1
        ];
    }
    
    private function assessKarakaStrength($planetData, $planetType) {
        $strength = 60; // Base strength
        $factors = [];
        $rashiNumber = $planetData['rashi_number'];
        
        // Venus strength assessment
        if ($planetType === 'venus') {
            // Venus is exalted in Pisces (12), own signs: Taurus (2), Libra (7)
            if ($rashiNumber == 12) { // Pisces - Exalted
                $strength += 25;
                $factors[] = '金星が魚座で高揚し、非常に強力です';
            } elseif ($rashiNumber == 2 || $rashiNumber == 7) { // Own signs
                $strength += 20;
                $factors[] = '金星が自座にあり強いです';
            } elseif ($rashiNumber == 6) { // Virgo - Debilitated
                $strength -= 20;
                $factors[] = '金星が乙女座で減衰しています';
            }
            
            // Friendly signs
            $friendlySigns = [4, 5, 8, 9]; // Cancer, Leo, Scorpio, Sagittarius
            if (in_array($rashiNumber, $friendlySigns)) {
                $strength += 10;
                $factors[] = '金星が友好的な星座にあります';
            }
        }
        
        // Jupiter strength assessment
        if ($planetType === 'jupiter') {
            // Jupiter is exalted in Cancer (4), own signs: Sagittarius (9), Pisces (12)
            if ($rashiNumber == 4) { // Cancer - Exalted
                $strength += 25;
                $factors[] = '木星が蟹座で高揚し、非常に強力です';
            } elseif ($rashiNumber == 9 || $rashiNumber == 12) { // Own signs
                $strength += 20;
                $factors[] = '木星が自座にあり強いです';
            } elseif ($rashiNumber == 10) { // Capricorn - Debilitated
                $strength -= 20;
                $factors[] = '木星が山羊座で減衰しています';
            }
            
            // Friendly signs
            $friendlySigns = [1, 5, 8]; // Aries, Leo, Scorpio
            if (in_array($rashiNumber, $friendlySigns)) {
                $strength += 10;
                $factors[] = '木星が友好的な星座にあります';
            }
        }
        
        $strength = max(20, min(95, $strength));
        
        return [
            'score' => $strength,
            'rating' => $strength >= 80 ? 'excellent' : ($strength >= 65 ? 'good' : ($strength >= 50 ? 'fair' : 'weak')),
            'factors' => $factors
        ];
    }
    
    private function generateKarakaAssessment($primaryStrength, $secondaryStrength, $gender) {
        $overall = [];
        
        // Primary karaka assessment
        $primaryName = ($gender === 'female') ? '木星' : '金星';
        switch ($primaryStrength['rating']) {
            case 'excellent':
                $overall[] = "{$primaryName}が非常に強く、結婚運が極めて良好です";
                break;
            case 'good':
                $overall[] = "{$primaryName}が良い状態で、結婚に有利です";
                break;
            case 'fair':
                $overall[] = "{$primaryName}の状態は平均的です";
                break;
            case 'weak':
                $overall[] = "{$primaryName}が弱く、結婚に努力が必要です";
                break;
        }
        
        // Combined assessment
        $combinedScore = ($primaryStrength['score'] + $secondaryStrength['score']) / 2;
        if ($combinedScore >= 75) {
            $overall[] = '結婚の星々の配置が素晴らしく、幸せな結婚が期待できます';
        } elseif ($combinedScore >= 60) {
            $overall[] = '結婚の星々のバランスが良く、良縁に恵まれるでしょう';
        } else {
            $overall[] = '結婚の星々を強化する祈りや行いが推奨されます';
        }
        
        return $overall;
    }
}

?>