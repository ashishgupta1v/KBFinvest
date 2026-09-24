<script setup>
import { computed, ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';

const props = defineProps({
  initialTopic: {
    type: String,
    default: 'General Financial Planning',
  },
});

const todayStr = new Date().toISOString().split('T')[0];

const form = useForm({
  name: '',
  phone: '',
  email: '',
  mode: 'Office visit',
  topic: props.initialTopic,
  date: todayStr,
  time: '11:30',
  notes: '',
  website_url: '',
});

const submitted = ref(false);
const currentStep = ref(1);
const preloadedContext = ref(null);

onMounted(() => {
  if (typeof window !== 'undefined') {
    const params = new URLSearchParams(window.location.search);
    const topicParam = params.get('topic');
    const calcParam = params.get('calc');
    const amountParam = params.get('amount');
    const yearsParam = params.get('years');
    const resultParam = params.get('result');
    const coverParam = params.get('cover');

    if (topicParam) {
      form.topic = topicParam;
    }

    if (calcParam || amountParam || resultParam) {
      preloadedContext.value = {
        calc: calcParam,
        amount: amountParam,
        years: yearsParam,
        result: resultParam,
        cover: coverParam,
      };

      const noteDetails = [];
      if (amountParam) noteDetails.push(`Target Amount: ₹${Number(amountParam).toLocaleString('en-IN')}`);
      if (yearsParam) noteDetails.push(`Horizon: ${yearsParam} years`);
      if (resultParam) noteDetails.push(`Estimated Outcome: ${resultParam}`);
      if (noteDetails.length) {
        form.notes = `[Pre-calculated with KB Finvest Tool] ${noteDetails.join(' | ')}`;
      }

      // Automatically advance to Step 2 for high conversion
      currentStep.value = 2;
    }
  }
});

function nextStep() {
  if (currentStep.value === 1 && !form.topic) return;
  if (currentStep.value === 2 && (!form.date || !form.time)) return;
  currentStep.value = Math.min(3, currentStep.value + 1);
}

function prevStep() {
  currentStep.value = Math.max(1, currentStep.value - 1);
}

// Topic Groups
const topicGroups = [
  {
    category: 'Investments & Wealth',
    icon: 'i-coin',
    topics: [
      { id: 'Mutual Funds & SIPs', title: 'Mutual Funds Portfolio & SIP Review', desc: 'Asset allocation & fund performance' },
      { id: 'PMS Introductions', title: 'PMS Portfolio Service (₹50L+)', desc: 'High-net-worth discretionary strategies' },
    ],
  },
  {
    category: 'Insurance & Protection',
    icon: 'i-shield',
    topics: [
      { id: 'Health Insurance', title: 'Health Insurance Gap & Portability', desc: 'Restoration, room rent & waiting period review' },
      { id: 'Life / Term Insurance', title: 'Life & Term Insurance Cover (HLV)', desc: 'Pure risk human life value calculation' },
    ],
  },
  {
    category: 'Lending & Credit',
    icon: 'i-tax',
    topics: [
      { id: 'CGTMSE Loan', title: 'Collateral-Free CGTMSE Loan', desc: 'MSME credit up to ₹5 Crore' },
      { id: 'Loan Against Property', title: 'Loan Against Property (LAP)', desc: 'Commercial & residential mortgage loans' },
      { id: 'Home Loan', title: 'Home Loan Purchase / Transfer', desc: 'Lowest rate balance transfers & fresh purchase' },
    ],
  },
  {
    category: 'Advisory & Planning',
    icon: 'i-chart',
    topics: [
      { id: 'Stressed Loan Advisory', title: 'Stressed Account / NPA Restructuring', desc: 'SMA/NPA negotiation & debt resolution' },
      { id: 'General Financial Planning', title: 'General 360° Financial Checkup', desc: 'Holistic wealth, tax & liability review' },
    ],
  },
];

// Quick Date Shortcuts
const dateShortcuts = computed(() => {
  const now = new Date();
  const tomorrow = new Date(now.getTime() + 86400000);
  const in2Days = new Date(now.getTime() + 2 * 86400000);

  const fmt = (d) => d.toISOString().split('T')[0];
  const label = (d, name) => ({
    val: fmt(d),
    label: name,
    sub: d.toLocaleDateString('en-IN', { weekday: 'short', month: 'short', day: 'numeric' }),
  });

  return [
    label(now, 'Today'),
    label(tomorrow, 'Tomorrow'),
    label(in2Days, 'In 2 Days'),
  ];
});

// Time Slots Grouped
const timeBuckets = [
  {
    period: 'Morning',
    icon: '🌅',
    slots: [
      { t: '10:00', l: '10:00 AM' },
      { t: '11:30', l: '11:30 AM' },
    ],
  },
  {
    period: 'Afternoon',
    icon: '☀️',
    slots: [
      { t: '14:30', l: '02:30 PM' },
      { t: '16:00', l: '04:00 PM' },
    ],
  },
  {
    period: 'Evening',
    icon: '🌆',
    slots: [
      { t: '17:30', l: '05:30 PM' },
      { t: '18:30', l: '06:30 PM' },
    ],
  },
];

// Contextual 'What to Bring' Checklist
const contextualChecklist = computed(() => {
  const t = form.topic;
  if (t === 'Mutual Funds & SIPs' || t === 'PMS Introductions') {
    return [
      { doc: 'PAN Card & Aadhaar Card', reason: 'Mandatory for SEBI KYC verification.' },
      { doc: 'Consolidated Account Statement (CAS)', reason: 'Latest CAMS/KFintech or Demat statement to audit current holdings.' },
      { doc: 'Cancelled Cheque or Bank Passbook', reason: 'To register IFSC for direct SIP / redemption bank mandate.' },
      { doc: 'Target Timeline & Goals', reason: 'Approximate time horizon (3y, 5y, 10y+) and liquidity requirements.' },
    ];
  }
  if (t === 'Health Insurance' || t === 'Life / Term Insurance') {
    return [
      { doc: 'Existing Policy Schedules', reason: 'Current coverage, policy wordings, and premium receipts.' },
      { doc: 'Medical History & Discharge Summaries', reason: 'Any pre-existing diseases, surgeries, or ongoing regular medications.' },
      { doc: 'Recent Form 16 / Income Proof', reason: 'Required by underwriters to approve higher Human Life Value (HLV) term cover.' },
      { doc: 'Aadhaar & PAN', reason: 'Required for IRDAI e-KYC compliance.' },
    ];
  }
  if (t === 'CGTMSE Loan' || t === 'Loan Against Property' || t === 'Home Loan') {
    return [
      { doc: 'Last 2–3 Years ITR & Computation', reason: 'Audited financial statements and tax filings for income validation.' },
      { doc: 'Last 6 Months Bank Statement', reason: 'Current and savings accounts showing cash flows and EMI clearances.' },
      { doc: 'Property Documents / Collateral Proof', reason: 'Title deed, approved map, or chain of title for mortgage verification.' },
      { doc: 'Business Registration Proof (Udyam/GST)', reason: 'For MSME / commercial loans.' },
    ];
  }
  // Stressed Loan Advisory / General Planning
  return [
    { doc: 'Existing Loan & EMI Sanction Letters', reason: 'Outstanding principal, interest rates, and loan tenure details.' },
    { doc: 'Bank Notices / Communication (if any)', reason: 'SMA/NPA letters or bank restructuring correspondence.' },
    { doc: 'PAN Card & Aadhaar', reason: 'Identity and credit report verification.' },
    { doc: 'Monthly Household Cash Flow Estimate', reason: 'Net monthly inflow vs living expenses and loan obligations.' },
  ];
});

const conflictError = computed(() => {
  if (form.errors.conflict) return form.errors.conflict;
  if (form.errors.time && form.errors.time.includes('already scheduled')) return form.errors.time;
  if (form.errors.phone && form.errors.phone.includes('already scheduled')) return form.errors.phone;
  return null;
});

function selectDate(dateVal) {
  form.date = dateVal;
  if (form.errors.date) form.clearErrors('date');
  if (form.errors.time) form.clearErrors('time');
  if (form.errors.conflict) form.clearErrors('conflict');
  if (form.errors.phone && form.errors.phone.includes('already scheduled')) {
    form.clearErrors('phone');
  }
}

function selectSlot(slotTime) {
  form.time = slotTime;
  if (form.errors.time) form.clearErrors('time');
  if (form.errors.conflict) form.clearErrors('conflict');
  if (form.errors.phone && form.errors.phone.includes('already scheduled')) {
    form.clearErrors('phone');
  }
}

function submitBooking() {
  form.post('/book', {
    preserveScroll: true,
    onSuccess: () => {
      submitted.value = true;
    },
    onError: (errors) => {
      setTimeout(() => {
        const conflictEl = document.querySelector('#slot-conflict-alert') || document.querySelector('.has-form-error');
        if (conflictEl) {
          conflictEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }, 50);
    },
  });
}

// Google Calendar URL Generator
const googleCalendarUrl = computed(() => {
  const start = new Date(`${form.date}T${form.time}:00+05:30`);
  const end = new Date(start.getTime() + 30 * 60000);
  const fmt = (d) => d.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');

  const title = encodeURIComponent(`KB Finvest Consultation — ${form.topic}`);
  const details = encodeURIComponent(`30-minute private consultation with Kulwinder Singh (+91 79734 61669).\nMode: ${form.mode}\nClient: ${form.name}`);
  const location = encodeURIComponent(form.mode === 'Office visit' ? '179-A, St. No. 1, Central Town, Ludhiana, Punjab' : form.mode);

  return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${fmt(start)}/${fmt(end)}&details=${details}&location=${location}`;
});

function downloadIcs() {
  const start = new Date(`${form.date}T${form.time}:00+05:30`);
  const end = new Date(start.getTime() + 30 * 60000);
  const f = (x) => x.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');

  const icsContent = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//KB Finvest//Appointment//EN',
    'BEGIN:VEVENT',
    `UID:${Date.now()}@kbfinvest`,
    `DTSTAMP:${f(new Date())}`,
    `DTSTART:${f(start)}`,
    `DTEND:${f(end)}`,
    `SUMMARY:KB Finvest Consultation — ${form.topic}`,
    `LOCATION:${form.mode === 'Office visit' ? '179-A, Central Town, Ludhiana' : form.mode}`,
    'DESCRIPTION:30-minute consultation with Kulwinder Singh, KB Finvest (+91 79734 61669).',
    'END:VEVENT',
    'END:VCALENDAR',
  ].join('\r\n');

  const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `KB-Finvest-Appointment-${form.date}.ics`;
  link.click();
}
</script>

<template>
  <AppLayout>
    <Head title="Book a 30-min Consultation — KB Finvest Ludhiana" />

    <!-- Hero Header with Ambient Glow -->
    <section class="relative py-16 lg:py-20 border-b border-kb-line bg-gradient-to-b from-kb-surface-2/60 via-kb-surface/40 to-kb-surface overflow-hidden">
      <div class="absolute -top-32 -left-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-20 right-10 w-96 h-96 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-4">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-xs font-semibold uppercase tracking-wider text-amber-300">
            <SvgIcon name="shield-check" class="w-3.5 h-3.5 text-amber-400" />
            <span>Private Wealth Concierge · Zero Obligation</span>
          </div>
          <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold font-display tracking-tight text-kb-text">
            Schedule a <span class="italic text-gold-gradient font-serif">30-Minute Consultation</span>
          </h1>
          <p class="text-base sm:text-lg text-kb-body leading-relaxed max-w-2xl">
            Meet directly with <strong>Kulwinder Singh</strong>. Free of charge, with zero obligation. Choose to visit our Central Town office in Ludhiana, connect over a phone call, or meet virtually.
          </p>
        </div>
      </div>
    </section>

    <!-- Booking Workspace -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          <!-- Left: Booking Form -->
          <div class="lg:col-span-7 card-luxury p-6 sm:p-10 space-y-8">
            <!-- SUCCESS STATE -->
            <div v-if="submitted" class="p-8 sm:p-10 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-center space-y-5 backdrop-blur-md">
              <div class="w-20 h-20 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto shadow-xl shadow-emerald-950/50 border border-emerald-500/30">
                <SvgIcon name="i-check" className="w-10 h-10" />
              </div>
              <div class="space-y-2">
                <h3 class="text-2xl sm:text-3xl font-bold text-emerald-300 font-display">Consultation Requested!</h3>
                <p class="text-xs sm:text-sm text-emerald-200/90 max-w-md mx-auto leading-relaxed">
                  Thank you, <strong>{{ form.name }}</strong>. We have logged your request for <strong>{{ form.date }} at {{ form.time }}</strong> via {{ form.mode }}. Kulwinder Singh will confirm with you on WhatsApp within 2 hours.
                </p>
              </div>

              <!-- Quick Action Calendar Links -->
              <div class="pt-4 flex flex-col sm:flex-row flex-wrap justify-center gap-3">
                <a
                  :href="googleCalendarUrl"
                  target="_blank"
                  rel="noopener"
                  class="btn-shimmer w-full sm:w-auto min-h-[44px] px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition"
                >
                  <SvgIcon name="i-cal" className="w-4 h-4" />
                  <span>Add to Google Calendar</span>
                </a>
                <button
                  @click="downloadIcs"
                  class="w-full sm:w-auto min-h-[44px] px-5 py-3 rounded-xl border border-emerald-400/50 text-emerald-300 hover:bg-emerald-900/40 font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer"
                >
                  <SvgIcon name="i-doc" className="w-4 h-4" />
                  <span>Download .ics (Apple/Outlook)</span>
                </button>
                <a
                  :href="`https://wa.me/917973461669?text=${encodeURIComponent('Hello Kulwinder, I just requested a consultation for ' + form.date + ' at ' + form.time + ' regarding ' + form.topic)}`"
                  target="_blank"
                  rel="noopener"
                  class="w-full sm:w-auto min-h-[44px] px-5 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 hover:bg-emerald-500/30 font-bold text-xs flex items-center justify-center gap-2 transition"
                >
                  <SvgIcon name="i-wa" className="w-4 h-4" />
                  <span>Notify Kulwinder on WhatsApp</span>
                </a>
              </div>
            </div>

            <!-- INTERACTIVE 3-STEP WIZARD FORM -->
            <form v-else @submit.prevent="submitBooking" class="space-y-6">
              <!-- Anti-bot honeypot -->
              <input
                type="text"
                name="website_url"
                v-model="form.website_url"
                class="hidden opacity-0 absolute -left-[9999px] pointer-events-none"
                tabindex="-1"
                autocomplete="off"
              />

              <!-- Pre-loaded Context Banner -->
              <div
                v-if="preloadedContext"
                class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-start justify-between gap-3 text-xs"
              >
                <div class="flex items-start gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-kb-accent flex items-center justify-center shrink-0 mt-0.5">
                    <SvgIcon name="i-growth" className="w-4 h-4" />
                  </div>
                  <div>
                    <div class="font-bold text-kb-text flex items-center gap-2">
                      <span>Calculated Plan Pre-Loaded:</span>
                      <span class="text-kb-accent font-mono">{{ form.topic }}</span>
                    </div>
                    <p class="text-kb-muted mt-0.5 leading-snug">
                      <span v-if="preloadedContext.amount">Target: ₹{{ Number(preloadedContext.amount).toLocaleString('en-IN') }} · </span>
                      <span v-if="preloadedContext.years">Horizon: {{ preloadedContext.years }} yrs · </span>
                      <span v-if="preloadedContext.result" class="text-emerald-400 font-semibold">Outcome: {{ preloadedContext.result }}</span>
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="currentStep = 1"
                  class="text-[11px] font-bold text-kb-accent hover:underline shrink-0"
                >
                  Change Topic
                </button>
              </div>

              <!-- Wizard Progress Bar & Step Indicators -->
              <div class="pb-6 border-b border-kb-line">
                <div class="flex items-center justify-between text-xs mb-2">
                  <span class="font-bold uppercase tracking-wider text-kb-accent">
                    Step {{ currentStep }} of 3: {{ currentStep === 1 ? 'Topic & Meeting Mode' : (currentStep === 2 ? 'Date & Time Slot' : 'Your Details & Confirm') }}
                  </span>
                  <span class="text-kb-muted font-mono text-[11px]">{{ Math.round((currentStep / 3) * 100) }}% Completed</span>
                </div>
                <div class="w-full h-1.5 bg-kb-surface-3 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-gold-gradient transition-all duration-300 ease-out"
                    :style="{ width: `${(currentStep / 3) * 100}%` }"
                  ></div>
                </div>
                <!-- Step Tabs for easy jumping back -->
                <div class="grid grid-cols-3 gap-2 mt-4">
                  <button
                    type="button"
                    @click="currentStep = 1"
                    :class="[
                      'text-left p-2.5 sm:p-3 rounded-xl transition border text-xs min-h-[48px] flex flex-col justify-center cursor-pointer',
                      currentStep === 1
                        ? 'bg-kb-surface-2 border-kb-accent text-kb-text font-bold shadow-sm'
                        : 'bg-kb-surface/40 border-kb-line/40 text-kb-muted hover:text-kb-text',
                    ]"
                  >
                    <div class="text-[10px] uppercase tracking-wider text-kb-accent font-semibold">Step 1</div>
                    <div class="truncate font-medium text-[11px] sm:text-xs">Topic & Mode</div>
                  </button>
                  <button
                    type="button"
                    @click="currentStep >= 2 ? currentStep = 2 : null"
                    :class="[
                      'text-left p-2.5 sm:p-3 rounded-xl transition border text-xs min-h-[48px] flex flex-col justify-center',
                      currentStep === 2
                        ? 'bg-kb-surface-2 border-kb-accent text-kb-text font-bold shadow-sm cursor-pointer'
                        : 'bg-kb-surface/40 border-kb-line/40 text-kb-muted hover:text-kb-text',
                      currentStep < 2 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
                    ]"
                  >
                    <div class="text-[10px] uppercase tracking-wider text-kb-accent font-semibold">Step 2</div>
                    <div class="truncate font-medium text-[11px] sm:text-xs">Date & Slot</div>
                  </button>
                  <button
                    type="button"
                    @click="currentStep >= 3 ? currentStep = 3 : null"
                    :class="[
                      'text-left p-2.5 sm:p-3 rounded-xl transition border text-xs min-h-[48px] flex flex-col justify-center',
                      currentStep === 3
                        ? 'bg-kb-surface-2 border-kb-accent text-kb-text font-bold shadow-sm cursor-pointer'
                        : 'bg-kb-surface/40 border-kb-line/40 text-kb-muted hover:text-kb-text',
                      currentStep < 3 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
                    ]"
                  >
                    <div class="text-[10px] uppercase tracking-wider text-kb-accent font-semibold">Step 3</div>
                    <div class="truncate font-medium text-[11px] sm:text-xs">Confirmation</div>
                  </button>
                </div>
              </div>

              <!-- STEP 1: Meeting Mode & Topic Selection -->
              <div v-show="currentStep === 1" class="space-y-6 animate-in fade-in">
                <!-- Mode Selector -->
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-amber-300 flex items-center gap-2">
                      <span>1. Choose Meeting Mode *</span>
                    </label>
                    <span class="text-[11px] text-kb-muted">In-person or remote</span>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <button
                      type="button"
                      @click="form.mode = 'Office visit'"
                      :class="[
                        'p-3.5 rounded-xl text-left border transition-all duration-200 flex flex-col justify-between gap-3 min-h-[90px] cursor-pointer',
                        form.mode === 'Office visit'
                          ? 'bg-amber-500/15 border-amber-400 text-kb-text shadow-md shadow-amber-500/10'
                          : 'bg-kb-surface-2/60 border-kb-line hover:border-kb-border text-kb-body',
                      ]"
                    >
                      <div class="flex items-center justify-between w-full">
                        <div class="w-8 h-8 rounded-lg bg-kb-surface-3 flex items-center justify-center">
                          <SvgIcon name="i-pin" :className="form.mode === 'Office visit' ? 'w-4 h-4 text-amber-400' : 'w-4 h-4 text-kb-muted'" />
                        </div>
                        <span v-if="form.mode === 'Office visit'" class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm"></span>
                      </div>
                      <div>
                        <div class="text-xs font-bold font-display text-kb-text">Office Visit</div>
                        <div class="text-[10px] text-kb-muted mt-0.5">Central Town, Ludhiana</div>
                      </div>
                    </button>

                    <button
                      type="button"
                      @click="form.mode = 'Phone call'"
                      :class="[
                        'p-3.5 rounded-xl text-left border transition-all duration-200 flex flex-col justify-between gap-3 min-h-[90px] cursor-pointer',
                        form.mode === 'Phone call'
                          ? 'bg-amber-500/15 border-amber-400 text-kb-text shadow-md shadow-amber-500/10'
                          : 'bg-kb-surface-2/60 border-kb-line hover:border-kb-border text-kb-body',
                      ]"
                    >
                      <div class="flex items-center justify-between w-full">
                        <div class="w-8 h-8 rounded-lg bg-kb-surface-3 flex items-center justify-center">
                          <SvgIcon name="i-phone" :className="form.mode === 'Phone call' ? 'w-4 h-4 text-amber-400' : 'w-4 h-4 text-kb-muted'" />
                        </div>
                        <span v-if="form.mode === 'Phone call'" class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm"></span>
                      </div>
                      <div>
                        <div class="text-xs font-bold font-display text-kb-text">Phone Call</div>
                        <div class="text-[10px] text-kb-muted mt-0.5">Direct line with Kulwinder</div>
                      </div>
                    </button>

                    <button
                      type="button"
                      @click="form.mode = 'Video call'"
                      :class="[
                        'p-3.5 rounded-xl text-left border transition-all duration-200 flex flex-col justify-between gap-3 min-h-[90px] cursor-pointer',
                        form.mode === 'Video call'
                          ? 'bg-amber-500/15 border-amber-400 text-kb-text shadow-md shadow-amber-500/10'
                          : 'bg-kb-surface-2/60 border-kb-line hover:border-kb-border text-kb-body',
                      ]"
                    >
                      <div class="flex items-center justify-between w-full">
                        <div class="w-8 h-8 rounded-lg bg-kb-surface-3 flex items-center justify-center">
                          <SvgIcon name="i-shield" :className="form.mode === 'Video call' ? 'w-4 h-4 text-amber-400' : 'w-4 h-4 text-kb-muted'" />
                        </div>
                        <span v-if="form.mode === 'Video call'" class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm"></span>
                      </div>
                      <div>
                        <div class="text-xs font-bold font-display text-kb-text">Video Call</div>
                        <div class="text-[10px] text-kb-muted mt-0.5">Google Meet / WhatsApp</div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Discussion Topic Selection -->
                <div class="space-y-3 pt-2">
                  <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-amber-300">
                      2. Select Discussion Priority *
                    </label>
                    <span class="text-[11px] text-kb-muted font-mono">{{ form.topic }}</span>
                  </div>

                  <div class="space-y-3">
                    <div
                      v-for="group in topicGroups"
                      :key="group.category"
                      class="space-y-1.5"
                    >
                      <div class="text-[10px] font-bold uppercase tracking-wider text-kb-muted flex items-center gap-1.5">
                        <SvgIcon :name="group.icon" className="w-3 h-3 text-amber-400" />
                        <span>{{ group.category }}</span>
                      </div>
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button
                          v-for="t in group.topics"
                          :key="t.id"
                          type="button"
                          @click="form.topic = t.id"
                          :class="[
                            'p-2.5 rounded-xl text-left border transition-all duration-150 cursor-pointer flex items-start justify-between gap-2',
                            form.topic === t.id
                              ? 'bg-amber-500/15 border-amber-400 text-kb-text shadow-sm'
                              : 'bg-kb-surface-2/40 border-kb-line/80 hover:border-kb-border hover:bg-kb-surface-2 text-kb-body',
                          ]"
                        >
                          <div class="space-y-0.5">
                            <div class="text-xs font-bold text-kb-text">{{ t.title }}</div>
                            <div class="text-[10px] text-kb-muted leading-tight">{{ t.desc }}</div>
                          </div>
                          <span
                            v-if="form.topic === t.id"
                            class="w-3.5 h-3.5 rounded-full bg-amber-400 text-slate-950 flex items-center justify-center text-[9px] font-bold shrink-0 mt-0.5"
                          >
                            ✓
                          </span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Step 1 Navigation -->
                <div class="pt-4 flex flex-col sm:flex-row justify-end items-stretch sm:items-center">
                  <button
                    type="button"
                    @click="nextStep"
                    class="btn-shimmer w-full sm:w-auto min-h-[44px] px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black flex items-center justify-center gap-2 hover:opacity-95 shadow-lg active:scale-95 cursor-pointer"
                  >
                    <span>Choose Date & Time Slot</span>
                    <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>

              <!-- STEP 2: Date & Time Slot Selection -->
              <div v-show="currentStep === 2" class="space-y-6 animate-in fade-in">
                <!-- Conflict Notification on Step 2 -->
                <div
                  v-if="conflictError"
                  class="p-4 rounded-xl bg-rose-950/70 border border-rose-500/80 text-rose-100 text-xs flex items-start justify-between gap-3 shadow-lg shadow-rose-950/40"
                >
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 font-bold text-sm shrink-0 mt-0.5">⚠️</span>
                    <div class="space-y-0.5">
                      <div class="font-bold text-rose-300">Slot Already Scheduled</div>
                      <p class="text-rose-200/90 leading-relaxed">
                        The slot <strong>{{ form.date }} at {{ form.time }}</strong> is already reserved for this phone number. Please choose an alternate date or time slot below:
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Date Shortcuts & Picker -->
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-amber-300">
                      Pick Date *
                    </label>
                    <span class="text-[11px] text-kb-muted font-mono">{{ form.date }}</span>
                  </div>

                  <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    <button
                      v-for="s in dateShortcuts"
                      :key="s.val"
                      type="button"
                      @click="selectDate(s.val)"
                      :class="[
                        'py-2 px-3 rounded-xl text-left border transition-all duration-150 cursor-pointer min-h-[48px] flex flex-col justify-center',
                        form.date === s.val
                          ? 'bg-amber-500/20 border-amber-400 text-kb-text shadow-sm'
                          : 'bg-kb-surface-2/50 border-kb-line hover:border-kb-border text-kb-body',
                      ]"
                    >
                      <div class="text-xs font-bold">{{ s.label }}</div>
                      <div class="text-[10px] text-kb-muted">{{ s.sub }}</div>
                    </button>

                    <!-- Custom Date Input -->
                    <div class="relative col-span-3 sm:col-span-1 min-h-[48px]">
                      <input
                        type="date"
                        v-model="form.date"
                        :min="todayStr"
                        required
                        aria-label="Select custom appointment date"
                        @input="selectDate($event.target.value)"
                        class="w-full h-full min-h-[48px] py-2 px-3 bg-kb-surface-2/50 border border-kb-line rounded-xl text-xs text-kb-text outline-none focus:border-amber-400 transition"
                      />
                    </div>
                  </div>
                  <div v-if="form.errors.date" class="text-xs text-rose-400">{{ form.errors.date }}</div>
                </div>

                <!-- Time Slot Buckets -->
                <div class="space-y-3 pt-2">
                  <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-amber-300">
                      Pick Time Slot *
                    </label>
                    <span class="text-[11px] text-kb-muted font-mono">{{ form.time }}</span>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div
                      v-for="bucket in timeBuckets"
                      :key="bucket.period"
                      class="p-3 rounded-xl bg-kb-surface-2/40 border border-kb-border/40 space-y-2"
                    >
                      <div class="text-[11px] font-bold text-kb-muted flex items-center gap-1.5">
                        <span>{{ bucket.icon }}</span>
                        <span>{{ bucket.period }}</span>
                      </div>
                      <div class="grid grid-cols-2 gap-2">
                        <button
                          v-for="slot in bucket.slots"
                          :key="slot.t"
                          type="button"
                          @click="selectSlot(slot.t)"
                          :class="[
                            'py-2 px-1 rounded-lg text-xs font-mono text-center transition-all duration-150 border cursor-pointer min-h-[44px] flex flex-col items-center justify-center',
                            conflictError && form.time === slot.t
                              ? 'bg-rose-950/70 text-rose-300 font-bold border-2 border-rose-500 shadow-md shadow-rose-950/50'
                              : form.time === slot.t
                                ? 'bg-amber-400 text-slate-950 font-bold border-amber-400 shadow-sm'
                                : 'bg-kb-surface-3/60 text-kb-body border-kb-line hover:border-kb-border hover:text-kb-text',
                          ]"
                        >
                          <span>{{ slot.l }}</span>
                          <span
                            v-if="conflictError && form.time === slot.t"
                            class="text-[9px] text-rose-400 font-sans uppercase font-bold tracking-tight"
                          >
                            Booked
                          </span>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-if="form.errors.time && !conflictError" class="text-xs text-rose-400">{{ form.errors.time }}</div>
                </div>

                <!-- Step 2 Navigation -->
                <div class="pt-4 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-kb-line">
                  <button
                    type="button"
                    @click="prevStep"
                    class="w-full sm:w-auto min-h-[44px] flex items-center justify-center px-5 py-2.5 rounded-xl border border-kb-line text-kb-body hover:text-kb-text hover:bg-kb-surface-2 text-xs font-semibold cursor-pointer"
                  >
                    ← Back to Topic
                  </button>
                  <button
                    type="button"
                    @click="nextStep"
                    class="btn-shimmer w-full sm:w-auto min-h-[44px] px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black flex items-center justify-center gap-2 hover:opacity-95 shadow-lg active:scale-95 cursor-pointer"
                  >
                    <span>Enter Your Details</span>
                    <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>

              <!-- STEP 3: Client Details & Final Confirmation -->
              <div v-show="currentStep === 3" class="space-y-6 animate-in fade-in">
                <!-- APPOINTMENT SLOT CONFLICT ALERT BANNER -->
                <div
                  v-if="conflictError"
                  id="slot-conflict-alert"
                  role="alert"
                  class="p-4 sm:p-5 rounded-2xl bg-rose-950/80 border-2 border-rose-500 text-rose-100 shadow-2xl shadow-rose-950/60 space-y-3 animate-in fade-in ring-4 ring-rose-500/20"
                >
                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center shrink-0">
                      <span class="text-xl">⚠️</span>
                    </div>
                    <div class="space-y-1">
                      <div class="flex flex-wrap items-center gap-2">
                        <h4 class="text-sm font-bold text-rose-300 font-display">Appointment Slot Conflict</h4>
                        <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full bg-rose-500/25 border border-rose-500/40 font-mono font-bold text-rose-200">
                          Already Booked
                        </span>
                      </div>
                      <p class="text-xs text-rose-100 font-medium leading-relaxed">
                        {{ conflictError }}
                      </p>
                    </div>
                  </div>

                  <div class="pt-1 flex flex-wrap items-center gap-2.5">
                    <button
                      type="button"
                      @click="currentStep = 2"
                      class="min-h-[40px] px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs flex items-center gap-2 shadow-lg transition cursor-pointer"
                    >
                      <SvgIcon name="i-cal" className="w-4 h-4" />
                      <span>Select Another Date or Time Slot</span>
                    </button>
                    <a
                      :href="`https://wa.me/917973461669?text=${encodeURIComponent('Hello Kulwinder, my number ' + form.phone + ' already has a consultation scheduled for ' + form.date + ' at ' + form.time + '. Can you check my existing booking or reschedule?')}`"
                      target="_blank"
                      rel="noopener"
                      class="min-h-[40px] px-3.5 py-2 rounded-xl border border-rose-400/40 hover:bg-rose-900/40 text-rose-200 text-xs font-semibold flex items-center gap-1.5 transition"
                    >
                      <SvgIcon name="i-wa" className="w-4 h-4 text-emerald-400" />
                      <span>WhatsApp Kulwinder Directly</span>
                    </a>
                  </div>
                </div>

                <!-- Summary Pill Card -->
                <div class="p-3.5 rounded-xl bg-kb-surface-2 border border-kb-border/50 text-xs flex flex-wrap items-center justify-between gap-3">
                  <div class="space-y-0.5">
                    <div class="text-kb-muted text-[10px] uppercase font-bold tracking-wider">Scheduled Consultation</div>
                    <div class="font-bold text-kb-text">{{ form.topic }}</div>
                  </div>
                  <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 rounded bg-kb-surface-3 text-kb-accent">{{ form.mode }}</span>
                    <span class="px-2 py-0.5 rounded bg-kb-surface-3 text-kb-text">{{ form.date }} · {{ form.time }}</span>
                  </div>
                </div>

                <!-- Contact Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Name -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-medium text-kb-body">Your Full Name *</label>
                    <input
                      type="text"
                      v-model="form.name"
                      required
                      placeholder="e.g. Jaspreet Singh"
                      :class="[
                        'w-full rounded-xl px-4 py-3 text-sm text-kb-text outline-none transition',
                        form.errors.name
                          ? 'border-2 border-rose-500 bg-rose-950/20 focus:border-rose-400'
                          : 'bg-kb-surface-2/60 border border-kb-line focus:border-amber-400 focus:bg-kb-surface-2'
                      ]"
                    />
                    <div v-if="form.errors.name" class="text-xs text-rose-400">{{ form.errors.name }}</div>
                  </div>

                  <!-- Phone -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-medium text-kb-body">WhatsApp / Mobile Number *</label>
                    <input
                      type="tel"
                      v-model="form.phone"
                      required
                      placeholder="+91 98765 43210"
                      :class="[
                        'w-full rounded-xl px-4 py-3 text-sm text-kb-text outline-none transition',
                        form.errors.phone
                          ? 'border-2 border-rose-500 bg-rose-950/20 text-rose-100 focus:border-rose-400'
                          : 'bg-kb-surface-2/60 border border-kb-line focus:border-amber-400 focus:bg-kb-surface-2'
                      ]"
                    />
                    <div v-if="form.errors.phone" class="text-xs text-rose-400 font-medium flex items-start gap-1 mt-1">
                      <span class="text-sm shrink-0">⚠️</span>
                      <span>{{ form.errors.phone }}</span>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Email -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-medium text-kb-body">Email Address (Optional)</label>
                    <input
                      type="email"
                      v-model="form.email"
                      placeholder="name@example.com"
                      class="w-full bg-kb-surface-2/60 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-amber-400 focus:bg-kb-surface-2 transition"
                    />
                  </div>

                  <!-- Notes -->
                  <div class="space-y-1.5">
                    <label class="text-xs font-medium text-kb-body">Specific Queries / Notes (Optional)</label>
                    <input
                      type="text"
                      v-model="form.notes"
                      placeholder="e.g. Need review of existing 5 SIPs"
                      class="w-full bg-kb-surface-2/60 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-amber-400 focus:bg-kb-surface-2 transition"
                    />
                  </div>
                </div>

                <!-- Trust Guarantee Micro-copy -->
                <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200/90 flex items-start gap-2.5 leading-relaxed">
                  <SvgIcon name="i-shield" className="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
                  <span>
                    <strong>Privacy & Confidentiality Guarantee:</strong> Your contact information is never shared or marketed. Used strictly for your private consultation with Kulwinder Singh.
                  </span>
                </div>

                <!-- Step 3 Navigation & Final Submit -->
                <div class="space-y-3 pt-2">
                  <!-- Conflict guidance right above submit button -->
                  <div
                    v-if="conflictError"
                    class="p-3.5 rounded-xl bg-rose-950/70 border border-rose-500 text-xs text-rose-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5"
                  >
                    <div class="flex items-center gap-2">
                      <span class="text-rose-400 font-bold">⚠️</span>
                      <span>Slot conflict: Please pick another slot to confirm.</span>
                    </div>
                    <button
                      type="button"
                      @click="currentStep = 2"
                      class="px-3 py-1.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs flex items-center gap-1.5 transition cursor-pointer"
                    >
                      <SvgIcon name="i-cal" className="w-3.5 h-3.5" />
                      <span>Pick Available Slot</span>
                    </button>
                  </div>

                  <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-kb-line pt-3">
                    <button
                      type="button"
                      @click="prevStep"
                      class="w-full sm:w-auto min-h-[44px] flex items-center justify-center px-5 py-2.5 rounded-xl border border-kb-line text-kb-body hover:text-kb-text hover:bg-kb-surface-2 text-xs font-semibold cursor-pointer"
                    >
                      ← Change Slot
                    </button>

                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="btn-shimmer w-full sm:w-auto min-h-[44px] px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:brightness-105 transition shadow-xl shadow-amber-500/15 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                    >
                      <SvgIcon name="i-cal" className="w-4 h-4" />
                      <span>{{ form.processing ? 'Securing Slot...' : 'Confirm 30-Min Consultation' }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Right Column: Sticky Live Summary, Contextual Checklist & Direct Desk Call -->
          <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
            <!-- Sticky Live Summary Card -->
            <div class="card-luxury p-6 space-y-5 border-amber-500/30">
              <div class="flex items-center justify-between pb-3.5 border-b border-kb-line">
                <div class="space-y-0.5">
                  <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Appointment Overview</span>
                  <p class="text-[11px] text-kb-muted">Real-time booking details</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">
                  Free · Zero Obligation
                </span>
              </div>

              <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Lead Advisor</span>
                  <div class="text-right">
                    <span class="font-bold text-kb-text block">Kulwinder Singh</span>
                    <span class="text-[10px] text-amber-300 font-mono">ARN-286477 (AMFI)</span>
                  </div>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Format</span>
                  <span class="font-bold text-amber-300 px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20">{{ form.mode }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Topic</span>
                  <span class="font-bold text-kb-text truncate max-w-[210px]" :title="form.topic">{{ form.topic }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Slot Scheduled</span>
                  <div class="text-right">
                    <span class="font-bold text-kb-text font-mono bg-kb-surface-2 px-2 py-0.5 rounded border border-kb-border/50">
                      {{ form.date }} · {{ form.time }}
                    </span>
                    <span v-if="conflictError" class="text-[10px] text-rose-400 font-bold block mt-1">
                      ⚠️ Slot Conflict: Already Booked
                    </span>
                  </div>
                </div>
              </div>

              <!-- Central Town Desk Address -->
              <div class="p-3.5 rounded-xl bg-kb-surface-2/60 border border-kb-border/50 text-xs text-kb-muted leading-relaxed space-y-1">
                <div class="font-bold text-kb-text flex items-center gap-1.5">
                  <SvgIcon name="i-pin" className="w-3.5 h-3.5 text-amber-400" />
                  <span>Central Town Consultation Room</span>
                </div>
                <p class="text-[11px]">
                  179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, Punjab.
                </p>
              </div>
            </div>

            <!-- Contextual 'What to Bring' Checklist -->
            <div class="card-luxury p-6 space-y-4">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 flex items-center justify-center text-amber-400">
                  <SvgIcon name="i-doc" className="w-4 h-4" />
                </div>
                <div>
                  <h3 class="text-sm font-bold text-kb-text font-display">What to Bring / Keep Ready</h3>
                  <p class="text-[11px] text-kb-muted">Customized for {{ form.topic }}</p>
                </div>
              </div>

              <ul class="space-y-3 text-xs text-kb-body pt-1">
                <li
                  v-for="(item, idx) in contextualChecklist"
                  :key="idx"
                  class="flex items-start gap-2.5 pb-2.5 border-b border-kb-line/40 last:border-0 last:pb-0"
                >
                  <span class="text-emerald-400 font-bold text-sm leading-none shrink-0 mt-0.5">✓</span>
                  <div>
                    <div class="font-semibold text-kb-text">{{ item.doc }}</div>
                    <div class="text-[11px] text-kb-muted leading-tight mt-0.5">{{ item.reason }}</div>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Direct Call alternative -->
            <div class="card-luxury p-6 space-y-4">
              <h3 class="text-sm font-bold text-kb-text font-display">Prefer speaking immediately?</h3>
              <p class="text-xs text-kb-muted leading-relaxed">
                Skip the form. Call during office hours to speak directly with Kulwinder Singh without call-centre hold music.
              </p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                <a
                  href="tel:+917973461669"
                  class="min-h-[44px] py-2.5 px-3 rounded-xl border border-kb-line hover:border-kb-border transition text-xs font-bold text-kb-text flex items-center justify-center gap-2 hover:bg-kb-surface-2"
                >
                  <SvgIcon name="i-phone" className="w-3.5 h-3.5 text-amber-400" />
                  <span>Call Office</span>
                </a>
                <a
                  href="https://wa.me/917973461669?text=Hello%20KB%20Finvest,%20I%20would%20like%20to%20speak%20with%20Kulwinder%20Singh."
                  target="_blank"
                  rel="noopener"
                  class="min-h-[44px] py-2.5 px-3 rounded-xl border border-emerald-500/40 text-emerald-400 bg-emerald-950/20 hover:bg-emerald-950/40 transition text-xs font-bold flex items-center justify-center gap-2"
                >
                  <SvgIcon name="i-wa" className="w-3.5 h-3.5" />
                  <span>WhatsApp</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

