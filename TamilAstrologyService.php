<?php

class TamilAstrologyService {
    private $nakshatras = [
        'Ashwini', 'Bharani', 'Krittika', 'Rohini', 'Mrigashirsha', 'Ardra', 'Punarvasu', 'Pushya', 'Ashlesha',
        'Magha', 'Purva Phalguni', 'Uttara Phalguni', 'Hasta', 'Chitra', 'Swati', 'Vishakha', 'Anuradha', 'Jyeshtha',
        'Mula', 'Purva Ashadha', 'Uttara Ashadha', 'Shravana', 'Dhanishtha', 'Shatabhisha', 'Purva Bhadrapada', 'Uttara Bhadrapada', 'Revati'
    ];
    
    private $nakshatraGana = [
        'Ashwini' => 'Deva', 'Bharani' => 'Manushya', 'Krittika' => 'Rakshasa', 'Rohini' => 'Manushya', 'Mrigashirsha' => 'Deva',
        'Ardra' => 'Manushya', 'Punarvasu' => 'Deva', 'Pushya' => 'Deva', 'Ashlesha' => 'Rakshasa',
        'Magha' => 'Rakshasa', 'Purva Phalguni' => 'Manushya', 'Uttara Phalguni' => 'Manushya', 'Hasta' => 'Deva', 'Chitra' => 'Rakshasa',
        'Swati' => 'Deva', 'Vishakha' => 'Rakshasa', 'Anuradha' => 'Deva', 'Jyeshtha' => 'Rakshasa',
        'Mula' => 'Rakshasa', 'Purva Ashadha' => 'Manushya', 'Uttara Ashadha' => 'Manushya', 'Shravana' => 'Deva', 'Dhanishtha' => 'Rakshasa',
        'Shatabhisha' => 'Rakshasa', 'Purva Bhadrapada' => 'Manushya', 'Uttara Bhadrapada' => 'Manushya', 'Revati' => 'Deva'
    ];
    
    private $nakshatraYoni = [
        'Ashwini' => ['animal' => 'Horse', 'gender' => 'Male'],
        'Bharani' => ['animal' => 'Elephant', 'gender' => 'Female'],
        'Krittika' => ['animal' => 'Goat', 'gender' => 'Female'],
        'Rohini' => ['animal' => 'Snake', 'gender' => 'Male'],
        'Mrigashirsha' => ['animal' => 'Snake', 'gender' => 'Female'],
        'Ardra' => ['animal' => 'Dog', 'gender' => 'Female'],
        'Punarvasu' => ['animal' => 'Cat', 'gender' => 'Female'],
        'Pushya' => ['animal' => 'Goat', 'gender' => 'Male'],
        'Ashlesha' => ['animal' => 'Cat', 'gender' => 'Male'],
        'Magha' => ['animal' => 'Rat', 'gender' => 'Male'],
        'Purva Phalguni' => ['animal' => 'Rat', 'gender' => 'Female'],
        'Uttara Phalguni' => ['animal' => 'Cow', 'gender' => 'Female'],
        'Hasta' => ['animal' => 'Buffalo', 'gender' => 'Female'],
        'Chitra' => ['animal' => 'Tiger', 'gender' => 'Female'],
        'Swati' => ['animal' => 'Buffalo', 'gender' => 'Male'],
        'Vishakha' => ['animal' => 'Tiger', 'gender' => 'Male'],
        'Anuradha' => ['animal' => 'Deer', 'gender' => 'Female'],
        'Jyeshtha' => ['animal' => 'Deer', 'gender' => 'Male'],
        'Mula' => ['animal' => 'Dog', 'gender' => 'Male'],
        'Purva Ashadha' => ['animal' => 'Monkey', 'gender' => 'Female'],
        'Uttara Ashadha' => ['animal' => 'Mongoose', 'gender' => 'Male'],
        'Shravana' => ['animal' => 'Monkey', 'gender' => 'Male'],
        'Dhanishtha' => ['animal' => 'Lion', 'gender' => 'Female'],
        'Shatabhisha' => ['animal' => 'Horse', 'gender' => 'Female'],
        'Purva Bhadrapada' => ['animal' => 'Lion', 'gender' => 'Male'],
        'Uttara Bhadrapada' => ['animal' => 'Cow', 'gender' => 'Male'],
        'Revati' => ['animal' => 'Elephant', 'gender' => 'Male']
    ];
    
    private $rajjuTypes = [
        'Pada' => ['Ashwini', 'Ashlesha', 'Magha', 'Jyeshtha', 'Mula', 'Revati'],
        'Kati' => ['Bharani', 'Pushya', 'Purva Phalguni', 'Anuradha', 'Purva Ashadha', 'Purva Bhadrapada'],
        'Nabhi' => ['Krittika', 'Punarvasu', 'Uttara Phalguni', 'Vishakha', 'Uttara Ashadha', 'Uttara Bhadrapada'],
        'Kantha' => ['Rohini', 'Ardra', 'Hasta', 'Swati', 'Shravana', 'Shatabhisha'],
        'Shira' => ['Mrigashirsha', 'Chitra', 'Dhanishtha']
    ];
    
    private $vedhaiPairs = [
        'Ashwini' => 'Jyeshtha', 'Bharani' => 'Anuradha', 'Krittika' => 'Vishakha',
        'Rohini' => 'Swati', 'Mrigashirsha' => 'Chitra', 'Ardra' => 'Hasta',
        'Punarvasu' => 'Uttara Phalguni', 'Pushya' => 'Purva Phalguni', 'Ashlesha' => 'Magha',
        'Magha' => 'Ashlesha', 'Purva Phalguni' => 'Pushya', 'Uttara Phalguni' => 'Punarvasu',
        'Hasta' => 'Ardra', 'Chitra' => 'Mrigashirsha', 'Swati' => 'Rohini',
        'Vishakha' => 'Krittika', 'Anuradha' => 'Bharani', 'Jyeshtha' => 'Ashwini',
        'Mula' => 'Revati', 'Purva Ashadha' => 'Uttara Bhadrapada', 'Uttara Ashadha' => 'Purva Bhadrapada',
        'Shravana' => 'Shatabhisha', 'Dhanishtha' => 'Shravana', 'Shatabhisha' => 'Shravana',
        'Purva Bhadrapada' => 'Uttara Ashadha', 'Uttara Bhadrapada' => 'Purva Ashadha', 'Revati' => 'Mula'
    ];
    
    public function calculate10Porutham($groomNakshatra, $brideNakshatra, $groomRashi, $brideRashi) {
        $poruthams = [];
        $totalScore = 0;
        $maxScore = 10;
        
        // 1. Dhina Porutham
        $dhina = $this->calculateDhinaPorutham($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Dhina Porutham', 'score' => $dhina, 'description' => $this->getDhinaDescription($dhina)];
        $totalScore += $dhina;
        
        // 2. Gana Porutham
        $gana = $this->calculateGanaPorutham($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Gana Porutham', 'score' => $gana, 'description' => $this->getGanaDescription($gana)];
        $totalScore += $gana;
        
        // 3. Mahendra Porutham
        $mahendra = $this->calculateMahendraPorutham($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Mahendra Porutham', 'score' => $mahendra, 'description' => $this->getMahendraDescription($mahendra)];
        $totalScore += $mahendra;
        
        // 4. Sthree Dheerkam
        $sthree = $this->calculateSthreeDheerkam($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Sthree Dheerkam', 'score' => $sthree, 'description' => $this->getSthreeDescription($sthree)];
        $totalScore += $sthree;
        
        // 5. Yoni Porutham
        $yoni = $this->calculateYoniPorutham($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Yoni Porutham', 'score' => $yoni, 'description' => $this->getYoniDescription($yoni)];
        $totalScore += $yoni;
        
        // 6. Rasi Porutham
        $rasi = $this->calculateRasiPorutham($groomRashi, $brideRashi);
        $poruthams[] = ['name' => 'Rasi Porutham', 'score' => $rasi, 'description' => $this->getRasiDescription($rasi)];
        $totalScore += $rasi;
        
        // 7. Rasi Athipathi Porutham
        $athipathi = $this->calculateRasiAthipathiPorutham($groomRashi, $brideRashi);
        $poruthams[] = ['name' => 'Rasi Athipathi Porutham', 'score' => $athipathi, 'description' => $this->getAthipathiDescription($athipathi)];
        $totalScore += $athipathi;
        
        // 8. Vasiya Porutham
        $vasiya = $this->calculateVasiyaPorutham($groomRashi, $brideRashi);
        $poruthams[] = ['name' => 'Vasiya Porutham', 'score' => $vasiya, 'description' => $this->getVasiyaDescription($vasiya)];
        $totalScore += $vasiya;
        
        // 9. Rajju Porutham
        $rajju = $this->calculateRajjuPorutham($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Rajju Porutham', 'score' => $rajju, 'description' => $this->getRajjuDescription($rajju)];
        $totalScore += $rajju;
        
        // 10. Vedhai Porutham
        $vedhai = $this->calculateVedhaiPorutham($groomNakshatra, $brideNakshatra);
        $poruthams[] = ['name' => 'Vedhai Porutham', 'score' => $vedhai, 'description' => $this->getVedhaiDescription($vedhai)];
        $totalScore += $vedhai;
        
        $percentage = ($totalScore / $maxScore) * 100;
        $level = $this->getLevel($percentage);
        $scoreClass = $this->getScoreClass($percentage);
        $recommendation = $this->getRecommendation($percentage);
        
        return [
            'totalScore' => $totalScore,
            'maxScore' => $maxScore,
            'percentage' => $percentage,
            'level' => $level,
            'scoreClass' => $scoreClass,
            'recommendation' => $recommendation,
            'poruthams' => $poruthams
        ];
    }
    
    private function calculateDhinaPorutham($groomNakshatra, $brideNakshatra) {
        $groomIndex = array_search($groomNakshatra, $this->nakshatras);
        $brideIndex = array_search($brideNakshatra, $this->nakshatras);
        
        $count = ($brideIndex - $groomIndex + 27) % 27 + 1;
        $goodCounts = [2, 4, 6, 8, 9, 11, 13, 15, 18, 20, 24, 26];
        
        return in_array($count, $goodCounts) ? 1 : 0;
    }
    
    private function calculateGanaPorutham($groomNakshatra, $brideNakshatra) {
        $groomGana = $this->nakshatraGana[$groomNakshatra] ?? 'Manushya';
        $brideGana = $this->nakshatraGana[$brideNakshatra] ?? 'Manushya';
        
        if ($groomGana === $brideGana) return 1;
        if ($groomGana === 'Deva' && $brideGana === 'Manushya') return 1;
        if ($groomGana === 'Manushya' && $brideGana === 'Deva') return 1;
        
        return 0;
    }
    
    private function calculateMahendraPorutham($groomNakshatra, $brideNakshatra) {
        $groomIndex = array_search($groomNakshatra, $this->nakshatras);
        $brideIndex = array_search($brideNakshatra, $this->nakshatras);
        
        $count = ($brideIndex - $groomIndex + 27) % 27 + 1;
        $goodCounts = [4, 7, 10, 13, 16, 19, 22, 25];
        
        return in_array($count, $goodCounts) ? 1 : 0;
    }
    
    private function calculateSthreeDheerkam($groomNakshatra, $brideNakshatra) {
        $groomIndex = array_search($groomNakshatra, $this->nakshatras);
        $brideIndex = array_search($brideNakshatra, $this->nakshatras);
        
        return $brideIndex > $groomIndex ? 1 : 0;
    }
    
    private function calculateYoniPorutham($groomNakshatra, $brideNakshatra) {
        $groomYoni = $this->nakshatraYoni[$groomNakshatra] ?? ['animal' => 'Horse', 'gender' => 'Male'];
        $brideYoni = $this->nakshatraYoni[$brideNakshatra] ?? ['animal' => 'Horse', 'gender' => 'Female'];
        
        if ($groomYoni['animal'] === $brideYoni['animal'] && $groomYoni['gender'] === 'Male' && $brideYoni['gender'] === 'Female') {
            return 1;
        }
        
        $friendlyAnimals = [
            'Horse' => ['Elephant', 'Goat'],
            'Elephant' => ['Horse'],
            'Goat' => ['Horse'],
            'Snake' => ['Snake'],
            'Dog' => ['Monkey'],
            'Cat' => ['Rat'],
            'Rat' => ['Cat'],
            'Cow' => ['Buffalo'],
            'Buffalo' => ['Cow'],
            'Tiger' => ['Tiger'],
            'Deer' => ['Deer'],
            'Monkey' => ['Dog'],
            'Mongoose' => ['Mongoose'],
            'Lion' => ['Lion']
        ];
        
        $friendlyList = $friendlyAnimals[$groomYoni['animal']] ?? [];
        return in_array($brideYoni['animal'], $friendlyList) ? 1 : 0;
    }
    
    private function calculateRasiPorutham($groomRashi, $brideRashi) {
        $rashis = ['Mesha', 'Vrishabha', 'Mithuna', 'Karka', 'Simha', 'Kanya', 'Tula', 'Vrishchika', 'Dhanus', 'Makara', 'Kumbha', 'Meena'];
        $groomIndex = array_search($groomRashi, $rashis);
        $brideIndex = array_search($brideRashi, $rashis);
        
        $distance = abs($groomIndex - $brideIndex);
        return !in_array($distance, [1, 5, 6, 8, 12]) ? 1 : 0;
    }
    
    private function calculateRasiAthipathiPorutham($groomRashi, $brideRashi) {
        $rasiLords = [
            'Mesha' => 'Mangal', 'Vrishabha' => 'Shukra', 'Mithuna' => 'Budh', 'Karka' => 'Chandra',
            'Simha' => 'Surya', 'Kanya' => 'Budh', 'Tula' => 'Shukra', 'Vrishchika' => 'Mangal',
            'Dhanus' => 'Guru', 'Makara' => 'Shani', 'Kumbha' => 'Shani', 'Meena' => 'Guru'
        ];
        
        $groomLord = $rasiLords[$groomRashi] ?? 'Surya';
        $brideLord = $rasiLords[$brideRashi] ?? 'Chandra';
        
        $friendships = [
            'Surya' => ['Chandra', 'Mangal', 'Guru'],
            'Chandra' => ['Surya', 'Budh'],
            'Mangal' => ['Surya', 'Chandra', 'Guru'],
            'Budh' => ['Surya', 'Shukra'],
            'Guru' => ['Surya', 'Chandra', 'Mangal'],
            'Shukra' => ['Budh', 'Shani'],
            'Shani' => ['Budh', 'Shukra']
        ];
        
        if ($groomLord === $brideLord) return 1;
        $friends = $friendships[$groomLord] ?? [];
        return in_array($brideLord, $friends) ? 1 : 0;
    }
    
    private function calculateVasiyaPorutham($groomRashi, $brideRashi) {
        $vasiyaGroups = [
            'Mesha' => ['Simha', 'Vrishchika'], 'Vrishabha' => ['Karka', 'Tula'],
            'Mithuna' => ['Kanya'], 'Karka' => ['Vrishchika', 'Dhanus'],
            'Simha' => ['Tula'], 'Kanya' => ['Meena', 'Mithuna'],
            'Tula' => ['Makara', 'Vrishabha'], 'Vrishchika' => ['Karka'],
            'Dhanus' => ['Meena'], 'Makara' => ['Mesha', 'Kumbha'],
            'Kumbha' => ['Makara'], 'Meena' => ['Kanya']
        ];
        
        $groomVasiya = $vasiyaGroups[$groomRashi] ?? [];
        return in_array($brideRashi, $groomVasiya) || $groomRashi === $brideRashi ? 1 : 0;
    }
    
    private function calculateRajjuPorutham($groomNakshatra, $brideNakshatra) {
        foreach ($this->rajjuTypes as $type => $nakshatras) {
            if (in_array($groomNakshatra, $nakshatras) && in_array($brideNakshatra, $nakshatras)) {
                return 0; // Same rajju - not good
            }
        }
        return 1; // Different rajju - good
    }
    
    private function calculateVedhaiPorutham($groomNakshatra, $brideNakshatra) {
        $groomVedhai = $this->vedhaiPairs[$groomNakshatra] ?? '';
        $brideVedhai = $this->vedhaiPairs[$brideNakshatra] ?? '';
        
        if ($groomVedhai === $brideNakshatra || $brideVedhai === $groomNakshatra) {
            return 0; // Vedhai present - not good
        }
        return 1; // No vedhai - good
    }
    
    // Description methods in Japanese
    private function getDhinaDescription($score) {
        return $score ? "健康と繁栄に良い影響があります" : "健康面で課題に直面する可能性があります";
    }
    
    private function getGanaDescription($score) {
        return $score ? "優れた気質の相性です" : "気質の違いがあります";
    }
    
    private function getMahendraDescription($score) {
        return $score ? "子孫に有利です" : "子供の見通しに影響する可能性があります";
    }
    
    private function getSthreeDescription($score) {
        return $score ? "長寿と繁栄に良い影響があります" : "長寿に関する懸念があります";
    }
    
    private function getYoniDescription($score) {
        return $score ? "優れた身体的相性です" : "身体的相性に問題があります";
    }
    
    private function getRasiDescription($score) {
        return $score ? "良好な占星術的調和があります" : "占星術的な不適合があります";
    }
    
    private function getAthipathiDescription($score) {
        return $score ? "惑星の支配者が相性良好です" : "惑星の支配者に対立があります";
    }
    
    private function getVasiyaDescription($score) {
        return $score ? "強い相互の魅力があります" : "自然な魅力が不足しています";
    }
    
    private function getRajjuDescription($score) {
        return $score ? "ラッジュ・ドーシャなし - 結婚に良好です" : "ラッジュ・ドーシャあり - 対策が必要です";
    }
    
    private function getVedhaiDescription($score) {
        return $score ? "ヴェーダイなし - 相性が確認されました" : "ヴェーダイあり - 不適合な星の組み合わせです";
    }

    private function getLevel($percentage) {
        if ($percentage >= 80) return "優秀";
        if ($percentage >= 60) return "非常に良い";
        if ($percentage >= 40) return "平均";
        return "注意が必要";
    }

    private function getScoreClass($percentage) {
        if ($percentage >= 80) return 'excellent';
        if ($percentage >= 60) return 'good';
        if ($percentage >= 40) return 'fair';
        return 'poor';
    }

    private function getRecommendation($percentage) {
        if ($percentage >= 80) {
            return "タミル式分析では非常に良好な相性を示しています。伝統的な知恵があなたたちの結びつきを祝福しています。";
        } elseif ($percentage >= 60) {
            return "タミル式分析では良好な相性を示しています。多くのポルタムが一致しており、幸福な関係が期待できます。";
        } elseif ($percentage >= 40) {
            return "タミル式分析ではまずまずの相性です。いくつかの課題はありますが、愛と理解で乗り越えられます。";
        } else {
            return "タミル式分析では注意が必要な相性です。伝統的な対策と祈りが推奨されます。";
        }
    }
}