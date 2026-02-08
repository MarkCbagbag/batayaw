// ===== LOVE QUIZ GAME =====
const quizQuestions = [
    {
        question: "What's my favorite thing about you?",
        answers: ["Your smile", "Your laugh", "Your heart", "Everything ❤️"],
        correct: 3
    },
    {
        question: "How many reasons do I love you?",
        answers: ["5", "9", "Infinite ♾️", "More than stars"]
    },
    {
        question: "When do I love you most?",
        answers: ["In the morning", "At night", "Always 24/7", "Every moment"],
        correct: 2
    },
    {
        question: "What makes me happiest?",
        answers: ["Seeing you smile", "Holding you", "Being with you", "All of the above! 💕"],
        correct: 3
    },
    {
        question: "You are my...",
        answers: ["Friend", "Love", "Everything", "Forever 💑"],
        correct: 3
    }
];

let currentQuizQuestion = 0;
let quizScore = 0;

function startQuiz() {
    currentQuizQuestion = 0;
    quizScore = 0;
    document.getElementById('quizModal').style.display = 'block';
    showQuizQuestion();
}

function showQuizQuestion() {
    const quizContent = document.getElementById('quizContent');
    const question = quizQuestions[currentQuizQuestion];
    
    let html = `
        <div style="margin: 0; text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
            <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; gap: 1rem; width: 100%;">
                <p style="font-size: 0.95rem; color: #ff6b9d; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin: 0;">
                    Question ${currentQuizQuestion + 1} / ${quizQuestions.length}
                </p>
                <div style="width: 120px; height: 6px; border-radius: 3px; background: #ffe6e6; overflow: hidden;">
                    <div style="width: ${((currentQuizQuestion + 1) / quizQuestions.length) * 100}%; height: 100%; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); transition: width 0.3s ease;"></div>
                </div>
            </div>
            <h3 style="margin: 0; color: #2c3e50; font-size: 1.4rem; font-weight: 700; line-height: 1.6; width: 100%;">
                ${question.question}
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.8rem; width: 100%; align-items: center;">
    `;
    
    question.answers.forEach((answer, index) => {
        html += `
            <button class="quiz-answer-btn" onclick="answerQuiz(${index})" style="animation: slideUp 0.5s ease-out ${0.1 * index}s backwards;">
                <span style="margin-right: 0.5rem;">●</span> ${answer}
            </button>
        `;
    });
    
    html += `</div></div>`;
    quizContent.innerHTML = html;
}

function answerQuiz(index) {
    const question = quizQuestions[currentQuizQuestion];
    if (index === question.correct) {
        quizScore++;
    }
    
    currentQuizQuestion++;
    
    if (currentQuizQuestion < quizQuestions.length) {
        showQuizQuestion();
    } else {
        showQuizResult();
    }
}

function showQuizResult() {
    const quizContent = document.getElementById('quizContent');
    const percentage = (quizScore / quizQuestions.length) * 100;
    
    let message = '';
    let emoji = '';
    let celebration = '';
    
    if (percentage === 100) {
        message = "Perfect! You know me so well! 🥰";
        emoji = "🏆";
        celebration = "🎉🎊✨";
    } else if (percentage >= 80) {
        message = "Awesome! You really know me! 💕";
        emoji = "⭐";
        celebration = "🎉✨";
    } else if (percentage >= 60) {
        message = "Good job! You know quite a bit! 😊";
        emoji = "👍";
        celebration = "😊💫";
    } else {
        message = "No worries! Let's spend more time together! 💑";
        emoji = "💫";
        celebration = "💕💫";
    }
    
    quizContent.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem; animation: heartBeat 1s ease-in-out infinite;">${emoji}</div>
            <div style="font-size: 2rem; margin-bottom: 1rem; overflow: hidden; height: 40px;">
                <div style="animation: slideUp 0.6s ease-out;">${celebration}</div>
            </div>
            <h3 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--primary-color); font-weight: 800;">
                ${quizScore}/${quizQuestions.length} Correct!
            </h3>
            <div style="font-size: 2.5rem; font-weight: 900; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 1.5rem;">
                ${percentage.toFixed(0)}%
            </div>
            <p style="font-size: 1.1rem; margin-bottom: 2rem; color: #666;">
                ${message}
            </p>
            <button class="game-btn" onclick="startQuiz()" style="width: 100%; margin-top: 1rem;">
                🔄 Try Again
            </button>
        </div>
    `;
}

function closeQuiz() {
    document.getElementById('quizModal').style.display = 'none';
}

// ===== MEMORY MATCH GAME =====
const memoryCards = ['❤️', '💕', '💖', '💗', '💝', '💘', '💞', '💓'];
let memoryGameCards = [];
let flippedCards = [];
let matchedPairs = 0;
let moves = 0;

function startMemoryGame() {
    memoryGameCards = [];
    flippedCards = [];
    matchedPairs = 0;
    moves = 0;
    document.getElementById('memoryModal').style.display = 'block';
    initializeMemoryGame();
}

function initializeMemoryGame() {
    // Create pairs
    const cards = [...memoryCards, ...memoryCards];
    
    // Shuffle
    for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
    }
    
    memoryGameCards = cards;
    renderMemoryGrid();
}

function renderMemoryGrid() {
    const grid = document.getElementById('memoryGrid');
    grid.innerHTML = '';
    
    memoryGameCards.forEach((card, index) => {
        const cardEl = document.createElement('div');
        cardEl.className = 'memory-card';
        cardEl.onclick = () => flipMemoryCard(index);
        
        if (flippedCards.includes(index)) {
            cardEl.textContent = card;
            cardEl.classList.add('flipped');
        } else {
            cardEl.textContent = '?';
        }
        
        grid.appendChild(cardEl);
    });
    
    document.getElementById('memoryScore').textContent = `Moves: ${moves} | Matched: ${matchedPairs}/${memoryCards.length}`;
}

function flipMemoryCard(index) {
    if (flippedCards.includes(index) || flippedCards.length >= 2) return;
    
    flippedCards.push(index);
    renderMemoryGrid();
    
    if (flippedCards.length === 2) {
        moves++;
        const [first, second] = flippedCards;
        
        setTimeout(() => {
            if (memoryGameCards[first] === memoryGameCards[second]) {
                matchedPairs++;
                flippedCards = [];
                
                if (matchedPairs === memoryCards.length) {
                    celebrateMemoryWin();
                } else {
                    renderMemoryGrid();
                }
            } else {
                flippedCards = [];
                renderMemoryGrid();
            }
        }, 600);
    }
}

function celebrateMemoryWin() {
    setTimeout(() => {
        const memoryContent = document.getElementById('memoryModal').querySelector('.modal-content');
        const grid = document.getElementById('memoryGrid');
        
        // Disable all cards
        const cards = grid.querySelectorAll('.memory-card');
        cards.forEach(card => card.style.pointerEvents = 'none');
        
        // Show celebration message
        setTimeout(() => {
            alert(`🎉 Congratulations! You won in ${moves} moves! 💕\n\nYou're amazing at this game!`);
            resetMemoryGame();
        }, 300);
    }, 300);
}

function resetMemoryGame() {
    initializeMemoryGame();
}

function closeMemoryGame() {
    document.getElementById('memoryModal').style.display = 'none';
}

// ===== SCRATCH CARD GAME =====
const scratchMessages = [
    "You make me the happiest person alive! 💕",
    "I can't wait to spend forever with you! 💑",
    "You are my greatest blessing! ✨",
    "Every day with you is a gift! 🎁",
    "I love you more than words can express! 💗",
    "You're my best friend and soulmate! 👫",
    "Let's build beautiful memories together! 🌹",
    "You complete me in every way! 💖"
];

let currentScratchMessage = '';
let isDrawing = false;
let canvas = null;
let ctx = null;

function startScratchCard() {
    document.getElementById('scratchModal').style.display = 'block';
    setupScratchCard();
}

function setupScratchCard() {
    canvas = document.getElementById('scratchCanvas');
    ctx = canvas.getContext('2d');
    currentScratchMessage = scratchMessages[Math.floor(Math.random() * scratchMessages.length)];
    
    // Draw hidden message
    ctx.fillStyle = 'white';
    ctx.font = 'bold 20px Poppins';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    
    // Word wrap the text
    const maxWidth = 280;
    const lineHeight = 30;
    const lines = [];
    let currentLine = '';
    const words = currentScratchMessage.split(' ');
    
    words.forEach(word => {
        const testLine = currentLine + word + ' ';
        const metrics = ctx.measureText(testLine);
        if (metrics.width > maxWidth && currentLine) {
            lines.push(currentLine);
            currentLine = word + ' ';
        } else {
            currentLine = testLine;
        }
    });
    lines.push(currentLine);
    
    // Calculate starting Y position to center text
    const totalHeight = lines.length * lineHeight;
    let startY = (200 - totalHeight) / 2 + 20;
    
    lines.forEach((line, index) => {
        ctx.fillText(line, 150, startY + (index * lineHeight));
    });
    
    // Draw scratch surface with gradient
    const gradientSurface = ctx.createLinearGradient(0, 0, 300, 200);
    gradientSurface.addColorStop(0, 'rgba(255, 107, 157, 0.95)');
    gradientSurface.addColorStop(1, 'rgba(254, 202, 87, 0.95)');
    ctx.fillStyle = gradientSurface;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Add decorative elements
    ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
    ctx.font = 'italic 16px Poppins';
    ctx.textAlign = 'center';
    ctx.fillText('✨ Scratch me! ✨', canvas.width / 2, 30);
    ctx.fillText('← Scratch here →', canvas.width / 2, canvas.height - 20);
    
    // Mouse events
    canvas.addEventListener('mousemove', scratchCanvas);
    canvas.addEventListener('mousedown', () => isDrawing = true);
    canvas.addEventListener('mouseup', () => isDrawing = false);
    canvas.addEventListener('mouseout', () => isDrawing = false);
    
    // Touch events for mobile
    canvas.addEventListener('touchmove', scratchCanvasTouch);
    canvas.addEventListener('touchstart', () => isDrawing = true);
    canvas.addEventListener('touchend', () => isDrawing = false);
}

function scratchCanvas(e) {
    if (!isDrawing) return;
    
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    scratchArea(x, y);
}

function scratchCanvasTouch(e) {
    if (!isDrawing) return;
    
    const rect = canvas.getBoundingClientRect();
    const touch = e.touches[0];
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    
    scratchArea(x, y);
}

function scratchArea(x, y) {
    ctx.clearRect(x - 20, y - 20, 40, 40);
}

function closeScratchCard() {
    document.getElementById('scratchModal').style.display = 'none';
    if (canvas) {
        canvas.removeEventListener('mousemove', scratchCanvas);
        canvas.removeEventListener('touchmove', scratchCanvasTouch);
    }
}

// ===== LOVE CALCULATOR =====
function calculateLove() {
    document.getElementById('calcModal').style.display = 'block';
    
    // Simulate calculation
    const calcResult = document.getElementById('calcResult');
    calcResult.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div style="font-size: 1.5rem; margin-bottom: 2rem; animation: pulse 1.5s ease-in-out infinite;">
                Calculating our love... 💕
            </div>
            <div style="display: flex; justify-content: space-around; margin-bottom: 1.5rem;">
                <div style="animation: heartBeat 0.8s ease-in-out infinite;">❤️</div>
                <div style="animation: heartBeat 0.8s ease-in-out infinite; animation-delay: 0.2s;">💕</div>
                <div style="animation: heartBeat 0.8s ease-in-out infinite; animation-delay: 0.4s;">💖</div>
            </div>
        </div>
    `;
    
    setTimeout(() => {
        const lovePercentage = 89 + Math.floor(Math.random() * 12); // 89-100%
        const hearts = '❤️'.repeat(Math.floor(lovePercentage / 10));
        
        const messages = [
            "Our love is infinite! 💕",
            "Perfect match! 💑",
            "Meant to be together! 💞",
            "You complete me! 💗",
            "Forever and always! 💖"
        ];
        
        const message = messages[Math.floor(Math.random() * messages.length)];
        
        calcResult.innerHTML = `
            <div style="padding: 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem; animation: slideDown 0.6s ease-out;">${lovePercentage}%</div>
                <div style="font-size: 2.5rem; margin-bottom: 1.5rem; animation: slideUp 0.6s ease-out 0.2s backwards;">
                    ${hearts}
                </div>
                <p style="font-size: 1.2rem; color: var(--primary-color); margin-bottom: 1rem; font-weight: 700;">
                    ${message}
                </p>
                <div style="background: linear-gradient(135deg, #ffe6e6, #fff9fb); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <p style="font-size: 0.95rem; color: #666; margin: 0;">
                        ✨ A love this strong was written in the stars! ⭐💫
                    </p>
                </div>
                <p style="font-size: 0.9rem; color: #999; margin: 0;">
                    Perfect compatibility detected! 💕
                </p>
            </div>
        `;
    }, 1500);
}

function closeCalc() {
    document.getElementById('calcModal').style.display = 'none';
}

// ===== SPIN THE WHEEL GAME =====
const wheelChallenges = [
    "Give me a hug! 🤗",
    "Send me a love song! 🎵",
    "Tell me your favorite memory with me! 📝",
    "Give me a kiss! 💋",
    "Dance with me! 💃",
    "Watch the sunset together! 🌅",
    "Write me a love poem! ✍️",
    "Cook dinner together! 🍳",
    "Cuddle time! 🛋️",
    "Take a photo together! 📸",
    "Call me and just talk! ☎️",
    "Plan our next adventure! ✈️"
];

let currentChallenge = '';

function startWheelGame() {
    document.getElementById('wheelModal').style.display = 'block';
    spinWheel();
}

function spinWheel() {
    const wheelContent = document.getElementById('wheelContent');
    
    // Show spinning animation
    wheelContent.innerHTML = `
        <div style="padding: 2rem;">
            <div style="font-size: 5rem; animation: rotate 2s linear infinite; display: inline-block;">🎡</div>
            <p style="font-size: 1.2rem; margin-top: 1rem; color: #ff6b9d; font-weight: 700;">Spinning...</p>
        </div>
    `;
    
    // Spin for 2 seconds
    setTimeout(() => {
        const index = Math.floor(Math.random() * wheelChallenges.length);
        currentChallenge = wheelChallenges[index];
        
        wheelContent.innerHTML = `
            <div style="padding: 2rem; text-align: center;">
                <div style="font-size: 6rem; margin-bottom: 1.5rem; animation: bounce 0.6s ease-in-out;">🎯</div>
                <div style="background: linear-gradient(135deg, #ffe6e6, #fff9fb); padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.5rem; color: #ff6b9d; margin: 0; font-weight: 700;">
                        ${currentChallenge}
                    </h3>
                </div>
                <button class="game-btn" onclick="spinWheel()">Spin Again</button>
            </div>
        `;
    }, 2000);
}

function closeWheel() {
    document.getElementById('wheelModal').style.display = 'none';
}

// ===== LOVE TRIVIA =====
const triviaQuestions = [
    { q: "On what day is Valentine's Day?", a: "February 14th" },
    { q: "What flower is most associated with love?", a: "Rose" },
    { q: "What color represents love?", a: "Red" },
    { q: "What bird is a symbol of love?", a: "Dove" },
    { q: "What planet is the god of love named after?", a: "Venus" },
    { q: "What gem is an engagement ring typically made of?", a: "Diamond" },
    { q: "How many strings does Cupid's bow have?", a: "One" },
    { q: "What does XOXO stand for?", a: "Hugs and Kisses" }
];

let currentTrivia = 0;
let triviaScore = 0;

function startTrivia() {
    currentTrivia = 0;
    triviaScore = 0;
    document.getElementById('triviaModal').style.display = 'block';
    showTriviaQuestion();
}

function showTriviaQuestion() {
    const triviaContent = document.getElementById('triviaContent');
    
    if (currentTrivia >= triviaQuestions.length) {
        const percentage = (triviaScore / triviaQuestions.length) * 100;
        triviaContent.innerHTML = `
            <div style="padding: 2rem; text-align: center;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🎊</div>
                <h3 style="font-size: 1.8rem; color: var(--primary-color); margin-bottom: 1rem; font-weight: 800;">
                    ${triviaScore}/${triviaQuestions.length} Correct!
                </h3>
                <p style="font-size: 2rem; color: #ff6b9d; margin-bottom: 1.5rem; font-weight: 700;">
                    ${percentage.toFixed(0)}%
                </p>
                <p style="font-size: 1rem; color: #666; margin-bottom: 2rem;">
                    You know a lot about love! 💕
                </p>
                <button class="game-btn" onclick="startTrivia()">Try Again</button>
            </div>
        `;
        return;
    }
    
    const question = triviaQuestions[currentTrivia];
    triviaContent.innerHTML = `
        <div style="padding: 2rem; text-align: center;">
            <p style="font-size: 0.95rem; color: #ff6b9d; font-weight: 700; margin-bottom: 1rem;">
                Question ${currentTrivia + 1}/${triviaQuestions.length}
            </p>
            <h3 style="font-size: 1.3rem; color: #2c3e50; margin-bottom: 2rem; font-weight: 700;">
                ${question.q}
            </h3>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button class="game-btn" onclick="checkTrivia(true)" style="width: 100px;">
                    I Know! ✓
                </button>
                <button class="game-btn" style="background: #ccc; width: 100px;" onclick="checkTrivia(false)">
                    Skip ✕
                </button>
            </div>
            <p style="font-size: 0.9rem; color: #999; margin-top: 1.5rem; font-style: italic;">
                Answer: ${question.a}
            </p>
        </div>
    `;
}

function checkTrivia(correct) {
    if (correct) {
        triviaScore++;
    }
    currentTrivia++;
    showTriviaQuestion();
}

function closeTrivia() {
    document.getElementById('triviaModal').style.display = 'none';
}

// ===== FORTUNE TELLER =====
const fortunes = [
    "A romantic dinner is in your future! 🍽️💕",
    "True love never ends! Forever is yours! 💫",
    "You will share many beautiful moments together! ✨",
    "Adventure and love await you both! 🌍💑",
    "Your love will grow stronger with each day! 📈❤️",
    "A special surprise is coming your way! 🎁",
    "You are destined to make each other smile! 😊💕",
    "Dance together under the stars! ⭐💃",
    "Your love story is just beginning! 📖💕",
    "Laughter and joy are your future! 😄",
    "You complete each other perfectly! 🧩❤️",
    "Forever feels like the right amount of time! ♾️💕",
    "Your hearts beat as one! 💓",
    "Love multiplies when shared with you! ✖️💕"
];

function startFortune() {
    document.getElementById('fortuneModal').style.display = 'block';
    readFortune();
}

function readFortune() {
    const fortuneContent = document.getElementById('fortuneContent');
    
    // Show mystical animation
    fortuneContent.innerHTML = `
        <div style="padding: 2rem; text-align: center;">
            <div style="font-size: 5rem; animation: blink 0.8s infinite;">🔮</div>
            <p style="font-size: 1.2rem; margin-top: 1rem; color: #ff6b9d; font-weight: 700;">Reading your fortune...</p>
        </div>
    `;
    
    setTimeout(() => {
        const fortune = fortunes[Math.floor(Math.random() * fortunes.length)];
        
        fortuneContent.innerHTML = `
            <div style="padding: 2rem; text-align: center;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✨</div>
                <div style="background: linear-gradient(135deg, rgba(255, 107, 157, 0.1), rgba(254, 202, 87, 0.1)); border: 2px solid #ffe6e6; padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                    <p style="font-size: 1.3rem; color: #2c3e50; margin: 0; font-weight: 700; line-height: 1.8;">
                        ${fortune}
                    </p>
                </div>
                <button class="game-btn" onclick="readFortune()">Read Again</button>
            </div>
        `;
    }, 2500);
}

function closeFortune() {
    document.getElementById('fortuneModal').style.display = 'none';
}
document.addEventListener('click', function(event) {
    const quizModal = document.getElementById('quizModal');
    const memoryModal = document.getElementById('memoryModal');
    const scratchModal = document.getElementById('scratchModal');
    const calcModal = document.getElementById('calcModal');
    const wheelModal = document.getElementById('wheelModal');
    const triviaModal = document.getElementById('triviaModal');
    const fortuneModal = document.getElementById('fortuneModal');
    
    if (event.target === quizModal) closeQuiz();
    if (event.target === memoryModal) closeMemoryGame();
    if (event.target === scratchModal) closeScratchCard();
    if (event.target === calcModal) closeCalc();
    if (event.target === wheelModal) closeWheel();
    if (event.target === triviaModal) closeTrivia();
    if (event.target === fortuneModal) closeFortune();
});
