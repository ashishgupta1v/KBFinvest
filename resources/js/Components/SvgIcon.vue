<script setup>
import { computed } from 'vue';
import {
  TrendingUp,
  PieChart,
  Award,
  Shield,
  ShieldCheck,
  ShieldAlert,
  Coins,
  Landmark,
  Home,
  Briefcase,
  Car,
  Heart,
  HeartPulse,
  Users,
  Calculator,
  Phone,
  Mail,
  MapPin,
  Clock,
  Calendar,
  ArrowRight,
  ArrowLeft,
  ArrowUpRight,
  Search,
  Menu,
  X,
  Moon,
  Sun,
  Check,
  CheckCircle2,
  Lock,
  LockKeyhole,
  FileText,
  FileCheck,
  FileQuestion,
  FileSearch,
  Scale,
  RotateCw,
  Target,
  Rocket,
  AlertTriangle,
  Info,
  Star,
  Printer,
  Receipt,
  BarChart3,
  LineChart,
  ExternalLink,
  HelpCircle,
  ChevronDown,
  ChevronRight,
  ChevronUp,
  ChevronLeft,
  Sparkles,
  Wallet,
  PiggyBank,
  CreditCard,
  Building2,
  Percent,
  BadgePercent,
  BadgeCheck,
  MessageCircle,
  MessageSquare,
  Send,
  Share2,
  Copy,
  Download,
  Layers,
  Activity,
  Compass,
  BookOpen,
  Instagram,
} from 'lucide-vue-next';

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  className: {
    type: String,
    default: 'w-5 h-5',
  },
  strokeWidth: {
    type: [Number, String],
    default: 1.75,
  },
});

// Comprehensive alias dictionary mapping every theme name and legacy alias to pure Lucide
const iconMap = {
  // Legacy 'i-' aliases
  'i-growth': TrendingUp,
  'i-pie': PieChart,
  'i-cert': Award,
  'i-shield': Shield,
  'i-coin': Coins,
  'i-bank': Landmark,
  'i-home': Home,
  'i-brief': Briefcase,
  'i-car': Car,
  'i-heart': HeartPulse,
  'i-family': Users,
  'i-calc': Calculator,
  'i-phone': Phone,
  'i-mail': Mail,
  'i-pin': MapPin,
  'i-clock': Clock,
  'i-cal': Calendar,
  'i-arr': ArrowRight,
  'i-search': Search,
  'i-menu': Menu,
  'i-x': X,
  'i-moon': Moon,
  'i-sun': Sun,
  'i-check': Check,
  'i-lock': Lock,
  'i-doc': FileText,
  'i-users': Users,
  'i-scale': Scale,
  'i-refresh': RotateCw,
  'i-target': Target,
  'i-rocket': Rocket,
  'i-alert': AlertTriangle,
  'i-info': Info,
  'i-star': Star,
  'i-trend': TrendingUp,
  'i-print': Printer,
  'i-tax': Receipt,
  'i-chart': BarChart3,
  'i-wa': MessageCircle,

  // Direct kebab-case aliases
  'shield-check': ShieldCheck,
  'shield-alert': ShieldAlert,
  'arrow-right': ArrowRight,
  'arrow-left': ArrowLeft,
  'arrow-up-right': ArrowUpRight,
  'trending-up': TrendingUp,
  'pie-chart': PieChart,
  'coins': Coins,
  'landmark': Landmark,
  'file-text': FileText,
  'file-check': FileCheck,
  'file-question': FileQuestion,
  'file-search': FileSearch,
  'external-link': ExternalLink,
  'chevron-down': ChevronDown,
  'chevron-right': ChevronRight,
  'chevron-up': ChevronUp,
  'chevron-left': ChevronLeft,
  'help-circle': HelpCircle,
  'piggy-bank': PiggyBank,
  'credit-card': CreditCard,
  'badge-check': BadgeCheck,
  'badge-percent': BadgePercent,
  'building-2': Building2,
  'message-circle': MessageCircle,
  'message-square': MessageSquare,
  'check-circle-2': CheckCircle2,
  'lock-keyhole': LockKeyhole,
  'heart-pulse': HeartPulse,
  'sparkles': Sparkles,

  // Direct PascalCase
  TrendingUp,
  PieChart,
  Award,
  Shield,
  ShieldCheck,
  ShieldAlert,
  Coins,
  Landmark,
  Home,
  Briefcase,
  Car,
  Heart,
  HeartPulse,
  Users,
  Calculator,
  Phone,
  Mail,
  MapPin,
  Clock,
  Calendar,
  ArrowRight,
  ArrowLeft,
  ArrowUpRight,
  Search,
  Menu,
  X,
  Moon,
  Sun,
  Check,
  CheckCircle2,
  Lock,
  LockKeyhole,
  FileText,
  FileCheck,
  FileQuestion,
  FileSearch,
  Scale,
  RotateCw,
  Target,
  Rocket,
  AlertTriangle,
  Info,
  Star,
  Printer,
  Receipt,
  BarChart3,
  LineChart,
  ExternalLink,
  HelpCircle,
  ChevronDown,
  ChevronRight,
  ChevronUp,
  ChevronLeft,
  Sparkles,
  Wallet,
  PiggyBank,
  CreditCard,
  Building2,
  Percent,
  BadgePercent,
  BadgeCheck,
  MessageCircle,
  MessageSquare,
  Send,
  Share2,
  Copy,
  Download,
  Layers,
  Activity,
  Compass,
  BookOpen,
  Instagram,
  'i-instagram': Instagram,
  'i-insta': Instagram,
  'instagram': Instagram,
};

const isCustomSvg = computed(() => {
  return props.name === 'i-guilloche';
});

const isWhatsApp = computed(() => {
  return props.name === 'i-wa' || props.name === 'whatsapp';
});

const lucideComponent = computed(() => {
  if (isCustomSvg.value || isWhatsApp.value) {
    return null;
  }
  return iconMap[props.name] || iconMap[props.name?.toLowerCase()] || HelpCircle;
});
</script>

<template>
  <!-- Official WhatsApp brand icon (Simple Icons path) -->
  <svg
    v-if="isWhatsApp"
    :class="['inline-block shrink-0', className]"
    viewBox="0 0 24 24"
    fill="currentColor"
    aria-hidden="true"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
  </svg>

  <!-- Pure Lucide component -->
  <component
    v-else-if="lucideComponent"
    :is="lucideComponent"
    :class="['inline-block shrink-0', className]"
    :stroke-width="strokeWidth"
    aria-hidden="true"
  />

  <!-- Fallback for custom background decorative patterns (i-guilloche) -->
  <svg
    v-else
    :class="['inline-block shrink-0', className]"
    aria-hidden="true"
  >
    <use :href="'#' + name" />
  </svg>
</template>
