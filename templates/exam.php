<?php
/**
 * Exam interface template - Match Screenshot Design
 */

if (!defined('ABSPATH')) {
    exit;
}

$pack_id = intval($atts['id']);

if (!$pack_id && isset($_GET['pack_id'])) {
    $pack_id = intval($_GET['pack_id']);
}

$pack = TryOutHub_Database::get_pack($pack_id);

if (!$pack) {
    echo '<p>Tryout tidak ditemukan.</p>';
    return;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam - <?php echo esc_html($pack->title); ?></title>
    <style>
        /* Reset */
        * { margin: 0 !important; padding: 0 !important; box-sizing: border-box !important; }
        
        html, body {
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background: #f5f5f5 !important;
        }
        
        /* Hide WordPress elements */
        #wpadminbar, header, footer, .site-header, .site-footer,
        .entry-title, .page-title, h1.entry-title {
            display: none !important;
        }
        
        /* Exam Container */
        .tryouthub-exam-container {
            width: 100vw !important;
            height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            background: #f5f5f5 !important;
        }
        
        /* Header */
        .tryouthub-exam-header {
            background: white !important;
            padding: 1.25rem 2rem !important;
            border-bottom: 1px solid #e5e7eb !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        
        .tryouthub-exam-logo-section {
            display: flex !important;
            align-items: center !important;
            gap: 1.5rem !important;
        }
        
        .tryouthub-exam-logo {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #0070F9 !important;
            text-decoration: none !important;
        }
        
        .tryouthub-exam-logo-icon {
            width: 32px !important;
            height: 32px !important;
            background: linear-gradient(135deg, #0070F9, #0060d9) !important;
            border-radius: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1rem !important;
        }
        
        .tryouthub-exam-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
        }
        
        /* Body */
        .tryouthub-exam-body {
            flex: 1 !important;
            display: flex !important;
            overflow: hidden !important;
        }
        
        /* Content Area (Left) */
        .tryouthub-exam-content {
            flex: 1 !important;
            padding: 2rem !important;
            overflow-y: auto !important;
            background: #f5f5f5 !important;
        }
        
        .tryouthub-question-wrapper {
            max-width: 900px !important;
            background: white !important;
            border-radius: 1rem !important;
            padding: 2rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
        }
        
        /* Question Header */
        .tryouthub-question-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 2rem !important;
        }
        
        .tryouthub-question-info {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }
        
        .tryouthub-question-label {
            font-size: 1rem !important;
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }
        
        .tryouthub-question-number-large {
            font-size: 2rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }
        
        .tryouthub-category-badge {
            padding: 0.5rem 1rem !important;
            background: #bae6fd !important;
            color: #0369a1 !important;
            border-radius: 2rem !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
        }
        
        .tryouthub-timer-box {
            padding: 0.75rem 1rem !important;
            background: #fef3c7 !important;
            border-radius: 0.5rem !important;
        }
        
        .tryouthub-timer-label {
            color: #92400e !important;
            font-weight: 500 !important;
            font-size: 0.85rem !important;
        }
        
        .tryouthub-timer-value {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #16a34a !important;
            margin-left: 0.5rem !important;
        }
        
        .timer-warning .tryouthub-timer-value { color: #f59e0b !important; }
        .timer-critical .tryouthub-timer-value { color: #ef4444 !important; }
        
        /* Question Content */
        .tryouthub-question-content {
            font-size: 1rem !important;
            line-height: 1.8 !important;
            margin-bottom: 1.5rem !important;
            color: #1e293b !important;
        }
        
        /* Options */
        .tryouthub-options {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.875rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        .tryouthub-option {
            display: flex !important;
            align-items: flex-start !important;
            padding: 1rem !important;
            background: white !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
        }
        
        .tryouthub-option:hover {
            background: #fafafa !important;
            border-color: #cbd5e1 !important;
        }
        
        .tryouthub-option.selected {
            background: #dbeafe !important;
            border-color: #0070F9 !important;
        }
        
        .tryouthub-option-label {
            width: 40px !important;
            height: 40px !important;
            background: white !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 700 !important;
            margin-right: 1rem !important;
            flex-shrink: 0 !important;
            font-size: 1rem !important;
            color: #64748b !important;
        }
        
        .tryouthub-option.selected .tryouthub-option-label {
            background: #0070F9 !important;
            border-color: #0070F9 !important;
            color: white !important;
        }
        
        .tryouthub-option-text {
            flex: 1 !important;
            font-size: 0.95rem !important;
            line-height: 1.6 !important;
            color: #334155 !important;
        }
        
        /* Bottom Navigation */
        .tryouthub-exam-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 1rem !important;
            padding-top: 1.5rem !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        
        .tryouthub-btn {
            padding: 0.75rem 1.5rem !important;
            border: none !important;
            border-radius: 0.625rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            text-decoration: none !important;
            font-size: 0.95rem !important;
        }
        
        .tryouthub-btn:disabled {
            opacity: 0.3 !important;
            cursor: not-allowed !important;
        }
        
        .tryouthub-btn-back {
            background: #f1f5f9 !important;
            color: #334155 !important;
        }
        
        .tryouthub-btn-back:hover:not(:disabled) {
            background: #e2e8f0 !important;
        }
        
        .tryouthub-btn-next {
            background: linear-gradient(135deg, #0070F9, #0060d9) !important;
            color: white !important;
        }
        
        .tryouthub-btn-next:hover:not(:disabled) {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0,112,249,0.3) !important;
        }
        
        .tryouthub-doubt-wrapper {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        
        .tryouthub-doubt-checkbox {
            width: 18px !important;
            height: 18px !important;
            cursor: pointer !important;
            accent-color: #f59e0b !important;
        }
        
        .tryouthub-doubt-label {
            color: #64748b !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            font-size: 0.9rem !important;
        }
        
        /* Sidebar (Right) */
        .tryouthub-exam-sidebar {
            width: 320px !important;
            background: white !important;
            padding: 2rem 1.5rem !important;
            overflow-y: auto !important;
            border-left: 1px solid #e5e7eb !important;
        }
        
        .tryouthub-sidebar-title {
            font-weight: 700 !important;
            margin-bottom: 1.25rem !important;
            color: #1a1a1a !important;
            font-size: 1.1rem !important;
        }
        
        /* Question Grid */
        .tryouthub-question-grid {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 0.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        .tryouthub-question-number {
            aspect-ratio: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            font-size: 0.9rem !important;
        }
        
        .tryouthub-question-number.unanswered {
            background: #e5e7eb !important;
            color: #94a3b8 !important;
        }
        
        .tryouthub-question-number.answered {
            background: #d1fae5 !important;
            color: #16a34a !important;
        }
        
        .tryouthub-question-number.doubt {
            background: #fed7aa !important;
            color: #ea580c !important;
        }
        
        .tryouthub-question-number.current {
            outline: 2px solid #0070F9 !important;
            outline-offset: 2px !important;
        }
        
        .tryouthub-question-number:hover {
            transform: scale(1.08) !important;
        }
        
        /* Legend */
        .tryouthub-legend {
            margin-bottom: 1.5rem !important;
        }
        
        .tryouthub-legend-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.625rem !important;
            margin-bottom: 0.625rem !important;
        }
        
        .tryouthub-legend-color {
            width: 24px !important;
            height: 24px !important;
            border-radius: 0.375rem !important;
            flex-shrink: 0 !important;
        }
        
        .tryouthub-legend-text {
            font-size: 0.85rem !important;
            color: #64748b !important;
        }
        
        .tryouthub-btn-finish {
            width: 100% !important;
            padding: 0.875rem !important;
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.625rem !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            font-size: 0.95rem !important;
        }
        
        .tryouthub-btn-finish:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(239,68,68,0.3) !important;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .tryouthub-exam-sidebar {
                width: 280px !important;
            }
            
            .tryouthub-question-grid {
                grid-template-columns: repeat(4, 1fr) !important;
            }
        }
        
        @media (max-width: 768px) {
            .tryouthub-exam-body {
                flex-direction: column !important;
            }
            
            .tryouthub-exam-sidebar {
                width: 100% !important;
                border-left: none !important;
                border-top: 1px solid #e5e7eb !important;
                max-height: 40vh !important;
            }
        }
    </style>
</head>
<body>

<div class="tryouthub-exam-container" id="tryouthub-exam">
    <!-- Header -->
    <div class="tryouthub-exam-header">
        <div class="tryouthub-exam-logo-section">
            <div class="tryouthub-exam-logo">
                <div class="tryouthub-exam-logo-icon">📚</div>
                TryOutHub
            </div>
            <div class="tryouthub-exam-title"><?php echo esc_html($pack->title); ?></div>
        </div>
    </div>
    
    <!-- Exam Body -->
    <div class="tryouthub-exam-body" id="exam-interface">
        <!-- Content Area -->
        <div class="tryouthub-exam-content">
            <div class="tryouthub-question-wrapper" id="question-content">
                <div style="text-align: center !important; padding: 3rem !important; color: #94a3b8 !important;">
                    <div style="font-size: 2rem !important; margin-bottom: 1rem !important;">⏳</div>
                    <div>Loading question...</div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="tryouthub-exam-sidebar">
            <div class="tryouthub-sidebar-title">Nomor Soal</div>
            
            <!-- Question Grid -->
            <div class="tryouthub-question-grid" id="question-grid">
                <!-- Will be populated by JS -->
            </div>
            
            <!-- Legend -->
            <div class="tryouthub-legend">
                <div class="tryouthub-legend-item">
                    <div class="tryouthub-legend-color" style="background: #d1fae5 !important;"></div>
                    <span class="tryouthub-legend-text">Dijawab</span>
                </div>
                <div class="tryouthub-legend-item">
                    <div class="tryouthub-legend-color" style="background: #fed7aa !important;"></div>
                    <span class="tryouthub-legend-text">Ragu</span>
                </div>
                <div class="tryouthub-legend-item">
                    <div class="tryouthub-legend-color" style="background: #e5e7eb !important;"></div>
                    <span class="tryouthub-legend-text">Belum</span>
                </div>
            </div>
            
            <button class="tryouthub-btn-finish" id="finish-exam-btn">
                Selesai
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let examData = {
        attemptId: null,
        packId: <?php echo $pack_id; ?>,
        questions: [],
        currentIndex: 0,
        answers: {},
        doubts: {},
        endTime: null,
        timerInterval: null,
    };

    // Start exam immediately (no loading screen)
    function startExam() {
        fetch('<?php echo rest_url('tryouthub/v1/start'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>',
            },
            body: JSON.stringify({
                pack_id: examData.packId,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                examData.attemptId = data.data.attempt_id;
                examData.questions = data.data.questions;
                examData.endTime = data.data.end_time;
                
                examData.questions.forEach(q => {
                    if (q.selected) {
                        examData.answers[q.id] = q.selected;
                    }
                });
                
                renderQuestionGrid();
                renderQuestion(0);
                startTimer();
            } else {
                alert('Gagal memulai tryout');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }

    function renderQuestionGrid() {
        const grid = document.getElementById('question-grid');
        grid.innerHTML = '';
        
        examData.questions.forEach((q, index) => {
            const hasAnswer = examData.answers[q.id];
            const isDoubt = examData.doubts[q.id];
            const isCurrent = index === examData.currentIndex;
            
            const btn = document.createElement('div');
            btn.className = 'tryouthub-question-number';
            
            if (isDoubt) {
                btn.classList.add('doubt');
            } else if (hasAnswer) {
                btn.classList.add('answered');
            } else {
                btn.classList.add('unanswered');
            }
            
            if (isCurrent) btn.classList.add('current');
            
            btn.textContent = index + 1;
            btn.onclick = () => renderQuestion(index);
            
            grid.appendChild(btn);
        });
    }

    function renderQuestion(index) {
        examData.currentIndex = index;
        const question = examData.questions[index];
        const selectedAnswer = examData.answers[question.id];
        const isDoubt = examData.doubts[question.id];
        
        const html = `
            <div class="tryouthub-question-header">
                <div class="tryouthub-question-info">
                    <span class="tryouthub-question-label">Soal No.</span>
                    <span class="tryouthub-question-number-large">${index + 1}</span>
                    <span class="tryouthub-category-badge">${question.category || 'Penalaran Umum'}</span>
                </div>
                <div class="tryouthub-timer-box" id="timer-box">
                    <span class="tryouthub-timer-label">Sisa Waktu:</span>
                    <span class="tryouthub-timer-value" id="timer-display">03:15:00</span>
                </div>
            </div>
            
            <div class="tryouthub-question-content">
                ${question.content}
            </div>
            
            <div class="tryouthub-options">
                <div class="tryouthub-option ${selectedAnswer === 'A' ? 'selected' : ''}" data-option="A">
                    <div class="tryouthub-option-label">A</div>
                    <div class="tryouthub-option-text">${question.option_a}</div>
                </div>
                <div class="tryouthub-option ${selectedAnswer === 'B' ? 'selected' : ''}" data-option="B">
                    <div class="tryouthub-option-label">B</div>
                    <div class="tryouthub-option-text">${question.option_b}</div>
                </div>
                <div class="tryouthub-option ${selectedAnswer === 'C' ? 'selected' : ''}" data-option="C">
                    <div class="tryouthub-option-label">C</div>
                    <div class="tryouthub-option-text">${question.option_c}</div>
                </div>
                <div class="tryouthub-option ${selectedAnswer === 'D' ? 'selected' : ''}" data-option="D">
                    <div class="tryouthub-option-label">D</div>
                    <div class="tryouthub-option-text">${question.option_d}</div>
                </div>
                <div class="tryouthub-option ${selectedAnswer === 'E' ? 'selected' : ''}" data-option="E">
                    <div class="tryouthub-option-label">E</div>
                    <div class="tryouthub-option-text">${question.option_e}</div>
                </div>
            </div>
            
            <div class="tryouthub-exam-footer">
                <button class="tryouthub-btn tryouthub-btn-back" id="prev-btn" ${index === 0 ? 'disabled' : ''}>
                    ← Sebelumnya
                </button>
                
                <div class="tryouthub-doubt-wrapper">
                    <input type="checkbox" id="doubt-checkbox" class="tryouthub-doubt-checkbox" ${isDoubt ? 'checked' : ''}>
                    <label for="doubt-checkbox" class="tryouthub-doubt-label">Ragu-ragu</label>
                </div>
                
                <button class="tryouthub-btn tryouthub-btn-next" id="next-btn" ${index === examData.questions.length - 1 ? 'disabled' : ''}>
                    Selanjutnya →
                </button>
            </div>
        `;
        
        document.getElementById('question-content').innerHTML = html;
        renderQuestionGrid();
        
        document.querySelectorAll('.tryouthub-option').forEach(opt => {
            opt.addEventListener('click', function() {
                const option = this.getAttribute('data-option');
                selectAnswer(question.id, option);
            });
        });
        
        const doubtCheckbox = document.getElementById('doubt-checkbox');
        if (doubtCheckbox) {
            doubtCheckbox.addEventListener('change', function() {
                toggleDoubt(question.id, this.checked);
            });
        }
        
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        
        if (prevBtn) prevBtn.addEventListener('click', () => renderQuestion(index - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => renderQuestion(index + 1));
    }

    let saveTimeout;
    function selectAnswer(questionId, option) {
        examData.answers[questionId] = option;
        
        document.querySelectorAll('.tryouthub-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        const selectedOpt = document.querySelector(`.tryouthub-option[data-option="${option}"]`);
        if (selectedOpt) {
            selectedOpt.classList.add('selected');
        }
        
        renderQuestionGrid();
        
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveAnswer(questionId, option);
        }, 500);
    }

    function toggleDoubt(questionId, isDoubt) {
        if (isDoubt) {
            examData.doubts[questionId] = true;
        } else {
            delete examData.doubts[questionId];
        }
        renderQuestionGrid();
    }

    function saveAnswer(questionId, option) {
        fetch('<?php echo rest_url('tryouthub/v1/answer'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>',
            },
            body: JSON.stringify({
                attempt_id: examData.attemptId,
                question_id: questionId,
                selected_option: option,
            }),
        });
    }

    function startTimer() {
        updateTimerDisplay();
        
        examData.timerInterval = setInterval(() => {
            updateTimerDisplay();
            
            const remaining = examData.endTime - Math.floor(Date.now() / 1000);
            const timerBox = document.getElementById('timer-box');
            
            if (remaining === 300) {
                alert('⚠️ Waktu tersisa 5 menit!');
                if (timerBox) timerBox.classList.add('timer-warning');
            }
            
            if (remaining === 60) {
                alert('⚠️ Waktu tersisa 1 menit!');
                if (timerBox) {
                    timerBox.classList.remove('timer-warning');
                    timerBox.classList.add('timer-critical');
                }
            }
            
            if (remaining <= 0) {
                finishExam(true);
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const remaining = Math.max(0, examData.endTime - Math.floor(Date.now() / 1000));
        const hours = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;
        
        const display = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        const timerEl = document.getElementById('timer-display');
        if (timerEl) {
            timerEl.textContent = display;
        }
    }

    setTimeout(() => {
        const finishBtn = document.getElementById('finish-exam-btn');
        if (finishBtn) {
            finishBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menyelesaikan tryout?')) {
                    finishExam(false);
                }
            });
        }
    }, 1000);

    function finishExam(autoSubmit) {
        clearInterval(examData.timerInterval);
        
        fetch('<?php echo rest_url('tryouthub/v1/finish'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>',
            },
            body: JSON.stringify({
                attempt_id: examData.attemptId,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const result = data.data;
                alert(`Tryout selesai!\n\nSkor: ${result.score}\nBenar: ${result.correct}\nSalah: ${result.wrong}\nTidak Dijawab: ${result.unanswered}`);
                window.location.href = '<?php echo home_url('/app#tryout'); ?>';
            }
        });
    }

    startExam();
})();
</script>

</body>
</html>
<?php
exit();
?>