<div>
    <nav class="flex items-center gap-2 text-xs font-bold text-mocha/70">
        <a href="{{ route('web.cart') }}" wire:navigate class="hover:text-brand">سلة الهدايا</a>
        <span>/</span>
        <span class="text-ink">صمّم كارتك</span>
    </nav>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-extrabold text-ink">صمّم كارتك بنفسك 🎨</h1>
            <p class="mt-1 text-sm font-bold text-mocha">غلاف ووش داخلي، اكتب وارسم وزيّن — وهيتطبع على كرت فاخر مع هديتك</p>
        </div>
        <span class="rounded-full bg-blush px-5 py-2 text-sm font-extrabold text-brand">
            {{ $price > 0 ? number_format($price) . ' ج.م' : 'مجاناً 🎁' }}
        </span>
    </div>

    @if ($error)
        <p class="mt-4 rounded-2xl bg-blush p-3 text-center text-sm font-bold text-brand">{{ $error }}</p>
    @endif

    <div
        class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3"
        x-data="hdkCardStudio()"
        x-init="init()"
        @pointermove.window="moveDrag($event)"
        @pointerup.window="drag = null"
    >
        {{-- المعاينة الحية --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-sand bg-white p-3 sm:p-5 lg:sticky lg:top-24">
                {{-- تبويب الوشين --}}
                <div class="mb-3 flex items-center justify-between" data-html2canvas-ignore>
                    <div class="flex items-center gap-1 rounded-full border border-sand bg-cream p-1">
                        <button type="button" @click="switchFace('front')" class="rounded-full px-4 py-1.5 text-xs font-extrabold transition" :class="face === 'front' ? 'bg-brand text-white shadow' : 'text-mocha hover:text-brand'">الغلاف 🎀</button>
                        <button type="button" @click="switchFace('inside')" class="rounded-full px-4 py-1.5 text-xs font-extrabold transition" :class="face === 'inside' ? 'bg-brand text-white shadow' : 'text-mocha hover:text-brand'">من جوه 📖</button>
                    </div>
                    <span class="text-[11px] font-bold text-mocha/60">الوشين بيتحفظوا مع بعض</span>
                </div>

                <div
                    x-ref="card"
                    class="relative aspect-[21/10] w-full touch-none select-none overflow-hidden rounded-xl shadow-lg transition-colors duration-300"
                    :style="`background-color: ${bg.c}; background-image: radial-gradient(${bg.dark ? 'rgba(255,255,255,.07)' : 'rgba(60,40,30,.07)'} 1px, transparent 1px); background-size: 14px 14px;`"
                >
                    {{-- الإطار الدهبي --}}
                    <div x-show="gold" class="pointer-events-none absolute inset-2.5 z-10 sm:inset-3 border border-[#C9A227]/50">
                        <div class="absolute -top-px -right-px size-5 border-t-2 border-r-2 border-[#C9A227]"></div>
                        <div class="absolute -top-px -left-px size-5 border-t-2 border-l-2 border-[#C9A227]"></div>
                        <div class="absolute -bottom-px -right-px size-5 border-b-2 border-r-2 border-[#C9A227]"></div>
                        <div class="absolute -bottom-px -left-px size-5 border-b-2 border-l-2 border-[#C9A227]"></div>
                    </div>

                    {{-- خط الطية --}}
                    <div class="pointer-events-none absolute inset-y-4 right-1/2 z-10 w-px opacity-25" :style="`background: linear-gradient(to bottom, transparent, ${bg.dark ? '#C9A227' : '#B8860B'}, transparent)`"></div>

                    {{-- محتوى الوش الحالي --}}
                    <div class="relative z-10 flex h-full flex-col justify-between p-5 sm:p-8">
                        <div class="flex items-center justify-between text-[9px] sm:text-[11px] font-bold" :class="bg.dark ? 'text-white/50' : 'text-mocha/60'">
                            <span class="tracking-widest">هداياك • HADAYAK 💗</span>
                            <span x-text="face === 'front' ? 'كرت إهداء فاخر' : 'رسالة من القلب'"></span>
                        </div>

                        <div class="mx-auto my-auto max-w-[85%] text-center">
                            <p
                                x-text="cur.title || '…'"
                                class="font-extrabold leading-snug"
                                :class="{ 'text-lg sm:text-2xl': size === 'sm', 'text-xl sm:text-3xl': size === 'md', 'text-2xl sm:text-4xl': size === 'lg' }"
                                :style="`color: ${color}; font-family: ${font}, 'Cairo', sans-serif`"
                            ></p>
                            <p
                                x-show="cur.message.trim().length"
                                x-text="cur.message"
                                class="mt-2 sm:mt-3 font-bold leading-relaxed"
                                :class="[ size === 'sm' ? 'text-[10px] sm:text-sm' : (size === 'md' ? 'text-xs sm:text-base' : 'text-sm sm:text-lg'), bg.dark ? 'text-white/80' : 'text-ink/75' ]"
                                :style="`font-family: ${font}, 'Cairo', sans-serif`"
                            ></p>
                        </div>

                        <div class="flex items-center justify-between text-[9px] sm:text-[11px] font-bold" :class="bg.dark ? 'text-white/40' : 'text-mocha/50'">
                            <span>بكل حب 🎀</span>
                            <span>صُنع عبر هداياك</span>
                        </div>
                    </div>

                    {{-- طبقة الرسم الحر: canvas لكل وش --}}
                    <canvas x-ref="cvFront" x-show="face === 'front'" class="absolute inset-0 z-20 h-full w-full" :class="drawMode ? 'cursor-crosshair' : 'pointer-events-none'" @pointerdown="startStroke($event)" @pointermove="stroke($event)" @pointerup="endStroke()" @pointerleave="endStroke()"></canvas>
                    <canvas x-ref="cvInside" x-show="face === 'inside'" class="absolute inset-0 z-20 h-full w-full" :class="drawMode ? 'cursor-crosshair' : 'pointer-events-none'" @pointerdown="startStroke($event)" @pointermove="stroke($event)" @pointerup="endStroke()" @pointerleave="endStroke()"></canvas>

                    {{-- الستيكرز والصور --}}
                    <template x-for="s in stickers[face]" :key="s.id">
                        <span
                            class="absolute z-30"
                            :class="drawMode ? 'pointer-events-none' : 'cursor-grab active:cursor-grabbing'"
                            :style="`left: ${s.x}%; top: ${s.y}%; transform: translate(-50%, 0);`"
                            @pointerdown="startDrag($event, s)"
                            @dblclick="removeSticker(s.id)"
                        >
                            <template x-if="s.type === 'img'">
                                <img :src="s.src" :style="`width: ${s.w}px`" class="rounded-lg border border-white/60 shadow-md" alt="">
                            </template>
                            <template x-if="s.type !== 'img'">
                                <span x-text="s.e" class="text-2xl drop-shadow-sm sm:text-4xl"></span>
                            </template>
                            <button
                                type="button"
                                data-html2canvas-ignore
                                @click.stop="removeSticker(s.id)"
                                class="absolute -top-2 -right-2 flex size-4 items-center justify-center rounded-full bg-brand text-[9px] font-bold leading-none text-white opacity-0 transition hover:opacity-100 sm:opacity-60"
                                aria-label="حذف"
                            >×</button>
                        </span>
                    </template>
                </div>

                <p class="mt-3 text-center text-xs font-bold text-mocha/70" data-html2canvas-ignore>
                    <span x-show="! drawMode">اسحب الستيكر تحركه 👆 — دوس مرتين عليه تشيله</span>
                    <span x-show="drawMode" class="text-brand">وضع الرسم شغال ✏️ — ارسم على الكارت بصباعك أو الماوس</span>
                </p>
            </div>
        </div>

        {{-- أدوات التصميم --}}
        <div class="space-y-5">
            {{-- ١: الخلفية --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <h2 class="text-base font-extrabold text-ink">١· اختار الخلفية</h2>
                <div class="mt-3 grid grid-cols-6 gap-2">
                    <template x-for="(b, i) in bgs" :key="b.c">
                        <button
                            type="button"
                            @click="bgIndex = i; if (b.dark && color === '#3A2A2E') color = '#FFF6EE'; if (! b.dark && color === '#FFF6EE') color = '#C2185B'"
                            class="aspect-square rounded-xl border-2 transition"
                            :class="bgIndex === i ? 'border-brand scale-105 shadow' : 'border-sand hover:border-brand/40'"
                            :style="`background-color: ${b.c}`"
                            :title="b.n"
                        ></button>
                    </template>
                </div>
            </div>

            {{-- ٢: الرسالة --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <h2 class="text-base font-extrabold text-ink">٢· اكتب رسالتك <span class="text-xs font-bold text-mocha/60" x-text="face === 'front' ? '(الغلاف)' : '(من جوه)'"></span></h2>
                <input
                    type="text"
                    x-model="cur.title"
                    maxlength="40"
                    placeholder="العنوان — مثلاً: كل سنة وانتي طيبة ❤️"
                    class="mt-3 w-full rounded-2xl border border-sand bg-cream p-3.5 text-sm font-extrabold text-ink placeholder:font-bold placeholder:text-mocha/50 focus:border-brand focus:outline-none"
                >
                <textarea
                    x-model="cur.message"
                    rows="3"
                    maxlength="160"
                    placeholder="رسالتك من القلب… (اختياري)"
                    class="mt-2 w-full rounded-2xl border border-sand bg-cream p-3.5 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none"
                ></textarea>

                <div class="mt-3 grid grid-cols-4 gap-1.5">
                    <template x-for="f in fonts" :key="f.v">
                        <button type="button" @click="font = f.v" class="rounded-xl border-2 px-1 py-2 text-center transition" :class="font === f.v ? 'border-brand bg-blush' : 'border-sand hover:border-brand/40'">
                            <span class="block text-base font-bold leading-none text-ink" :style="`font-family: ${f.v}, 'Cairo', sans-serif`">هدية</span>
                            <span class="mt-1 block text-[9px] font-bold text-mocha" x-text="f.n"></span>
                        </button>
                    </template>
                </div>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <template x-for="c in colors" :key="c">
                            <button
                                type="button"
                                @click="color = c"
                                class="size-8 rounded-full border-2 transition hover:scale-110"
                                :class="color === c ? 'border-brand ring-2 ring-brand/30' : 'border-sand'"
                                :style="`background-color: ${c}`"
                                aria-label="لون الخط"
                            ></button>
                        </template>
                        <label class="relative flex size-8 cursor-pointer items-center justify-center rounded-full border-2 border-sand bg-white text-xs transition hover:scale-110" title="لون مخصص">
                            🎨
                            <input type="color" x-model="color" class="absolute inset-0 size-full cursor-pointer opacity-0">
                        </label>
                    </div>
                    <div class="flex items-center gap-1 rounded-full border border-sand p-1">
                        <button type="button" @click="size = 'sm'" class="rounded-full px-2.5 py-1 text-xs font-extrabold transition" :class="size === 'sm' ? 'bg-brand text-white' : 'text-mocha'">ص</button>
                        <button type="button" @click="size = 'md'" class="rounded-full px-2.5 py-1 text-sm font-extrabold transition" :class="size === 'md' ? 'bg-brand text-white' : 'text-mocha'">و</button>
                        <button type="button" @click="size = 'lg'" class="rounded-full px-2.5 py-1 text-base font-extrabold transition" :class="size === 'lg' ? 'bg-brand text-white' : 'text-mocha'">ك</button>
                    </div>
                </div>
            </div>

            {{-- ٣: الرسم الحر --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-extrabold text-ink">٣· ارسم بإيدك ✏️</h2>
                    <button type="button" role="switch" @click="drawMode = ! drawMode" class="relative h-6 w-11 rounded-full transition-colors" :class="drawMode ? 'bg-brand' : 'bg-sand'">
                        <span class="absolute top-0.5 size-5 rounded-full bg-white shadow transition-all" :class="drawMode ? 'right-0.5' : 'right-[22px]'"></span>
                    </button>
                </div>

                <div x-show="drawMode" class="mt-3 space-y-3">
                    <div class="flex items-center gap-1.5">
                        <template x-for="t in tools" :key="t.v">
                            <button type="button" @click="tool = t.v" class="flex flex-1 items-center justify-center gap-1 rounded-xl border-2 px-2 py-2 text-xs font-extrabold transition" :class="tool === t.v ? 'border-brand bg-blush text-brand' : 'border-sand text-mocha hover:border-brand/40'">
                                <span x-text="t.i"></span><span x-text="t.n"></span>
                            </button>
                        </template>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <template x-for="w in [2, 4, 8, 14]" :key="w">
                                <button type="button" @click="lineWidth = w" class="flex size-8 items-center justify-center rounded-xl border-2 transition" :class="lineWidth === w ? 'border-brand bg-blush' : 'border-sand hover:border-brand/40'">
                                    <span class="rounded-full bg-ink" :style="`width: ${Math.min(w + 2, 16)}px; height: ${Math.min(w + 2, 16)}px`"></span>
                                </button>
                            </template>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="undoStroke()" class="flex size-8 items-center justify-center rounded-xl border-2 border-sand text-sm text-mocha transition hover:border-brand/40" title="تراجع">↩️</button>
                            <button type="button" @click="clearDrawing()" class="flex size-8 items-center justify-center rounded-xl border-2 border-sand text-sm text-mocha transition hover:border-brand/40" title="مسح الرسم">🗑️</button>
                        </div>
                    </div>
                    <p class="text-[11px] font-bold text-mocha/60">الرسم بلون الخط المختار فوق — والممحاة بتمسح رسمك بس</p>
                </div>
            </div>

            {{-- ٤: اللمسات --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <h2 class="text-base font-extrabold text-ink">٤· اللمسة الأخيرة</h2>

                <label class="mt-3 flex cursor-pointer items-center justify-between rounded-2xl bg-cream p-3.5">
                    <span class="flex items-center gap-2 text-sm font-extrabold text-ink"><span class="text-lg">✨</span> إطار دهبي فاخر</span>
                    <button type="button" role="switch" @click="gold = ! gold" class="relative h-6 w-11 rounded-full transition-colors" :class="gold ? 'bg-brand' : 'bg-sand'">
                        <span class="absolute top-0.5 size-5 rounded-full bg-white shadow transition-all" :class="gold ? 'right-0.5' : 'right-[22px]'"></span>
                    </button>
                </label>

                <p class="mt-4 text-xs font-extrabold text-mocha">ستيكرز — دوس تضيف (لحد ٨ لكل وش)</p>
                <div class="mt-2 grid grid-cols-6 gap-1.5">
                    <template x-for="e in emojis" :key="e">
                        <button type="button" @click="addSticker(e)" class="flex aspect-square items-center justify-center rounded-xl bg-cream text-xl transition hover:scale-110 hover:bg-blush" x-text="e"></button>
                    </template>
                </div>

                <label class="mt-3 flex cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-brand/40 bg-cream p-3 text-xs font-extrabold text-mocha transition hover:border-brand hover:text-brand">
                    📷 ضيف صورة خاصة بيك على الكارت
                    <input type="file" accept="image/*" class="sr-only" @change="addImage($event)">
                </label>
            </div>

            {{-- الاعتماد --}}
            <div class="space-y-2">
                <button
                    type="button"
                    @click="confirmCard()"
                    :disabled="saving || ! faces.front.title.trim()"
                    class="w-full rounded-full bg-brand px-8 py-4 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span x-show="! saving">اعتماد الكارت وإضافته للطلب 💝</span>
                    <span x-show="saving">ثواني بنجهّز كارتك… 🎀</span>
                </button>
                <div class="flex items-center justify-center gap-4 text-xs font-bold">
                    <button type="button" @click="saveDraft()" class="text-mocha transition hover:text-brand" x-text="draftSaved ? 'اتحفظت ✅' : '💾 حفظ مسودة'"></button>
                    <span class="text-sand">•</span>
                    <button type="button" @click="resetAll()" class="text-mocha transition hover:text-brand">🗑️ مسح الكل</button>
                    <span class="text-sand">•</span>
                    <a href="{{ route('web.cart') }}" wire:navigate class="text-mocha transition hover:text-brand">رجوع للسلة</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function hdkCardStudio() {
            return {
                bgs: [
                    { c: '#FFFDF8', n: 'كريمي', dark: false },
                    { c: '#FBE6EC', n: 'وردي حالم', dark: false },
                    { c: '#F3EBDD', n: 'رملي دافي', dark: false },
                    { c: '#4A1024', n: 'عنابي ملكي', dark: true },
                    { c: '#10263F', n: 'كحلي ليلي', dark: true },
                    { c: '#143D2E', n: 'زمردي', dark: true },
                ],
                bgIndex: 0,
                colors: ['#C2185B', '#B8860B', '#3A2A2E', '#FFF6EE'],
                color: '#C2185B',
                fonts: [
                    { v: "'Cairo'", n: 'هداياك' },
                    { v: "'Amiri'", n: 'نسخ فاخر' },
                    { v: "'Reem Kufi'", n: 'كوفي' },
                    { v: "'Aref Ruqaa'", n: 'رقعة' },
                ],
                font: "'Cairo'",
                size: 'md',
                gold: true,
                face: 'front',
                faces: {
                    front: { title: 'كل سنة وانت طيب 🎉', message: 'من القلب لأغلى الناس… عقبال مليون سنة فرح وسعادة' },
                    inside: { title: 'مع أطيب الأمنيات 💐', message: '' },
                },
                emojis: ['🎁','❤️','🌹','✨','🎉','🎂','💍','🦋','🌙','⭐','🎈','💐'],
                stickers: { front: [], inside: [] },
                nextId: 1,
                drag: null,
                // الرسم
                drawMode: false,
                tool: 'pen',
                tools: [ { v: 'pen', n: 'قلم', i: '✒️' }, { v: 'brush', n: 'فرشاة', i: '🖌️' }, { v: 'eraser', n: 'ممحاة', i: '🧽' } ],
                lineWidth: 4,
                drawing: false,
                history: { front: [], inside: [] },
                saving: false,
                draftSaved: false,

                get bg() { return this.bgs[this.bgIndex] },
                get cur() { return this.faces[this.face] },
                canvas(f) { return f === 'front' ? this.$refs.cvFront : this.$refs.cvInside },

                init() {
                    this.$nextTick(() => {
                        ['front', 'inside'].forEach(f => {
                            const cv = this.canvas(f);
                            const r = this.$refs.card.getBoundingClientRect();
                            cv.width = Math.round(r.width * 2);
                            cv.height = Math.round(r.height * 2);
                        });
                        this.restoreDraft();
                    });
                },

                switchFace(f) { this.face = f },

                // ===== الستيكرز =====
                addSticker(e) {
                    if (this.stickers[this.face].length >= 8) return;
                    this.stickers[this.face].push({ id: this.nextId++, type: 'emoji', e, x: 15 + Math.random() * 70, y: 12 + Math.random() * 25 });
                },
                addImage(ev) {
                    const file = ev.target.files[0];
                    ev.target.value = '';
                    if (! file || file.size > 4 * 1024 * 1024 || this.stickers[this.face].length >= 8) return;
                    const reader = new FileReader();
                    reader.onload = () => this.stickers[this.face].push({ id: this.nextId++, type: 'img', src: reader.result, w: 90, x: 30 + Math.random() * 40, y: 15 + Math.random() * 20 });
                    reader.readAsDataURL(file);
                },
                removeSticker(id) { this.stickers[this.face] = this.stickers[this.face].filter(s => s.id !== id) },
                startDrag(ev, s) { if (this.drawMode) return; ev.preventDefault(); this.drag = s.id },
                moveDrag(ev) {
                    if (this.drag === null) return;
                    const r = this.$refs.card.getBoundingClientRect();
                    const s = this.stickers[this.face].find(x => x.id === this.drag);
                    if (! s) return;
                    s.x = Math.min(94, Math.max(3, (ev.clientX - r.left) / r.width * 100));
                    s.y = Math.min(88, Math.max(4, (ev.clientY - r.top) / r.height * 100));
                },

                // ===== الرسم الحر =====
                ctx() { const cv = this.canvas(this.face); return cv.getContext('2d') },
                pos(ev) {
                    const cv = this.canvas(this.face);
                    const r = cv.getBoundingClientRect();
                    return { x: (ev.clientX - r.left) / r.width * cv.width, y: (ev.clientY - r.top) / r.height * cv.height };
                },
                startStroke(ev) {
                    if (! this.drawMode) return;
                    ev.preventDefault();
                    const ctx = this.ctx();
                    try { this.history[this.face].push(ctx.getImageData(0, 0, this.canvas(this.face).width, this.canvas(this.face).height)); if (this.history[this.face].length > 15) this.history[this.face].shift(); } catch (e) {}
                    this.drawing = true;
                    const p = this.pos(ev);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                },
                stroke(ev) {
                    if (! this.drawing || ! this.drawMode) return;
                    const ctx = this.ctx();
                    const p = this.pos(ev);
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    ctx.lineWidth = this.lineWidth * 2;
                    if (this.tool === 'eraser') {
                        ctx.globalCompositeOperation = 'destination-out';
                        ctx.shadowBlur = 0;
                    } else {
                        ctx.globalCompositeOperation = 'source-over';
                        ctx.strokeStyle = this.color;
                        ctx.shadowBlur = this.tool === 'brush' ? 6 : 0;
                        ctx.shadowColor = this.color;
                    }
                    ctx.lineTo(p.x, p.y);
                    ctx.stroke();
                },
                endStroke() { if (this.drawing) { this.ctx().closePath(); this.drawing = false } },
                undoStroke() {
                    const h = this.history[this.face];
                    const cv = this.canvas(this.face);
                    const ctx = cv.getContext('2d');
                    if (h.length) { ctx.putImageData(h.pop(), 0, 0) } else { ctx.clearRect(0, 0, cv.width, cv.height) }
                },
                clearDrawing() {
                    const cv = this.canvas(this.face);
                    cv.getContext('2d').clearRect(0, 0, cv.width, cv.height);
                    this.history[this.face] = [];
                },

                // ===== المسودة =====
                saveDraft() {
                    try {
                        localStorage.setItem('hdk_card_draft', JSON.stringify({
                            bgIndex: this.bgIndex, color: this.color, font: this.font, size: this.size, gold: this.gold,
                            faces: this.faces, stickers: this.stickers, nextId: this.nextId,
                            cvFront: this.$refs.cvFront.toDataURL(), cvInside: this.$refs.cvInside.toDataURL(),
                        }));
                        this.draftSaved = true;
                        setTimeout(() => this.draftSaved = false, 2000);
                    } catch (e) {}
                },
                restoreDraft() {
                    try {
                        const d = JSON.parse(localStorage.getItem('hdk_card_draft') || 'null');
                        if (! d) return;
                        Object.assign(this, { bgIndex: d.bgIndex, color: d.color, font: d.font, size: d.size, gold: d.gold, faces: d.faces, stickers: d.stickers, nextId: d.nextId });
                        ['cvFront', 'cvInside'].forEach(k => {
                            if (! d[k]) return;
                            const img = new Image();
                            img.onload = () => this.$refs[k].getContext('2d').drawImage(img, 0, 0, this.$refs[k].width, this.$refs[k].height);
                            img.src = d[k];
                        });
                    } catch (e) {}
                },
                resetAll() {
                    if (! confirm('تمسح التصميم كله وتبدأ من الأول؟')) return;
                    try { localStorage.removeItem('hdk_card_draft') } catch (e) {}
                    window.location.reload();
                },

                // ===== الاعتماد: الوشين في صورة واحدة =====
                async confirmCard() {
                    if (this.saving) return;
                    this.saving = true;
                    const prevFace = this.face;
                    const prevDraw = this.drawMode;
                    this.drawMode = false;
                    try {
                        const shots = {};
                        for (const f of ['front', 'inside']) {
                            this.face = f;
                            await this.$nextTick();
                            await new Promise(r => setTimeout(r, 120));
                            shots[f] = await html2canvas(this.$refs.card, { scale: 2.5, useCORS: true, backgroundColor: null });
                        }
                        this.face = prevFace;
                        const gap = 48;
                        const out = document.createElement('canvas');
                        out.width = shots.front.width;
                        out.height = shots.front.height + shots.inside.height + gap;
                        const ctx = out.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, out.width, out.height);
                        ctx.drawImage(shots.front, 0, 0);
                        ctx.drawImage(shots.inside, 0, shots.front.height + gap);
                        try { localStorage.removeItem('hdk_card_draft') } catch (e) {}
                        await this.$wire.saveCard(out.toDataURL('image/png'));
                    } catch (e) {
                        this.saving = false;
                        this.drawMode = prevDraw;
                        alert('حصلت مشكلة، جرب تاني');
                    }
                },
            };
        }
    </script>
</div>
