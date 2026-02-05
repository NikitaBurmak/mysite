import { initAuth } from './auth.js';
import { initTopics } from './topics.js';
import { initAnecdotes } from './anecdotes';

document.addEventListener('DOMContentLoaded' , () => {
    initAuth();
    initTopics();
    initAnecdotes();
})
