"""Generate Figma-importable SVG screens for BIU LMS.

Plain SVG only: rect/circle/path/polygon/text with inline attributes, no CSS, no filters,
no foreignObject. Group ids become Figma layer names.

Usage (from docs/sdlc/):  python figma-svg/generate.py figma-svg
"""
import math
import os
import sys
from xml.sax.saxutils import escape

OUT = sys.argv[1]
os.makedirs(OUT, exist_ok=True)

FONT = "Public Sans, Inter, Arial, sans-serif"
MONO = "Roboto Mono, Consolas, monospace"
LIGHT = dict(
    primary="#1572E8", secondary="#6861CE", success="#31CE36", danger="#F25961",
    warning="#FFAD46", info="#48ABF7", neutral="#8D9498", sidebar="#1A2035", card="#FFFFFF",
    page="#F5F7FD", subtle="#F2F3F8", text="#2A2F5B", muted="#8D9498",
    ondark="#B9BABF", white="#FFFFFF", border="#EBEDF2",
    success_t="#1F9D23", warning_t="#C77A12", danger_t="#D63E46", info_t="#1A7DC4",
    secondary_t="#5149B8", primary_t="#1572E8", neutral_t="#5F676B",
)
DARK = dict(card="#202940", page="#1A2035", subtle="#283046", text="#E4E6EF", muted="#9AA0AC",
            border="#2F374B", success_t="#5BE060", warning_t="#FFC46B", danger_t="#FF8A90",
            info_t="#7CC4FA", secondary_t="#A29CF0", primary_t="#5B9DF0", neutral_t="#B9BABF")
C = dict(LIGHT)
W, H = 1440, 900
SIDE = 260
CX = SIDE + 32           # content x
CW = W - SIDE - 64       # content width (1116)

ICONS = {
    "dashboard": '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
    "book": '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>',
    "barcode": '<path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M8 7v10"/><path d="M12 7v10"/><path d="M16 7v10"/>',
    "author": '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>',
    "publisher": '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/>',
    "tag": '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r="1"/>',
    "members": '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    "plan": '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
    "borrow": '<path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/>',
    "bookmark": '<path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>',
    "chart": '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
    "usercheck": '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>',
    "shield": '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>',
    "search": '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
    "bell": '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
    "clock": '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    "calendar": '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>',
    "moon": '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',
    "sun": '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
    "plus": '<path d="M5 12h14"/><path d="M12 5v14"/>',
    "check": '<path d="M20 6 9 17l-5-5"/>',
    "x": '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
    "logout": '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
    "download": '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/>',
    "upload": '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/>',
    "printer": '<path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
    "mail": '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
    "arrowleft": '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
    "chevron": '<path d="m6 9 6 6 6-6"/>',
    "dollar": '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
}

COVERS = [("primary", "secondary"), ("success", "info"), ("warning", "danger"), ("secondary", "primary"),
          ("info", "primary"), ("danger", "warning"), ("sidebar", "primary"), ("success", "secondary")]

# --------------------------------------------------------------- primitives


def attrs(**kw):
    out = []
    for k, v in kw.items():
        if v is None:
            continue
        out.append(f'{k.rstrip("_").replace("_", "-")}="{v}"')
    return " ".join(out)


def g(id_, *children, transform=None):
    t = f' transform="{transform}"' if transform else ""
    return f'<g id="{escape(id_)}"{t}>' + "".join(children) + "</g>"


def rect(x, y, w, h, fill, rx=0, stroke=None, sw=None, opacity=None, id_=None, dash=None):
    return "<rect " + attrs(id=id_, x=x, y=y, width=w, height=h, rx=rx or None, fill=fill,
                            stroke=stroke, stroke_width=sw, stroke_dasharray=dash, fill_opacity=opacity) + "/>"


def circle(cx, cy, r, fill, opacity=None, stroke=None, sw=None):
    return "<circle " + attrs(cx=cx, cy=cy, r=r, fill=fill, fill_opacity=opacity, stroke=stroke, stroke_width=sw) + "/>"


def line(x1, y1, x2, y2, color=None, sw=1):
    return "<line " + attrs(x1=x1, y1=y1, x2=x2, y2=y2, stroke=color or C["border"], stroke_width=sw) + "/>"


def text(x, y, s, size=14, weight=400, fill=None, anchor=None, id_=None, font=FONT):
    return ("<text " + attrs(id=id_, x=x, y=y, font_family=font, font_size=size, font_weight=weight,
                             fill=fill or C["text"], text_anchor=anchor) + ">" + escape(s) + "</text>")


def tw(s, size=14, weight=400):
    """Approximate rendered text width for layout."""
    k = 0.56 if weight < 600 else 0.6
    return len(s) * size * k


def mid(top, h, size):
    """Baseline that vertically centres text of `size` in a box."""
    return round(top + h / 2 + size * 0.36, 1)


def icon(name, x, y, size=18, color=None):
    s = size / 24
    return (f'<g id="icon/{name}" transform="translate({x} {y}) scale({s:.4f})" fill="none" '
            f'stroke="{color or C["muted"]}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
            + ICONS[name] + "</g>")


def star(cx, cy, r, fill):
    pts = []
    for k in range(10):
        a = -math.pi / 2 + k * math.pi / 5
        rr = r if k % 2 == 0 else r * 0.45
        pts.append(f"{cx + rr * math.cos(a):.1f},{cy + rr * math.sin(a):.1f}")
    return f'<polygon points="{" ".join(pts)}" fill="{fill}"/>'


def stars(x, cy, n, r=6, gap=14):
    return g(f"Rating/{n}", *[star(round(x + r + i * gap, 1), cy, r, C["warning"] if i < n else C["border"])
                              for i in range(5)])


def cover(x, y, w, h, i=0):
    c1, c2 = COVERS[i % len(COVERS)]
    return g("Cover", rect(x, y, w, h, C[c1], rx=4), rect(x, y, max(6, round(w * 0.12)), h, C[c2], rx=2),
             rect(round(x + w * 0.24), round(y + h * 0.18), round(w * 0.6), 5, C["white"], rx=2, opacity=0.85),
             rect(round(x + w * 0.24), round(y + h * 0.18) + 11, round(w * 0.4), 5, C["white"], rx=2, opacity=0.6))

# --------------------------------------------------------------- components


BTN = {"primary": ("primary", "white", None), "secondary": ("secondary", "white", None),
       "danger": ("danger", "white", None), "outline": (None, "primary", "primary"),
       "outline_danger": (None, "danger", "danger")}


def button(x, y, label, variant="primary", h=36, size=14, w=None, icon_name=None):
    bg, fg, stroke = BTN[variant]
    bg = C[bg] if bg else "none"
    fg = C[fg]
    stroke = C[stroke] if stroke else None
    iw = 22 if icon_name else 0
    w = w or round(tw(label, size, 600) + 32 + iw)
    parts = [rect(x, y, w, h, bg, rx=4, stroke=stroke, sw=1 if stroke else None)]
    tx = x + (w - tw(label, size, 600) - iw) / 2
    if icon_name:
        parts.append(icon(icon_name, round(tx), y + (h - 16) / 2, 16, fg))
        tx += iw
    parts.append(text(round(tx, 1), mid(y, h, size), label, size, 600, fg))
    return g(f"Button/{variant}/{label}", *parts), w


def btn_w(label, variant="primary", icon_name=None, h=36, size=14):
    return button(0, 0, label, variant, h, size, icon_name=icon_name)[1]


def small_btn(x, y, label, variant="primary"):
    return button(x, y, label, variant, h=28, size=12)


TONE = {"available": "success", "returned": "success", "active": "success", "fulfilled": "success",
        "created": "success", "borrowed": "warning", "pending": "warning", "damaged": "warning",
        "suspend": "warning", "terminated": "danger", "overdue": "danger", "lost": "danger", "expired": "danger",
        "deleted": "danger", "updated": "info", "librarian": "info", "admin": "secondary",
        "member": "neutral", "inactive": "neutral", "cancelled": "neutral", "used": "info"}


def badge(x, y, label, tone=None):
    tone = tone or ("danger" if label.startswith("0 ") else TONE.get(label.split()[-1], "success"))
    w = round(tw(label, 12, 600) + 18)
    return g(f"Badge/{label}", rect(x, y, w, 22, C[tone], rx=11, opacity=0.15),
             text(x + 9, mid(y, 22, 12), label, 12, 600, C[tone + "_t"])), w


def card(x, y, w, h, id_="Card", children=()):
    return g(id_, rect(x, y, w, h, C["card"], rx=8, stroke=C["border"], sw=1), *children)


def kpi(x, y, w, label, value, tone, icon_name, value_color=None):
    h = 84
    return g(f"KPI/{label}",
             rect(x, y, w, h, C["card"], rx=8, stroke=C["border"], sw=1),
             circle(x + 40, y + h / 2, 24, C[tone], 0.15),
             icon(icon_name, x + 29, y + h / 2 - 11, 22, C[tone]),
             text(x + 76, y + 36, label, 13, 500, C["muted"]),
             text(x + 76, y + 62, value, 24, 700, value_color or C["text"]))


def field(x, y, w, label, value, state="filled", h=38):
    stroke = {"focus": C["primary"], "error": C["danger"]}.get(state, C["border"])
    color = C["muted"] if state == "placeholder" else C["text"]
    parts = [text(x, y + 13, label, 13, 600)]
    if state == "focus":
        parts.append(rect(x - 3, y + 20 - 3, w + 6, h + 6, C["primary"], rx=6, opacity=0.15))
    parts += [rect(x, y + 20, w, h, C["card"], rx=4, stroke=stroke, sw=1),
              text(x + 12, mid(y + 20, h, 14), value, 14, 400, color)]
    return g(f"Field/{label}", *parts)


def checkbox(x, y, label, checked=False):
    parts = [rect(x, y + 3, 16, 16, C["primary"] if checked else C["card"], rx=3,
                  stroke=None if checked else C["border"], sw=None if checked else 1.5)]
    if checked:
        parts.append(icon("check", x + 2, y + 5, 12, C["white"]))
    parts.append(text(x + 26, y + 16, label, 13))
    return g(f"Checkbox/{label}", *parts)


def alert(x, y, w, msg, tone, h=40):
    return g(f"Alert/{tone}", rect(x, y, w, h, C[tone], rx=4, opacity=0.12),
             text(x + 14, mid(y, h, 13), msg, 13, 500, C[tone + "_t"]))


def kv_rows(x, y, w, items, gap=34):
    parts = []
    for i, (k, v) in enumerate(items):
        yy = y + i * gap
        parts += [text(x, yy + 21, k, 13, 400, C["muted"]), text(x + w, yy + 21, v, 13, 600, anchor="end")]
        if i < len(items) - 1:
            parts.append(line(x, yy + gap, x + w, yy + gap))
    return g("Details", *parts)


def table(x, y, w, cols, rows, row_h=48, id_="Table", highlight=None):
    """cols: [(title, width)]. Cell: str | ('b', *labels) | ('btn', [(label, variant)]) | ('cover', i)
    | ('strong'|'muted'|'red'|'green'|'link', str) | ('person', name, sub) | ('avatar', name, sub)
    | ('check', bool) | ('stars', n)."""
    parts = []
    cx = x
    for title, cw in cols:
        parts.append(text(cx + 8, y + 22, title, 12, 600, C["muted"]))
        cx += cw
    parts.append(line(x, y + 36, x + w, y + 36))
    ry = y + 36
    for ri, row in enumerate(rows):
        cells = []
        if highlight == ri:
            cells.append(rect(x, ry, w, row_h, C["primary"], opacity=0.07))
        cx = x
        for (title, cw), cell in zip(cols, row):
            base = mid(ry, row_h, 13)
            if isinstance(cell, str):
                cells.append(text(cx + 8, base, cell, 13))
            elif cell[0] in ("strong", "muted", "red", "green", "link"):
                weight = 400 if cell[0] == "muted" else 600
                color = {"muted": C["muted"], "red": C["danger_t"], "green": C["success_t"],
                         "link": C["primary"]}.get(cell[0], C["text"])
                cells.append(text(cx + 8, base, cell[1], 13, weight, color))
            elif cell[0] == "person":
                cells += [text(cx + 8, ry + row_h / 2 - 3, cell[1], 13, 600),
                          text(cx + 8, ry + row_h / 2 + 14, cell[2], 12, 400, C["muted"])]
            elif cell[0] == "avatar":
                ini = "".join(p[0] for p in cell[1].split()[:2])
                cells += [circle(cx + 24, ry + row_h / 2, 16, C["secondary"], 0.15),
                          text(cx + 24, ry + row_h / 2 + 4, ini, 11, 700, C["secondary_t"], anchor="middle")]
                if len(cell) > 2 and cell[2]:
                    cells += [text(cx + 48, ry + row_h / 2 - 3, cell[1], 13, 600),
                              text(cx + 48, ry + row_h / 2 + 14, cell[2], 12, 400, C["muted"])]
                else:
                    cells.append(text(cx + 48, base, cell[1], 13, 600))
            elif cell[0] == "b":
                bx = cx + 8
                for lab in cell[1:]:
                    el, bw = badge(bx, ry + (row_h - 22) / 2, lab)
                    cells.append(el)
                    bx += bw + 6
            elif cell[0] == "btn":
                bx = cx + 8
                for lab, var in cell[1]:
                    el, bw = small_btn(bx, ry + (row_h - 28) / 2, lab, var)
                    cells.append(el)
                    bx += bw + 6
            elif cell[0] == "cover":
                cells.append(cover(cx + 8, ry + 6, 28, row_h - 12, cell[1] if len(cell) > 1 else 0))
            elif cell[0] == "check":
                cells.append(icon("check" if cell[1] else "x", cx + 10, ry + row_h / 2 - 9, 18,
                                  C["success"] if cell[1] else C["muted"]))
            elif cell[0] == "stars":
                cells.append(stars(cx + 8, ry + row_h / 2, cell[1], 5, 12))
            cx += cw
        cells.append(line(x, ry + row_h, x + w, ry + row_h))
        parts.append(g(f"Row {ri + 1}", *cells))
        ry += row_h
    return g(id_, *parts), ry


def toolbar(x, y, w, search_ph, filters=(), show=True):
    parts = []
    if show:
        parts += [text(x + 20, y + 38, "Show", 13, 400, C["muted"]),
                  rect(x + 60, y + 18, 64, 30, C["card"], rx=4, stroke=C["border"], sw=1),
                  text(x + 72, y + 38, "10", 13), icon("chevron", x + 100, y + 26, 14, C["muted"]),
                  text(x + 134, y + 38, "entries", 13, 400, C["muted"])]
    sx = x + w - 256
    parts += [rect(sx, y + 18, 236, 30, C["card"], rx=4, stroke=C["border"], sw=1),
              icon("search", sx + 10, y + 25, 16, C["muted"]),
              text(sx + 34, y + 38, search_ph, 13, 400, C["muted"])]
    fx = sx - 16
    for label, value in reversed(filters):
        bw = 150
        fx -= bw
        parts += [g(f"Filter/{label}", rect(fx, y + 18, bw, 30, C["card"], rx=4, stroke=C["border"], sw=1),
                    text(fx + 12, y + 38, value, 13), icon("chevron", fx + bw - 24, y + 26, 14, C["muted"]))]
        fx -= tw(label, 13) + 10
        parts.append(text(round(fx), y + 38, label, 13, 400, C["muted"]))
        fx -= 20
    return g("Toolbar", *parts)


def pagination(x, y, w, shown, total, last):
    parts = [text(x + 20, y + 32, f"Showing 1 to {shown} of {total} entries", 13, 400, C["muted"])]
    n = int(last.replace(",", ""))
    labels = ["‹"] + ([str(i) for i in range(1, n + 1)] if n <= 4 else ["1", "2", "3", "…", last]) + ["›"]
    widths = [max(30, round(tw(lab, 13, 600) + 16)) for lab in labels]
    px = x + w - 20 - sum(widths) - 4 * (len(labels) - 1)
    for lab, bw in zip(labels, widths):
        on = lab == "1"
        parts += [rect(px, y + 14, bw, 30, C["primary"] if on else C["card"], rx=4,
                       stroke=None if on else C["border"], sw=None if on else 1),
                  text(px + bw / 2, y + 34, lab, 13, 600, C["white"] if on else C["text"], anchor="middle")]
        px += bw + 4
    return g("Pagination", *parts)


def tab_bar(x, y, w, items, active=0):
    parts = []
    tx = x + 20
    for i, (lab, cnt) in enumerate(items):
        on = i == active
        wt = 600 if on else 500
        lw = tw(lab, 14, wt)
        item = [text(tx, y + 30, lab, 14, wt, C["primary"] if on else C["muted"])]
        pw = 0
        if cnt is not None:
            pw = round(tw(cnt, 11, 700) + 14)
            item += [rect(round(tx + lw + 8), y + 15, pw, 20, C["primary"] if on else C["subtle"], rx=10,
                          opacity=0.12 if on else None),
                     text(round(tx + lw + 8 + pw / 2), y + 29, cnt, 11, 700, C["primary"] if on else C["muted"],
                          anchor="middle")]
            pw += 8
        if on:
            item.append(rect(tx, y + 45, round(lw + pw), 3, C["primary"]))
        parts.append(g(f"Tab/{lab}{' (active)' if on else ''}", *item))
        tx = round(tx + lw + pw + 32)
    parts.append(line(x, y + 48, x + w, y + 48))
    return g("Tabs", *parts)


def form_grid(x, y, w, rows, gap=16):
    """rows: list of rows; each row a list of specs (kind, label, value[, state[, hint]]).
    kinds: f text, s select, d date, t textarea, chips, check, radio=((options), selected)."""
    parts = []
    for row in rows:
        n = len(row)
        fw = (w - gap * (n - 1)) / n
        rh = 0
        for i, spec in enumerate(row):
            kind, label, value = spec[0], spec[1], spec[2]
            state = spec[3] if len(spec) > 3 else "filled"
            hint = spec[4] if len(spec) > 4 else None
            fx, fwr = round(x + i * (fw + gap)), round(fw)
            if kind in ("f", "s", "d"):
                el = [field(fx, y, fwr, label, value, state)]
                if kind == "s":
                    el.append(icon("chevron", fx + fwr - 28, y + 31, 16, C["muted"]))
                if kind == "d":
                    el.append(icon("calendar", fx + fwr - 30, y + 30, 17, C["muted"]))
                h = 58
            elif kind == "t":
                el = [text(fx, y + 13, label, 13, 600), rect(fx, y + 20, fwr, 96, C["card"], rx=4, stroke=C["border"], sw=1)]
                for li, ln in enumerate(value.split("\n")):
                    el.append(text(fx + 12, y + 46 + li * 21, ln, 14, 400,
                                   C["muted"] if state == "placeholder" else C["text"]))
                el = [g(f"Textarea/{label}", *el)]
                h = 116
            elif kind == "chips":
                el = [text(fx, y + 13, label, 13, 600), rect(fx, y + 20, fwr, 38, C["card"], rx=4, stroke=C["border"], sw=1)]
                cx = fx + 7
                for name in value:
                    cw_ = round(tw(name, 12, 600) + 34)
                    el += [g(f"Chip/{name}", rect(cx, y + 27, cw_, 24, C["primary"], rx=12, opacity=0.12),
                             text(cx + 10, mid(y + 27, 24, 12), name, 12, 600, C["primary"]),
                             icon("x", cx + cw_ - 20, y + 33, 12, C["primary"]))]
                    cx += cw_ + 6
                el += [text(cx + 6, mid(y + 20, 38, 14), "Type to add an author…", 14, 400, C["muted"]),
                       icon("chevron", fx + fwr - 28, y + 31, 16, C["muted"])]
                el = [g(f"Multi-select/{label}", *el)]
                h = 58
            elif kind == "check":
                el = [checkbox(fx, y, label, value)]
                h = 22
            elif kind == "radio":
                opts, sel = value
                el = [text(fx, y + 13, label, 13, 600)]
                ox = fx
                for o in opts:
                    on = o == sel
                    el.append(circle(ox + 9, y + 38, 8, C["card"], stroke=C["primary"] if on else C["border"], sw=1.5))
                    if on:
                        el.append(circle(ox + 9, y + 38, 4, C["primary"]))
                    el.append(text(ox + 24, y + 43, o, 14))
                    ox += round(24 + tw(o, 14) + 28)
                el = [g(f"Radio/{label}", *el)]
                h = 50
            if hint:
                el.append(text(fx, y + h + 16, hint, 12, 400, C["danger_t"] if state == "error" else C["muted"]))
                h += 22
            parts += el
            rh = max(rh, h)
        y += rh + 18
    return parts, y


NAV = [("MAIN", [("Dashboard", "dashboard")]),
       ("CATALOG", [("Books", "book"), ("Book Copies", "barcode"), ("Authors", "author"),
                    ("Publishers", "publisher"), ("Categories", "tag")]),
       ("MEMBERSHIP", [("Members", "members"), ("Member Plans", "plan")]),
       ("CIRCULATION", [("Borrow Books", "borrow"), ("Reservations", "bookmark")]),
       ("ADMIN", [("Reports", "chart"), ("Staff Accounts", "usercheck"),
                  ("Roles & Permissions", "members"), ("Audit Log", "shield")])]


def logo(x, y, dark=True):
    return g("Logo", rect(x, y, 36, 36, C["primary"], rx=8),
             icon("book", x + 8, y + 8, 20, C["white"]),
             text(x + 48, mid(y, 36, 17), "BIU Library", 17, 700, C["white"] if dark else C["text"]))


def sidebar(active):
    parts = [rect(0, 0, SIDE, H, C["sidebar"]), logo(24, 20)]
    y = 80
    for grp, items in NAV:
        parts.append(text(28, y + 18, grp, 11, 600, "#8D9498"))
        y += 28
        for label, ic in items:
            on = label == active
            item = []
            if on:
                item.append(rect(12, y, SIDE - 24, 36, C["primary"], rx=4))
            col = C["white"] if on else C["ondark"]
            item.append(icon(ic, 28, y + 9, 18, col))
            item.append(text(58, mid(y, 36, 14), label, 14, 600 if on else 500, col))
            parts.append(g(f"Nav/{label}{' (active)' if on else ''}", *item))
            y += 36
    parts.append(g("Logout", icon("logout", 28, H - 44, 18, C["ondark"]),
                   text(58, mid(H - 53, 36, 14), "Sign out", 14, 500, C["ondark"])))
    return g("Sidebar", *parts)


def navbar(user, theme_icon="moon"):
    x = SIDE
    return g("Top Navbar",
             rect(x, 0, W - x, 64, C["card"]), line(x, 64, W, 64),
             rect(x + 24, 14, 360, 36, C["subtle"], rx=18),
             icon("search", x + 38, 24, 16, C["muted"]),
             text(x + 62, mid(14, 36, 14), "Search books, members, barcodes…", 14, 400, C["muted"]),
             icon(theme_icon, W - 220, 21, 22, C["muted"]),
             icon("bell", W - 178, 21, 22, C["muted"]),
             circle(W - 160, 22, 7, C["danger"]),
             text(W - 160, 26, "3", 10, 700, C["white"], anchor="middle"),
             circle(W - 122, 32, 18, C["secondary"]),
             text(W - 122, 37, user[4] if user.startswith("Hi,") else user[0], 14, 700, C["white"], anchor="middle"),
             text(W - 96, 37, user, 14, 600))


def page_title(title, crumb, y=88):
    return g("Page Title", text(CX, y + 22, title, 22, 700), text(CX, y + 44, crumb, 13, 400, C["muted"]))


def header_buttons(specs, y=96):
    """specs: [(label, variant, icon)] from right to left."""
    bx = CX + CW
    out = []
    for lab, var, ic in specs:
        bw = btn_w(lab, var, ic)
        bx -= bw
        out.append(button(bx, y, lab, var, icon_name=ic)[0])
        bx -= 12
    return g("Actions", *out)


def overlay():
    return rect(0, 0, W, H, "#0B1020", opacity=0.62, id_="Overlay")


def modal_head(x, y, w, title, sub=None):
    parts = [text(x + 24, y + 40, title, 18, 700), icon("x", x + w - 44, y + 24, 20, C["muted"])]
    if sub:
        parts.append(text(x + 24, y + 62, sub, 13, 400, C["muted"]))
    parts.append(line(x, y + (84 if sub else 64), x + w, y + (84 if sub else 64)))
    return g("Modal header", *parts)


def svg(name, body, w=W, h=H, bg=None):
    bg = bg or C["page"]
    doc = (f'<svg xmlns="http://www.w3.org/2000/svg" width="{w}" height="{h}" viewBox="0 0 {w} {h}">'
           f'<g id="{escape(name)}">{rect(0, 0, w, h, bg, id_="Background")}{body}</g></svg>\n')
    path = os.path.join(OUT, name.split(" ")[0].lower().replace("/", "-") + ".svg")
    with open(path, "w", encoding="utf-8") as f:
        f.write(doc)
    return path

# --------------------------------------------------------------- page templates


def list_page(active, title, crumb, cols, rows, search_ph, total, last, actions=(), filters=(),
              tabs=None, user="Hi, Librarian", row_h=52, id_="DataTable", highlight=None):
    body = [sidebar(active), navbar(user), page_title(title, crumb), header_buttons(actions)]
    cy = 156
    inner = []
    ty = cy
    if tabs:
        inner.append(tab_bar(CX, cy, CW, tabs))
        ty = cy + 48
    inner.append(toolbar(CX, ty, CW, search_ph, filters))
    tb, end = table(CX + 20, ty + 64, CW - 40, cols, rows, row_h, id_, highlight)
    inner += [tb, pagination(CX, end, CW, len(rows), total, last)]
    body.append(card(CX, cy, CW, end - cy + 64, f"{title} card", inner))
    return "".join(body)


def form_page(active, title, crumb, section, rows, w=None, side=None, save="Save", user="Hi, Librarian",
              alert_=None):
    w = w or round(CW * 0.62)
    body = [sidebar(active), navbar(user), page_title(title, crumb)]
    cy = 156
    inner = [text(CX + 24, cy + 36, section, 16, 600), line(CX, cy + 56, CX + w, cy + 56)]
    y0 = cy + 76
    if alert_:
        inner.append(alert(CX + 24, y0, w - 48, *alert_))
        y0 += 58
    parts, end = form_grid(CX + 24, y0, w - 48, rows)
    inner += parts
    inner.append(line(CX, end + 2, CX + w, end + 2))
    s, sw_ = button(CX + 24, end + 20, save)
    inner += [s, button(CX + 24 + sw_ + 12, end + 20, "Cancel", "outline")[0]]
    body.append(card(CX, cy, w, end + 76 - cy, "Form card", inner))
    if side:
        body.append(side(CX + w + 16, cy, CW - w - 16))
    return "".join(body)

# --------------------------------------------------------------- screens


def auth_card(cw, chh, children):
    x, y = (W - cw) / 2, (H - chh) / 2
    return [rect(0, 0, W, H, C["sidebar"]),
            circle(140, 120, 260, C["primary"], 0.08), circle(1320, 820, 300, C["secondary"], 0.08),
            g("Auth Card", rect(x, y, cw, chh, C["card"], rx=12), logo(x + 32, y + 32, dark=False),
              *children(x, y))]


def screen_login():
    cw = 420

    def kids(x, y):
        return [text(x + 32, y + 112, "Sign in", 22, 700),
                text(x + 32, y + 134, "Use your library account", 13, 400, C["muted"]),
                alert(x + 32, y + 152, cw - 64, "These credentials do not match our records.", "danger"),
                field(x + 32, y + 204, cw - 64, "Email", "librarian@biu.edu", "focus"),
                field(x + 32, y + 270, cw - 64, "Password", "••••••••••"),
                checkbox(x + 32, y + 334, "Remember me"),
                text(x + cw - 32, y + 350, "Forgot password?", 13, 600, C["primary"], anchor="end"),
                button(x + 32, y + 368, "Sign in", w=cw - 64, h=42)[0],
                text(W / 2, y + 440, "New member?  Create an account", 13, 600, C["primary"], anchor="middle"),
                text(W / 2, y + 468, "BIU Library Management System · v1.0", 12, 400, C["muted"], anchor="middle")]
    return svg("01-Login", "".join(auth_card(cw, 490, kids)), bg=C["sidebar"])


def screen_register():
    cw = 460

    def kids(x, y):
        w = cw - 64
        return [text(x + 32, y + 112, "Create account", 22, 700),
                text(x + 32, y + 134, "Join the BIU Library as a member", 13, 400, C["muted"]),
                field(x + 32, y + 156, w, "Full name", "Sok Dara"),
                field(x + 32, y + 224, w, "Email", "sok.dara@student.biu.edu", "focus"),
                field(x + 32, y + 292, w, "Phone (optional)", "012 345 678"),
                field(x + 32, y + 360, w, "Address (optional)", "Phnom Penh"),
                field(x + 32, y + 428, w, "Password", "••••••••••"),
                field(x + 32, y + 496, w, "Confirm password", "••••••••••"),
                text(x + 32, y + 572, "At least 8 characters.", 12, 400, C["muted"]),
                button(x + 32, y + 590, "Create account", w=w, h=42)[0],
                text(W / 2, y + 662, "Already have an account?  Sign in", 13, 600, C["primary"], anchor="middle")]
    return svg("33-Register", "".join(auth_card(cw, 690, kids)), bg=C["sidebar"])


def screen_forgot():
    cw = 420

    def kids(x, y):
        bx = W / 2 - (tw("Back to sign in", 13, 600) + 22) / 2
        return [text(x + 32, y + 112, "Reset password", 22, 700),
                text(x + 32, y + 134, "We'll email you a link to set a new one.", 13, 400, C["muted"]),
                alert(x + 32, y + 152, cw - 64, "If that email exists, a reset link was sent.", "success"),
                field(x + 32, y + 204, cw - 64, "Email", "dara.sok@biu.edu", "focus"),
                button(x + 32, y + 282, "Send reset link", w=cw - 64, h=42)[0],
                g("Back link", icon("arrowleft", round(bx), y + 345, 16, C["primary"]),
                  text(round(bx + 22), y + 358, "Back to sign in", 13, 600, C["primary"])),
                text(W / 2, y + 384, "Reset links expire after 60 minutes.", 12, 400, C["muted"], anchor="middle")]
    return svg("06-Forgot-Password", "".join(auth_card(cw, 410, kids)), bg=C["sidebar"])


def dashboard_body(theme_icon="moon"):
    body = [sidebar("Dashboard"), navbar("Hi, Admin", theme_icon),
            page_title("Dashboard", "Library overview · Monday, 23 Nov 2026"),
            header_buttons([("+ New borrow", "primary", None), ("Export report", "outline", "download")])]
    kw = (CW - 4 * 16) / 5
    tiles = [("Total titles", "1,284", "primary", "book"), ("Copies available", "3,912", "info", "barcode"),
             ("Active loans", "426", "success", "borrow"), ("Overdue loans", "37", "danger", "clock"),
             ("Active members", "2,051", "secondary", "members")]
    body.append(g("KPI Row", *[kpi(round(CX + i * (kw + 16)), 156, round(kw), *t,
                                   value_color=C["danger"] if t[0].startswith("Overdue") else None)
                               for i, t in enumerate(tiles)]))
    chw = round(CW * 0.62)
    cy, chh = 256, 268
    vals = [40, 55, 35, 60, 72, 48, 30, 66, 80, 58, 44, 70, 90, 62, 50, 38, 64, 76, 52, 46, 68, 84, 58, 42, 61, 73, 88, 66, 54, 79]
    bars = []
    gx, gy, gw, gh = CX + 48, cy + 60, chw - 72, 170
    for i in range(5):
        yy = gy + gh - i * gh / 4
        bars.append(line(gx, yy, gx + gw, yy))
        bars.append(text(gx - 10, yy + 4, str(i * 10), 11, 400, C["muted"], anchor="end"))
    bw = gw / len(vals)
    for i, v in enumerate(vals):
        h = v / 100 * gh * 0.9
        bars.append(rect(round(gx + i * bw + 3, 1), round(gy + gh - h, 1), round(bw - 6, 1), round(h, 1), C["primary"], rx=3,
                         opacity=1 if i == len(vals) - 1 else 0.75))
    body.append(card(CX, cy, chw, chh, "Chart: Loans per day", [
        text(CX + 20, cy + 32, "Loans per day", 16, 600),
        text(CX + chw - 20, cy + 32, "Last 30 days", 12, 500, C["muted"], anchor="end"), g("Bars", *bars)]))
    tx = CX + chw + 16
    tw_ = CW - chw - 16
    tops = [("Clean Code", 92), ("Introduction to Algorithms", 76), ("Database System Concepts", 63),
            ("A History of Cambodia", 49), ("Principles of Microeconomics", 37)]
    rows = []
    for i, (n, v) in enumerate(tops):
        yy = cy + 64 + i * 40
        rows += [text(tx + 20, yy, n, 13, 500),
                 rect(tx + 20, yy + 8, tw_ - 80, 8, C["subtle"], rx=4),
                 rect(tx + 20, yy + 8, round((tw_ - 80) * v / 100), 8, C["secondary"], rx=4),
                 text(tx + tw_ - 20, yy + 16, str(v), 12, 600, C["muted"], anchor="end")]
    body.append(card(tx, cy, tw_, chh, "Top borrowed titles", [text(tx + 20, cy + 32, "Top borrowed titles", 16, 600), *rows]))
    ty = 540
    cols = [("Member", 220), ("Book", 250), ("Barcode", 130), ("Due date", 130), ("Days late", 100), ("Fine", 90), ("Status", 156)]
    tb, end = table(CX + 20, ty + 50, CW - 40, cols, [
        ["Sok Dara · M-0021", ("strong", "Clean Code"), "BC-000134", "10 Nov 2026", ("red", "13"), "$6.50", ("b", "overdue")],
        ["Chan Lina · M-0108", ("strong", "Database System Concepts"), "BC-000877", "15 Nov 2026", ("red", "8"), "$4.00", ("b", "overdue")],
        ["Keo Vanna · M-0245", ("strong", "Introduction to Algorithms"), "BC-000412", "18 Nov 2026", ("red", "5"), "$2.50", ("b", "overdue")],
        ["Lim Sopheak · M-0310", ("strong", "Principles of Microeconomics"), "BC-000990", "20 Nov 2026", ("red", "3"), "$1.50", ("b", "overdue")],
    ], row_h=44, id_="Overdue table")
    body.append(card(CX, ty, CW, end - ty + 16, "Overdue loans", [
        text(CX + 20, ty + 32, "Overdue loans", 16, 600),
        text(CX + CW - 20, ty + 32, "View all →", 13, 600, C["primary"], anchor="end"), tb]))
    return "".join(body)


def screen_dashboard():
    return svg("02-Dashboard", dashboard_body())


def screen_dashboard_dark():
    C.update(DARK)
    try:
        return svg("31-Dashboard-Dark", dashboard_body("sun"))
    finally:
        C.clear()
        C.update(LIGHT)


def screen_books():
    acts = ("btn", [("Edit", "primary"), ("Delete", "danger")])
    rows = [
        [("cover", 0), "978-0132350884", ("strong", "Clean Code"), "Robert C. Martin", "Computing", "Prentice Hall", ("b", "4 available", "2 borrowed"), "A-12", acts],
        [("cover", 1), "978-0262046305", ("strong", "Introduction to Algorithms"), "Cormen, Leiserson…", "Computing", "MIT Press", ("b", "0 available"), "A-14", acts],
        [("cover", 2), "978-0073523323", ("strong", "Database System Concepts"), "Silberschatz", "Computing", "McGraw-Hill", ("b", "3 available"), "A-15", acts],
        [("cover", 3), "978-1405170833", ("strong", "A History of Cambodia"), "David Chandler", "History", "Westview", ("b", "2 available", "1 borrowed"), "H-03", acts],
        [("cover", 4), "978-1305585126", ("strong", "Principles of Microeconomics"), "N. Gregory Mankiw", "Economics", "Cengage", ("b", "1 available", "1 damaged"), "E-07", acts],
        [("cover", 5), "978-0143127741", ("strong", "Sapiens"), "Yuval Noah Harari", "History", "Harper", ("b", "5 available"), "H-11", acts],
        [("cover", 6), "978-0596007126", ("strong", "Head First Design Patterns"), "Freeman & Robson", "Computing", "O'Reilly", ("b", "2 available", "1 lost"), "A-18", acts],
    ]
    cols = [("Cover", 48), ("ISBN", 122), ("Title", 192), ("Author(s)", 132), ("Category", 84),
            ("Publisher", 92), ("Copies", 206), ("Shelf", 50), ("Action", 150)]
    return svg("03-Books-List", list_page(
        "Books", "All Books", "Catalog / Books", cols, rows, "Search title, ISBN, author…", "1,284", "129",
        actions=[("+ Add book", "primary", None), ("Import CSV", "outline", "upload")],
        filters=[("Category", "All categories")], row_h=56, id_="Books DataTable"))


def screen_book_form():
    rows = [
        [("f", "ISBN", "978-0132350884", "error", "The isbn has already been taken."), ("f", "Title", "Clean Code")],
        [("chips", "Author(s)", ["Robert C. Martin"])],
        [("s", "Publisher", "Prentice Hall"), ("s", "Category", "Computing")],
        [("f", "Publish year", "2008"), ("f", "Pages", "464"), ("s", "Language", "English")],
        [("f", "Shelf location", "A-12", "filled", "Row and shelf code, for example A-12.")],
    ]

    def side(x, y, w):
        return card(x, y, w, 520, "Cover & copies", [
            text(x + 24, y + 36, "Cover image", 16, 600),
            g("Dropzone", rect(x + 24, y + 56, w - 48, 240, C["subtle"], rx=8, stroke=C["border"], sw=1.5, dash="6 4"),
              cover(round(x + w / 2 - 40), y + 78, 80, 108, 0),
              icon("upload", round(x + w / 2 - 10), y + 204, 20, C["primary"]),
              text(x + w / 2, y + 246, "Drag & drop or click to replace", 13, 600, anchor="middle"),
              text(x + w / 2, y + 266, "JPG or PNG, up to 2 MB", 12, 400, C["muted"], anchor="middle")),
            line(x, y + 320, x + w, y + 320),
            text(x + 24, y + 356, "Copies", 16, 600),
            field(x + 24, y + 374, w - 48, "Initial copies", "3"),
            text(x + 24, y + 452, "Creates barcodes BC-000135 to BC-000137,", 12, 400, C["muted"]),
            text(x + 24, y + 470, "all with status available.", 12, 400, C["muted"])])
    return svg("07-Book-Form", form_page("Books", "Add Book", "Catalog / Books / Add", "Book details", rows,
                                         side=side, save="Save book"))


def screen_book_detail():
    body = [sidebar("Books"), navbar("Hi, Librarian"), page_title("Clean Code", "Catalog / Books / Clean Code"),
            header_buttons([("+ Add copy", "primary", None), ("Edit", "outline", None)])]
    cy, lw = 156, 340
    lx = CX
    info = [("ISBN", "978-0132350884"), ("Author", "Robert C. Martin"), ("Publisher", "Prentice Hall"),
            ("Category", "Computing"), ("Year", "2008"), ("Pages", "464"), ("Language", "English"), ("Shelf", "A-12")]
    body.append(card(lx, cy, lw, 650, "Book info", [
        g("Cover area", rect(lx + 20, cy + 20, lw - 40, 250, C["subtle"], rx=8), cover(lx + 100, cy + 36, 140, 200, 0)),
        text(lx + lw / 2, cy + 302, "Clean Code", 18, 700, anchor="middle"),
        text(lx + lw / 2, cy + 324, "A Handbook of Agile Software Craftsmanship", 12, 400, C["muted"], anchor="middle"),
        stars(lx + 88, cy + 348, 4),
        text(lx + 164, cy + 353, "4.3 · 12 reviews", 12, 600, C["muted"]),
        kv_rows(lx + 24, cy + 368, lw - 48, info, 32)]))
    rx, rw = CX + lw + 16, CW - lw - 16
    stats = [("Available", "4", "success"), ("Borrowed", "2", "warning"), ("Damaged", "0", "warning"), ("Lost", "0", "danger")]
    sw_ = (rw - 40 - 36) / 4
    sp = []
    for i, (lab, v, tone) in enumerate(stats):
        sx = round(rx + 20 + i * (sw_ + 12))
        sp.append(g(f"Stat/{lab}", rect(sx, cy + 52, round(sw_), 60, C[tone], rx=6, opacity=0.1),
                    text(sx + 16, cy + 80, v, 20, 700, C[tone + "_t"]), text(sx + 16, cy + 100, lab, 12, 500, C["muted"])))
    cols = [("Barcode", 120), ("Status", 110), ("Current borrower", 220), ("Due date", 120), ("Action", 150)]
    ed = ("btn", [("Edit", "outline")])
    tb, end = table(rx + 20, cy + 128, rw - 40, cols, [
        ["BC-000131", ("b", "available"), ("muted", "—"), ("muted", "—"), ed],
        ["BC-000132", ("b", "borrowed"), ("person", "Chan Lina", "M-0108"), "30 Nov 2026", ed],
        ["BC-000133", ("b", "available"), ("muted", "—"), ("muted", "—"), ed],
        ["BC-000134", ("b", "borrowed"), ("person", "Sok Dara", "M-0021"), ("red", "10 Nov 2026"), ed],
        ["BC-000135", ("b", "available"), ("muted", "—"), ("muted", "—"), ed],
        ["BC-000136", ("b", "available"), ("muted", "—"), ("muted", "—"), ed],
    ], row_h=44, id_="Copies table")
    body.append(card(rx, cy, rw, end - cy + 16, "Copies", [text(rx + 20, cy + 34, "Copies (6)", 16, 600),
                                                            text(rx + rw - 20, cy + 34, "Print labels", 13, 600, C["primary"], anchor="end"),
                                                            *sp, tb]))
    ry = end + 32
    revs = [("Chan Lina", 5, "12 Nov 2026", ["Changed how I name functions. Every CS student", "should read chapters 2 and 3."]),
            ("Keo Vanna", 3, "02 Oct 2026", ["Useful, but some Java examples feel dated."])]
    rp = [text(rx + 20, ry + 34, "Reviews", 16, 600), text(rx + rw - 20, ry + 34, "Average 4.3 of 5", 13, 600, C["muted"], anchor="end")]
    yy = ry + 56
    for name, n, date, lines in revs:
        ini = "".join(p[0] for p in name.split())
        el = [circle(rx + 38, yy + 18, 18, C["secondary"], 0.15),
              text(rx + 38, yy + 22, ini, 12, 700, C["secondary_t"], anchor="middle"),
              text(rx + 66, yy + 14, name, 13, 600), stars(rx + 66, yy + 30, n, 5, 12),
              text(rx + 136, yy + 34, date, 12, 400, C["muted"])]
        for li, ln in enumerate(lines):
            el.append(text(rx + 66, yy + 58 + li * 19, ln, 13))
        el.append(small_btn(rx + rw - 20 - btn_w("Remove", "outline_danger", h=28, size=12), yy, "Remove", "outline_danger")[0])
        rp.append(g(f"Review/{name}", *el))
        yy += 52 + 19 * len(lines) + 12
    body.append(card(rx, ry, rw, yy - ry + 4, "Reviews", rp))
    return svg("08-Book-Detail", "".join(body))


def screen_copies():
    acts = ("btn", [("Edit", "primary"), ("Delete", "danger")])
    rows = [
        ["BC-000134", ("strong", "Clean Code"), "978-0132350884", ("b", "borrowed"), ("person", "Sok Dara", "due 10 Nov 2026"), acts],
        ["BC-000135", ("strong", "Clean Code"), "978-0132350884", ("b", "available"), ("muted", "—"), acts],
        ["BC-000412", ("strong", "Introduction to Algorithms"), "978-0262046305", ("b", "borrowed"), ("person", "Keo Vanna", "due 18 Nov 2026"), acts],
        ["BC-000452", ("strong", "Database System Concepts"), "978-0073523323", ("b", "available"), ("muted", "—"), acts],
        ["BC-000518", ("strong", "Principles of Microeconomics"), "978-1305585126", ("b", "damaged"), ("muted", "—"), acts],
        ["BC-000603", ("strong", "Head First Design Patterns"), "978-0596007126", ("b", "lost"), ("muted", "—"), acts],
        ["BC-000711", ("strong", "Sapiens"), "978-0143127741", ("b", "available"), ("muted", "—"), acts],
        ["BC-000877", ("strong", "Database System Concepts"), "978-0073523323", ("b", "borrowed"), ("person", "Chan Lina", "due 15 Nov 2026"), acts],
    ]
    cols = [("Barcode", 130), ("Book", 290), ("ISBN", 150), ("Status", 120), ("Current borrower", 210), ("Action", 176)]
    return svg("09-Book-Copies", list_page(
        "Book Copies", "Book Copies", "Catalog / Book Copies", cols, rows, "Search barcode or title…", "4,560", "456",
        actions=[("+ Add copy", "primary", None), ("Print labels", "outline", "printer")],
        filters=[("Status", "All statuses")], row_h=56, id_="Copies DataTable"))


def screen_copy_form():
    rows = [
        [("s", "Book", "Clean Code · 978-0132350884")],
        [("f", "Barcode", "BC-000137", "filled", "Leave blank to auto-generate."), ("s", "Status", "Available")],
        [("f", "Number of copies", "1", "filled", "More than 1 creates sequential barcodes.")],
    ]

    def side(x, y, w):
        return card(x, y, w, 250, "Book summary", [
            text(x + 24, y + 36, "Book summary", 16, 600),
            cover(x + 24, y + 60, 90, 130, 0),
            text(x + 132, y + 80, "Clean Code", 15, 600),
            text(x + 132, y + 100, "Robert C. Martin", 13, 400, C["muted"]),
            text(x + 132, y + 120, "Shelf A-12", 13, 400, C["muted"]),
            badge(x + 132, y + 136, "4 available")[0],
            badge(x + 140 + badge(0, 0, "4 available")[1], y + 136, "2 borrowed")[0],
            text(x + 24, y + 222, "6 copies on record", 12, 400, C["muted"])])
    return svg("10-Book-Copy-Form", form_page("Book Copies", "Add Book Copy", "Catalog / Book Copies / Add",
                                              "Copy details", rows, side=side, save="Save copy"))


def crud(n):
    return ("btn", [("Edit", "primary"), ("Delete", "danger")])


def screen_authors():
    data = [("Robert C. Martin", "unclebob@cleancoder.com", "6", "Software craftsman, Agile Manifesto"),
            ("Thomas H. Cormen", "cormen@cs.dartmouth.edu", "3", "Professor of computer science"),
            ("Abraham Silberschatz", "avi@cs.yale.edu", "4", "Database and OS researcher, Yale"),
            ("David Chandler", "d.chandler@monash.edu", "2", "Historian of Southeast Asia"),
            ("N. Gregory Mankiw", "mankiw@harvard.edu", "5", "Economist, Harvard University"),
            ("Yuval Noah Harari", "info@ynharari.com", "3", "Historian and author"),
            ("Eric Freeman", "eric@wickedlysmart.com", "2", "Co-author of the Head First series"),
            ("Martin Fowler", "fowler@thoughtworks.com", "7", "Refactoring and enterprise patterns")]
    rows = [[str(i + 1), ("avatar", n), e, b, ("muted", bio), crud(i)] for i, (n, e, b, bio) in enumerate(data)]
    cols = [("#", 50), ("Name", 220), ("Email", 250), ("Books", 80), ("Bio", 326), ("Action", 150)]
    return svg("11-Authors", list_page("Authors", "Authors", "Catalog / Authors", cols, rows,
                                       "Search name or email…", "642", "65", actions=[("+ Add author", "primary", None)],
                                       id_="Authors DataTable"))


def screen_author_form():
    rows = [[("f", "First name", "Robert"), ("f", "Last name", "Martin")],
            [("f", "Email", "unclebob@cleancoder.com")],
            [("t", "Bio", "Software engineer and author, known as “Uncle Bob”.\nCo-author of the Agile Manifesto.")]]

    def side(x, y, w):
        books = [("Clean Code", "2008", 0), ("The Clean Coder", "2011", 3), ("Clean Architecture", "2017", 4)]
        items = [text(x + 24, y + 36, "Books by this author", 16, 600)]
        for i, (t, yr, ci) in enumerate(books):
            yy = y + 60 + i * 64
            items += [cover(x + 24, yy, 36, 50, ci), text(x + 74, yy + 22, t, 14, 600),
                      text(x + 74, yy + 42, yr, 12, 400, C["muted"])]
        return card(x, y, w, 260, "Books by this author", items)
    return svg("12-Author-Form", form_page("Authors", "Edit Author", "Catalog / Authors / Edit", "Author details",
                                           rows, side=side, save="Update author"))


def screen_publishers():
    data = [("Prentice Hall", "info@prenhall.com", "+1 800 282 0693", "Upper Saddle River, NJ"),
            ("MIT Press", "books@mit.edu", "+1 617 253 5646", "Cambridge, MA"),
            ("McGraw-Hill", "support@mheducation.com", "+1 800 338 3987", "New York, NY"),
            ("Westview Press", "westview@perseus.com", "+1 303 444 3541", "Boulder, CO"),
            ("Cengage", "help@cengage.com", "+1 800 354 9706", "Boston, MA"),
            ("Harper", "info@harpercollins.com", "+1 212 207 7000", "New York, NY"),
            ("O'Reilly Media", "orders@oreilly.com", "+1 707 827 7000", "Sebastopol, CA")]
    rows = [[str(i + 1), ("strong", n), e, p, ("muted", a), crud(i)] for i, (n, e, p, a) in enumerate(data)]
    cols = [("#", 50), ("Name", 230), ("Email", 250), ("Phone", 150), ("Address", 246), ("Action", 150)]
    return svg("13-Publishers", list_page("Publishers", "Publishers", "Catalog / Publishers", cols, rows,
                                          "Search publishers…", "87", "9", actions=[("+ Add publisher", "primary", None)],
                                          id_="Publishers DataTable"))


def screen_publisher_form():
    rows = [[("f", "Name", "O'Reilly Media")],
            [("f", "Email", "orders@oreilly.com"), ("f", "Phone", "+1 707 827 7000", "filled", "Up to 15 characters.")],
            [("t", "Address", "1005 Gravenstein Highway North\nSebastopol, CA 95472")]]
    return svg("14-Publisher-Form", form_page("Publishers", "Add Publisher", "Catalog / Publishers / Add",
                                              "Publisher details", rows, save="Save publisher"))


def screen_categories():
    data = [("Computing", "computing", "Programming, algorithms, databases", "412"),
            ("History", "history", "World and Southeast Asian history", "236"),
            ("Economics", "economics", "Micro, macro and development economics", "158"),
            ("Literature", "literature", "Khmer and world literature, poetry", "201"),
            ("Science", "science", "Physics, chemistry and biology", "174"),
            ("Law", "law", "Cambodian and international law", "63"),
            ("Business", "business", "Management, marketing, accounting", "40")]
    rows = [[str(i + 1), ("strong", n), ("muted", s), d, b, crud(i)] for i, (n, s, d, b) in enumerate(data)]
    cols = [("#", 50), ("Name", 190), ("Slug", 170), ("Description", 380), ("Books", 96), ("Action", 190)]
    return svg("15-Categories", list_page("Categories", "Categories", "Catalog / Categories", cols, rows,
                                          "Search categories…", "7", "1", actions=[("+ Add category", "primary", None)],
                                          id_="Categories DataTable"))


def screen_category_form():
    rows = [[("f", "Name", "Computer Science", "focus")],
            [("f", "Slug", "computer-science", "filled", "Generated from the name. Must be unique.")],
            [("t", "Description", "Programming, algorithms, databases and\nsoftware engineering.")]]
    return svg("16-Category-Form", form_page("Categories", "Add Category", "Catalog / Categories / Add",
                                             "Category details", rows, save="Save category"))


def screen_members():
    acts = ("btn", [("View", "outline"), ("Edit", "primary")])
    data = [("Sok Dara", "M-0021", "sok.dara@student.biu.edu", "Student", "01 Sep 2025", "31 Aug 2027", "2", "active"),
            ("Chan Lina", "M-0108", "lina.chan@student.biu.edu", "Student", "15 Sep 2025", "14 Sep 2027", "1", "active"),
            ("Heng Rithy", "M-0133", "rithy.heng@gmail.com", "Public", "10 Oct 2024", "09 Oct 2025", "0", "expired"),
            ("Keo Vanna", "M-0245", "vanna.keo@student.biu.edu", "Student", "02 Feb 2026", "01 Feb 2028", "3", "suspend"),
            ("Dr. Meas Sokha", "M-0262", "sokha.meas@biu.edu", "Staff", "05 Jan 2026", "04 Jan 2029", "4", "active"),
            ("Lim Sopheak", "M-0310", "sopheak.lim@student.biu.edu", "Student", "20 Mar 2026", "19 Mar 2028", "1", "active"),
            ("Ouk Bopha", "M-0377", "bopha.ouk@yahoo.com", "Public", "11 Jun 2026", "10 Jun 2027", "0", "inactive"),
            ("Pich Sreymom", "M-0412", "sreymom.pich@student.biu.edu", "—", "23 Nov 2026", "—", "0", "pending")]
    review = ("btn", [("Review", "primary"), ("Edit", "outline")])
    rows = [[("person", n, c), e, p, j, x, l, ("b", s), review if s == "pending" else acts]
            for n, c, e, p, j, x, l, s in data]
    cols = [("Member", 180), ("Email", 230), ("Plan", 90), ("Joined", 110), ("Expires", 110), ("Loans", 70),
            ("Status", 110), ("Action", 176)]
    return svg("17-Members", list_page("Members", "Members", "Membership / Members", cols, rows,
                                       "Search name, code, email…", "2,318", "232",
                                       actions=[("+ Add member", "primary", None)],
                                       filters=[("Plan", "All plans"), ("Status", "All statuses")], row_h=56,
                                       id_="Members DataTable"))


def screen_member_form():
    rows = [[("f", "Member code", "M-0412", "filled", "Auto-generated, editable."), ("f", "Full name", "Pich Sreymom")],
            [("f", "Email", "sreymom.pich@biu.edu"), ("f", "Phone", "012 345 678")],
            [("s", "Plan", "Student · 14 days"), ("s", "Status", "Active")],
            [("d", "Join date", "23 Nov 2026"), ("d", "Expiry date", "23 Nov 2027", "filled", "Defaults to join date + 1 year.")],
            [("t", "Address", "St. 271, Sangkat Tuol Tumpung\nPhnom Penh")],
            [("check", "Create a login so the member can use the portal", True)]]

    def side(x, y, w):
        return card(x, y, w, 300, "Plan summary", [
            text(x + 24, y + 36, "Student plan", 16, 600),
            badge(x + w - 24 - badge(0, 0, "1,612 used", "info")[1], y + 20, "1,612 used", "info")[0],
            kv_rows(x + 24, y + 52, w - 48, [("Loan period", "14 days"), ("Fine per day", "$0.50"),
                                              ("Discount fee", "$0.00"), ("Max active loans", "5")]),
            g("Alert/info", rect(x + 24, y + 200, w - 48, 60, C["info"], rx=4, opacity=0.12),
              text(x + 38, y + 225, "A welcome email with a password link", 13, 500, C["info_t"]),
              text(x + 38, y + 245, "is sent when a login is created.", 13, 500, C["info_t"]))])
    return svg("18-Member-Form", form_page("Members", "Add Member", "Membership / Members / Add", "Member details",
                                           rows, side=side, save="Save member"))


def screen_member_profile():
    body = [sidebar("Members"), navbar("Hi, Librarian"), page_title("Sok Dara", "Membership / Members / M-0021"),
            header_buttons([("+ New borrow", "primary", None), ("Edit", "outline", None)])]
    cy, lw, lx = 156, 340, CX
    info = [("Email", "sok.dara@student.biu.edu"), ("Phone", "012 888 021"), ("Plan", "Student · 14 days"),
            ("Joined", "01 Sep 2025"), ("Expires", "31 Aug 2027"), ("Login", "Linked")]
    bw = round(tw("active", 12, 600) + 18)
    body.append(card(lx, cy, lw, 540, "Profile", [
        circle(lx + lw / 2, cy + 68, 44, C["secondary"], 0.15),
        text(lx + lw / 2, cy + 78, "SD", 26, 700, C["secondary_t"], anchor="middle"),
        text(lx + lw / 2, cy + 140, "Sok Dara", 18, 700, anchor="middle"),
        text(lx + lw / 2, cy + 162, "M-0021", 13, 400, C["muted"], anchor="middle"),
        badge(round(lx + lw / 2 - bw / 2), cy + 174, "active")[0],
        kv_rows(lx + 24, cy + 208, lw - 48, info),
        g("Outstanding fines", rect(lx + 24, cy + 428, lw - 48, 84, C["danger"], rx=6, opacity=0.1),
          text(lx + 40, cy + 456, "Outstanding fines", 13, 600, C["danger_t"]),
          text(lx + 40, cy + 492, "$6.50", 26, 700, C["danger_t"]),
          small_btn(lx + lw - 40 - btn_w("Collect", "danger", h=28, size=12), cy + 466, "Collect", "danger")[0])]))
    rx, rw = CX + lw + 16, CW - lw - 16
    kw = (rw - 32) / 3
    tiles = [("Active loans", "2", "success", "book"), ("Overdue", "1", "danger", "clock"), ("Total borrowed", "18", "primary", "borrow")]
    body.append(g("KPI Row", *[kpi(round(rx + i * (kw + 16)), cy, round(kw), *t,
                                   value_color=C["danger"] if t[0] == "Overdue" else None) for i, t in enumerate(tiles)]))
    ay = cy + 100
    tb, end = table(rx + 20, ay + 50, rw - 40, [("Book", 240), ("Barcode", 110), ("Borrowed", 110), ("Due", 110), ("Status", 150)], [
        [("strong", "Clean Code"), "BC-000134", "27 Oct 2026", ("red", "10 Nov 2026"), ("b", "overdue")],
        [("strong", "Database System Concepts"), "BC-000452", "23 Nov 2026", "07 Dec 2026", ("b", "active")]],
        row_h=46, id_="Active loans table")
    body.append(card(rx, ay, rw, end - ay + 16, "Active loans", [text(rx + 20, ay + 32, "Active loans", 16, 600), tb]))
    hy = end + 32
    tb, end = table(rx + 20, hy + 50, rw - 40, [("Book", 240), ("Borrowed", 104), ("Due", 104), ("Returned", 104), ("Fine", 72), ("Status", 96)], [
        [("strong", "Principles of Microeconomics"), "02 Oct 2026", "16 Oct 2026", "15 Oct 2026", "$0.00", ("b", "returned")],
        [("strong", "A History of Cambodia"), "15 Sep 2026", "29 Sep 2026", "03 Oct 2026", ("red", "$2.00"), ("b", "returned")],
        [("strong", "Sapiens"), "01 Sep 2026", "15 Sep 2026", "12 Sep 2026", "$0.00", ("b", "returned")],
        [("strong", "Head First Design Patterns"), "10 Jul 2026", "24 Jul 2026", "—", ("red", "$12.00"), ("b", "lost")]],
        row_h=44, id_="Loan history table")
    body.append(card(rx, hy, rw, end - hy + 16, "Loan history", [
        text(rx + 20, hy + 32, "Loan history", 16, 600),
        text(rx + rw - 20, hy + 32, "18 loans · View all →", 13, 600, C["primary"], anchor="end"), tb]))
    return svg("19-Member-Profile", "".join(body))


def screen_plans():
    body = [sidebar("Member Plans"), navbar("Hi, Admin"), page_title("Member Plans", "Membership / Member Plans"),
            header_buttons([("+ Add plan", "primary", None)])]
    plans = [("Student", "Undergraduate and postgraduate students", "14", "$0.50", "$0.00", "5", "1,612", True),
             ("Staff & Faculty", "Lecturers and administrative staff", "30", "$0.25", "$0.00", "10", "594", False),
             ("Public", "Community readers with a library card", "7", "$1.00", "$10.00", "3", "112", False)]
    pw = (CW - 32) / 3
    cy = 156
    for i, (n, d, days, fine, fee, mx, mem, top) in enumerate(plans):
        x = round(CX + i * (pw + 16))
        w = round(pw)
        el = [text(x + 24, cy + 40, n, 18, 700), text(x + 24, cy + 62, d, 13, 400, C["muted"]),
              line(x, cy + 84, x + w, cy + 84),
              text(x + 24, cy + 132, days, 36, 700, C["primary"]),
              text(round(x + 32 + tw(days, 36, 700)), cy + 132, "days", 16, 600, C["muted"]),
              text(x + 24, cy + 154, "Loan period", 12, 400, C["muted"]),
              kv_rows(x + 24, cy + 168, w - 48, [("Fine per day", fine), ("Discount fee", fee),
                                                  ("Max active loans", mx), ("Members", mem)])]
        if top:
            bw = round(tw("Most used", 12, 600) + 18)
            el.append(badge(x + w - 24 - bw, cy + 24, "Most used", "info")[0])
        half = (w - 60) / 2
        el += [button(x + 24, cy + 324, "Edit", "outline", w=round(half))[0],
               button(round(x + 36 + half), cy + 324, "Delete", "outline_danger", w=round(half))[0]]
        body.append(card(x, cy, w, 384, f"Plan/{n}", el))
    body.append(alert(CX, cy + 400, CW, "Plan changes apply to new loans only. Fines on returned loans are stored at return time.", "info", h=44))
    return svg("20-Member-Plans", "".join(body))


def screen_plan_form():
    rows = [[("f", "Plan name", "Student")],
            [("f", "Loan duration (days)", "14"), ("f", "Fine per day ($)", "0.50")],
            [("f", "Discount fee ($)", "0.00"), ("f", "Max active loans", "5", "focus")],
            [("t", "Description", "Undergraduate and postgraduate students\nwith a valid BIU student ID.")]]
    return svg("21-Member-Plan-Form", form_page(
        "Member Plans", "Edit Member Plan", "Membership / Member Plans / Edit", "Plan details", rows,
        save="Update plan", user="Hi, Admin",
        alert_=("1,612 members use this plan. Changes apply to new loans only.", "warning")))


def loans_body():
    ret = ("btn", [("Return", "primary")])
    rows = [
        [("person", "Sok Dara", "M-0021"), ("strong", "Clean Code"), "BC-000134", "27 Oct 2026", ("red", "10 Nov 2026"), ("b", "overdue"), "Sophea", ret],
        [("person", "Chan Lina", "M-0108"), ("strong", "Database Concepts"), "BC-000877", "01 Nov 2026", ("red", "15 Nov 2026"), ("b", "overdue"), "Sophea", ret],
        [("person", "Sok Dara", "M-0021"), ("strong", "Database Concepts"), "BC-000452", "23 Nov 2026", "07 Dec 2026", ("b", "active"), "Vichea", ret],
        [("person", "Dr. Meas Sokha", "M-0262"), ("strong", "Sapiens"), "BC-000711", "20 Nov 2026", "20 Dec 2026", ("b", "active"), "Vichea", ret],
        [("person", "Lim Sopheak", "M-0310"), ("strong", "Microeconomics"), "BC-000990", "06 Nov 2026", ("red", "20 Nov 2026"), ("b", "overdue"), "Sophea", ret],
        [("person", "Chan Lina", "M-0108"), ("strong", "Clean Code"), "BC-000132", "16 Nov 2026", "30 Nov 2026", ("b", "active"), "Sophea", ret],
        [("person", "Keo Vanna", "M-0245"), ("strong", "History of Cambodia"), "BC-000221", "01 Nov 2026", "15 Nov 2026", ("b", "returned"), "Vichea", ("muted", "on 14 Nov")],
        [("person", "Heng Rithy", "M-0133"), ("strong", "Head First Patterns"), "BC-000603", "10 Jul 2026", "24 Jul 2026", ("b", "lost"), "Sophea", ("muted", "fine $12.00")],
    ]
    cols = [("Member", 170), ("Book", 190), ("Barcode", 110), ("Borrowed", 110), ("Due", 110), ("Status", 110),
            ("Staff", 90), ("Action", 186)]
    return list_page("Borrow Books", "Borrow Books", "Circulation / Borrow Books", cols, rows,
                     "Member, barcode, title…", "1,902", "238",
                     actions=[("+ New borrow", "primary", None)], filters=[("Staff", "All staff")],
                     tabs=[("All", "1,902"), ("Active", "426"), ("Overdue", "37"), ("Returned", "1,439")],
                     row_h=56, id_="Loans DataTable")


def screen_loans():
    return svg("22-Borrow-List", loans_body())


def screen_return_modal():
    mw, mh = 560, 590
    x, y = (W - mw) / 2, 155
    parts, _ = form_grid(x + 24, y + 176, mw - 48, [
        [("d", "Due date", "10 Nov 2026"), ("d", "Return date", "23 Nov 2026")],
        [("radio", "Condition", (["Good", "Damaged", "Lost"], "Good"))]])
    m = [rect(x, y, mw, mh, C["card"], rx=12), modal_head(x, y, mw, "Return book"),
         g("Loan summary", rect(x + 24, y + 84, mw - 48, 72, C["subtle"], rx=6), cover(x + 40, y + 94, 36, 52, 0),
           text(x + 90, y + 115, "Clean Code", 14, 600),
           text(x + 90, y + 136, "BC-000134 · Sok Dara (M-0021) · Student plan", 12, 400, C["muted"])),
         *parts,
         g("Fine", rect(x + 24, y + 332, mw - 48, 76, C["danger"], rx=6, opacity=0.1),
           text(x + 40, y + 361, "Overdue fine", 13, 600, C["danger_t"]),
           text(x + 40, y + 383, "13 days late × $0.50 per day", 13, 400, C["danger_t"]),
           text(x + mw - 40, y + 380, "$6.50", 26, 700, C["danger_t"], anchor="end")),
         checkbox(x + 24, y + 422, "Fine collected now", True),
         alert(x + 24, y + 460, mw - 48, "Keo Vanna reserved this title. The copy will be held 3 days.", "info"),
         line(x, y + 522, x + mw, y + 522)]
    cw_ = btn_w("Confirm return")
    ob = btn_w("Cancel", "outline")
    m += [button(x + mw - 24 - cw_, y + 538, "Confirm return")[0],
          button(x + mw - 24 - cw_ - 12 - ob, y + 538, "Cancel", "outline")[0]]
    return svg("23-Return-Modal", loans_body() + overlay() + g("Modal/Return book", *m))


def screen_reservations():
    rows = [
        [("person", "Keo Vanna", "M-0245"), ("strong", "Introduction to Algorithms"), "18 Nov 2026", "#1", ("person", "26 Nov 2026", "notified 23 Nov"), ("b", "pending"), ("btn", [("Hold copy", "primary"), ("Cancel", "outline_danger")])],
        [("person", "Sok Dara", "M-0021"), ("strong", "Introduction to Algorithms"), "20 Nov 2026", "#2", ("muted", "waiting"), ("b", "pending"), ("btn", [("Cancel", "outline_danger")])],
        [("person", "Chan Lina", "M-0108"), ("strong", "Sapiens"), "21 Nov 2026", "#1", ("muted", "waiting"), ("b", "pending"), ("btn", [("Cancel", "outline_danger")])],
        [("person", "Lim Sopheak", "M-0310"), ("strong", "Clean Code"), "08 Nov 2026", "—", ("muted", "picked up 12 Nov"), ("b", "fulfilled"), ("muted", "—")],
        [("person", "Ouk Bopha", "M-0377"), ("strong", "Database System Concepts"), "01 Nov 2026", "—", ("muted", "05 Nov 2026"), ("b", "expired"), ("muted", "—")],
        [("person", "Heng Rithy", "M-0133"), ("strong", "A History of Cambodia"), "28 Oct 2026", "—", ("muted", "—"), ("b", "cancelled"), ("muted", "by member")],
        [("person", "Dr. Meas Sokha", "M-0262"), ("strong", "Head First Design Patterns"), "25 Oct 2026", "—", ("muted", "picked up 27 Oct"), ("b", "fulfilled"), ("muted", "—")],
    ]
    cols = [("Member", 180), ("Book", 250), ("Reserved", 120), ("Queue", 70), ("Hold expires", 150), ("Status", 116), ("Action", 190)]
    return svg("24-Reservations", list_page(
        "Reservations", "Reservations", "Circulation / Reservations", cols, rows, "Member or title…", "340", "34",
        filters=[("Book", "All books")],
        tabs=[("All", "340"), ("Pending", "14"), ("Fulfilled", "286"), ("Cancelled", "31"), ("Expired", "9")],
        row_h=56, id_="Reservations DataTable"))


def screen_reports():
    body = [sidebar("Reports"), navbar("Hi, Admin"), page_title("Reports", "Administration / Reports"),
            header_buttons([("Export PDF", "outline", "download"), ("Export CSV", "outline", "download")])]
    cy = 156
    seg = [rect(CX + 20, cy + 18, 336, 36, C["subtle"], rx=6)]
    for i, lab in enumerate(["Overdue", "Circulation", "Inventory"]):
        sx = CX + 20 + i * 112
        on = i == 1
        if on:
            seg.append(rect(sx + 3, cy + 21, 106, 30, C["primary"], rx=4))
        seg.append(text(sx + 56, cy + 41, lab, 13, 600, C["white"] if on else C["muted"], anchor="middle"))
    ax = CX + CW - 20 - btn_w("Apply")
    fl = [button(ax, cy + 18, "Apply")[0]]
    sx = ax - 12 - 170
    fl += [rect(sx, cy + 18, 170, 36, C["card"], rx=4, stroke=C["border"], sw=1), text(sx + 12, cy + 41, "All categories", 13),
           icon("chevron", sx + 146, cy + 28, 14, C["muted"])]
    for lab in ["23 Nov 2026", "01 Sep 2026"]:
        sx -= 12 + 150
        fl += [rect(sx, cy + 18, 150, 36, C["card"], rx=4, stroke=C["border"], sw=1), text(sx + 12, cy + 41, lab, 13),
               icon("calendar", sx + 124, cy + 27, 16, C["muted"])]
    fl.append(text(sx - 12, cy + 41, "Period", 13, 400, C["muted"], anchor="end"))
    body.append(card(CX, cy, CW, 72, "Report filters", [g("Report type", *seg), g("Filters", *fl)]))
    # chart
    ch_y, ch_h = cy + 88, 300
    gx, gy, gw, gh = CX + 56, ch_y + 64, CW - 80, 180
    bor = [142, 156, 171, 165, 180, 192, 174, 188, 201, 196, 185, 178]
    ret = [130, 149, 160, 168, 170, 181, 169, 180, 190, 193, 180, 166]
    weeks = ["7 Sep", "14 Sep", "21 Sep", "28 Sep", "5 Oct", "12 Oct", "19 Oct", "26 Oct", "2 Nov", "9 Nov", "16 Nov", "23 Nov"]
    bars = []
    for i in range(5):
        yy = gy + gh - i * gh / 4
        bars += [line(gx, yy, gx + gw, yy), text(gx - 10, yy + 4, str(i * 50), 11, 400, C["muted"], anchor="end")]
    gwid = gw / 12
    bw = (gwid - 20) / 2
    for i in range(12):
        bx = gx + i * gwid + 10
        for j, (v, col) in enumerate([(bor[i], C["primary"]), (ret[i], C["success"])]):
            h = v / 200 * gh
            bars.append(rect(round(bx + j * bw, 1), round(gy + gh - h, 1), round(bw - 3, 1), round(h, 1), col, rx=2))
        bars.append(text(round(bx + bw, 1), gy + gh + 18, weeks[i], 11, 400, C["muted"], anchor="middle"))
    lg = [rect(CX + CW - 196, ch_y + 23, 10, 10, C["primary"], rx=2), text(CX + CW - 180, ch_y + 32, "Borrowed", 12, 500, C["muted"]),
          rect(CX + CW - 104, ch_y + 23, 10, 10, C["success"], rx=2), text(CX + CW - 88, ch_y + 32, "Returned", 12, 500, C["muted"])]
    body.append(card(CX, ch_y, CW, ch_h, "Chart: Circulation by week", [
        text(CX + 20, ch_y + 34, "Circulation by week", 16, 600), g("Legend", *lg), g("Bars", *bars)]))
    # inventory
    iy = ch_y + ch_h + 16
    iw = round(CW * 0.4)
    inv = [("available", 3912, "success"), ("borrowed", 426, "warning"), ("damaged", 141, "info"), ("lost", 81, "danger")]
    total = sum(v for _, v, _ in inv)
    seg, sx = [], CX + 24
    for lab, v, tone in inv:
        w = (iw - 48) * v / total
        seg.append(rect(round(sx, 1), iy + 92, round(w, 1), 14, C[tone]))
        sx += w
    leg = []
    for i, (lab, v, tone) in enumerate(inv):
        yy = iy + 128 + i * 34
        leg += [circle(CX + 30, yy + 16, 5, C[tone]), text(CX + 44, yy + 21, lab.capitalize(), 13),
                text(CX + iw - 80, yy + 21, f"{v:,}", 13, 600, anchor="end"),
                text(CX + iw - 24, yy + 21, f"{v / total * 100:.1f}%", 13, 400, C["muted"], anchor="end")]
    ih = 900 - 40 - iy
    body.append(card(CX, iy, iw, ih, "Inventory by status", [
        text(CX + 24, iy + 34, "Inventory by status", 16, 600),
        text(CX + 24, iy + 76, f"{total:,} copies", 24, 700), g("Stacked bar", *seg), g("Legend", *leg)]))
    tx, tw2 = CX + iw + 16, CW - iw - 16
    tb, _ = table(tx + 20, iy + 50, tw2 - 40, [("Category", 200), ("Loans", 100), ("Returned", 110), ("Overdue", 100), ("Overdue rate", 104)], [
        [("strong", "Computing"), "812", "774", ("red", "14"), "1.7%"],
        [("strong", "History"), "386", "371", ("red", "6"), "1.6%"],
        [("strong", "Economics"), "301", "282", ("red", "9"), "3.0%"],
        [("strong", "Literature"), "244", "238", ("red", "3"), "1.2%"],
        [("strong", "Science"), "159", "151", ("red", "5"), "3.1%"]], row_h=40, id_="Category table")
    body.append(card(tx, iy, tw2, ih, "Circulation by category", [text(tx + 20, iy + 34, "Circulation by category", 16, 600), tb]))
    return svg("25-Reports", "".join(body))


def screen_users():
    edit = ("btn", [("Edit", "primary"), ("Delete", "outline_danger")])
    rows = [
        [("avatar", "System Admin", "IT office"), "admin@biu.edu", ("link", "Administrator"), ("b", "active"), "23 Nov 2026, 08:02", ("btn", [("Edit", "primary")])],
        [("avatar", "Chan Sophea", "Circulation librarian"), "sophea.chan@biu.edu", ("link", "Librarian"), ("b", "active"), "23 Nov 2026, 07:55", edit],
        [("avatar", "Vichea Long", "Cataloguer"), "vichea.long@biu.edu", ("link", "Cataloguer"), ("b", "active"), "22 Nov 2026, 16:40", edit],
        [("avatar", "Ly Sokunthea", "Student helper"), "sokunthea.ly@biu.edu", ("link", "Returns assistant"), ("b", "active"), "21 Nov 2026, 19:12", edit],
        [("avatar", "Ros Chantha", "Former librarian"), "chantha.ros@biu.edu", ("link", "Librarian"), ("b", "terminated"), "02 Jun 2026, 11:03", edit],
    ]
    cols = [("Name", 240), ("Email", 230), ("Role", 160), ("Status", 120), ("Last login", 170), ("Action", 156)]
    return svg("26-Users", list_page("Staff Accounts", "Staff Accounts", "Administration / Staff Accounts", cols, rows,
                                     "Search name or email…", "12", "2", actions=[("+ Add staff", "primary", None)],
                                     filters=[("Role", "All roles"), ("Status", "All statuses")], user="Hi, Admin",
                                     row_h=58, id_="Staff DataTable"))


def screen_user_form():
    rows = [[("f", "First name", "Sophea"), ("f", "Last name", "Chan")],
            [("f", "Email", "sophea.chan@biu.edu"), ("f", "Phone", "015 222 333")],
            [("f", "Position", "Circulation librarian"), ("s", "Role", "Librarian")],
            [("s", "Status", "Active", "filled", "Terminated staff cannot sign in.")],
            [("f", "Password", "••••••••••", "filled", "At least 8 characters."),
             ("f", "Confirm password", "••••••••", "error", "The passwords do not match.")]]

    def side(x, y, w):
        areas = [("Books, copies, authors", "Full"), ("Publishers, categories", "Full"), ("Members and plans", "Full"),
                 ("Borrow desk", "Full"), ("Reservations", "Full"), ("Administration", "None")]
        return card(x, y, w, 330, "Role access", [
            text(x + 24, y + 36, "What Librarian can do", 16, 600),
            kv_rows(x + 24, y + 52, w - 48, areas),
            text(x + 24, y + 290, "You can only give a role that has", 12, 400, C["muted"]),
            text(x + 24, y + 308, "no more access than your own.", 12, 400, C["muted"])])
    return svg("27-User-Form", form_page("Staff Accounts", "Add Staff Account", "Administration / Staff Accounts / Add",
                                         "Account details", rows, side=side, save="Create account", user="Hi, Admin"))


def screen_roles():
    edit = ("btn", [("Edit", "primary"), ("Delete", "outline_danger")])
    rows = [
        [("strong", "Administrator"), "Full access to everything.", ("b", "Full access"), "1", ("muted", "Built in")],
        [("strong", "Librarian"), "Catalogue, members and the borrow desk.", "34 permissions", "6", edit],
        [("strong", "Cataloguer"), "Keeps books, authors and publishers up to date.", "20 permissions", "3", edit],
        [("strong", "Returns assistant"), "Part-time helper at the returns desk.", "1 permission", "2", edit],
    ]
    cols = [("Role", 200), ("Description", 400), ("Access", 190), ("Staff", 110), ("Action", 176)]
    return svg("34-Roles", list_page("Roles & Permissions", "Roles", "Administration / Roles & Permissions", cols, rows, "Search roles…", "4", "1",
                                     actions=[("+ Add role", "primary", None)], user="Hi, Admin", row_h=56,
                                     id_="Roles DataTable"))


def tick(x, y, on):
    parts = [rect(x, y, 16, 16, C["primary"] if on else C["card"], rx=3,
                  stroke=None if on else C["border"], sw=None if on else 1.5)]
    if on:
        parts.append(icon("check", x + 2, y + 2, 12, C["white"]))
    return "".join(parts)


def screen_role_form():
    crud = ["View", "Add", "Edit", "Delete"]
    yes, no, view = (1, 1, 1, 1), (0, 0, 0, 0), (1, 0, 0, 0)

    def grp(title, noun, flags, *more, is_open=True):
        return title, [(f"{v} {noun}", f) for v, f in zip(crud, flags)] + list(more), is_open
    cols = [[grp("Authors", "authors", yes), grp("Books", "books", yes, is_open=False),
             ("Borrow desk", [("View loans", 1), ("Lend books", 0), ("Receive returns", 0)], True),
             grp("Staff accounts", "staff accounts", no, is_open=False)],
            [grp("Book copies", "book copies", yes), grp("Categories", "categories", yes, is_open=False),
             grp("Members", "members", view, ("Approve registrations", 0)), grp("Roles", "roles", no, is_open=False)],
            [grp("Member plans", "member plans", view), grp("Publishers", "publishers", yes, is_open=False),
             ("Reservations", [("View reservations", 1), ("Manage reservations", 0)], True)]]
    body = [sidebar("Roles & Permissions"), navbar("Hi, Admin"),
            page_title("Update Role", "Administration / Roles & Permissions / Edit")]
    cy = 148
    inner = [text(CX + 24, cy + 32, "Role and permissions", 16, 600), line(CX, cy + 50, CX + CW, cy + 50)]
    parts, y = form_grid(CX + 24, cy + 62, CW - 48, [[("f", "Role name", "Cataloguer"),
                                                      ("f", "Description", "Keeps books, authors and publishers up to date.")]])
    inner += parts + [text(CX + 24, y + 6, "Permissions", 13, 600),
                      text(CX + 112, y + 6, "Tick what people with this role may do. You can only give access that you have yourself.", 12, 400, C["muted"])]
    ticked = sum(f for col in cols for _, items, _ in col for _, f in items)
    total = sum(len(items) for col in cols for _, items, _ in col)
    inner += [g("Select all", rect(CX + 24, y + 26, 16, 16, C["primary"], rx=3), rect(CX + 28, y + 33, 8, 2, C["white"]),
                text(CX + 50, y + 39, "Select all permissions", 13, 600)),
              text(CX + 210, y + 39, f"{ticked} of {total} selected", 13, 400, C["muted"])]
    bx = CX + CW - 24
    for lab in ["Collapse all", "Expand all"]:
        bw = small_btn(0, 0, lab, "outline")[1]
        bx -= bw
        inner.append(small_btn(bx, y + 20, lab, "outline")[0])
        bx -= 8
    gap = 16
    gw = (CW - 48 - 2 * gap) / 3
    bottom = 0
    for ci, col in enumerate(cols):
        gx, gy = round(CX + 24 + ci * (gw + gap)), y + 68
        for title, items, is_open in col:
            n = sum(f for _, f in items)
            gh = 30 + 24 * (len(items) + 1) if is_open else 26
            cw_ = round(tw(title, 13, 600) + tw(f"{n}/{len(items)}", 12) + 44)
            cxv, cyv = gx + 20, gy
            arrow = icon("chevron", cxv, cyv - 6, 12, C["text"])
            if not is_open:
                arrow = f'<g transform="rotate(-90 {cxv + 6} {cyv})">{arrow}</g>'
            el = [rect(gx, gy, round(gw), gh, C["card"], rx=6, stroke=C["border"], sw=1),
                  rect(gx + 14, gy - 9, cw_, 18, C["card"]), arrow,
                  text(gx + 38, gy + 5, title, 13, 600),
                  text(gx + 44 + round(tw(title, 13, 600)), gy + 5, f"{n}/{len(items)}", 12, 400, C["muted"])]
            if is_open:
                el += [tick(gx + 16, gy + 20, n == len(items)), text(gx + 42, gy + 33, "Select all", 13, 400, C["muted"])]
                if 0 < n < len(items):
                    el += [rect(gx + 16, gy + 20, 16, 16, C["primary"], rx=3), rect(gx + 20, gy + 27, 8, 2, C["white"])]
                for k, (lab, f) in enumerate(items):
                    el += [tick(gx + 16, gy + 44 + k * 24, f), text(gx + 42, gy + 57 + k * 24, lab, 13)]
            inner.append(g(f"Group/{title}{'' if is_open else ' (collapsed)'}", *el))
            gy += gh + 24
        bottom = max(bottom, gy)
    el, bw = button(CX + 24, bottom, "Save role")
    inner += [el, button(CX + 36 + bw, bottom, "Cancel", "outline")[0]]
    body.append(card(CX, cy, CW, bottom + 52 - cy, "Form card", inner))
    h = max(H, bottom + 80)
    doc = "".join(body).replace(rect(0, 0, SIDE, H, C["sidebar"]), rect(0, 0, SIDE, h, C["sidebar"]), 1)
    return svg("35-Role-Form", doc, h=h)


def screen_review_registration():
    body = [sidebar("Members"), navbar("Hi, Librarian"), page_title("Review Registration", "Membership / Members / Review")]
    cy, lw = 156, 430
    body.append(card(CX, cy, lw, 290, "Submitted card", [
        text(CX + 24, cy + 36, "Submitted by the member", 16, 600),
        kv_rows(CX + 24, cy + 52, lw - 48, [("Code", "M-0412"), ("Name", "Pich Sreymom"), ("Email", "sreymom.pich@student.biu.edu"),
                                            ("Phone", "098 111 222"), ("Address", "Battambang"), ("Registered", "23 Nov 2026, 09:14")])]))
    body.append(g("Alert/warning", rect(CX, cy + 306, lw, 64, C["warning"], rx=6, opacity=0.14),
                  text(CX + 16, cy + 333, "Check the person's ID before approving.", 13, 600, C["warning_t"]),
                  text(CX + 16, cy + 353, "The email was typed by the member, not verified.", 13, 400, C["warning_t"])))
    rx, rw = CX + lw + 16, CW - lw - 16
    parts, y = form_grid(rx + 24, cy + 72, rw - 48, [
        [("s", "Existing card with the same email", "Do not link, make this a new membership", "filled",
          "Linking gives this login the loans and history of that card.")],
        [("s", "Plan", "Student · 14 days"), ("d", "Expiry date", "23 Nov 2027")]])
    el, bw = button(rx + 24, y + 16, "Approve")
    el2, bw2 = button(rx + 36 + bw, y + 16, "Reject", "danger")
    body.append(card(rx, cy, rw, y + 72 - cy, "Decision card", [
        text(rx + 24, cy + 36, "Decision", 16, 600), line(rx, cy + 56, rx + rw, cy + 56), *parts,
        line(rx, y, rx + rw, y), el, el2, button(rx + 48 + bw + bw2, y + 16, "Back", "outline")[0]]))
    return svg("38-Review-Registration", "".join(body))


def portal_page(active, title, sub, action=None):
    x0, cw = 120, W - 240
    body = [portal_nav(active), g("Header", text(x0, 118, title, 24, 700), text(x0, 142, sub, 13, 400, C["muted"]))]
    if action:
        body.append(button(x0 + cw - btn_w(action), 100, action)[0])
    return body, x0, cw


def portal_table(x0, y, cw, title, cols, rows):
    tb, end = table(x0 + 20, y + 50, cw - 40, cols, rows, row_h=52, id_=f"{title} table")
    return card(x0, y, cw, end - y + 20, title, [text(x0 + 20, y + 32, title, 16, 600), tb]), end + 40


def screen_portal_book():
    body, x0, cw = portal_page("Catalog", "Introduction to Algorithms", "Thomas H. Cormen")
    y = 170
    facts = [("Category", "Computing"), ("Publisher", "MIT Press"), ("Year", "2022"), ("Pages", "1,312"), ("Language", "English"),
             ("ISBN", "9780262046305"), ("Shelf", "A-14"), ("Copies", "0 of 2 available")]
    el = [rect(x0 + 20, y + 20, 240, 320, C["subtle"], rx=8), cover(x0 + 60, y + 60, 160, 220, 1)]
    for i, (k, v) in enumerate(facts):
        fx, fy = x0 + 300 + (i % 2) * 400, y + 50 + (i // 2) * 60
        el += [text(fx, fy, k, 13, 500, C["muted"]), text(fx, fy + 22, v, 14, 600)]
    el += [button(x0 + 300, y + 290, "Reserve")[0],
           text(x0 + 410, y + 313, "All copies are out. You join the queue and we hold the next one for you.", 13, 400, C["muted"])]
    body.append(card(x0, y, cw, 360, "Book card", el))
    return svg("39-Member-Book", "".join(body))


def screen_portal_loans():
    body, x0, cw = portal_page("My loans", "My Loans", "Bring books back to the library desk. Staff confirm the return.")
    cols = [("Book", 420), ("Barcode", 180), ("Borrowed", 180), ("Due", 180), ("Status", 200)]
    c1, y = portal_table(x0, 170, cw, "On loan now", cols, [
        [("strong", "Clean Code"), "BIU-00001", "27 Oct 2026", ("red", "10 Nov 2026"), ("b", "overdue")],
        [("strong", "Database System Concepts"), "BIU-00004", "23 Nov 2026", "07 Dec 2026", ("b", "borrowed")]])
    c2, _ = portal_table(x0, y, cw, "History", cols, [
        [("strong", "A History of Cambodia"), "BIU-00007", "15 Sep 2026", "29 Sep 2026", ("b", "returned")]])
    return svg("40-Member-Loans", "".join(body + [c1, c2]))


def screen_portal_reservations():
    body, x0, cw = portal_page("Reservations", "Reservations", "When a book is ready, collect it at the desk before the hold ends.",
                               "Find a book")
    cols = [("Book", 400), ("Reserved", 180), ("Status", 200), ("Collect before", 220), ("", 160)]
    cancel = ("btn", [("Cancel", "outline_danger")])
    c1, y = portal_table(x0, 170, cw, "Open", cols, [
        [("strong", "Introduction to Algorithms"), "21 Nov 2026", ("b", "Ready to collect"), "26 Nov 2026, 09:30", cancel],
        [("strong", "Sapiens"), "23 Nov 2026", ("b", "pending"), "—", cancel]])
    c2, _ = portal_table(x0, y, cw, "Closed", cols, [
        [("strong", "Refactoring"), "02 Nov 2026", ("b", "Collected"), "—", ("muted", "")]])
    return svg("41-Member-Reservations", "".join(body + [c1, c2]))


def screen_portal_profile():
    body, x0, cw = portal_page("Profile", "Profile", "Member M-0021 · Active")
    p1, y1 = form_grid(x0 + 20, 220, cw - 40, [[("f", "Name", "Sok Dara"),
                                                ("f", "Email", "sok.dara@student.biu.edu", "placeholder", "Ask the library desk to change your email.")],
                                               [("f", "Phone", "012 345 678"), ("f", "Address", "Phnom Penh")]])
    body.append(card(x0, 166, cw, y1 - 166, "Details card", [text(x0 + 20, 200, "Your details", 16, 600), *p1]))
    y = y1 + 20
    p2, y2 = form_grid(x0 + 20, y + 54, cw - 40, [[("f", "Current password", "••••••••")],
                                                  [("f", "New password", ""), ("f", "Confirm new password", "")]])
    body.append(card(x0, y, cw, y2 - y, "Password card", [text(x0 + 20, y + 34, "Change password", 16, 600), *p2]))
    body.append(button(x0, y2 + 20, "Save changes")[0])
    return svg("42-Member-Profile", "".join(body))


def screen_audit():
    body = [sidebar("Audit Log"), navbar("Hi, Admin"), page_title("Audit Log", "Administration / Audit Log"),
            header_buttons([("Export CSV", "outline", "download")])]
    cy = 156
    lw = round(CW * 0.64)
    data = [("10:42:18", "Chan Sophea", "updated", "book_copies", "#134", "10.0.4.21"),
            ("10:42:18", "Chan Sophea", "created", "borrow_transactions", "#1902", "10.0.4.21"),
            ("10:15:03", "Vichea Long", "created", "books", "#1284", "10.0.4.37"),
            ("09:58:47", "Vichea Long", "updated", "books", "#1102", "10.0.4.37"),
            ("09:31:12", "System Admin", "updated", "member_plans", "#1", "10.0.1.2"),
            ("09:02:40", "Chan Sophea", "deleted", "reviews", "#88", "10.0.4.21"),
            ("08:47:05", "Chan Sophea", "created", "members", "#412", "10.0.4.21"),
            ("08:02:11", "System Admin", "updated", "users", "#7", "10.0.1.2"),
            ("00:00:04", "Scheduler", "updated", "borrow_transactions", "#1874", "127.0.0.1"),
            ("00:00:02", "Scheduler", "updated", "members", "#133", "127.0.0.1")]
    rows = [[("person", "23 Nov 2026", t), u, ("b", a), ("muted", tb_), r, ("muted", ip)] for t, u, a, tb_, r, ip in data]
    tb, end = table(CX + 20, cy + 64, lw - 40, [("Time", 126), ("User", 124), ("Action", 96), ("Table", 166), ("Record", 72), ("IP", 90)],
                    rows, row_h=50, id_="Audit DataTable", highlight=0)
    body.append(card(CX, cy, lw, end - cy + 64, "Audit log card", [
        toolbar(CX, cy, lw, "Search user or record…", [("Table", "All tables"), ("Action", "All actions")], show=False),
        tb, pagination(CX, end, lw, 10, "18,442", "1,845")]))
    rx, rw = CX + lw + 16, CW - lw - 16
    det = [text(rx + 20, cy + 36, "Change detail", 16, 600),
           badge(rx + rw - 20 - round(tw("updated", 12, 600) + 18), cy + 20, "updated")[0],
           kv_rows(rx + 20, cy + 52, rw - 40, [("Record", "book_copies #134"), ("User", "Chan Sophea · librarian"),
                                                ("IP address", "10.0.4.21"), ("Time", "23 Nov 2026, 10:42:18")], 32),
           text(rx + 20, cy + 216, "Changed fields", 13, 600)]
    dt, dend = table(rx + 20, cy + 226, rw - 40, [("Field", 104), ("Old", 120), ("New", rw - 40 - 224)], [
        ["status", ("red", "available"), ("green", "borrowed")],
        ["updated_at", ("muted", "20 Nov 09:12"), "23 Nov 10:42"]], row_h=40, id_="Diff table")
    det += [dt, text(rx + 20, dend + 34, "Raw JSON", 13, 600)]
    code = ['{', '  "old": {', '    "status": "available",', '    "updated_at": "2026-11-20 09:12"', '  },',
            '  "new": {', '    "status": "borrowed",', '    "updated_at": "2026-11-23 10:42"', '  }', '}']
    by = dend + 46
    cl = [rect(rx + 20, by, rw - 40, len(code) * 19 + 24, C["sidebar"], rx=6)]
    for i, ln in enumerate(code):
        ind = len(ln) - len(ln.lstrip(" "))
        col = "#7CC4FA" if '":' in ln else "#E4E6EF"
        cl.append(text(round(rx + 34 + ind * 7.2, 1), by + 26 + i * 19, ln.strip(), 12, 400, col, font=MONO))
    det.append(g("Code block", *cl))
    body.append(card(rx, cy, rw, by + len(code) * 19 + 44 - cy, "Change detail", det))
    return svg("28-Audit-Log", "".join(body))


def portal_nav(active):
    parts = [rect(0, 0, W, 64, C["card"]), line(0, 64, W, 64), logo(32, 14, dark=False)]
    tx = 260
    for lab in ["Home", "Catalog", "My loans", "Reservations", "Profile"]:
        on = lab == active
        parts.append(text(tx, 38, lab, 14, 600 if on else 500, C["primary"] if on else C["muted"]))
        if on:
            parts.append(rect(tx, 61, round(tw(lab, 14, 600)), 3, C["primary"]))
        tx += round(tw(lab, 14, 600) + 30)
    parts += [icon("bell", W - 178, 21, 22, C["muted"]), circle(W - 160, 22, 7, C["danger"]),
              text(W - 160, 26, "2", 10, 700, C["white"], anchor="middle"),
              circle(W - 122, 32, 18, C["secondary"]), text(W - 122, 37, "D", 14, 700, C["white"], anchor="middle"),
              text(W - 96, 37, "Sok Dara", 14, 600)]
    return g("Portal Navbar", *parts)


def portal_body():
    body = [portal_nav("Home")]
    x0, cw = 120, W - 240
    body.append(g("Welcome", text(x0, 118, "Welcome back, Dara", 24, 700),
                  text(x0, 142, "Member M-0021 · Student plan · membership expires 31 Aug 2027", 13, 400, C["muted"])))
    sw_ = btn_w("Search catalog", "outline", "search")
    body.append(button(x0 + cw - sw_, 100, "Search catalog", "outline", icon_name="search")[0])
    kw = (cw - 3 * 16) / 4
    tiles = [("On loan", "2", "success", "book"), ("Overdue", "1", "danger", "clock"),
             ("Fines owed", "$6.50", "warning", "dollar"), ("Reservations", "1", "secondary", "bookmark")]
    body.append(g("KPI Row", *[kpi(round(x0 + i * (kw + 16)), 170, round(kw), *t,
                                   value_color=C["danger"] if t[0] == "Overdue" else None) for i, t in enumerate(tiles)]))
    cy = 278
    lw = round(cw * 0.64)
    cols = [("Book", 250), ("Borrowed", 105), ("Due", 105), ("Status", 110), ("", 158)]
    tb, end = table(x0 + 20, cy + 50, lw - 40, cols, [
        [("strong", "Clean Code"), "27 Oct 2026", ("red", "10 Nov 2026"), ("b", "overdue"), ("muted", "Return at desk")],
        [("strong", "Database System Concepts"), "23 Nov 2026", "07 Dec 2026", ("b", "borrowed"), ("btn", [("Renew", "outline")])],
        [("strong", "Principles of Microeconomics"), "02 Oct 2026", "16 Oct 2026", ("b", "returned"), ("btn", [("Write review", "outline")])],
        [("strong", "A History of Cambodia"), "15 Sep 2026", "29 Sep 2026", ("b", "returned"), ("btn", [("Write review", "outline")])],
    ], row_h=52, id_="My loans table")
    body.append(card(x0, cy, lw, end - cy + 20, "My loans", [text(x0 + 20, cy + 32, "My loans", 16, 600), tb]))
    nx = x0 + lw + 16
    nw = cw - lw - 16
    notes = [("danger", "Overdue: Clean Code", "13 days late · fine $6.50 so far", True),
             ("success", "Reservation ready", "Introduction to Algorithms — pick up by 26 Nov", True),
             ("warning", "Due soon", "Database System Concepts — due 07 Dec", False)]
    items = [text(nx + 20, cy + 32, "Notifications", 16, 600), text(nx + nw - 20, cy + 32, "Mark all read", 12, 600, C["primary"], anchor="end")]
    for i, (tone, t1, t2, unread) in enumerate(notes):
        yy = cy + 56 + i * 72
        items.append(g(f"Notification {i + 1}",
                       rect(nx + 12, yy, nw - 24, 64, C["subtle"] if unread else C["card"], rx=6),
                       circle(nx + 32, yy + 32, 5, C[tone]),
                       text(nx + 48, yy + 27, t1, 13, 600 if unread else 500, C["text"] if unread else C["muted"]),
                       text(nx + 48, yy + 47, t2, 12, 400, C["muted"])))
    body.append(card(nx, cy, nw, end - cy + 20, "Notifications", items))
    return "".join(body)


def screen_portal():
    return svg("05-Member-Portal", portal_body())


def screen_catalog():
    body = [portal_nav("Catalog")]
    x0, cw = 120, W - 240
    body.append(g("Header", text(x0, 118, "Catalog", 24, 700),
                  text(x0, 142, "1,284 titles · reserve a title and collect it at the desk", 13, 400, C["muted"])))
    body.append(g("Search", rect(x0, 164, cw - 236, 44, C["card"], rx=22, stroke=C["border"], sw=1),
                  icon("search", x0 + 18, 176, 20, C["muted"]),
                  text(x0 + 50, 192, "Search by title, author or ISBN", 15, 400, C["muted"]),
                  rect(x0 + cw - 220, 164, 220, 44, C["card"], rx=22, stroke=C["border"], sw=1),
                  text(x0 + cw - 200, 192, "Sort: Most borrowed", 14), icon("chevron", x0 + cw - 44, 178, 16, C["muted"])))
    chips, cx = [], x0
    for i, lab in enumerate(["All", "Computing", "History", "Economics", "Literature", "Science", "Law"]):
        on = i == 0
        w = round(tw(lab, 13, 600) + 32)
        chips += [rect(cx, 224, w, 32, C["primary"] if on else C["card"], rx=16, stroke=None if on else C["border"], sw=None if on else 1),
                  text(cx + w / 2, 245, lab, 13, 600, C["white"] if on else C["text"], anchor="middle")]
        cx += w + 8
    body.append(g("Category chips", *chips))
    books = [("Clean Code", "Robert C. Martin", 4, "12", "4 available", "A-12"),
             ("Introduction to Algorithms", "Thomas H. Cormen", 5, "31", "0 available", "A-14"),
             ("Database System Concepts", "A. Silberschatz", 4, "9", "3 available", "A-15"),
             ("A History of Cambodia", "David Chandler", 4, "7", "2 available", "H-03"),
             ("Principles of Microeconomics", "N. Gregory Mankiw", 3, "15", "1 available", "E-07"),
             ("Sapiens", "Yuval Noah Harari", 5, "22", "0 available", "H-11"),
             ("Head First Design Patterns", "Freeman & Robson", 4, "6", "2 available", "A-18"),
             ("Refactoring", "Martin Fowler", 5, "11", "1 available", "A-21")]
    gw = (cw - 48) / 4
    for i, (t, a, n, cnt, av, shelf) in enumerate(books):
        x = round(x0 + (i % 4) * (gw + 16))
        y = 276 + (i // 4) * 280
        w = round(gw)
        el = [rect(x + 12, y + 12, w - 24, 132, C["subtle"], rx=6), cover(round(x + w / 2 - 42), y + 24, 84, 108, i),
              text(x + 16, y + 172, t, 15, 600), text(x + 16, y + 192, a, 13, 400, C["muted"]),
              stars(x + 16, y + 210, n), text(x + 92, y + 215, f"({cnt})", 12, 400, C["muted"]),
              badge(x + 16, y + 228, av)[0]]
        if av.startswith("0 "):
            bw = btn_w("Reserve", h=28, size=12)
            el.append(small_btn(x + w - 16 - bw, y + 225, "Reserve")[0])
        else:
            el.append(text(x + w - 16, y + 244, f"Shelf {shelf}", 12, 600, C["muted"], anchor="end"))
        body.append(card(x, y, w, 264, f"Book card/{t}", el))
    return svg("29-Member-Catalog", "".join(body))


def screen_review_modal():
    mw, mh = 520, 420
    x, y = (W - mw) / 2, 200
    parts, _ = form_grid(x + 24, y + 176, mw - 48, [
        [("t", "Your review", "Clear examples and short chapters. The\nchapter on elasticity helped me in class.",
          "filled", "Reviews are public and appear on the book page.")]])
    m = [rect(x, y, mw, mh, C["card"], rx=12),
         modal_head(x, y, mw, "Write a review", "Principles of Microeconomics · N. Gregory Mankiw"),
         text(x + 24, y + 116, "Your rating", 13, 600),
         g("Rating input", *[star(x + 38 + i * 36, y + 146, 14, C["warning"] if i < 4 else C["border"]) for i in range(5)]),
         text(x + 222, y + 152, "Very good", 13, 600, C["warning_t"]),
         *parts, line(x, y + 350, x + mw, y + 350)]
    sb = btn_w("Submit review")
    ob = btn_w("Cancel", "outline")
    m += [button(x + mw - 24 - sb, y + 368, "Submit review")[0],
          button(x + mw - 24 - sb - 12 - ob, y + 368, "Cancel", "outline")[0]]
    return svg("30-Review-Modal", portal_body() + overlay() + g("Modal/Write a review", *m))


def screen_404():
    body = [sidebar(""), navbar("Hi, Librarian")]
    mx = CX + CW / 2
    b1 = btn_w("Back to dashboard")
    b2 = btn_w("Go back", "outline", "arrowleft")
    bx = mx - (b1 + b2 + 12) / 2
    body.append(g("Empty state",
                  circle(mx, 300, 110, C["primary"], 0.06), circle(mx, 300, 70, C["primary"], 0.1),
                  icon("book", mx - 30, 270, 60, C["primary"]),
                  text(mx, 480, "404", 64, 800, C["primary"], anchor="middle"),
                  text(mx, 524, "Page not found", 22, 700, anchor="middle"),
                  text(mx, 552, "The page you're looking for doesn't exist or was moved.", 14, 400, C["muted"], anchor="middle"),
                  button(round(bx), 584, "Go back", "outline", icon_name="arrowleft")[0],
                  button(round(bx + b2 + 12), 584, "Back to dashboard")[0]))
    return svg("32-Not-Found", "".join(body))


def style_guide():
    w, h = 1440, 1000
    parts = [text(64, 80, "BIU LMS — Style Guide", 28, 700),
             text(64, 106, "Tokens from public/assets/css/kaiadmin.css · Font: Public Sans", 14, 400, C["muted"])]
    sw = [("brand/primary", "primary"), ("brand/secondary", "secondary"), ("status/success", "success"),
          ("status/danger", "danger"), ("status/warning", "warning"), ("status/info", "info"),
          ("surface/sidebar", "sidebar"), ("surface/card", "card"), ("surface/page", "page"),
          ("surface/subtle", "subtle"), ("text/primary", "text"), ("text/muted", "muted"),
          ("text/on-dark", "ondark"), ("border/default", "border")]
    parts.append(text(64, 156, "Color — light", 16, 600))
    cells = []
    for i, (n, k) in enumerate(sw):
        x, y = 64 + (i % 7) * 186, 172 + (i // 7) * 112
        cells.append(g(f"Swatch/{n}", rect(x, y, 170, 56, C[k], rx=8, stroke=C["border"], sw=1),
                       text(x, y + 76, n, 12, 600), text(x, y + 94, C[k], 12, 400, C["muted"])))
    parts.append(g("Light swatches", *cells))
    parts.append(text(64, 420, "Color — dark theme overrides", 16, 600))
    dark = [("surface/card", "card"), ("surface/page", "page"), ("surface/subtle", "subtle"),
            ("text/primary", "text"), ("text/muted", "muted"), ("border/default", "border")]
    parts.append(g("Dark swatches", *[g(f"Swatch/dark/{n}", rect(64 + i * 186, 436, 170, 56, DARK[k], rx=8, stroke=C["border"], sw=1),
                                         text(64 + i * 186, 512, n, 12, 600), text(64 + i * 186, 530, DARK[k], 12, 400, C["muted"]))
                                       for i, (n, k) in enumerate(dark)]))
    parts.append(text(64, 584, "Type", 16, 600))
    ramp = [("H1 · 28 Bold", 28, 700), ("H2 · 20 SemiBold", 20, 600), ("H3 · 16 SemiBold", 16, 600),
            ("Body · 14 Regular", 14, 400), ("Caption · 12 Medium", 12, 500)]
    yy = 620
    for lab, s, wgt in ramp:
        parts.append(text(64, yy, lab, s, wgt))
        yy += s + 18
    ctl = [text(64, 826, "Controls", 16, 600),
           tab_bar(64, 838, 560, [("All", "1,902"), ("Active", "426"), ("Overdue", "37")]),
           checkbox(64, 906, "Checked", True), checkbox(184, 906, "Unchecked", False),
           circle(333, 917, 8, C["card"], stroke=C["primary"], sw=1.5), circle(333, 917, 4, C["primary"]),
           text(348, 922, "Radio", 14),
           stars(430, 917, 4), text(508, 922, "Rating", 13, 400, C["muted"]),
           g("Chip", rect(64, 940, 136, 24, C["primary"], rx=12, opacity=0.12),
             text(74, mid(940, 24, 12), "Robert C. Martin", 12, 600, C["primary"]), icon("x", 180, 946, 12, C["primary"])),
           ]
    sx = 216
    for lab, var in [("Small", "primary"), ("Small outline", "outline"), ("Remove", "outline_danger")]:
        el, bw = small_btn(sx, 938, lab, var)
        ctl.append(el)
        sx += bw + 10
    parts.append(g("Controls", *ctl))
    parts.append(text(760, 584, "Icons", 16, 600))
    parts.append(g("Icons", *[icon(n, 760 + (i % 20) * 31, 600 + (i // 20) * 32, 20, C["text"]) for i, n in enumerate(ICONS)]))
    parts.append(text(760, 694, "Components", 16, 600))
    bx = 760
    comps = []
    for lab, var in [("Primary", "primary"), ("Secondary", "secondary"), ("Danger", "danger"), ("Outline", "outline"),
                     ("Outline danger", "outline_danger")]:
        el, bw = button(bx, 712, lab, var)
        comps.append(el)
        bx += bw + 12
    bx = 760
    for st in ["available", "returned", "borrowed", "pending", "overdue", "lost", "updated", "inactive"]:
        el, bw = badge(bx, 766, st)
        comps.append(el)
        bx += bw + 8
    comps.append(field(760, 806, 300, "Label", "Placeholder", "placeholder"))
    comps.append(field(1080, 806, 300, "Focused", "Typing…", "focus"))
    comps.append(kpi(760, 886, 260, "Total titles", "1,284", "primary", "book"))
    comps.append(kpi(1036, 886, 260, "Overdue", "37", "danger", "clock", C["danger"]))
    parts.append(g("Components", *comps))
    return svg("00-Style-Guide", "".join(parts), w, h, bg=C["card"])


SCREENS = [style_guide, screen_login, screen_dashboard, screen_books, None, screen_portal,
           screen_forgot, screen_book_form, screen_book_detail, screen_copies, screen_copy_form,
           screen_authors, screen_author_form, screen_publishers, screen_publisher_form,
           screen_categories, screen_category_form, screen_members, screen_member_form, screen_member_profile,
           screen_plans, screen_plan_form, screen_loans, screen_return_modal, screen_reservations,
           screen_reports, screen_users, screen_user_form, screen_audit, screen_catalog, screen_review_modal,
           screen_dashboard_dark, screen_404, screen_register, screen_roles, screen_role_form, screen_review_registration, screen_portal_book, screen_portal_loans,
           screen_portal_reservations, screen_portal_profile]


def screen_borrow():
    body = [sidebar("Borrow Books"), navbar("Hi, Librarian"), page_title("New Borrow", "Circulation / Borrow Books / New")]
    cy = 156
    lw = round(CW * 0.62)
    lx = CX
    cols = [("Barcode", 110), ("Title", 230), ("Author", 140), ("Shelf", 70), ("", 102)]
    tb, end = table(lx + 20, cy + 210, lw - 40, cols, [
        ["BC-000134", ("strong", "Clean Code"), "R. C. Martin", "A-12", ("btn", [("Remove", "danger")])],
        ["BC-000452", ("strong", "Database System Concepts"), "Silberschatz", "A-15", ("btn", [("Remove", "danger")])],
    ], id_="Cart")
    body.append(card(lx, cy, lw, end - cy + 24, "Step 1 · Scan copies", [
        text(lx + 20, cy + 34, "1. Scan copies", 16, 600),
        text(lx + lw - 20, cy + 34, "2 in cart", 13, 600, C["primary"], anchor="end"),
        field(lx + 20, cy + 54, lw - 40, "Barcode", "BC-000452", "focus"),
        text(lx + 20, cy + 134, "Scanner acts as a keyboard — press Enter to add the copy.", 12, 400, C["muted"]),
        alert(lx + 20, cy + 150, lw - 40, "BC-000318 is currently borrowed. Offer the member a reservation instead?", "danger"),
        tb]))
    rx = CX + lw + 16
    rw = CW - lw - 16
    half = (rw - 52) / 2
    body.append(card(rx, cy, rw, 516, "Step 2 · Member & dates", [
        text(rx + 20, cy + 34, "2. Member & dates", 16, 600),
        field(rx + 20, cy + 54, rw - 40, "Member", "Sok Dara · M-0021"),
        icon("chevron", rx + rw - 46, cy + 86, 16, C["muted"]),
        g("Eligibility", rect(rx + 20, cy + 126, rw - 40, 64, C["success"], rx=4, opacity=0.12),
          text(rx + 34, cy + 152, "Active · Student plan", 13, 600, C["success_t"]),
          text(rx + 34, cy + 174, "14 days · $0.50/day fine · 1 of 5 loans used", 12, 400, C["success_t"])),
        field(rx + 20, cy + 206, round(half), "Borrow date", "23 Nov 2026"),
        field(round(rx + 32 + half), cy + 206, round(half), "Due date", "07 Dec 2026"),
        text(rx + 20, cy + 290, "Due date = borrow date + plan loan days (BR-02).", 12, 400, C["muted"]),
        text(rx + 20, cy + 308, "Staff can edit it before confirming.", 12, 400, C["muted"]),
        line(rx + 20, cy + 330, rx + rw - 20, cy + 330),
        text(rx + 20, cy + 360, "Summary", 13, 600),
        text(rx + rw - 20, cy + 360, "2 copies · due 07 Dec", 13, 500, C["muted"], anchor="end"),
        button(rx + 20, cy + 400, "Confirm borrow (2 copies)", w=rw - 40, h=42)[0],
        button(rx + 20, cy + 452, "Cancel", "outline", w=rw - 40, h=42)[0]]))
    return svg("04-Borrow-Desk", "".join(body))


SCREENS[4] = screen_borrow
if __name__ == "__main__":
    for fn in SCREENS:
        p = fn()
        print(os.path.basename(p), os.path.getsize(p))
