const API_URL = '/api';
const QUEST_ID = 1;

async function startQuest() {
    clearError();
    try {
        const response = await fetch(`${API_URL}/quests/${QUEST_ID}/start`, { method: 'POST' });
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);
        
        document.getElementById('start-btn').classList.add('hidden');
        document.getElementById('game-area').classList.remove('hidden');
        renderNode(data.current_node);
    } catch (e) {
        showError(e.message);
    }
}

async function makeChoice(transitionId) {
    clearError();
    try {
        const response = await fetch(`${API_URL}/progress/choose`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quest_id: QUEST_ID, transition_id: transitionId })
        });
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);
        
        renderNode(data.current_node);
    } catch (e) {
        showError(e.message);
    }
}

function renderNode(node) {
    document.getElementById('node-title').textContent = node.title;
    document.getElementById('node-content').textContent = node.content;
    
    const choicesContainer = document.getElementById('choices-container');
    choicesContainer.innerHTML = '';

    if (node.is_ending) {
        const restartBtn = document.createElement('button');
        restartBtn.textContent = 'Начать заново';
        restartBtn.onclick = () => {
            document.getElementById('game-area').classList.add('hidden');
            document.getElementById('start-btn').classList.remove('hidden');
        };
        choicesContainer.appendChild(restartBtn);
    } else {
        fetchChoices();
    }
}

async function fetchChoices() {
    try {
        const response = await fetch(`${API_URL}/progress?quest_id=${QUEST_ID}`);
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);
        
        const choicesContainer = document.getElementById('choices-container');
        choicesContainer.innerHTML = '';
        
        data.choices.forEach(choice => {
            const btn = document.createElement('button');
            btn.textContent = choice.text;
            btn.onclick = () => makeChoice(choice.transition_id);
            choicesContainer.appendChild(btn);
        });
    } catch (e) {
        showError(e.message);
    }
}

function showError(msg) {
    const el = document.getElementById('error-msg');
    el.textContent = "Ошибка: " + msg;
    el.classList.remove('hidden');
}

function clearError() {
    const el = document.getElementById('error-msg');
    el.textContent = "";
    el.classList.add('hidden');
}