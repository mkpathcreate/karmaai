/* Japan-Indo Horoscope - Optimized JavaScript */

// === Utility Functions ===
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showCosmicLoader() {
    const loader = document.getElementById('cosmicLoader');
    if (loader) {
        loader.style.display = 'flex';
    }
}

function hideCosmicLoader() {
    const loader = document.getElementById('cosmicLoader');
    if (loader) {
        loader.style.display = 'none';
    }
}

// === Unified City Loading Function ===
function loadCities(selectId, prefecture, person = null) {
    const citySelect = document.getElementById(selectId);
    
    if (!prefecture) {
        citySelect.innerHTML = '<option value="">都道府県を先にお選びください</option>';
        citySelect.disabled = true;
        return;
    }
    
    citySelect.innerHTML = '<option value="">市区町村を読み込み中...</option>';
    citySelect.disabled = true;
    
    fetch('api.php?action=cities&prefecture=' + encodeURIComponent(prefecture))
        .then(response => response.json())
        .then(data => {
            citySelect.innerHTML = '<option value="">市区町村をお選びください</option>';
            data.cities.forEach(city => {
                citySelect.innerHTML += `<option value="${city}">${city}</option>`;
            });
            citySelect.disabled = false;
            
            // Add auto-calculation listener if person is specified (for compatibility form)
            if (person) {
                citySelect.removeEventListener('change', citySelect.calcHandler);
                citySelect.calcHandler = function() { checkAutoCalculation(person); };
                citySelect.addEventListener('change', citySelect.calcHandler);
            }
        })
        .catch(error => {
            console.error('市区町村の読み込みエラー:', error);
            citySelect.innerHTML = '<option value="">市区町村の読み込みに失敗しました</option>';
        });
}

// Compatibility wrapper functions for backward compatibility
function loadCitiesForMarriage(prefecture) {
    loadCities('user_city', prefecture);
}

function loadCitiesForPerson(person, prefecture) {
    loadCities(person + '_city', prefecture, person);
}

// === Form Auto-Calculation (Compatibility Form) ===
function checkAutoCalculation(person) {
    const date = document.querySelector(`input[name="${person}_date"]`).value;
    const time = document.querySelector(`input[name="${person}_time"]`).value;
    const prefecture = document.querySelector(`select[name="${person}_prefecture"]`).value;
    const city = document.querySelector(`select[name="${person}_city"]`).value;
    
    console.log(`${person}のフォーム完了状況:`, {date, time, prefecture, city});
    
    if (date && time && prefecture && city) {
        console.log(`${person}の基本情報が完了しました`);
    }
}

function setupAutoCalculation() {
    console.log('自動計算リスナーを設定中...');
    ['groom', 'bride'].forEach(person => {
        const dateField = document.querySelector(`input[name="${person}_date"]`);
        const timeField = document.querySelector(`input[name="${person}_time"]`);
        
        if (dateField) {
            dateField.addEventListener('change', () => {
                console.log(`${person}の日付が変更されました:`, dateField.value);
                checkAutoCalculation(person);
            });
        }
        
        if (timeField) {
            timeField.addEventListener('change', () => {
                console.log(`${person}の時刻が変更されました:`, timeField.value);
                checkAutoCalculation(person);
            });
        }
    });
}

// === Unified Privacy Agreement Handler ===
function setupPrivacyAgreement(checkboxId, buttonId) {
    const privacyCheckbox = document.getElementById(checkboxId);
    const submitButton = document.getElementById(buttonId);
    
    if (privacyCheckbox && submitButton) {
        privacyCheckbox.addEventListener('change', function() {
            if (this.checked) {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
            } else {
                submitButton.disabled = true;
                submitButton.style.opacity = '0.5';
                submitButton.style.cursor = 'not-allowed';
            }
        });
        
        // Initial state
        submitButton.style.opacity = '0.5';
        submitButton.style.cursor = 'not-allowed';
    }
}

// === Unified Privacy Policy Modal ===
function setupPrivacyModal() {
    const privacyLinks = ['privacyPolicyLink', 'marriagePrivacyLink'];
    const modal = document.getElementById('privacyModal');
    const closeButtons = ['closeModal', 'closeModalBtn'];
    
    if (!modal) return;
    
    // Function to open modal
    function openModal(e) {
        e.preventDefault();
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    // Function to close modal
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Setup privacy links
    privacyLinks.forEach(linkId => {
        const link = document.getElementById(linkId);
        if (link) {
            link.addEventListener('click', openModal);
        }
    });
    
    // Setup close buttons
    closeButtons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', closeModal);
        }
    });
    
    // Close when clicking outside modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
}

// === LINE Share Functions ===
function setupLineShare() {
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        const lineSection = document.getElementById('lineShareSection');
        if (lineSection) {
            lineSection.style.display = 'block';
            
            const lineButton = document.getElementById('lineShareButton');
            if (lineButton) {
                lineButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    shareLine();
                });
            }
        }
    }
}

function shareLine() {
    const url = window.location.href;
    let message = '🔮 ヴェーダ・タミル式ホロスコープ相性診断の結果 ✨\\n\\n';
    
    const veddicScore = document.querySelector('.score-circle span');
    if (veddicScore) {
        message += `📊 相性スコア: ${veddicScore.textContent}\\n`;
    }
    
    const compatLevel = document.querySelector('.compatibility-score h2');
    if (compatLevel) {
        message += `💫 判定: ${compatLevel.textContent}\\n`;
    }
    
    message += '\\n古代インドの智慧で相性を診断してみませんか？\\n';
    message += url;
    
    const encodedMessage = encodeURIComponent(message);
    const lineUrl = `https://line.me/R/msg/text/?${encodedMessage}`;
    
    window.open(lineUrl, '_blank');
}

function shareLineGeneral() {
    const url = window.location.href;
    let message = '🔮 ヴェーダ・南インド式ホロスコープ相性診断 ✨\\n\\n';
    message += '古代インドの智慧で相性を無料診断！\\n';
    message += '北インド式と南インド式の両方で詳しく分析してくれます 📊\\n\\n';
    message += '💕 結婚相性やお付き合いの参考にどうぞ\\n';
    message += '🌟 ナクシャトラ・ラーシ・グナ分析\\n';
    message += '🏛️ 10ポルタム分析（南インド式）\\n\\n';
    message += 'あなたも試してみませんか？\\n';
    message += url;
    
    const encodedMessage = encodeURIComponent(message);
    const lineUrl = `https://line.me/R/msg/text/?${encodedMessage}`;
    
    window.open(lineUrl, '_blank');
}

// === Scroll Animations ===
function handleScrollAnimations() {
    const elements = document.querySelectorAll('.fade-in-up');
    const windowHeight = window.innerHeight;
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < windowHeight - elementVisible) {
            element.style.animationPlayState = 'running';
            element.style.opacity = '1';
        }
    });
}

// === Form Submission Handlers ===
function setupFormSubmission() {
    const form = document.getElementById('horoscopeForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        showCosmicLoader();
        
        const formData = new FormData(form);
        
        fetch('compatibility_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideCosmicLoader();
            console.log('AJAX Response:', data);
            
            if (data.success) {
                displayResults(data);
                setTimeout(() => {
                    document.getElementById('results-section').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            } else {
                showError(data.error || '計算中にエラーが発生しました');
            }
        })
        .catch(error => {
            hideCosmicLoader();
            console.error('Error:', error);
            showError('通信エラーが発生しました。もう一度お試しください。');
        });
    });
}

function setupMarriageFormSubmission() {
    const form = document.getElementById('marriageForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        showCosmicLoader();
        
        const formData = new FormData(form);
        formData.append('action', 'predict_marriage');
        
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideCosmicLoader();
            console.log('Marriage Prediction Response:', data);
            
            if (data.success) {
                displayMarriageResults(data);
                setTimeout(() => {
                    document.getElementById('marriage-results-section').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            } else {
                showMarriageError(data.error || '計算中にエラーが発生しました');
            }
        })
        .catch(error => {
            hideCosmicLoader();
            console.error('Error:', error);
            showMarriageError('通信エラーが発生しました。もう一度お試しください。');
        });
    });
}

// === Error Display Functions ===
function showError(message) {
    const resultsSection = document.getElementById('results-section');
    if (!resultsSection) return;

    resultsSection.innerHTML = `
        <div class="error" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 20px 0;">
            ❌ エラー: ${escapeHtml(message)}
        </div>
    `;
    resultsSection.style.display = 'block';
}

function showMarriageError(message) {
    const resultsSection = document.getElementById('marriage-results-section');
    if (!resultsSection) return;

    resultsSection.innerHTML = `
        <div class="error" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin: 20px 0;">
            ❌ エラー: ${escapeHtml(message)}
        </div>
    `;
    resultsSection.style.display = 'block';
}

// === Results Display Functions ===
function getCompatibilityLevel(percentage) {
    if (percentage >= 80) return "非常に良い相性";
    if (percentage >= 60) return "良い相性";
    if (percentage >= 40) return "平均的な相性";
    return "改善が必要";
}

function displayResults(data) {
    const resultsSection = document.getElementById('results-section');
    if (!resultsSection) return;

    const results = data.results;
    const groom = data.groom;
    const bride = data.bride;

    const resultsHTML = `
        <div class="results card fade-in-up">
            <div class="astro-info">
                <div class="astro-card">
                    <h4>👨 ${escapeHtml(groom.name || 'お相手様')}</h4>
                    <p><strong>ナクシャトラ:</strong> ${results.groom.nakshatra}</p>
                    <p><strong>ラーシ:</strong> ${results.groom.rashi}</p>
                    <p><strong>月の経度:</strong> ${parseFloat(results.groom.moonLon).toFixed(2)}°</p>
                </div>
                <div class="astro-card">
                    <h4>👩 ${escapeHtml(bride.name || 'お相手様')}</h4>
                    <p><strong>ナクシャトラ:</strong> ${results.bride.nakshatra}</p>
                    <p><strong>ラーシ:</strong> ${results.bride.rashi}</p>
                    <p><strong>月の経度:</strong> ${parseFloat(results.bride.moonLon).toFixed(2)}°</p>
                </div>
            </div>

            ${results.compatibility.gunas ? buildVedicSection(results.compatibility.gunas) : ''}
            ${results.tamil ? buildTamilSection(results.tamil) : ''}
            ${results.doshas && results.doshas.length > 0 ? buildDoshaSection(results.doshas) : ''}
            
            <div style="background: linear-gradient(135deg, #1a237e 0%, #303f9f 100%); color: white; padding: 30px; border-radius: 20px; margin-top: 40px; text-align: center;">
                <h3>🧘 🔮 宇宙からのお告げ 🔮 🧘</h3>
                <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8; font-style: italic;">古代インドの聖者による、あなたたちへの神聖なメッセージです</p>
                <p style="margin-top: 15px; font-size: 1.1rem; line-height: 1.6;">${escapeHtml(results.compatibility.recommendation)}</p>
            </div>

            <div id="lineShareSection" style="margin-top: 30px; text-align: center; padding: 25px; background: rgba(0, 195, 0, 0.1); border-radius: 15px; border: 2px solid #00c300;">
                <h4 style="color: #00c300; margin-bottom: 15px; font-size: 1.2rem;">📱 結果をLINEでシェア</h4>
                <p style="color: #424242; margin-bottom: 20px; font-size: 0.9rem;">この診断結果をお友達に共有してみませんか？</p>
                <a id="lineShareButton" href="#" style="display: inline-block; background: linear-gradient(135deg, #00c300 0%, #00b300 100%); color: white; padding: 15px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 15px rgba(0, 195, 0, 0.3); transition: all 0.3s ease;">
                    <span style="margin-right: 8px;">📱</span>LINEでシェア
                </a>
            </div>
        </div>
    `;

    resultsSection.innerHTML = resultsHTML;
    resultsSection.style.display = 'block';
    resultsSection.classList.add('fade-in');
    
    setupLineShare();
}

function buildVedicSection(gunas) {
    let totalScore = 0;
    let maxScore = 0;
    gunas.forEach(guna => {
        totalScore += guna.score;
        maxScore += guna.max;
    });
    const percentage = Math.round((totalScore / maxScore) * 100);
    const scoreClass = percentage >= 80 ? 'score-excellent' : percentage >= 60 ? 'score-good' : percentage >= 40 ? 'score-average' : 'score-poor';
    
    let html = `
        <div class="japanese-section card fade-in-up">
            <h3 style="margin-bottom: 25px; color: #1a237e;">📊 詳細グナ分析（ヴェーダ式）</h3>
            
            <div style="text-align: center; margin-bottom: 25px;">
                <div class="score-circle ${scoreClass}" style="width: 130px; height: 130px; font-size: 1.6rem; margin: 0 auto;">
                    <span>${totalScore}/${maxScore}</span>
                </div>
                <h4 style="margin-top: 15px; color: #1a237e;">${percentage}% - ${getCompatibilityLevel(percentage)}</h4>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px;">
    `;

    if (gunas && Array.isArray(gunas)) {
        gunas.forEach(guna => {
            const percentage = (guna.score / guna.max) * 100;
            const emoji = percentage >= 75 ? "✅" : (percentage >= 50 ? "⚠️" : "❌");
            const borderColor = percentage >= 75 ? '#4caf50' : (percentage >= 50 ? '#ff9800' : '#f44336');
            
            html += `
                <div class="explanation-card" style="border-left-color: ${borderColor};">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-weight: 600; color: #1a237e;">${emoji} ${guna.name}</span>
                        <span style="font-weight: bold; color: #1976d2;">${guna.score}/${guna.max}</span>
                    </div>
                    <div style="color: #424242; font-size: 0.9rem; line-height: 1.4;">
                        ${guna.description}
                    </div>
                </div>
            `;
        });
    }

    html += `
            </div>
        </div>
    `;

    return html;
}

function buildTamilSection(tamil) {
    let html = `
        <div class="japanese-section tamil-section">
            <h3 style="margin-bottom: 25px; color: #e65100;">🏛️ 10ポルタム分析（タミル式）</h3>
            
            <div style="text-align: center; margin-bottom: 25px;">
                <div class="score-circle ${tamil.scoreClass}" style="width: 130px; height: 130px; font-size: 1.6rem; margin: 0 auto;">
                    <span>${tamil.totalScore}/${tamil.maxScore}</span>
                </div>
                <h4 style="margin-top: 15px; color: #e65100;">${tamil.level}</h4>
                <p style="color: #bf360c; margin-top: 8px;">${tamil.recommendation}</p>
            </div>
    `;

    if (tamil.porutham && Array.isArray(tamil.porutham)) {
        html += `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
        `;

        tamil.porutham.forEach(p => {
            const emoji = p.score === p.max ? "✅" : (p.score > 0 ? "⚠️" : "❌");
            const borderColor = p.score === p.max ? '#4caf50' : (p.score > 0 ? '#ff9800' : '#f44336');

            html += `
                <div class="explanation-card" style="border-left-color: ${borderColor};">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-weight: 600; color: #e65100;">${emoji} ${p.name}</span>
                        <span style="font-weight: bold; color: #bf360c;">${p.score}/${p.max}</span>
                    </div>
                    <div style="color: #424242; font-size: 0.85rem; line-height: 1.4;">
                        ${p.description}
                    </div>
                </div>
            `;
        });

        html += `
            </div>
        `;
    }

    html += `
        </div>
    `;

    return html;
}

function buildDoshaSection(doshas) {
    let html = `
        <div class="dosha-info">
            <h3 style="color: #e65100; margin-bottom: 20px;">⚠️ ドーシャ分析</h3>
    `;

    doshas.forEach(dosha => {
        html += `
            <div style="margin-bottom: 15px; padding: 15px; background: rgba(255,255,255,0.7); border-radius: 10px;">
                <h4 style="color: #d84315; margin-bottom: 8px;">${dosha.name}</h4>
                <p style="color: #424242; font-size: 0.9rem; line-height: 1.4;">${dosha.description}</p>
            </div>
        `;
    });

    html += `
        </div>
    `;

    return html;
}

function displayMarriageResults(data) {
    const resultsSection = document.getElementById('marriage-results-section');
    if (!resultsSection) return;

    const user = data.user;
    const results = data.results;

    const resultsHTML = `
        <div class="card fade-in-up" style="background: linear-gradient(135deg, rgba(255,235,238,0.95) 0%, rgba(248,249,250,0.95) 100%); border: 2px solid rgba(233, 30, 99, 0.3); padding: 25px; margin: 20px 0; border-radius: 15px;">
            <h3 style="color: #c2185b; text-align: center; margin-bottom: 25px; font-size: 22px;">💕 ${escapeHtml(user.name ? user.name + '様の' : '')}結婚運勢診断結果</h3>
            
            <!-- User Information -->
            <div style="background: rgba(255,255,255,0.8); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">📊 あなたの情報</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <p><strong>年齢:</strong> ${results.age}歳</p>
                    <p><strong>ナクシャトラ:</strong> ${results.nakshatra}</p>
                    <p><strong>ラーシ:</strong> ${results.rashi}</p>
                    <p><strong>月の経度:</strong> ${parseFloat(results.moon_longitude).toFixed(2)}°</p>
                </div>
                ${user.gender === 'other' ? '<p style="color: #666; font-size: 0.9rem; margin-top: 10px;"><em>※ その他の性別を選択されたため、結果は近似値となります</em></p>' : ''}
            </div>

            <!-- Marriage Possibility -->
            <div style="background: rgba(255,255,255,0.8); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">💖 結婚の可能性</h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #e91e63, #ad1457); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 1.4rem; font-weight: bold;">
                        ${results.marriage_possibility.probability}%
                    </div>
                </div>
                <p style="font-size: 1.1rem; text-align: center; margin-bottom: 10px;"><strong>${results.marriage_possibility.category}</strong></p>
                <ul style="color: #424242; line-height: 1.6;">
                    ${results.marriage_possibility.reasoning.map(reason => `<li>${reason}</li>`).join('')}
                </ul>
            </div>

            <!-- Marriage Timing -->
            <div style="background: rgba(255,255,255,0.8); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">⏰ 結婚時期の予測</h4>
                <p style="font-size: 1.1rem; margin-bottom: 10px;"><strong>予想年齢:</strong> ${results.marriage_timing.predicted_year}年 (${results.marriage_timing.age_range})</p>
                <p style="margin-bottom: 10px;"><strong>カテゴリー:</strong> ${results.marriage_timing.category}</p>
                <p style="color: #424242; line-height: 1.6;">${results.marriage_timing.explanation}</p>
            </div>

            <!-- Partner Suggestions -->
            <div style="background: rgba(255,255,255,0.8); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">💕 理想的なパートナー</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <p style="font-weight: 600; margin-bottom: 8px;">最適なナクシャトラ:</p>
                        <ul style="color: #424242;">
                            ${results.partner_suggestions.compatible_nakshatras.map(n => `<li>${n}</li>`).join('')}
                        </ul>
                    </div>
                    <div>
                        <p style="font-weight: 600; margin-bottom: 8px;">最適なラーシ:</p>
                        <ul style="color: #424242;">
                            ${results.partner_suggestions.compatible_rashis.map(r => `<li>${r}</li>`).join('')}
                        </ul>
                    </div>
                </div>
                <p style="color: #424242; line-height: 1.6;"><strong>アドバイス:</strong> ${results.partner_suggestions.advice}</p>
            </div>

            ${results.kuja_dosha ? `
            <!-- Kuja Dosha Analysis -->
            <div style="background: ${results.kuja_dosha.has_dosha ? 'rgba(255, 193, 7, 0.1)' : 'rgba(76, 175, 80, 0.1)'}; padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid ${results.kuja_dosha.has_dosha ? '#ffc107' : '#4caf50'};">
                <h4 style="color: ${results.kuja_dosha.has_dosha ? '#e65100' : '#2e7d32'}; margin-bottom: 15px;">🔴 クジャ・ドーシャ分析</h4>
                <p style="font-size: 1.1rem; margin-bottom: 10px;"><strong>状態:</strong> ${results.kuja_dosha.has_dosha ? 'クジャ・ドーシャあり' : 'クジャ・ドーシャなし'}</p>
                <p style="margin-bottom: 10px;"><strong>火星の位置:</strong> ${results.kuja_dosha.mars_position.house}室 (${results.kuja_dosha.mars_position.sign})</p>
                <p style="color: #424242; line-height: 1.6;">${results.kuja_dosha.explanation}</p>
                ${results.kuja_dosha.remedies ? `<p style="margin-top: 10px; color: #424242;"><strong>対処法:</strong> ${results.kuja_dosha.remedies}</p>` : ''}
            </div>
            ` : ''}

            ${results.seventh_house ? `
            <!-- 7th House Analysis -->
            <div style="background: rgba(255,255,255,0.8); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">🏠 第7室分析（結婚の室）</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <p><strong>7室主:</strong> ${results.seventh_house.lord}</p>
                    <p><strong>7室の星座:</strong> ${results.seventh_house.sign}</p>
                    <p><strong>支配惑星:</strong> ${results.seventh_house.ruling_planet}</p>
                </div>
                <p style="color: #424242; line-height: 1.6;">${results.seventh_house.analysis}</p>
            </div>
            ` : ''}

            ${results.karaka_analysis ? `
            <!-- Karaka Analysis -->
            <div style="background: rgba(255,255,255,0.8); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">⭐ カラカ分析（結婚の表示星）</h4>
                
                <!-- Primary Karaka -->
                <div style="margin-bottom: 15px; padding: 15px; background: rgba(233, 30, 99, 0.1); border-radius: 8px;">
                    <h5 style="color: #c2185b; margin-bottom: 10px;">主要カラカ: ${results.karaka_analysis.primary_karaka.planet}</h5>
                    <p style="margin-bottom: 8px;"><strong>強度:</strong> 
                        <span style="padding: 2px 8px; border-radius: 12px; font-size: 0.9rem; background: ${results.karaka_analysis.primary_karaka.strength.level === '強い' ? '#4caf50' : results.karaka_analysis.primary_karaka.strength.level === '中程度' ? '#ff9800' : '#f44336'}; color: white;">
                            ${results.karaka_analysis.primary_karaka.strength.level}
                        </span>
                    </p>
                    ${results.karaka_analysis.primary_karaka.strength.factors.length > 0 ? `
                        <div style="margin-top: 10px;">
                            ${results.karaka_analysis.primary_karaka.strength.factors.map(factor => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${factor}</p>`).join('')}
                        </div>
                    ` : ''}
                </div>

                <!-- Secondary Karaka -->
                <div style="margin-bottom: 15px; padding: 15px; background: rgba(26, 35, 126, 0.1); border-radius: 8px;">
                    <h5 style="color: #1a237e; margin-bottom: 10px;">補助カラカ: ${results.karaka_analysis.secondary_karaka.planet}</h5>
                    <p style="margin-bottom: 8px;"><strong>強度:</strong> 
                        <span style="padding: 2px 8px; border-radius: 12px; font-size: 0.9rem; background: ${results.karaka_analysis.secondary_karaka.strength.level === '強い' ? '#4caf50' : results.karaka_analysis.secondary_karaka.strength.level === '中程度' ? '#ff9800' : '#f44336'}; color: white;">
                            ${results.karaka_analysis.secondary_karaka.strength.level}
                        </span>
                    </p>
                    ${results.karaka_analysis.secondary_karaka.strength.factors.length > 0 ? `
                        <div style="margin-top: 10px;">
                            ${results.karaka_analysis.secondary_karaka.strength.factors.map(factor => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${factor}</p>`).join('')}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Overall Assessment -->
                <div style="background: #c8e6c9; padding: 15px; border-radius: 8px;">
                    <h5 style="color: #1b5e20; margin-bottom: 10px;">📊 総合評価</h5>
                    ${results.karaka_analysis.overall_assessment.map(assessment => `<p style="font-size: 0.95rem; color: #2e7d32; margin-bottom: 8px; font-weight: 500;">• ${assessment}</p>`).join('')}
                </div>
            </div>
            ` : ''}

            <!-- Delay Prediction -->
            ${results.delay_prediction ? `
            <div style="background: ${results.delay_prediction.has_delay ? 'rgba(255, 193, 7, 0.1)' : 'rgba(76, 175, 80, 0.1)'}; padding: 20px; border-radius: 12px; border-left: 4px solid ${results.delay_prediction.has_delay ? '#ffc107' : '#4caf50'};">
                <h4 style="color: ${results.delay_prediction.has_delay ? '#e65100' : '#2e7d32'}; margin-bottom: 15px;">⏳ 遅延の可能性</h4>
                <p style="font-size: 1.1rem; margin-bottom: 10px;"><strong>判定:</strong> ${results.delay_prediction.has_delay ? '遅延の可能性あり' : '順調な進展が期待される'}</p>
                <p style="color: #424242; line-height: 1.6; margin-bottom: 10px;">${results.delay_prediction.explanation}</p>
                ${results.delay_prediction.remedies ? `<p style="color: #424242; line-height: 1.6;"><strong>改善策:</strong> ${results.delay_prediction.remedies}</p>` : ''}
            </div>
            ` : ''}
        </div>
    `;

    resultsSection.innerHTML = resultsHTML;
    resultsSection.style.display = 'block';
}

// === Main Initialization ===
document.addEventListener('DOMContentLoaded', function() {
    // Load cities on page load for selected prefectures
    const groomPrefecture = document.querySelector('select[name="groom_prefecture"]')?.value;
    const bridePrefecture = document.querySelector('select[name="bride_prefecture"]')?.value;
    
    if (groomPrefecture) {
        loadCitiesForPerson('groom', groomPrefecture);
        setTimeout(() => {
            const groomCitySelect = document.getElementById('groom_city');
            const savedCity = document.querySelector('input[name="saved_groom_city"]')?.value;
            if (groomCitySelect && savedCity) {
                groomCitySelect.value = savedCity;
            }
        }, 500);
    }
    
    if (bridePrefecture) {
        loadCitiesForPerson('bride', bridePrefecture);
        setTimeout(() => {
            const brideCitySelect = document.getElementById('bride_city');
            const savedCity = document.querySelector('input[name="saved_bride_city"]')?.value;
            if (brideCitySelect && savedCity) {
                brideCitySelect.value = savedCity;
            }
        }, 500);
    }
    
    // Setup all functionality
    setupAutoCalculation();
    setupLineShare();
    setupPrivacyAgreement('privacyAgree', 'submitBtn');
    setupPrivacyAgreement('marriagePrivacyCheck', 'marriageSubmitBtn');
    setupPrivacyModal();
    
    // Setup scroll animations
    document.querySelectorAll('.fade-in-up').forEach(element => {
        element.style.animationPlayState = 'paused';
    });
    
    window.addEventListener('scroll', handleScrollAnimations);
    handleScrollAnimations();
    
    // Setup form submissions
    setupFormSubmission();
    setupMarriageFormSubmission();
});

// === Global Functions for HTML onclick handlers ===
window.loadCities = loadCitiesForPerson;
window.loadCitiesForMarriage = loadCitiesForMarriage;
window.shareLineGeneral = shareLineGeneral;