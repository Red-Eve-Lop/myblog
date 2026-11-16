import './bootstrap';

import { createApp } from 'vue';
import App from './App.vue';

createApp(App, { categories: window.blogCategories, publishedPosts: window.blogPosts }).mount('#app');
