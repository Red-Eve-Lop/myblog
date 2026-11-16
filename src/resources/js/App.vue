<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    categories: { type: Array, required: true },
    publishedPosts: { type: Array, required: true },
});
const selectedCategory = ref(null);

const posts = [];

const samplePosts = computed(() => posts.map((post) => ({
    ...post,
    categories: props.categories.filter((category) => category.sample_key === post.categoryKey),
    sample: true,
})));
const allPosts = computed(() => [...props.publishedPosts, ...samplePosts.value]);
const visiblePosts = computed(() => selectedCategory.value === null
    ? allPosts.value
    : allPosts.value.filter((post) => post.categories.some((category) => category.id === selectedCategory.value)));
</script>

<template>
    <div class="site-shell">
        <header class="site-header" id="top">
            <div class="header-inner">
                <a class="brand" href="#top" aria-label="ふわふわのブログ トップへ">
                    <span class="brand-mark" aria-hidden="true">f<span>.</span></span>
                    <span class="brand-copy">
                        <strong>ふわふわのブログ</strong>
                        <small>MOTORCYCLE & EVERYDAY</small>
                    </span>
                </a>
                <nav class="main-nav" aria-label="メインナビゲーション">
                    <a href="#stories">記事</a>
                    <a href="/about">管理人について</a>
                    <a class="nav-start" href="#stories">読みはじめる <span aria-hidden="true">↗</span></a>
                </nav>
            </div>
        </header>

        <main>
            <section class="hero" aria-labelledby="hero-title">
                <div class="hero-copy">
                    <div class="eyebrow"><span class="eyebrow-line"></span> A LITTLE JOURNAL ON TWO WHEELS</div>
                    <h1 id="hero-title">走る日も、<br>走らない日も。</h1>
                    <p class="hero-description">
                        バイクで出かけた道のこと。<br>
                        なんでもない日々のこと。<br>
                        気の向くままに、ゆるりと綴ります。
                    </p>
                    <a class="hero-link" href="#stories">記事をのぞいてみる <span aria-hidden="true">↗</span></a>
                    <div class="hero-note"><span>✳</span> のんびり、マイペースに。</div>
                </div>

                <div class="hero-art" aria-hidden="true">
                    <svg class="landscape" viewBox="0 0 760 620" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="sky" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0" stop-color="#b9c9b9"/>
                                <stop offset="1" stop-color="#e9ddc0"/>
                            </linearGradient>
                            <linearGradient id="road" x1="0" x2="1" y1="0" y2="1">
                                <stop stop-color="#b8b09b"/>
                                <stop offset="1" stop-color="#e3d3b6"/>
                            </linearGradient>
                        </defs>
                        <rect width="760" height="620" fill="url(#sky)"/>
                        <circle cx="566" cy="151" r="52" fill="#eacb8f" opacity=".9"/>
                        <path d="M0 338 130 225 214 288 330 174 469 306 555 234 760 344V620H0Z" fill="#889d8c"/>
                        <path d="M0 367 160 273 284 349 421 259 548 343 664 282 760 335V620H0Z" fill="#718a77"/>
                        <path d="M0 405C113 369 231 371 347 390c142 23 272-3 413-40v270H0Z" fill="#526f5d"/>
                        <path d="M0 461c169-53 291-48 402-20 142 36 263 13 358-20v199H0Z" fill="#405d4d"/>
                        <path d="M302 620c29-67 58-112 92-149 33-36 71-52 120-57-37 18-56 34-71 55-22 31-37 73-43 151Z" fill="url(#road)"/>
                        <path d="M355 620c12-38 24-66 37-89m14-21 7-10" fill="none" stroke="#f1e4c9" stroke-width="3" stroke-linecap="round" stroke-dasharray="27 22"/>
                        <g fill="#293f34">
                            <path d="M30 450 54 351 78 450Zm45-7 29-126 31 126Zm565-20 26-115 29 115Zm51 2 22-95 22 95Z"/>
                            <path d="M49 435h10v45H49zm50-8h10v44H99zm561-19h10v42h-10zm49 1h9v39h-9z"/>
                        </g>
                        <g transform="translate(431 445)" fill="none" stroke="#273d34" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="0" cy="26" r="13" stroke-width="5"/>
                            <circle cx="48" cy="26" r="13" stroke-width="5"/>
                            <path d="M0 26 13 7h17l18 19M13 7l12 19H0m25 0 8-18m-4-1h10m-9 0-5-7" stroke-width="4"/>
                            <path d="m18 4 5-15 10-5 7 11-7 12M23-11l-7 8-8 1" stroke-width="5"/>
                            <circle cx="32" cy="-23" r="6" fill="#273d34" stroke="none"/>
                        </g>
                        <path d="M38 43h65M38 52h38" stroke="#f6f1e3" stroke-width="2" opacity=".65"/>
                        <text x="39" y="81" fill="#f6f1e3" font-size="12" font-family="sans-serif" letter-spacing="3">TAKE THE SCENIC ROUTE</text>
                    </svg>
                    <div class="art-caption"><span>THE OPEN ROAD</span><span>自分のペースで、どこまでも。</span></div>
                </div>
                <div class="hero-index" aria-hidden="true"><span>01 — 03</span>　RIDE / LIFE / JOURNAL</div>
            </section>

            <div class="marquee" aria-hidden="true">
                <span>GOOD DAYS, GOOD RIDES</span><i>✳</i><span>暮らしと、ときどきバイク。</span><i>✳</i><span>TAKE IT EASY</span><i>✳</i><span>GOOD DAYS, GOOD RIDES</span>
            </div>

            <div class="content-wrap">
                <section id="stories" class="stories" aria-labelledby="stories-title">
                    <div class="section-heading">
                        <div>
                            <p class="section-kicker"><span>01</span> THE JOURNAL</p>
                            <h2 id="stories-title">最近のこと<span class="heading-dot">.</span></h2>
                            <p class="section-description">走った記録も、日々のつぶやきも。</p>
                        </div>
                    </div>

                    <div class="filters" role="group" aria-label="記事のカテゴリー">
                        <button
                            type="button"
                            :class="{ active: selectedCategory === null }"
                            :aria-pressed="selectedCategory === null"
                            @click="selectedCategory = null"
                        >すべて</button>
                        <button
                            v-for="category in categories"
                            :key="category.id"
                            type="button"
                            :class="{ active: selectedCategory === category.id }"
                            :aria-pressed="selectedCategory === category.id"
                            @click="selectedCategory = category.id"
                        >{{ category.name }}</button>
                    </div>

                    <div class="post-grid">
                        <article v-for="post in visiblePosts" :key="`${post.sample ? 'sample' : 'post'}-${post.id}`" class="post-card">
                            <a v-if="post.image" :href="post.url" class="post-art post-image-link">
                                <img :src="post.image" alt="" class="post-image">
                            </a>
                            <div v-else class="post-art" :class="`post-art--${post.artwork ?? 'road'}`" aria-hidden="true">
                                <span class="post-art-label">{{ post.label }}</span>
                                <span class="art-sun"></span>
                                <span class="art-hill art-hill-back"></span>
                                <span class="art-hill art-hill-front"></span>
                                <span class="art-path"></span>
                                <span v-if="post.sample" class="post-art-number">{{ post.number }} / 03</span>
                            </div>
                            <div class="post-meta"><span>{{ post.categories.map((category) => category.name).join('・') || '未分類' }}</span><span>{{ post.sample ? 'サンプル記事' : post.date }}</span></div>
                            <h3><a v-if="post.url" :href="post.url">{{ post.title }}</a><template v-else>{{ post.title }}</template></h3>
                            <p>{{ post.description }}</p>
                        </article>
                    </div>
                    <p v-if="visiblePosts.length === 0" class="section-description">このカテゴリーの記事はまだありません。</p>
                </section>

                <aside id="about" class="about" aria-labelledby="about-title">
                    <div class="about-inner">
                        <div class="about-stamp" aria-hidden="true"><span>f.</span><small>TAKE IT EASY</small></div>
                        <div class="about-copy">
                            <p class="section-kicker"><span>02</span> ABOUT THIS BLOG</p>
                            <h2 id="about-title">寄り道、大歓迎。</h2>
                            <p>バイクのこと、日々のこと。<br>好きなものや、そのとき思ったことを、<br class="desktop-break">気ままに書き留めていく場所です。</p>
                            <div class="about-signature">Thanks for stopping by <span>✳</span></div>
                        </div>
                        <div class="about-side" aria-hidden="true">A LITTLE<br>SPACE TO<br>SLOW DOWN<span>↗</span></div>
                    </div>
                </aside>
            </div>
        </main>

        <footer class="site-footer">
            <div class="footer-inner">
                <div><strong>ふわふわのブログ<span>.</span></strong><p>バイクと、日々のこと。</p></div>
                <a href="#top">ページの先頭へ <span aria-hidden="true">↑</span></a>
            </div>
            <div class="footer-bottom"><span>FREELY, AT YOUR OWN PACE.</span><span>ふわふわのブログ</span></div>
        </footer>
    </div>
</template>
