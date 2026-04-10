<div class="dd-wrap">
    <div class="icon-btn" id="t3" onclick="toggle('m3','t3')">
        <svg viewBox="0 0 16 16">
            <circle cx="3" cy="8" r="1.2" />
            <circle cx="8" cy="8" r="1.2" />
            <circle cx="13" cy="8" r="1.2" />
        </svg>
    </div>
    <div class="dd-menu right" id="m3">
        <div class="dd-item" onclick="pick('m3','t3','Rename')">
            <svg viewBox="0 0 16 16">
                <path d="M11.5 2.5a1.415 1.415 0 0 1 2 2L5 13H3v-2L11.5 2.5z" />
            </svg>
            Return
        </div>
        <div class="dd-item" onclick="pick('m3','t3','Move to')">
            <svg viewBox="0 0 16 16">
                <path d="M2 8h10M9 5l3 3-3 3" fill="none" stroke="var(--color-text-secondary)" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Lost
        </div>
        <div class="dd-item" onclick="pick('m3','t3','Copy link')">
            <svg viewBox="0 0 16 16">
                <path d="M6.5 9.5a3 3 0 0 0 4.24 0l2-2a3 3 0 0 0-4.24-4.24L7.5 4.26" fill="none"
                    stroke="var(--color-text-secondary)" stroke-width="1.3" stroke-linecap="round" />
                <path d="M9.5 6.5a3 3 0 0 0-4.24 0l-2 2a3 3 0 0 0 4.24 4.24l.99-.99" fill="none"
                    stroke="var(--color-text-secondary)" stroke-width="1.3" stroke-linecap="round" />
            </svg>
            Overdue
        </div>
        <div class="dd-divider"></div>
        <div class="dd-item danger" onclick="pick('m3','t3','Remove')">
            <svg viewBox="0 0 16 16">
                <polyline points="2,4 14,4" />
                <path d="M5 4V3h6v1M6 7v5M10 7v5" />
                <rect x="3" y="4" width="10" height="9" rx="1" />
            </svg>
            Lost
        </div>
    </div>
</div>
