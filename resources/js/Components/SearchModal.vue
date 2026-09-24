<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import SvgIcon from './SvgIcon.vue';

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(['close']);
const query = ref('');
const inputRef = ref(null);

const searchIndex = [
  // Services
  { k: 'Investments', t: 'Mutual Funds', d: 'SIP, lump sums, STP and portfolio rebalancing', url: '/investments?tab=mf' },
  { k: 'Investments', t: 'PMS Introductions', d: 'Discretionary portfolios above ₹50 lakh regulatory floor', url: '/investments?tab=pms' },
  { k: 'Investments', t: 'Shares & Bonds', d: 'Direct equity, index ETFs, Sovereign Gold Bonds', url: '/investments?tab=mkt' },
  { k: 'Insurance', t: 'Health Insurance', d: 'Room rent, PED waiting, top-ups and claim assistance', url: '/insurance?tab=health' },
  { k: 'Insurance', t: 'Life Insurance', d: 'Term plans, HLV calculation and claim support', url: '/insurance?tab=life' },
  { k: 'Insurance', t: 'Motor Insurance', d: 'Own-damage, zero-dep add-ons and NCB protection', url: '/insurance?tab=motor' },
  { k: 'Loans', t: 'Loan Against Property (LAP)', d: 'Release value from residential, commercial or industrial property', url: '/loans?tab=lap' },
  { k: 'Loans', t: 'Home Loans', d: 'Purchase, construction, and balance transfer', url: '/loans?tab=home' },
  { k: 'Loans', t: 'Business & MSME Loans', d: 'Working capital, unsecured business credit and term loans', url: '/loans?tab=biz' },
  { k: 'Loans', t: 'CGTMSE Collateral-Free Loans', d: 'Credit guarantee cover up to ₹10 crore for micro & small enterprises', url: '/loans?tab=cgtmse' },
  { k: 'Loans', t: 'Loan Against Securities (LAMF)', d: 'Borrow against mutual funds & shares without selling', url: '/loans?tab=lamf' },
  { k: 'Advisory', t: 'Pre-IPO & Unlisted Shares', d: 'Off-market growth opportunities for informed investors', url: '/advisory?tab=pe' },
  { k: 'Advisory', t: 'Stressed Account & NPA Handling', d: 'Restructuring, OTS analysis and confidential assistance', url: '/advisory?tab=stress' },

  // Calculators
  { k: 'Calculator', t: 'SIP Calculator', d: 'Calculate monthly compounding and wealth projection', url: '/calculators?calc=sip' },
  { k: 'Calculator', t: 'Lump Sum Calculator', d: 'Estimate one-time investment future value', url: '/calculators?calc=lump' },
  { k: 'Calculator', t: 'Step-up SIP Calculator', d: 'Annual percentage increments in monthly investments', url: '/calculators?calc=stepup' },
  { k: 'Calculator', t: 'SWP Retirement Calculator', d: 'Monthly income duration from a corpus', url: '/calculators?calc=swp' },
  { k: 'Calculator', t: 'Loan EMI Calculator', d: 'Equated monthly instalments and interest breakdown', url: '/calculators?calc=emi' },
  { k: 'Calculator', t: 'Loan Eligibility (FOIR)', d: 'Income capacity and permissible loan ceiling', url: '/calculators?calc=elig' },
  { k: 'Calculator', t: 'CGTMSE Fee Checker', d: 'Indicative annual guarantee fee slabs', url: '/calculators?calc=cgtmse' },
  { k: 'Calculator', t: 'Human Life Value (HLV)', d: 'Assess life cover gap for your family', url: '/calculators?calc=hlv' },

  // Resources & Legal
  { k: 'Resources', t: 'Document Checklists', d: 'Printable KYC and loan application preparation lists', url: '/resources' },
  { k: 'Resources', t: 'Financial Glossary', d: 'Plain-English definitions of complex financial terms', url: '/resources' },
  { k: 'Resources', t: 'Frequently Asked Questions', d: 'Common questions on fees, safety, and process', url: '/resources' },
  { k: 'Legal', t: 'Regulatory Disclosures', d: 'AMFI, SEBI, IRDAI and RBI compliance statements', url: '/legal/disclosures' },
  { k: 'Book', t: 'Book a 30-min Consultation', d: 'Schedule an appointment at Central Town or over phone', url: '/book' },
];

const results = computed(() => {
  const q = query.value.trim().toLowerCase();
  if (q.length < 2) return [];
  return searchIndex.filter((item) => {
    return item.t.toLowerCase().includes(q) || item.d.toLowerCase().includes(q) || item.k.toLowerCase().includes(q);
  });
});

function navigate(url) {
  emit('close');
  router.visit(url);
}

function handleKeyDown(e) {
  if (e.key === 'Escape' && props.show) {
    emit('close');
  }
}

watch(
  () => props.show,
  (val) => {
    if (val) {
      query.value = '';
      setTimeout(() => inputRef.value?.focus(), 50);
    }
  }
);

onMounted(() => window.addEventListener('keydown', handleKeyDown));
onUnmounted(() => window.removeEventListener('keydown', handleKeyDown));
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-start justify-center pt-6 sm:pt-16 px-3 sm:px-4 bg-black/75 backdrop-blur-md">
    <div
      class="w-full max-w-2xl bg-kb-surface border border-kb-border/50 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200"
    >
      <div class="flex items-center px-4 py-3 border-b border-kb-line bg-kb-surface-2/60">
        <SvgIcon name="i-search" className="w-5 h-5 text-kb-accent mr-3" />
        <input
          ref="inputRef"
          v-model="query"
          type="text"
          placeholder="Search services, calculators, FAQs, terms (e.g. 'CGTMSE', 'SIP', 'Health')..."
          class="w-full bg-transparent text-kb-text placeholder:text-kb-muted outline-none text-base font-medium"
        />
        <button
          @click="emit('close')"
          class="px-2 py-1 text-xs text-kb-muted bg-kb-surface rounded border border-kb-line hover:text-kb-text"
        >
          Esc
        </button>
      </div>

      <!-- Results or Quick Shortcuts -->
      <div class="max-h-[60vh] sm:max-h-[440px] overflow-y-auto p-3 divide-y divide-kb-line/30">
        <!-- Quick Shortcuts when empty -->
        <div v-if="query.length < 2" class="p-3 space-y-4">
          <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-kb-muted mb-2.5 flex items-center gap-1.5">
              <SvgIcon name="i-growth" className="w-3.5 h-3.5 text-kb-accent" />
              <span>Popular Calculators & Tools</span>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="chip in [
                  { t: 'SIP Calculator', url: '/calculators?calc=sip' },
                  { t: 'CGTMSE MSME Loans', url: '/loans?tab=cgtmse' },
                  { t: 'Loan EMI Calculator', url: '/calculators?calc=emi' },
                  { t: 'Health Insurance', url: '/insurance?tab=health' },
                  { t: 'PMS Introductions (₹50L+)', url: '/investments?tab=pms' },
                  { t: 'Book Consultation', url: '/book' },
                ]"
                :key="chip.t"
                type="button"
                @click="navigate(chip.url)"
                class="px-3 py-1.5 rounded-lg bg-kb-surface-2 hover:bg-kb-surface-3 border border-kb-line hover:border-kb-border text-xs text-kb-body hover:text-kb-text transition flex items-center gap-1.5"
              >
                <span>{{ chip.t }}</span>
                <SvgIcon name="i-arr" className="w-3 h-3 text-kb-accent" />
              </button>
            </div>
          </div>

          <div class="pt-2 border-t border-kb-line/40">
            <div class="text-[11px] font-bold uppercase tracking-wider text-kb-muted mb-2 flex items-center gap-1.5">
              <SvgIcon name="i-pin" className="w-3.5 h-3.5 text-kb-accent" />
              <span>Direct Assistance</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <Link
                href="/book"
                @click="emit('close')"
                class="p-2.5 rounded-xl bg-kb-surface-2/60 hover:bg-kb-surface-2 border border-kb-line hover:border-kb-accent/40 transition flex items-center justify-between group"
              >
                <div>
                  <div class="text-xs font-semibold text-kb-text group-hover:text-kb-accent">Schedule 30-min Call</div>
                  <div class="text-[11px] text-kb-muted">Free in-person or phone consultation</div>
                </div>
                <SvgIcon name="i-cal" className="w-4 h-4 text-kb-accent" />
              </Link>
              <a
                href="https://wa.me/917973461669?text=Hello%20KB%20Finvest,%20I%20have%20a%20query."
                target="_blank"
                rel="noopener"
                class="p-2.5 rounded-xl bg-emerald-950/20 hover:bg-emerald-950/40 border border-emerald-500/30 transition flex items-center justify-between group"
              >
                <div>
                  <div class="text-xs font-semibold text-emerald-300">Chat on WhatsApp</div>
                  <div class="text-[11px] text-emerald-400/80">Direct reply from Kulwinder Singh</div>
                </div>
                <SvgIcon name="i-wa" className="w-4 h-4 text-emerald-400" />
              </a>
            </div>
          </div>
        </div>

        <div v-else-if="results.length === 0" class="p-8 text-center text-sm text-kb-muted">
          No matches found for "<span class="text-kb-text">{{ query }}</span>".
          <div class="mt-2 text-xs">
            Try searching for <span class="text-kb-accent">SIP</span>, <span class="text-kb-accent">CGTMSE</span>, or
            <span class="text-kb-accent">Health</span>.
          </div>
        </div>

        <button
          v-for="(item, idx) in results"
          :key="idx"
          @click="navigate(item.url)"
          class="w-full text-left p-3.5 hover:bg-kb-surface-2/80 rounded-xl transition flex items-start gap-3 group"
        >
          <span
            class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-kb-surface-3 text-kb-accent border border-kb-border/40 shrink-0 mt-0.5"
          >
            {{ item.k }}
          </span>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-kb-text group-hover:text-kb-accent transition truncate">
              {{ item.t }}
            </div>
            <div class="text-xs text-kb-muted truncate mt-0.5">
              {{ item.d }}
            </div>
          </div>
          <SvgIcon
            name="i-arr"
            className="w-4 h-4 text-kb-muted group-hover:text-kb-accent transition group-hover:translate-x-1 shrink-0 mt-1"
          />
        </button>
      </div>
    </div>
  </div>
</template>
