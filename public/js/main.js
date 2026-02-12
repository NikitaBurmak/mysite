import { initAuth } from './auth.js';
import { initTopics } from './topics.js';
import { initAnecdotes } from './anecdotes.js';

document.addEventListener('DOMContentLoaded', async () => {
    await initAuth();
    initTopics();
    initAnecdotes();
});
