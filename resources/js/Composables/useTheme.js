import { ref, watch, onMounted } from 'vue';

const STORAGE_KEY = 'kb_theme';
const isDark = ref(true); // default dark

function applyTheme(dark) {
    const theme = dark ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    // update browser theme-color meta for status bar
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
        meta.setAttribute('content', dark ? '#070E1C' : '#F5F1EA');
    }
    try {
        localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {}
}

export function useTheme() {
    onMounted(() => {
        // Read saved preference
        let saved = null;
        try {
            saved = localStorage.getItem(STORAGE_KEY);
        } catch (e) {}

        if (saved === 'light') {
            isDark.value = false;
        } else if (saved === 'dark') {
            isDark.value = true;
        } else {
            // Respect OS preference if no saved preference
            isDark.value = !window.matchMedia('(prefers-color-scheme: light)').matches;
        }

        applyTheme(isDark.value);
    });

    watch(isDark, (val) => {
        applyTheme(val);
    });

    function toggleTheme() {
        isDark.value = !isDark.value;
    }

    return { isDark, toggleTheme };
}
