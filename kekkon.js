// Load cities for selected prefecture
function loadCities(person, prefecture) {
    const citySelect = document.getElementById(person + '_city');
    
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
            
            // Add event listener for auto-calculation when city is selected
            citySelect.removeEventListener('change', citySelect.calcHandler);
            citySelect.calcHandler = function() { checkAutoCalculation(person); };
            citySelect.addEventListener('change', citySelect.calcHandler);
        })
        .catch(error => {
            console.error('市区町村の読み込みエラー:', error);
            citySelect.innerHTML = '<option value="">市区町村の読み込みに失敗しました</option>';
        });
}

// Load cities for marriage prediction form
function loadCitiesForMarriage(prefecture) {
    const citySelect = document.getElementById('user_city');
    
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
        })
        .catch(error => {
            console.error('市区町村の読み込みエラー:', error);
            citySelect.innerHTML = '<option value="">市区町村の読み込みに失敗しました</option>';
        });
}

// Check form completion (nakshatra/rashi calculated on form submission)
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

// Calculate Nakshatra and Rashi via AJAX (for results display only)
function calculateAstrology(person, date, time, prefecture, city) {
    // ナクシャトラとラーシは結果ページでのみ表示されます
    console.log(`${person}の占星術データは結果計算時に処理されます`);
}

// Setup event listeners for auto-calculation
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

// Load cities on page load for selected prefectures
document.addEventListener('DOMContentLoaded', function() {
    const groomPrefecture = document.querySelector('select[name="groom_prefecture"]').value;
    const bridePrefecture = document.querySelector('select[name="bride_prefecture"]').value;
    
    if (groomPrefecture) {
        loadCities('groom', groomPrefecture);
        setTimeout(() => {
            const groomCityValue = document.querySelector('input[name="groom_city_value"]')?.value || '';
            if (groomCityValue) {
                document.getElementById('groom_city').value = groomCityValue;
            }
        }, 500);
    }
    
    if (bridePrefecture) {
        loadCities('bride', bridePrefecture);
        setTimeout(() => {
            const brideCityValue = document.querySelector('input[name="bride_city_value"]')?.value || '';
            if (brideCityValue) {
                document.getElementById('bride_city').value = brideCityValue;
            }
        }, 500);
    }
    
    // Setup auto-calculation listeners
    setupAutoCalculation();
    
    // Setup LINE share for mobile browsers
    setupLineShare();
    
    // Setup privacy agreement checkbox
    setupPrivacyAgreement();
    
    // Setup marriage privacy agreement checkbox
    setupMarriagePrivacyAgreement();
    
    // Setup privacy policy modal
    setupPrivacyModal();
    
    // Set initial state for fade-in elements
    document.querySelectorAll('.fade-in-up').forEach(element => {
        element.style.animationPlayState = 'paused';
    });
    
    // Handle scroll events
    window.addEventListener('scroll', handleScrollAnimations);
    
    // Trigger animations on page load
    handleScrollAnimations();
    
    // Setup AJAX form submission
    setupFormSubmission();
    setupMarriageFormSubmission();
});

// Mobile detection and LINE share setup
function setupLineShare() {
    // Detect mobile browsers
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        const lineSection = document.getElementById('lineShareSection');
        if (lineSection) {
            lineSection.style.display = 'block';
            
            // Setup LINE share button
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
    // Get current results for sharing
    const url = window.location.href;
    
    // Create share message
    let message = '🔮 ヴェーダ・タミル式ホロスコープ相性診断の結果 ✨\n\n';
    
    // Get compatibility scores if available
    const veddicScore = document.querySelector('.score-circle span');
    if (veddicScore) {
        message += `📊 相性スコア: ${veddicScore.textContent}\n`;
    }
    
    // Get compatibility level
    const compatLevel = document.querySelector('.compatibility-score h2');
    if (compatLevel) {
        message += `💫 判定: ${compatLevel.textContent}\n`;
    }
    
    message += '\n古代インドの智慧で相性を診断してみませんか？\n';
    message += url;
    
    // Encode message for LINE
    const encodedMessage = encodeURIComponent(message);
    
    // Create LINE share URL
    const lineUrl = `https://line.me/R/msg/text/?${encodedMessage}`;
    
    // Open LINE share
    window.open(lineUrl, '_blank');
}

function shareLineGeneral() {
    // Get current URL
    const url = window.location.href;
    
    // Create general share message for the app
    let message = '🔮 ヴェーダ・南インド式ホロスコープ相性診断 ✨\n\n';
    message += '古代インドの智慧で相性を無料診断！\n';
    message += '北インド式と南インド式の両方で詳しく分析してくれます 📊\n\n';
    message += '💕 結婚相性やお付き合いの参考にどうぞ\n';
    message += '🌟 ナクシャトラ・ラーシ・グナ分析\n';
    message += '🏛️ 10ポルタム分析（南インド式）\n\n';
    message += 'あなたも試してみませんか？\n';
    message += url;
    
    // Encode message for LINE
    const encodedMessage = encodeURIComponent(message);
    
    // Create LINE share URL
    const lineUrl = `https://line.me/R/msg/text/?${encodedMessage}`;
    
    // Open LINE share
    window.open(lineUrl, '_blank');
}

// Privacy agreement checkbox handler
function setupPrivacyAgreement() {
    const privacyCheckbox = document.getElementById('privacyAgree');
    const submitButton = document.getElementById('submitBtn');
    
    console.log('Setting up privacy agreement:', {privacyCheckbox, submitButton});
    
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

// Marriage form privacy agreement checkbox handler
function setupMarriagePrivacyAgreement() {
    const marriagePrivacyCheckbox = document.getElementById('marriagePrivacyCheck');
    const marriageSubmitButton = document.getElementById('marriageSubmitBtn');
    
    console.log('Setting up marriage privacy agreement:', {marriagePrivacyCheckbox, marriageSubmitButton});
    
    if (marriagePrivacyCheckbox && marriageSubmitButton) {
        marriagePrivacyCheckbox.addEventListener('change', function() {
            if (this.checked) {
                marriageSubmitButton.disabled = false;
                marriageSubmitButton.style.opacity = '1';
                marriageSubmitButton.style.cursor = 'pointer';
            } else {
                marriageSubmitButton.disabled = true;
                marriageSubmitButton.style.opacity = '0.5';
                marriageSubmitButton.style.cursor = 'not-allowed';
            }
        });
        
        // Initial state
        marriageSubmitButton.style.opacity = '0.5';
        marriageSubmitButton.style.cursor = 'not-allowed';
    }
}

// Privacy policy modal setup
function setupPrivacyModal() {
    const privacyLink = document.getElementById('privacyPolicyLink');
    const marriagePrivacyLink = document.getElementById('marriagePrivacyLink');
    const modal = document.getElementById('privacyModal');
    const closeBtn = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    // Function to open modal
    function openModal(e) {
        e.preventDefault();
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }
    
    // Setup both privacy links
    if (privacyLink) {
        privacyLink.addEventListener('click', openModal);
    }
    
    if (marriagePrivacyLink) {
        marriagePrivacyLink.addEventListener('click', openModal);
    }
    
    if (modal) {
        
        // Close modal functions
        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
        
        // Close with X button
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
        
        // Close with close button
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }
        
        // Close when clicking outside the modal
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
}

// Marriage form privacy policy modal setup
function setupMarriagePrivacyModal() {
    const marriagePrivacyLink = document.getElementById('marriagePrivacyLink');
    const modal = document.getElementById('privacyModal');
    const closeBtn = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    if (marriagePrivacyLink && modal) {
        // Open modal
        marriagePrivacyLink.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
        
        // Close modal functions (reuse existing functions)
        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
        
        // Close with X button
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
        
        // Close with close button
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }
        
        // Close when clicking outside the modal
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
}

// Scroll animation for fade-in-up elements
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

// AJAX form submission setup
function setupFormSubmission() {
    const form = document.getElementById('horoscopeForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show cosmic loader
        showCosmicLoader();
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch('compatibility_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideCosmicLoader();
            
            console.log('AJAX Response:', data); // Debug log
            
            if (data.success) {
                displayResults(data);
                // Scroll to results
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

function displayResults(data) {
    const resultsSection = document.getElementById('results-section');
    if (!resultsSection) return;

    const results = data.results;
    const groom = data.groom;
    const bride = data.bride;

    // Build results HTML with beautiful design
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

            <!-- LINE Share Button for Mobile -->
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
    
    // Setup LINE share for mobile after results are displayed
    setupLineShare();
}

function buildVedicSection(gunas) {
    // Calculate totals
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
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 12px;">
    `;

    if (tamil.poruthams && Array.isArray(tamil.poruthams)) {
        tamil.poruthams.forEach(porutham => {
            const emoji = porutham.score ? "✅" : "❌";
            const borderColor = porutham.score ? '#4caf50' : '#f44336';
            
            html += `
                <div class="explanation-card" style="border-left-color: ${borderColor};">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-weight: 600; color: #e65100; font-size: 0.9rem;">${emoji} ${porutham.name}</span>
                        <span style="font-weight: bold; color: #ff9800; font-size: 0.9rem;">${porutham.score}/1</span>
                    </div>
                    <div style="color: #424242; font-size: 0.8rem; line-height: 1.3;">
                        ${porutham.description}
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

function buildDoshaSection(doshas) {
    let html = `
        <div class="dosha-info card fade-in-up">
            <h4>🔍 ドーシャ分析</h4>
    `;

    doshas.forEach(dosha => {
        html += `<p style="margin-top: 10px;">${dosha}</p>`;
    });

    html += `
        </div>
    `;

    return html;
}

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

function getCompatibilityLevel(percentage) {
    if (percentage >= 75) return "優秀";
    if (percentage >= 60) return "非常に良い";
    if (percentage >= 45) return "平均";
    return "注意が必要";
}

// Marriage form AJAX submission setup
function setupMarriageFormSubmission() {
    const marriageForm = document.getElementById('marriageForm');
    if (!marriageForm) return;

    marriageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show cosmic loader
        showCosmicLoader();
        
        // Prepare form data
        const formData = new FormData(marriageForm);
        formData.append('action', 'predict_marriage');
        
        // Send AJAX request to api.php
        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideCosmicLoader();
            
            console.log('Marriage Prediction Response:', data); // Debug log
            
            if (data.success) {
                displayMarriageResults(data);
                // Scroll to results
                setTimeout(() => {
                    document.getElementById('marriage-results-section').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            } else {
                showMarriageError(data.error || '結婚運勢の計算中にエラーが発生しました');
            }
        })
        .catch(error => {
            hideCosmicLoader();
            console.error('Marriage Prediction Error:', error);
            showMarriageError('通信エラーが発生しました。もう一度お試しください。');
        });
    });
}

function displayMarriageResults(data) {
    const resultsSection = document.getElementById('marriage-results-section');
    if (!resultsSection) return;

    const userData = data.user_data;
    const prediction = data.marriage_prediction;
    const timing = data.timing_prediction;
    const partner = data.partner_suggestions;
    const delay = data.delay_prediction;
    const kujaDosha = data.kuja_dosha;
    const seventhHouse = data.seventh_house;
    const karakaAnalysis = data.karaka_analysis;
    const genderNote = data.gender_note;

    // Build marriage results HTML
    const resultsHTML = `
        <div style="margin-top: 30px; padding: 25px; background: rgba(255,255,255,0.9); border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #c2185b; text-align: center; margin-bottom: 20px;">🔮 あなたの結婚運勢</h3>
            
            ${genderNote ? `
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <p style="color: #856404; margin: 0; font-size: 0.9rem;">
                        ⚠️ ${escapeHtml(genderNote)}
                    </p>
                </div>
            ` : ''}
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <!-- User Info -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #e91e63;">
                    <h4 style="color: #c2185b; margin-bottom: 15px;">📊 あなたの星座情報</h4>
                    ${userData.name ? `<p><strong>お名前:</strong> ${escapeHtml(userData.name)}様</p>` : ''}
                    <p><strong>現在の年齢:</strong> ${userData.current_age}歳</p>
                    <p><strong>ナクシャトラ:</strong> ${escapeHtml(userData.nakshatra)}</p>
                    <p><strong>ラーシ:</strong> ${escapeHtml(userData.rashi)}</p>
                    <p><strong>月の経度:</strong> ${parseFloat(userData.moonLon).toFixed(2)}°</p>
                </div>
                
                <!-- Marriage Prediction -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #4caf50;">
                    <h4 style="color: #388e3c; margin-bottom: 15px;">💕 結婚の可能性</h4>
                    <p><strong>結婚できますか？</strong> ${prediction.will_marry}</p>
                    <p><strong>確率:</strong> ${prediction.probability_percentage}%</p>
                    <div style="margin-top: 10px;">
                        ${prediction.reasoning.map(reason => `<p style="font-size: 0.9rem; color: #666;">• ${escapeHtml(reason)}</p>`).join('')}
                    </div>
                </div>
            </div>
            
            <!-- Marriage Timing -->
            <div style="background: #e3f2fd; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #2196f3;">
                <h4 style="color: #1976d2; margin-bottom: 15px;">⏰ 結婚時期の予測</h4>
                <p><strong>予測:</strong> ${escapeHtml(timing.message)}</p>
                <p><strong>推奨年齢:</strong> ${timing.age_range}</p>
                <p><strong>カテゴリー:</strong> ${timing.description}</p>
            </div>
            
            <!-- Partner Suggestions -->
            <div style="background: #fff3e0; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #ff9800;">
                <h4 style="color: #f57c00; margin-bottom: 15px;">🌟 理想的なパートナー</h4>
                <p><strong>相性の良いナクシャトラ:</strong> ${partner.best_nakshatra_matches.join(", ")}</p>
                <p><strong>相性の良いラーシ:</strong> ${partner.best_rashi_matches.join(", ")}</p>
                <div style="margin-top: 15px; padding: 15px; background: rgba(255,255,255,0.7); border-radius: 8px;">
                    <p style="font-style: italic; color: #424242;">${escapeHtml(partner.recommendations.advice)}</p>
                </div>
            </div>
            
            <!-- Delay Prediction -->
            <div style="background: #fce4ec; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #e91e63;">
                <h4 style="color: #c2185b; margin-bottom: 15px;">⚠️ 結婚の遅れについて</h4>
                <p>${escapeHtml(delay.message)}</p>
                <div style="margin-top: 15px;">
                    <h5 style="color: #ad1457; margin-bottom: 10px;">🙏 改善のための対策:</h5>
                    ${delay.remedies.map(remedy => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${escapeHtml(remedy)}</p>`).join('')}
                </div>
            </div>
            
            <!-- Kuja Dosha Analysis -->
            <div style="background: ${kujaDosha.present ? '#ffebee' : '#e8f5e8'}; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid ${kujaDosha.present ? '#f44336' : '#4caf50'};">
                <h4 style="color: ${kujaDosha.present ? '#d32f2f' : '#388e3c'}; margin-bottom: 15px;">
                    ${kujaDosha.present ? '🔥 マンガリク・ドーシャ分析' : '✅ マンガリク・ドーシャ分析'}
                </h4>
                <p><strong>結果:</strong> ${kujaDosha.present ? 'マンガリク・ドーシャが検出されました' : 'マンガリク・ドーシャは検出されません'}</p>
                ${kujaDosha.present ? `<p><strong>重要度:</strong> ${kujaDosha.severity === 'high' ? '高' : kujaDosha.severity === 'moderate' ? '中' : '軽'}</p>` : ''}
                <p><strong>火星の位置:</strong> ${escapeHtml(kujaDosha.mars_rashi)} (${parseFloat(kujaDosha.mars_longitude).toFixed(2)}°)</p>
                ${kujaDosha.description ? `<p><strong>説明:</strong> ${escapeHtml(kujaDosha.description)}</p>` : ''}
                <p style="margin-top: 10px; font-weight: 600; color: ${kujaDosha.present ? '#d32f2f' : '#388e3c'};">${escapeHtml(kujaDosha.marriage_impact)}</p>
                ${kujaDosha.remedies && kujaDosha.remedies.length > 0 ? `
                    <div style="margin-top: 15px; padding: 15px; background: rgba(255,255,255,0.8); border-radius: 8px;">
                        <h5 style="color: #ad1457; margin-bottom: 10px;">🙏 マンガリク・ドーシャの改善策:</h5>
                        ${kujaDosha.remedies.map(remedy => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${escapeHtml(remedy)}</p>`).join('')}
                    </div>
                ` : ''}
            </div>
            
            <!-- 7th House Analysis -->
            <div style="background: #f3e5f5; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #9c27b0;">
                <h4 style="color: #7b1fa2; margin-bottom: 15px;">🏠 第7室（結婚の部屋）分析</h4>
                <p><strong>ラグナ（上昇宮）:</strong> ${escapeHtml(seventhHouse.ascendant_rashi)} (${seventhHouse.ascendant_degree}°)</p>
                <p><strong>第7室の星座:</strong> ${escapeHtml(seventhHouse.seventh_house_sign)}</p>
                <p><strong>第7室の支配星:</strong> ${escapeHtml(seventhHouse.seventh_house_lord)}</p>
                <p><strong>結婚運スコア:</strong> ${seventhHouse.house_strength.score}/100 
                    <span style="color: ${seventhHouse.house_strength.rating === 'excellent' ? '#4caf50' : seventhHouse.house_strength.rating === 'good' ? '#8bc34a' : seventhHouse.house_strength.rating === 'fair' ? '#ff9800' : '#f44336'}; font-weight: bold;">
                        (${seventhHouse.house_strength.rating === 'excellent' ? '優秀' : seventhHouse.house_strength.rating === 'good' ? '良好' : seventhHouse.house_strength.rating === 'fair' ? '普通' : '要注意'})
                    </span>
                </p>
                
                <div style="margin-top: 15px;">
                    <h5 style="color: #7b1fa2; margin-bottom: 10px;">💫 結婚に関する洞察:</h5>
                    ${seventhHouse.marriage_insights.map(insight => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 8px;">• ${escapeHtml(insight)}</p>`).join('')}
                </div>
                
                <div style="margin-top: 15px;">
                    <h5 style="color: #7b1fa2; margin-bottom: 10px;">👤 理想的なパートナーの特徴:</h5>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        ${seventhHouse.spouse_characteristics.map(char => `<span style="background: rgba(156, 39, 176, 0.1); color: #7b1fa2; padding: 4px 8px; border-radius: 12px; font-size: 0.85rem;">${escapeHtml(char)}</span>`).join('')}
                    </div>
                </div>
                
                ${seventhHouse.house_strength.factors.length > 0 ? `
                    <div style="margin-top: 15px; padding: 15px; background: rgba(255,255,255,0.8); border-radius: 8px;">
                        <h5 style="color: #7b1fa2; margin-bottom: 10px;">📊 分析要因:</h5>
                        ${seventhHouse.house_strength.factors.map(factor => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${escapeHtml(factor)}</p>`).join('')}
                    </div>
                ` : ''}
            </div>
            
            <!-- Karaka Analysis -->
            <div style="background: #e8f5e8; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #4caf50;">
                <h4 style="color: #2e7d32; margin-bottom: 15px;">⭐ 結婚カラカ（表示体）分析</h4>
                
                <!-- Primary Karaka -->
                <div style="background: rgba(255,255,255,0.8); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #2e7d32; margin-bottom: 10px;">🌟 主要カラカ: ${escapeHtml(karakaAnalysis.primary_karaka.name)}</h5>
                    <p><strong>役割:</strong> ${escapeHtml(karakaAnalysis.primary_karaka.role)}</p>
                    <p><strong>位置:</strong> ${escapeHtml(karakaAnalysis.primary_karaka.position.rashi)} (${parseFloat(karakaAnalysis.primary_karaka.position.longitude).toFixed(2)}°)</p>
                    <p><strong>強度:</strong> ${karakaAnalysis.primary_karaka.strength.score}/100 
                        <span style="color: ${karakaAnalysis.primary_karaka.strength.rating === 'excellent' ? '#4caf50' : karakaAnalysis.primary_karaka.strength.rating === 'good' ? '#8bc34a' : karakaAnalysis.primary_karaka.strength.rating === 'fair' ? '#ff9800' : '#f44336'}; font-weight: bold;">
                            (${karakaAnalysis.primary_karaka.strength.rating === 'excellent' ? '優秀' : karakaAnalysis.primary_karaka.strength.rating === 'good' ? '良好' : karakaAnalysis.primary_karaka.strength.rating === 'fair' ? '普通' : '弱い'})
                        </span>
                    </p>
                    ${karakaAnalysis.primary_karaka.strength.factors.length > 0 ? `
                        <div style="margin-top: 10px;">
                            ${karakaAnalysis.primary_karaka.strength.factors.map(factor => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${escapeHtml(factor)}</p>`).join('')}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Secondary Karaka -->
                <div style="background: rgba(255,255,255,0.8); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #2e7d32; margin-bottom: 10px;">🌙 補助カラカ: ${escapeHtml(karakaAnalysis.secondary_karaka.name)}</h5>
                    <p><strong>役割:</strong> ${escapeHtml(karakaAnalysis.secondary_karaka.role)}</p>
                    <p><strong>位置:</strong> ${escapeHtml(karakaAnalysis.secondary_karaka.position.rashi)} (${parseFloat(karakaAnalysis.secondary_karaka.position.longitude).toFixed(2)}°)</p>
                    <p><strong>強度:</strong> ${karakaAnalysis.secondary_karaka.strength.score}/100 
                        <span style="color: ${karakaAnalysis.secondary_karaka.strength.rating === 'excellent' ? '#4caf50' : karakaAnalysis.secondary_karaka.strength.rating === 'good' ? '#8bc34a' : karakaAnalysis.secondary_karaka.strength.rating === 'fair' ? '#ff9800' : '#f44336'}; font-weight: bold;">
                            (${karakaAnalysis.secondary_karaka.strength.rating === 'excellent' ? '優秀' : karakaAnalysis.secondary_karaka.strength.rating === 'good' ? '良好' : karakaAnalysis.secondary_karaka.strength.rating === 'fair' ? '普通' : '弱い'})
                        </span>
                    </p>
                    ${karakaAnalysis.secondary_karaka.strength.factors.length > 0 ? `
                        <div style="margin-top: 10px;">
                            ${karakaAnalysis.secondary_karaka.strength.factors.map(factor => `<p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">• ${escapeHtml(factor)}</p>`).join('')}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Overall Assessment -->
                <div style="background: #c8e6c9; padding: 15px; border-radius: 8px;">
                    <h5 style="color: #1b5e20; margin-bottom: 10px;">📊 総合評価</h5>
                    ${karakaAnalysis.overall_assessment.map(assessment => `<p style="font-size: 0.95rem; color: #2e7d32; margin-bottom: 8px; font-weight: 500;">• ${escapeHtml(assessment)}</p>`).join('')}
                </div>
            </div>
        </div>
    `;

    resultsSection.innerHTML = resultsHTML;
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

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}