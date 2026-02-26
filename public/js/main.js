import { initAuth } from './auth.js';
import { initTopics } from './topics.js';
import { initAnecdotes } from './anecdotes.js';
import { initAddAnecdote } from './addAnecdote.js';

document.addEventListener('DOMContentLoaded', async () => {
    await initAuth();
    initTopics();
    initAnecdotes();
    initAddAnecdote();
});
