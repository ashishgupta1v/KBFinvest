<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const checklists = computed(() => props.data.checklists || []);
const faqCategories = computed(() => props.data.faqCategories || []);
const faqs = computed(() => props.data.faqs || []);
const glossary = computed(() => props.data.glossary || []);
const areas = computed(() => props.data.areas || {});

const activeFaqCat = ref('gen');
const glossarySearch = ref('');
const activeChecklistModal = ref(null);
const openFaqIndex = ref(null);

// Interactive document checklist tracking
const checkedDocs = ref({});

function isChecked(item) {
  return !!checkedDocs.value[item];
}

function toggleDoc(item) {
  checkedDocs.value[item] = !checkedDocs.value[item];
  try {
    localStorage.setItem('kb_checked_docs', JSON.stringify(checkedDocs.value));
  } catch (e) {}
}

const modalCheckedCount = computed(() => {
  if (!activeChecklistModal.value) return 0;
  return activeChecklistModal.value.items.filter((it) => checkedDocs.value[it]).length;
});

onMounted(() => {
  try {
    const saved = localStorage.getItem('kb_checked_docs');
    if (saved) checkedDocs.value = JSON.parse(saved);
  } catch (e) {}
});

const filteredFaqs = computed(() => {
  return faqs.value.filter((f) => f.c === activeFaqCat.value);
});

const filteredGlossary = computed(() => {
  const q = glossarySearch.value.trim().toLowerCase();
  if (!q) return glossary.value;
  return glossary.value.filter((g) => g.t.toLowerCase().includes(q) || g.d.toLowerCase().includes(q));
});

function toggleFaq(idx) {
  openFaqIndex.value = openFaqIndex.value === idx ? null : idx;
}
</script>

<template>
  <AppLayout>
    <Head title="Resources & Knowledge Base — Checklists, FAQs & Glossary in Ludhiana" />

    <!-- Hero Header -->
    <section class="py-12 border-b border-kb-line bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Resources & Education
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold font-display">
            Learn it once, <span class="italic text-gold-gradient font-serif">decide</span> for life
          </h1>
          <p class="text-base text-kb-body leading-relaxed">
            Document checklists, frequently asked questions, and a plain-English glossary to decode the jargon in factsheets, policy wordings, and loan sanctions.
          </p>
        </div>
      </div>
    </section>

    <!-- SECTION 1: DOCUMENT CHECKLISTS -->
    <section class="py-16 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold font-display text-kb-text">
            Document <span class="italic text-gold-gradient font-serif">Checklists</span>
          </h2>
          <p class="text-xs text-kb-muted mt-1">
            Open, review and verify before your visit or application. No email address required.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="c in checklists"
            :key="c.id"
            class="card-luxury p-6 flex flex-col justify-between space-y-4 group"
          >
            <div>
              <div class="w-11 h-11 rounded-xl bg-kb-surface-3 flex items-center justify-center text-kb-accent mb-3 group-hover:scale-105 group-hover:bg-kb-accent group-hover:text-black transition shadow-md">
                <SvgIcon :name="c.ic" className="w-5 h-5" />
              </div>
              <h3 class="text-base font-bold text-kb-text group-hover:text-kb-accent transition">{{ c.t }}</h3>
              <p class="text-xs text-kb-muted mt-1 leading-relaxed">{{ c.d }}</p>
            </div>

            <button
              @click="activeChecklistModal = c"
              class="w-full min-h-[44px] py-2.5 px-4 rounded-xl border border-kb-border text-kb-accent hover:bg-kb-surface-2 transition text-xs font-bold flex items-center justify-center gap-2 active:scale-95 cursor-pointer"
            >
              <span>View & track checklist ({{ c.items.length }})</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 2: FREQUENTLY ASKED QUESTIONS -->
    <section class="py-16 border-b border-kb-line bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold font-display text-kb-text">
            Frequently Asked <span class="italic text-gold-gradient font-serif">Questions</span>
          </h2>
          <p class="text-xs text-kb-muted mt-1">
            Clear, honest answers to the questions clients ask before engaging with us.
          </p>
        </div>

        <!-- FAQ Category Selector -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2 pt-1 border-b border-kb-line">
          <button
            v-for="cat in faqCategories"
            :key="cat.id"
            @click="activeFaqCat = cat.id"
            :class="[
              'px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap border min-h-[44px] cursor-pointer',
              activeFaqCat === cat.id
                ? 'bg-kb-accent text-black border-kb-accent shadow-md shadow-amber-500/10'
                : 'bg-kb-surface text-kb-body border-kb-line hover:border-kb-border hover:text-kb-text',
            ]"
          >
            {{ cat.label }}
          </button>
        </div>

        <!-- FAQ Accordion -->
        <div class="space-y-3">
          <div
            v-for="(faq, idx) in filteredFaqs"
            :key="idx"
            class="card-frame overflow-hidden transition-all duration-200"
          >
            <button
              @click="toggleFaq(idx)"
              class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-kb-text hover:text-kb-accent transition"
            >
              <span>{{ faq.q }}</span>
              <span class="text-lg font-mono text-kb-accent w-6 h-6 rounded-full bg-kb-surface-3 flex items-center justify-center shrink-0">
                {{ openFaqIndex === idx ? '−' : '+' }}
              </span>
            </button>
            <div
              v-if="openFaqIndex === idx"
              class="px-5 pb-5 text-xs text-kb-body leading-relaxed border-t border-kb-line/40 pt-4"
            >
              {{ faq.a }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 3: PLAIN-ENGLISH GLOSSARY -->
    <section class="py-16 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-kb-text">
              Plain-English <span class="italic text-gold-gradient font-serif">Glossary</span>
            </h2>
            <p class="text-xs text-kb-muted mt-1">
              The financial terms you will meet in fund factsheets, policy documents, and sanction letters.
            </p>
          </div>

          <!-- Filter Input -->
          <div class="w-full sm:w-72">
            <input
              type="text"
              v-model="glossarySearch"
              placeholder="Search terms (e.g. 'NAV', 'FOIR')..."
              class="w-full bg-kb-surface border border-kb-line rounded-xl px-4 py-2.5 text-xs text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="(item, idx) in filteredGlossary"
            :key="idx"
            class="p-4 rounded-xl bg-kb-surface border border-kb-line space-y-1.5 hover:border-kb-border transition"
          >
            <div class="text-sm font-bold text-kb-accent font-display">{{ item.t }}</div>
            <div class="text-xs text-kb-body leading-relaxed">{{ item.d }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 4: AREAS WE SERVE -->
    <section class="py-16 bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold font-display text-kb-text">
            Ludhiana first, <span class="italic text-gold-gradient font-serif">Punjab</span> next door
          </h2>
          <p class="text-xs text-kb-muted mt-1">
            Most clients meet us at our Central Town office. For everywhere else, we run the same process over phone, video, and WhatsApp.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="card-luxury p-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-accent flex items-center gap-2">
              <SvgIcon name="i-pin" className="w-4 h-4" />
              <span>Ludhiana & District</span>
            </h3>
            <div class="flex flex-wrap gap-2 pt-2">
              <span
                v-for="a in areas.ludhiana"
                :key="a"
                class="px-2.5 py-1 rounded-lg bg-kb-surface-3 border border-kb-line text-xs text-kb-body"
              >
                {{ a }}
              </span>
            </div>
          </div>

          <div class="card-luxury p-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-accent flex items-center gap-2">
              <SvgIcon name="i-shield" className="w-4 h-4" />
              <span>Rest of Punjab & Nearby</span>
            </h3>
            <div class="flex flex-wrap gap-2 pt-2">
              <span
                v-for="a in areas.punjab"
                :key="a"
                class="px-2.5 py-1 rounded-lg bg-kb-surface-3 border border-kb-line text-xs text-kb-body"
              >
                {{ a }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Interactive Checklist Detail Modal -->
    <div
      v-if="activeChecklistModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/80 backdrop-blur-md animate-in fade-in"
    >
      <div class="w-full max-w-lg bg-kb-surface border border-kb-border rounded-2xl p-5 sm:p-8 shadow-2xl space-y-5 card-luxury max-h-[90vh] overflow-y-auto no-scrollbar">
        <div class="flex items-center justify-between pb-4 border-b border-kb-line">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-kb-surface-3 flex items-center justify-center text-kb-accent shadow-inner">
              <SvgIcon :name="activeChecklistModal.ic" className="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-kb-text font-display">{{ activeChecklistModal.t }}</h3>
              <p class="text-[11px] text-kb-muted">Tick off what you have already prepared</p>
            </div>
          </div>
          <button @click="activeChecklistModal = null" class="p-1.5 rounded-lg border border-kb-line text-kb-muted hover:text-kb-text">
            <SvgIcon name="i-x" className="w-5 h-5" />
          </button>
        </div>

        <p class="text-xs text-kb-body leading-relaxed">{{ activeChecklistModal.d }}</p>

        <!-- Document Readiness Progress Bar -->
        <div class="space-y-1.5 p-3 rounded-xl bg-kb-surface-2/70 border border-kb-line">
          <div class="flex items-center justify-between text-xs">
            <span class="text-kb-muted">Your Document Readiness:</span>
            <span class="font-bold text-kb-accent font-mono">{{ modalCheckedCount }} of {{ activeChecklistModal.items.length }} ready</span>
          </div>
          <div class="w-full bg-kb-surface-3 rounded-full h-2 overflow-hidden border border-kb-line">
            <div
              class="bg-gradient-to-r from-amber-400 to-emerald-400 h-full transition-all duration-300"
              :style="{ width: `${(modalCheckedCount / activeChecklistModal.items.length) * 100}%` }"
            ></div>
          </div>
        </div>

        <!-- Interactive Document List -->
        <ul class="space-y-2.5 max-h-72 overflow-y-auto pr-2">
          <li
            v-for="(item, idx) in activeChecklistModal.items"
            :key="idx"
            @click="toggleDoc(item)"
            :class="[
              'flex items-start gap-3 text-xs p-3 rounded-xl border transition cursor-pointer select-none',
              isChecked(item)
                ? 'bg-emerald-950/30 border-emerald-500/40 text-emerald-200'
                : 'bg-kb-surface-2/60 border-kb-line/60 text-kb-body hover:border-kb-border hover:text-kb-text',
            ]"
          >
            <input
              type="checkbox"
              :checked="isChecked(item)"
              class="mt-0.5 accent-emerald-500 rounded cursor-pointer shrink-0"
            />
            <span :class="{ 'line-through opacity-70': isChecked(item) }">{{ item }}</span>
          </li>
        </ul>

        <div class="p-3 rounded-xl bg-amber-950/20 border border-amber-500/30 text-[11px] text-amber-200/90 flex items-start gap-2">
          <SvgIcon name="i-info" className="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
          <span>Bring original documents where possible. We sight them, take copies, and return the originals immediately.</span>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <Link
            :href="`/book?topic=${encodeURIComponent(activeChecklistModal.t)}`"
            @click="activeChecklistModal = null"
            class="flex-1 min-h-[44px] flex items-center justify-center text-center py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black btn-shimmer active:scale-95 shadow-lg shadow-amber-500/15"
          >
            Book a slot with these documents
          </Link>
          <button
            @click="window.print()"
            class="p-3 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl border border-kb-line text-kb-muted hover:text-kb-text hover:bg-kb-surface-2 cursor-pointer"
            title="Print checklist"
          >
            <SvgIcon name="i-print" className="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
