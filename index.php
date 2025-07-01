<?php
require_once 'LocationService.php';
require_once 'AstrologyService.php';
require_once 'TamilAstrologyService.php';
require_once 'DatabaseService.php';
require_once 'MarriageService.php';

$locationService = new LocationService();
$astrologyService = new AstrologyService();
$tamilAstrologyService = new TamilAstrologyService();
$databaseService = new DatabaseService();
$marriageService = new MarriageService();

// Function to get detailed Guna descriptions in Japanese
function getGunaDescriptionJapanese($gunaName, $score, $maxScore) {
    $descriptions = [
        'Varna' => $score === $maxScore ? "精神的な相性が完璧です" : "精神的な違いが存在する可能性があります",
        'Vashya' => $score === $maxScore ? "優れた相互の魅力と支配力" : ($score > 0 ? "良好な魅力レベル" : "自然な魅力が不足している可能性があります"),
        'Tara' => $score === $maxScore ? "繁栄と幸福に非常に有利" : ($score > 0 ? "一般的に有利" : "いくつかの困難に直面する可能性があります"),
        'Yoni' => $score === $maxScore ? "完璧な身体的・性的相性" : ($score > 0 ? "良好な身体的相性" : "身体的相性に懸念があります"),
        'Graha Maitri' => $score === $maxScore ? "優れた精神的相性と友情" : ($score > 0 ? "良好な精神的つながり" : "精神的相性に課題があります"),
        'Gana' => $score === $maxScore ? "完璧な気質の適合" : ($score > 0 ? "良好な気質の相性" : "気質の違いが問題を引き起こす可能性があります"),
        'Rashi' => $score === $maxScore ? "優れた全体的な調和と愛" : ($score > 0 ? "良好な全体的相性" : "関係の困難に直面する可能性があります"),
        'Nadi' => $score === $maxScore ? "健康な子孫と長寿に完璧" : "ナディ・ドーシャ - 健康と子供に影響する可能性があります",
        'Rajju' => $score === $maxScore ? "ラッジュ・ドーシャなし - 長寿に有利" : "ラッジュ・ドーシャが検出 - 関係の寿命に影響する可能性があります"
    ];
    
    return $descriptions[$gunaName] ?? "相性評価が完了しました";
}

// Get all prefectures for dropdown
$prefectures = $locationService->getAllPrefectures();


// Handle form submission
$results = null;
if ($_POST && isset($_POST['calculate'])) {
    try {
        $groomData = [
            'name' => $_POST['groom_name'],
            'date' => $_POST['groom_date'],
            'time' => $_POST['groom_time'],
            'prefecture' => $_POST['groom_prefecture'],
            'city' => $_POST['groom_city']
        ];
        
        $brideData = [
            'name' => $_POST['bride_name'],
            'date' => $_POST['bride_date'],
            'time' => $_POST['bride_time'],
            'prefecture' => $_POST['bride_prefecture'],
            'city' => $_POST['bride_city']
        ];
        
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
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ヴェーダ・タミル式ホロスコープ相性診断 | 日本</title>
    <meta name="description" content="古代インドの智慧に基づく本格的なホロスコープ相性診断。ヴェーダ占星術とタミル占星術の両方で宇宙的な相性を発見してください。">
    <meta name="description" content="インドの伝統的な占星術を使って、北インド式と南インド（タミル）式の両方から結婚相性をチェックできる無料診断サイトです。楽しくご縁を占ってみませんか？">
    <link rel="stylesheet" href="img/theme-override.css">
    <link rel="stylesheet" href="kekkon.css">
</head>
<body>
    <!-- Space-themed Loader -->
    <div class="loader-overlay" id="cosmicLoader">
        <div class="loader-container">
            <div class="cosmic-loader">
                <div class="cosmic-particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                </div>
                <div class="orbit orbit-1"></div>
                <div class="orbit orbit-2"></div>
                <div class="orbit orbit-3"></div>
                <div class="planet"></div>
            </div>
            <div class="loader-text">🔮 宇宙のエネルギーを計算中...</div>
            <div class="loader-subtext">星々の配置を分析しています</div>
        </div>
    </div>

    <!-- Meteor Shower Background -->
    <div class="meteor-background">
        <div class="star"></div>
        <div class="meteor-1"></div>
        <div class="meteor-2"></div>
        <div class="meteor-3"></div>
        <div class="meteor-4"></div>
        <div class="meteor-5"></div>
        <div class="meteor-6"></div>
        <div class="meteor-7"></div>
        <div class="meteor-8"></div>
        <div class="meteor-9"></div>
        <div class="meteor-10"></div>
        <div class="meteor-11"></div>
        <div class="meteor-12"></div>
        <div class="meteor-13"></div>
        <div class="meteor-14"></div>
        <div class="meteor-15"></div>
    </div>
    
    <div class="container">
        <div class="header">
            <h1>🕉️ ヴェーダ・インド式ホロスコープ相性診断</h1>
            <p class="subtitle">伝統的な占星術で、あなたの人生とパートナーシップの調和を導きます。</p>
        </div>

       

            <!-- New Hero Section for Marriage Prediction -->
            <div class="card fade-in-up" style="background: linear-gradient(135deg, rgba(255,235,238,0.95) 0%, rgba(252,228,236,0.95) 100%); padding: 40px 20px; text-align: center; border-radius: 20px; margin: 20px 0; box-shadow: 0 12px 30px rgba(233,30,99,0.15); border: 2px solid rgba(233, 30, 99, 0.3);">
                <h2 style="font-size: clamp(24px, 5vw, 32px); margin-bottom: 15px; color: #c2185b; font-weight: 700;">💕 結婚運勢・相性診断 💕</h2>
                <p style="font-size: clamp(14px, 3vw, 18px); line-height: 1.6; color: #424242; margin: 0; max-width: 700px; margin: 0 auto;">
                    古代インドの伝統的占星術で、あなたの結婚運勢と理想的なパートナーを詳しく分析します
                </p>
            </div>

            <!-- Marriage Prediction Form Section -->
            <form id="marriageForm">
                <div class="form-grid">
                    <div class="person-form" style="grid-column: 1 / -1; background: linear-gradient(135deg, rgba(255,235,238,0.95) 0%, rgba(248,249,250,0.95) 100%); border: 2px solid rgba(233, 30, 99, 0.2);">
                        <h3 style="color: #c2185b; font-size: clamp(18px, 4vw, 22px); margin-bottom: 25px; text-align: center;">💕 あなたの情報を入力してください</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 600px; margin: 0 auto;">
                            <div class="form-group">
                                <label>お名前:</label>
                                <input type="text" name="user_name" placeholder="お名前をご入力ください（任意）">
                            </div>
                            <div class="form-group">
                                <label>生年月日:</label>
                                <input type="date" name="user_date" required max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label>出生時刻:</label>
                                <input type="time" name="user_time" required>
                            </div>
                            <div class="form-group">
                                <label>性別:</label>
                                <div style="display: flex; gap: 25px; margin-top: 8px; flex-wrap: wrap; justify-content: center;">
                                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; min-width: 60px;">
                                        <input type="radio" name="user_gender" value="male" required style="margin-right: 8px;">
                                        <span>男性</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; min-width: 60px;">
                                        <input type="radio" name="user_gender" value="female" required style="margin-right: 8px;">
                                        <span>女性</span>
                                    </label>
                                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; min-width: 70px;">
                                        <input type="radio" name="user_gender" value="other" required style="margin-right: 8px;">
                                        <span>その他</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>都道府県:</label>
                                <select name="user_prefecture" required onchange="loadCitiesForMarriage(this.value)">
                                    <option value="">都道府県をお選びください</option>
                                    <?php foreach ($prefectures as $prefecture): ?>
                                        <option value="<?= htmlspecialchars($prefecture) ?>">
                                            <?= htmlspecialchars($prefecture) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>市区町村:</label>
                                <select name="user_city" id="user_city" required disabled>
                                    <option value="">都道府県を先にお選びください</option>
                                </select>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>

                <div class="card fade-in-up" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%); padding: 35px; text-align: center; border-radius: 20px; margin: 30px 0; box-shadow: 0 8px 25px rgba(0,0,0,0.1); border: 2px solid rgba(212, 175, 55, 0.3);">
            <!-- Privacy Policy Agreement -->
            <div class="privacy-agreement" style="margin: 20px 0; padding: 15px; background: rgba(233, 30, 99, 0.05); border-radius: 8px; border: 1px solid rgba(233, 30, 99, 0.1);">
                            <label style="display: flex; align-items: flex-start; cursor: pointer; font-size: 14px; line-height: 1.5;">
                                <input type="checkbox" id="marriagePrivacyCheck" required style="margin-right: 2px; margin-top: 2px; min-width: 16px;">
                                <span>
                                    <a href="#" id="marriagePrivacyLink" style="color: #c2185b; text-decoration: underline;">プライバシーポリシー</a>に同意します
                                    <br><span style="color: #666; font-size: 12px;">※ 入力いただいた情報は占い結果の生成にのみ使用され、第三者と共有されることはありません</span>
                                </span>
                            </label>
                        </div>
                        
                        <!-- Disclaimer -->
                        <div style="background: rgba(255, 193, 7, 0.1); padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ffc107;">
                            <p style="margin: 0; font-size: 13px; color: #666; line-height: 1.4;">
                                ⚠️ <strong>ご注意:</strong> この診断はエンターテイメント目的で提供されています。結果は参考程度にお楽しみください。重要な人生の決定は、複数の要因を考慮して慎重に行ってください。
                            </p>
                        </div>
                        
                        <div style="text-align: center; margin-top: 25px;">
                            <button type="submit" id="marriageSubmitBtn" class="calculate-btn" disabled style="background: linear-gradient(135deg, #e91e63, #ad1457); padding: 12px 25px; font-size: 16px;">
                                💕 結婚運勢を占う
                            </button>
                        </div>    
            </div>


                
                <div id="marriage-results-section" style="display: none;">
                    <!-- Results will be loaded here via AJAX -->
                </div>
            </form>

            <div class="zen-divider" style="margin: 40px 0;">
                <span>✨</span>
            </div>

            <?php if (isset($error)): ?>
                <div class="error">❌ エラー: <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
 <!-- HERO SECTION - MOVE THIS WHERE YOU WANT IT -->
 <div class="card fade-in-up" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%); padding: 35px; text-align: center; border-radius: 20px; margin: 30px 0; box-shadow: 0 8px 25px rgba(0,0,0,0.1); border: 2px solid rgba(212, 175, 55, 0.3);">
            <h2 style="font-size: 28px; margin-bottom: 20px; color: #1a237e; font-weight: 700;">✨ インド式占星術によるパートナー相性診断 ✨</h2>
            <p style="font-size: 16px; line-height: 1.6; max-width: 800px; margin: auto;">
                インドには、パートナーの相性を占う2つの伝統的な方法があります。<br>
                北インドの「クンダリ・マッチング」と南インドの「ポルッタム占い」。<br>
                生年月日と出生地を入力するだけで、あなたと相手の"星のご縁"を2つの視点から楽しく診断できます✨
            </p>
        </div>
        <!-- END HERO SECTION -->
            <form method="POST" id="horoscopeForm">
                <div class="form-grid">
                    <div class="person-form">
                        <h3>👨 情報</h3>
                        <div class="form-group">
                            <label>お名前:</label>
                            <input type="text" name="groom_name" value="<?= htmlspecialchars($_POST['groom_name'] ?? '') ?>" placeholder="お名前をご入力ください（任意）">
                        </div>
                        <div class="form-group">
                            <label>生年月日:</label>
                            <input type="date" name="groom_date" required value="<?= htmlspecialchars($_POST['groom_date'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label>出生時刻:</label>
                            <input type="time" name="groom_time" required value="<?= htmlspecialchars($_POST['groom_time'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>都道府県:</label>
                            <select name="groom_prefecture" required onchange="loadCities('groom', this.value)">
                                <option value="">都道府県をお選びください</option>
                                <?php foreach ($prefectures as $prefecture): ?>
                                    <option value="<?= htmlspecialchars($prefecture) ?>" <?= ($_POST['groom_prefecture'] ?? '') === $prefecture ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prefecture) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>市区町村:</label>
                            <select name="groom_city" id="groom_city" required disabled>
                                <option value="">都道府県を先にお選びください</option>
                            </select>
                        </div>
                        <!-- Hidden inputs for JavaScript -->
                        <input type="hidden" name="groom_city_value" value="<?= htmlspecialchars($_POST['groom_city'] ?? '') ?>">
                        <!-- ナクシャトラとラーシは結果ページで表示されます -->
                    </div>

                    <div class="person-form">
                        <h3>👩 情報</h3>
                        <div class="form-group">
                            <label>お名前:</label>
                            <input type="text" name="bride_name" value="<?= htmlspecialchars($_POST['bride_name'] ?? '') ?>" placeholder="お名前をご入力ください（任意）">
                        </div>
                        <div class="form-group">
                            <label>生年月日:</label>
                            <input type="date" name="bride_date" required value="<?= htmlspecialchars($_POST['bride_date'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label>出生時刻:</label>
                            <input type="time" name="bride_time" required value="<?= htmlspecialchars($_POST['bride_time'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>都道府県:</label>
                            <select name="bride_prefecture" required onchange="loadCities('bride', this.value)">
                                <option value="">都道府県をお選びください</option>
                                <?php foreach ($prefectures as $prefecture): ?>
                                    <option value="<?= htmlspecialchars($prefecture) ?>" <?= ($_POST['bride_prefecture'] ?? '') === $prefecture ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prefecture) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>市区町村:</label>
                            <select name="bride_city" id="bride_city" required disabled>
                                <option value="">都道府県を先にお選びください</option>
                            </select>
                        </div>
                        <!-- Hidden inputs for JavaScript -->
                        <input type="hidden" name="bride_city_value" value="<?= htmlspecialchars($_POST['bride_city'] ?? '') ?>">
                        <!-- ナクシャトラとラーシは結果ページで表示されます -->
                    </div>
                </div>

               

                <div class="card fade-in-up" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%); padding: 35px; text-align: center; border-radius: 20px; margin: 30px 0; box-shadow: 0 8px 25px rgba(0,0,0,0.1); border: 2px solid rgba(212, 175, 55, 0.3);">
            
             <!-- Privacy Policy Agreement -->
             <div class="privacy-agreement" style="margin: 20px 0; padding: 15px; background: rgba(26, 35, 126, 0.05); border-radius: 8px; border: 1px solid rgba(26, 35, 126, 0.1);">
                    <label style="display: flex; align-items: flex-start; cursor: pointer; font-size: 14px; line-height: 1.5;">
                        <input type="checkbox" id="privacyAgree" name="privacy_agree" required style="margin-right: 8px; margin-top: 2px; min-width: 16px;">
                        <span>
                            <a href="#" id="privacyPolicyLink" style="color: #1a237e; text-decoration: underline;">プライバシーポリシー</a>に同意します
                            <br><span style="color: #666; font-size: 12px;">※ 入力いただいた情報は占い結果の生成にのみ使用され、第三者と共有されることはありません</span>
                        </span>
                    </label>
                </div>
                
                <!-- Disclaimer -->
                <div style="background: rgba(255, 193, 7, 0.1); padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ffc107;">
                    <p style="margin: 0; font-size: 13px; color: #666; line-height: 1.4;">
                        ⚠️ <strong>ご注意:</strong> この診断はエンターテイメント目的で提供されています。結果は参考程度にお楽しみください。重要な人生の決定は、複数の要因を考慮して慎重に行ってください。
                    </p>
                </div>
        </div>

                <button type="submit" name="calculate" class="calculate-btn" id="submitBtn" disabled>
                    🔮 相性を診断する
                </button>
            </form>

            <div id="results-section">
            <?php if ($results): ?>
                <div class="results card fade-in-up">
                   

                    <div class="astro-info">
                        <div class="astro-card">
                            <h4>👨 <?= !empty($results['groom']['name']) ? htmlspecialchars($results['groom']['name']) . '様' : 'お相手様' ?></h4>
                            <p><strong>ナクシャトラ:</strong> <?= htmlspecialchars($results['groom']['nakshatra']) ?></p>
                            <p><strong>ラーシ:</strong> <?= htmlspecialchars($results['groom']['rashi']) ?></p>
                            <p><strong>月の経度:</strong> <?= number_format($results['groom']['moonLon'], 2) ?>°</p>
                        </div>
                        <div class="astro-card">
                            <h4>👩 <?= !empty($results['bride']['name']) ? htmlspecialchars($results['bride']['name']) . '様' : 'お相手様' ?></h4>
                            <p><strong>ナクシャトラ:</strong> <?= htmlspecialchars($results['bride']['nakshatra']) ?></p>
                            <p><strong>ラーシ:</strong> <?= htmlspecialchars($results['bride']['rashi']) ?></p>
                            <p><strong>月の経度:</strong> <?= number_format($results['bride']['moonLon'], 2) ?>°</p>
                        </div>
                    </div>

                    <?php if (isset($results['compatibility']['gunas'])): ?>
                        <div class="japanese-section card fade-in-up">
                            <h3 style="margin-bottom: 25px; color: #1a237e;">📊 詳細グナ分析（ヴェーダ式）</h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px;">
                                <?php foreach ($results['compatibility']['gunas'] as $guna): ?>
                                    <?php
                                    $percentage = ($guna['score'] / $guna['max']) * 100;
                                    $emoji = $percentage >= 75 ? "✅" : ($percentage >= 50 ? "⚠️" : "❌");
                                    $description = getGunaDescriptionJapanese($guna['name'], $guna['score'], $guna['max']);
                                    ?>
                                    <div class="explanation-card" style="border-left-color: <?= $percentage >= 75 ? '#4caf50' : ($percentage >= 50 ? '#ff9800' : '#f44336') ?>;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <span style="font-weight: 600; color: #1a237e;"><?= $emoji ?> <?= htmlspecialchars($guna['name']) ?></span>
                                            <span style="font-weight: bold; color: #1976d2;"><?= $guna['score'] ?>/<?= $guna['max'] ?></span>
                                        </div>
                                        <div style="color: #424242; font-size: 0.9rem; line-height: 1.4;">
                                            <?= htmlspecialchars($description) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($results['tamil'])): ?>
                        <div class="japanese-section tamil-section">
                            <h3 style="margin-bottom: 25px; color: #e65100;">🏛️ 10ポルタム分析（タミル式）</h3>
                            
                            <div style="text-align: center; margin-bottom: 25px;">
                                <div class="score-circle <?= $results['tamil']['scoreClass'] ?>" style="width: 130px; height: 130px; font-size: 1.6rem; margin: 0 auto;">
                                    <span><?= $results['tamil']['totalScore'] ?>/<?= $results['tamil']['maxScore'] ?></span>
                                </div>
                                <h4 style="margin-top: 15px; color: #e65100;"><?= htmlspecialchars($results['tamil']['level']) ?></h4>
                                <p style="color: #bf360c; margin-top: 8px;"><?= htmlspecialchars($results['tamil']['recommendation']) ?></p>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px;">
                                <?php foreach ($results['tamil']['poruthams'] as $porutham): ?>
                                    <?php
                                    $emoji = $porutham['score'] ? "✅" : "❌";
                                    $borderColor = $porutham['score'] ? '#4caf50' : '#f44336';
                                    ?>
                                    <div class="explanation-card" style="border-left-color: <?= $borderColor ?>;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="font-weight: 600; color: #e65100; font-size: 0.9rem;"><?= $emoji ?> <?= htmlspecialchars($porutham['name']) ?></span>
                                            <span style="font-weight: bold; color: #ff9800; font-size: 0.9rem;"><?= $porutham['score'] ?>/1</span>
                                        </div>
                                        <div style="color: #424242; font-size: 0.8rem; line-height: 1.3;">
                                            <?= htmlspecialchars($porutham['description']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($results['doshas']): ?>
                        <div class="dosha-info card fade-in-up">
                            <h4>🔍 ドーシャ分析</h4>
                            <?php foreach ($results['doshas'] as $dosha): ?>
                                <p style="margin-top: 10px;"><?= $dosha ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%); color: white; padding: 30px; border-radius: 20px; margin-top: 40px; text-align: center;">
                        <h3>🧘 🔮 宇宙からのお告げ 🔮 🧘</h3>
                        <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8; font-style: italic;">古代インドの聖者による、あなたたちへの神聖なメッセージです</p>
                        <p style="margin-top: 15px; font-size: 1.1rem; line-height: 1.6;"><?= htmlspecialchars($results['compatibility']['recommendation']) ?></p>
                        <?php if (isset($matchId)): ?>
                            <p style="margin-top: 20px; opacity: 0.8; font-size: 0.9rem;">
                                📋 診断ID: <?= htmlspecialchars($matchId) ?> | 保存日時: <?= date('Y年m月d日 H:i:s') ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- LINE Share Button for Mobile -->
                    <div id="lineShareSection" style="display: none; margin-top: 30px; text-align: center; padding: 25px; background: rgba(0, 195, 0, 0.1); border-radius: 15px; border: 2px solid #00c300;">
                        <h4 style="color: #00c300; margin-bottom: 15px; font-size: 1.2rem;">📱 結果をLINEでシェア</h4>
                        <p style="color: #424242; margin-bottom: 20px; font-size: 0.9rem;">この診断結果をお友達に共有してみませんか？</p>
                        <a id="lineShareButton" href="#" style="display: inline-block; background: linear-gradient(135deg, #00c300 0%, #00b300 100%); color: white; padding: 15px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 15px rgba(0, 195, 0, 0.3); transition: all 0.3s ease;">
                            <span style="margin-right: 8px;">📱</span>LINEでシェア
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            </div>
            <div class="zen-divider">
                    <span>✨</span>
                </div>
            <!-- Japanese Detailed Explanations Section -->
            <div class="japanese-section card fade-in-up" style="margin-top: 50px;">
                <h2 style="color: #1a237e; margin-bottom: 30px; text-align: center;">📚 相性診断システムについて</h2>
                
                <!-- Vedic System Explanation in Japanese -->
                <div style="margin-bottom: 50px;">
                    <h3 style="color: #1976d2; margin-bottom: 25px; border-bottom: 3px solid #1976d2; padding-bottom: 15px;">🕉️ ヴェーダ式アシュタクート（9グナ）システム</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 25px;">
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">1. ヴァルナ（1点）</h4>
                            <p style="color: #424242; line-height: 1.6;">精神的な相性とカーストの調和を表します。四つのヴァルナ：ブラーミン（司祭）、クシャトリヤ（武士）、ヴァイシャ（商人）、シュードラ（労働者）に基づきます。新婦のヴァルナが新郎と同等か下位の場合に完璧な適合となります。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">2. ヴァシャ（2点）</h4>
                            <p style="color: #424242; line-height: 1.6;">関係における相互の魅力と支配力を示します。動物のシンボルに基づきます：人間、野生動物、小動物、水生動物、昆虫。相性の良いグループは自然に惹かれ合います。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">3. タラ（3点）</h4>
                            <p style="color: #424242; line-height: 1.6;">繁栄と幸福のための宿の相性を決定します。各人のナクシャトラから星を数えることに基づきます。有利な数（1,3,5,7,9）は幸運をもたらし、その他は困難を招く可能性があります。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">4. ヨーニ（4点）</h4>
                            <p style="color: #424242; line-height: 1.6;">動物のシンボルに基づく身体的・性的相性を評価します。各ナクシャトラには関連する動物があります。相性の良い動物（友好/中立）は敵対的な動物よりも高いスコアを得ます。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">5. グラハ・マイトリ（5点）</h4>
                            <p style="color: #424242; line-height: 1.6;">月座の支配星に基づく精神的相性と友情を評価します。支配惑星が友好関係にある時、カップルは調和を享受します。敵対的な惑星は精神的な不和を引き起こす可能性があります。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">6. ガナ（6点）</h4>
                            <p style="color: #424242; line-height: 1.6;">三つのガナに基づく気質の相性：デーヴァ（神聖/穏やか）、マヌシャ（人間/バランス）、ラークシャサ（鬼/攻撃的）。同じガナが理想的で、一部の組み合わせは不適合です。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">7. ラーシ（7点）</h4>
                            <p style="color: #424242; line-height: 1.6;">月座の位置に基づく全体的な占星術的調和。月座間の距離とそれらの相互アスペクトを考慮します。近い座は一般的により高いスコアを得ます。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">8. ナディ（8点）</h4>
                            <p style="color: #424242; line-height: 1.6;">子孫と健康にとって最も重要な要因。三つのナディ：アーディ、マッディヤ、アンテャ。同じナディは非常に不吉とされ（ナディ・ドーシャ）、子供と長寿に影響します。</p>
                        </div>
                        <div class="explanation-card">
                            <h4 style="color: #1a237e; margin-bottom: 12px;">9. ラッジュ（2点）</h4>
                            <p style="color: #424242; line-height: 1.6;">長寿と関係の持続期間に関係します。身体部位に基づく五つのラッジュ：パーダ、カティ、ナービ、カンタ、シラ。同じラッジュは不吉とされます（ラッジュ・ドーシャ）。</p>
                        </div>
                    </div>
                </div>

                <!-- Tamil System Explanation in Japanese -->
                <div style="margin-bottom: 50px;">
                    <h3 style="color: #ff9800; margin-bottom: 25px; border-bottom: 3px solid #ff9800; padding-bottom: 15px;">🏛️ タミル式10ポルタムシステム</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 25px;">
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">1. ディナ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">健康と繁栄のための宿の数え方に基づきます。新婦から新郎への星を数えます。有利な数（2,4,6,8,9,11,13,15,18,20,24,26）は良好な健康と富を保証します。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">2. ガナ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">デーヴァ、マヌシャ、ラークシャサの分類を使用した気質の相性。ヴェーダシステムとは異なり、タミルには異なる相性マトリックスを持つより厳格な規則があります。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">3. マヘンドラ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">子孫と家族の成長に有利。特定の星の位置（新郎の星から4、7、10、13、16、19、22、25番目）に基づきます。健康な子供と家族の繁栄を保証します。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">4. ストリー・ディールガム</h4>
                            <p style="color: #424242; line-height: 1.6;">妻の長寿と幸福に関係します。新郎から新婦の星への数え方。不利な位置（新郎から3、5、7番目）は妻の健康と長寿に影響する可能性があります。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">5. ヨーニ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">動物の分類に基づく身体的・性的相性。ヴェーダのヨーニに似ていますが、タミル特有の動物グループ分けと相性規則があります。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">6. ラーシ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">全体的な調和のための月座の相性。吉祥とされる月座間の特定の距離（2、3、4、5、6、9、10、11、12番目）を考慮します。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">7. ラーシ・アティパティ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">月座を支配する惑星の支配者の相性。惑星の友好関係に基づいてパートナー間の精神的調和と理解を保証します。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">8. ヴァシヤ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">関係における相互の魅力と支配。パートナー間の自然な魅力と影響を生み出す月座の組み合わせに基づきます。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">9. ラッジュ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">身体部位の分類に基づく長寿と関係の持続期間。タミルシステムはヴェーダシステムよりも異なるラッジュ分類とより厳格な規則を持っています。</p>
                        </div>
                        <div class="explanation-card tamil-section">
                            <h4 style="color: #e65100; margin-bottom: 12px;">10. ヴェーダイ・ポルタム</h4>
                            <p style="color: #424242; line-height: 1.6;">不適合な星の組み合わせをチェックします。負の影響（ヴェーダイ）を生み出し、結婚の調和のために避けるべき特定の星のペア。</p>
                        </div>
                    </div>
                </div>

                <!-- Scoring Comparison in Japanese -->
                <div style="background: linear-gradient(135deg, #e1f5fe 0%, #f3e5f5 100%); padding: 30px; border-radius: 20px; margin-bottom: 40px;">
                    <h3 style="color: #1a237e; margin-bottom: 25px; text-align: center;">⚖️ システム比較とスコアの違い</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                        <div>
                            <h4 style="color: #1976d2; margin-bottom: 20px;">🕉️ ヴェーダシステム</h4>
                            <ul style="color: #424242; line-height: 2; margin-left: 25px; list-style: none;">
                                <li style="margin-bottom: 10px;">🔹 <strong>重み付けスコア:</strong> 異なるグナが異なる最大ポイント（1-8）を持つ</li>
                                <li style="margin-bottom: 10px;">🔹 <strong>部分スコア:</strong> 部分的な相性を達成可能（例：1.5/3）</li>
                                <li style="margin-bottom: 10px;">🔹 <strong>総スコア:</strong> 最大38ポイント</li>
                                <li style="margin-bottom: 10px;">🔹 <strong>合格スコア:</strong> 18以上のポイント（47%）が許容される</li>
                                <li><strong>焦点:</strong> 子孫（ナディ）を重視した包括的な人生の側面</li>
                            </ul>
                        </div>
                        <div>
                            <h4 style="color: #ff9800; margin-bottom: 20px;">🏛️ タミルシステム</h4>
                            <ul style="color: #424242; line-height: 2; margin-left: 25px; list-style: none;">
                                <li style="margin-bottom: 10px;">🔸 <strong>二進スコア:</strong> 各ポルタムは0または1（合格/不合格）</li>
                                <li style="margin-bottom: 10px;">🔸 <strong>等しい重み:</strong> 全10ポルタムが同じ重要性を持つ</li>
                                <li style="margin-bottom: 10px;">🔸 <strong>総スコア:</strong> 最大10ポイント</li>
                                <li style="margin-bottom: 10px;">🔸 <strong>合格スコア:</strong> 6以上のポイント（60%）が許容される</li>
                                <li><strong>焦点:</strong> 特定のタミル文化規則によるより厳格な相性</li>
                            </ul>
                        </div>
                    </div>
                    <div style="background: rgba(255,255,255,0.9); padding: 25px; border-radius: 15px; margin-top: 30px;">
                        <h4 style="color: #1a237e; margin-bottom: 15px;">💡 なぜスコアが異なるのか：</h4>
                        <div style="color: #424242; line-height: 1.8;">
                            <p style="margin-bottom: 12px;"><strong>1. 異なる計算方法:</strong> ヴェーダは重み付けスコアを使用し、タミルは二進の合格/不合格を使用。</p>
                            <p style="margin-bottom: 12px;"><strong>2. 文化的変化:</strong> 各システムは地域の伝統に基づいて異なる側面を重視。</p>
                            <p style="margin-bottom: 12px;"><strong>3. 相性規則:</strong> 異なる占星術的解釈により、一つのシステムで相性が良いものが他では良くない場合がある。</p>
                            <p><strong>4. 歴史的発展:</strong> システムは異なる地域で独立して発展し、異なる優先事項を持つ。</p>
                        </div>
                    </div>
                </div>

                <!-- Compatibility Index in Japanese -->
                <div style="background: #f8f9fa; padding: 30px; border-radius: 20px;">
                    <h3 style="color: #1a237e; margin-bottom: 25px; text-align: center;">📊 相性スコア指標</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                        <div>
                            <h4 style="color: #1976d2; margin-bottom: 20px;">ヴェーダシステム評価</h4>
                            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px; border-radius: 6px; background: #e8f5e8;">
                                    <span style="color: #2e7d32; font-weight: 600;">🌟 優秀（32-38点）</span>
                                    <span style="color: #2e7d32; font-weight: 600;">84-100%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px; border-radius: 6px; background: #fff3e0;">
                                    <span style="color: #ef6c00; font-weight: 600;">⚡ 平均（18-24点）</span>
                                    <span style="color: #ef6c00; font-weight: 600;">47-65%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 8px; border-radius: 6px; background: #ffebee;">
                                    <span style="color: #c62828; font-weight: 600;">⚠️ 注意（0-17点）</span>
                                    <span style="color: #c62828; font-weight: 600;">0-46%</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 style="color: #ff9800; margin-bottom: 20px;">タミルシステム評価</h4>
                            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px; border-radius: 6px; background: #e8f5e8;">
                                    <span style="color: #2e7d32; font-weight: 600;">🎯 プールナ（8-10点）</span>
                                    <span style="color: #2e7d32; font-weight: 600;">80-100%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px; border-radius: 6px; background: #e3f2fd;">
                                    <span style="color: #1565c0; font-weight: 600;">✅ ウッタマム（6-7点）</span>
                                    <span style="color: #1565c0; font-weight: 600;">60-79%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px; border-radius: 6px; background: #fff3e0;">
                                    <span style="color: #ef6c00; font-weight: 600;">⚠️ アタマム（4-5点）</span>
                                    <span style="color: #ef6c00; font-weight: 600;">40-59%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 8px; border-radius: 6px; background: #ffebee;">
                                    <span style="color: #c62828; font-weight: 600;">❌ アダマム（0-3点）</span>
                                    <span style="color: #c62828; font-weight: 600;">0-39%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: rgba(26, 35, 126, 0.1); padding: 20px; border-radius: 12px; margin-top: 25px; text-align: center;">
                        <p style="color: #1a237e; margin: 0; font-weight: 500; font-size: 1.05rem;">
                            💡 <strong>ヒント:</strong> 両方のシステムが貴重な洞察を提供します。包括的な相性評価のために両方のスコアを一緒に考慮してください。
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="kekkon.js"></script>

    <footer style="margin-top: 50px; padding: 30px; text-align: center; background: rgba(255,255,255,0.1); border-radius: 15px;">
        <p style="font-size: 14px; color: #fff; margin-bottom: 20px; line-height: 1.6; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
            ※ この診断はエンタメ目的で提供されています。結果は参考程度にお楽しみください 😊
        </p>
        
        <!-- LINE Share Button -->
        <div id="footerLineShare" style="margin: 25px 0; padding: 20px; background: rgba(0, 195, 0, 0.15); border-radius: 12px; border: 1px solid rgba(0, 195, 0, 0.3);">
            <p style="color: #fff; margin-bottom: 15px; font-size: 14px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                🔮 この診断をお友達にもシェアしませんか？
            </p>
            <a id="footerLineShareButton" href="#" onclick="shareLineGeneral(); return false;" style="display: inline-block; background: linear-gradient(135deg, #00c300 0%, #00b300 100%); color: white; padding: 12px 24px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 3px 12px rgba(0, 195, 0, 0.4); transition: all 0.3s ease; border: none; cursor: pointer;">
                <span style="margin-right: 6px;">📱</span>LINEでシェア
            </a>
        </div>
        <div style="padding: 15px; background: rgba(26, 35, 126, 0.2); border-radius: 10px; border-left: 4px solid #d4af37; backdrop-filter: blur(5px);">
            <p style="font-size: 14px; color: #fff; margin: 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                <strong>Powered by</strong><br>                
                <span style="font-size: 12px; opacity: 0.9;"><a href="https://www.izumodata.jp" target="_blank" style="color: #fff; text-decoration: none; opacity: 0.8;"> Izumo Data Tech GKK</a></span>
            </p>
        </div>
        
        <!-- Privacy Policy Link -->
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2);">
            <a href="#" id="privacyPolicyLink" style="color: #fff; text-decoration: underline; font-size: 13px; opacity: 0.8; cursor: pointer;">
                プライバシーポリシー
            </a>
        </div>
    </footer>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; overflow-y: auto;">
        <div style="background: white; margin: 5% auto; padding: 30px; border-radius: 15px; max-width: 700px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
            <button id="closeModal" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
            
            <div style="line-height: 1.6; color: #333;">
                <h2 style="color: #1a237e; margin-bottom: 25px; text-align: center; border-bottom: 2px solid #1a237e; padding-bottom: 10px;">
                    🔐 プライバシーポリシー / Privacy Policy
                </h2>
                
                <p style="margin-bottom: 20px;">
                    当サイトでは、ユーザーによるホロスコープ相性診断のために、以下の個人情報を収集・保存します。
                </p>
                
                <h3 style="color: #1976d2; margin: 25px 0 15px 0;">■ 収集する情報 / Information Collected</h3>
                <ul style="margin-left: 20px; margin-bottom: 20px;">
                    <li>生年月日 / Date of Birth</li>
                    <li>出生時刻 / Time of Birth</li>
                    <li>出生地（都道府県・市区町村）/ Place of Birth (Prefecture, City/Town)</li>
                    <li>（任意）お名前（仮名化）/ (Optional) Name (pseudonymized)</li>
                </ul>
                
                <h3 style="color: #1976d2; margin: 25px 0 15px 0;">■ 利用目的 / Purpose of Use</h3>
                <p style="margin-bottom: 20px;">
                    入力いただいた情報は、相性診断の結果を生成するためだけに使用されます。<br>
                    <em>The provided information is used solely for generating horoscope compatibility results.</em>
                </p>
                
                <h3 style="color: #1976d2; margin: 25px 0 15px 0;">■ 保管について / Data Storage</h3>
                <p style="margin-bottom: 20px;">
                    情報はサーバー上の安全なデータベースに保存されますが、第三者と共有されることはありません。<br>
                    <em>The data is stored securely on our server and is not shared with any third parties.</em>
                </p>
                
                <h3 style="color: #1976d2; margin: 25px 0 15px 0;">■ 保持期間 / Retention Period</h3>
                <p style="margin-bottom: 20px;">
                    データは一定期間保存され、定期的に削除または匿名化されます。<br>
                    <em>Data is retained for a limited time and periodically deleted or anonymized.</em>
                </p>
                
                <h3 style="color: #1976d2; margin: 25px 0 15px 0;">■ ユーザーの権利 / User Rights</h3>
                <p style="margin-bottom: 20px;">
                    情報の削除をご希望の場合は、以下の連絡先までご連絡ください。<br>
                    <em>If you wish to request deletion of your data, please contact us at:</em>
                </p>
                
                <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                    <strong>📧 rhythawelfare@gmail.com</strong>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button id="closeModalBtn" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; font-weight: 600;">
                        閉じる / Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>