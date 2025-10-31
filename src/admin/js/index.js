/*
 * Summernote Admin Toolkit (OOP refactor)
 * - Fixes selection loss on toolbar interactions
 * - Makes font-size / line-height / letter-spacing work with full selection
 * - Adds safe, block-scoped styling + deep formatting utilities
 * - Video upload helper
 *
 * Usage:
 *   SummernoteAdmin.boot();
 */

class SummernoteAdmin {
    static __instance = null;

    static getInstance() {
        if (!this.__instance) this.__instance = new SummernoteAdmin();
        return this.__instance;
    }

    static boot() {
        document.addEventListener("DOMContentLoaded", () => {
            const app = new SummernoteAdmin();
            app.init();
        });
    }

    constructor() {
        this.__SN_GLOBAL_BOUND__ = false;
        this.BAD_CLASS_REGEX = [/^p\d+$/i, /^Mso/i, /^Apple-/i, /^ql-/i];
        this.BLOCK_TAG_RE = /^(P|DIV|H[1-6]|BLOCKQUOTE|LI|TD|TH)$/;
        this.TAGS = "p,div,h1,h2,h3,h4,h5,h6,blockquote,li,td,th";
        this.DEFAULT_FONT_FAMILY =
            'Inter, system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans KR", sans-serif';
        this.DEFAULT_FONT_SIZE_PX = 18;
        this.DEFAULT_LINE_HEIGHT = "1.3";
        this.DEFAULT_ALIGN = "justify";
    }

    init() {
        this.initMobile();
        this.initTabMenu();
        this.bindGlobalRangeGuards();
        this.ensureLetterSpacingStyles();
        this.initSummernoteAll();
    }

    /* ----------------------- UI Basics ----------------------- */
    initTabMenu() {
        const tabMenu = document.querySelectorAll("[data-tab-menu]");
        [...tabMenu].forEach((menu) => {
            const content = document.getElementById(menu.dataset.tabMenu);
            if (!content) return;
            const items = [...menu.children];
            const sections = [...content.children];
            if (!sections || !items) return;
            items.forEach((item, index) => {
                item.addEventListener("click", (e) => {
                    e.preventDefault();
                    items.forEach((el, i) => {
                        el.classList.remove("active");
                        sections[i].classList.remove("active");
                    });
                    item.classList.add("active");
                    sections[index].classList.add("active");
                });
            });
            items[0]?.click();
        });
    }

    initMobile() {
        const menuOpenBtn = document.getElementById("menu-open-btn");
        const menuCloseBtn = document.getElementById("menu-close-btn");
        const drawer = document.querySelector(".no-admin-drawer");
        const backdrop = document.getElementById("backdrop");
        const triggers = [menuOpenBtn, menuCloseBtn].filter(Boolean);
        const clickHandler = (e) => {
            e.preventDefault();
            drawer?.classList.toggle("active");
            backdrop?.classList.toggle("active");
        };
        backdrop?.addEventListener("click", clickHandler);
        triggers.forEach((btn) => btn.addEventListener("click", clickHandler));
    }

    /* -------------------- Summernote Core -------------------- */
    bindGlobalRangeGuards() {
        if (this.__SN_GLOBAL_BOUND__) return;
        this.__SN_GLOBAL_BOUND__ = true;
        $(document)
            .on(
                "mousedown.keepRange.summer",
                ".note-toolbar, .note-popover, .note-air-popover, .note-editor .dropdown-menu, .note-editor .note-btn, [data-event]",
                (e) => {
                    const ctx = this.getCtxFromTarget(e.target);
                    if (ctx) ctx.invoke("editor.saveRange");
                }
            )
            .on(
                "show.bs.dropdown.keepRange.summer",
                '.note-editor [data-toggle="dropdown"], .note-editor .dropdown-toggle',
                (e) => {
                    const ctx = this.getCtxFromTarget(e.target);
                    if (ctx) ctx.invoke("editor.saveRange");
                }
            );
    }

    getCtxFromTarget(target) {
        const $editor = $(target).closest(".note-editor");
        if (!$editor.length) return null;
        const $note = $editor.prev(".summernote");
        if (!$note.length) return null;
        return $note.data("summernote") || null;
    }

    ensureLetterSpacingStyles() {
        if (document.getElementById("sn-letterspacing-style")) return;
        const style = document.createElement("style");
        style.id = "sn-letterspacing-style";
        style.textContent = `
      .note-editor .dropdown-menu.note-letterspacing-menu{padding:8px;min-width:240px;max-height:260px;overflow:auto}
      .note-editor .note-letterspacing-menu .ls-item{position:relative;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:8px 10px;border-radius:8px;text-decoration:none;color:inherit}
      .note-editor .note-letterspacing-menu .ls-item:hover{background:rgba(0,0,0,.06)}
      .note-editor .note-letterspacing-menu .ls-item.active{background:rgba(0,0,0,.09)}
      .note-editor .note-letterspacing-menu .ls-item.active::before{content:"✓";position:absolute;left:8px;font-size:12px;opacity:.8}
      .note-editor .note-letterspacing-menu .ls-preview{flex:1;font-size:14px;line-height:1;white-space:nowrap;padding:4px 8px;border-radius:6px;background:rgba(0,0,0,.04);margin-left:12px}
      .note-editor .note-letterspacing-menu .ls-value{font-variant-numeric:tabular-nums;min-width:48px;text-align:right;font-size:12px;opacity:.85}
      .note-editor .note-btn-letters .note-icon{margin-right:6px}
    `;
        document.head.appendChild(style);
    }

    // ✅ NEW: 항상 유효한 Range를 보장 (전체선택/유실 대응)
    ensureValidRange(ctx) {
        if (!ctx) return;
        const editable = ctx?.layoutInfo?.editable?.[0];
        if (!editable || !document.contains(editable)) return;

        // 1) 포커스 & 복구 시도
        ctx.invoke("editor.focus");
        ctx.invoke("editor.restoreRange");
        let rng = ctx.invoke("editor.getLastRange");

        const isBad =
            !rng ||
            !(rng.sc instanceof Node) ||
            !(rng.ec instanceof Node) ||
            !editable.contains(rng.sc) ||
            !editable.contains(rng.ec);

        if (!isBad) return;

        // 2) 에디터 전체 선택 → 저장
        ctx.invoke("editor.selectAll");
        ctx.invoke("editor.saveRange");
        ctx.invoke("editor.restoreRange");

        // 3) 그래도 실패하면 에디터 끝으로 커서 강제
        const sel = window.getSelection();
        if (!sel) return;
        const r = document.createRange();
        try {
            if (editable.lastChild) {
                // 텍스트/요소 모두 대응: 내용 기준
                r.selectNodeContents(editable);
                r.collapse(false);
            } else {
                // 비어있으면 빈 단락 하나
                const p = document.createElement("p");
                p.innerHTML = "<br>";
                editable.appendChild(p);
                r.selectNodeContents(p);
                r.collapse(true);
            }
            sel.removeAllRanges();
            sel.addRange(r);
            ctx.invoke("editor.saveRange");
        } catch (_) {
            // no-op
        }
    }

    initSummernoteAll() {
        document
            .querySelectorAll(".summernote")
            .forEach((el) => this.initSummernote(el));
    }

    initSummernote(element) {
        const self = this;

        $(element).summernote({
            lang: "ko-KR",
            height: 350,
            toolbar: [
                ["style", ["style"]],
                ["font", ["bold", "italic", "underline", "clear", "fontsize"]],
                ["fontname", ["fontname"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video", "videoUpload"]],
                ["height", ["height", "letterSpacing"]],
                ["view", ["fullscreen", "codeview", "help"]],
                ["preset", ["defaultReset"]],
            ],
            fontSizes: [
                "8",
                "9",
                "10",
                "11",
                "12",
                "14",
                "16",
                "18",
                "20",
                "24",
                "28",
                "32",
                "36",
                "40",
                "48",
            ],
            lineHeights: ["1", "1.2", "1.3", "1.5", "1.75", "2", "2.5", "3"],
            buttons: {
                letterSpacing: (ctx) => self.btnLetterSpacing(ctx),
                videoUpload: (ctx) => self.btnVideoUpload(ctx),
                defaultReset: (ctx) => self.btnDefaultReset(ctx),
            },
            callbacks: {
                onInit() {
                    $(element).summernote("focus");
                    $(element).summernote("saveRange");

                    const $editor = $(element).next(".note-editor");
                    self.installHardEventBlockers($editor);
                    self.patchEditorCore($(element).data("summernote"));

                    const $editable = $editor.find(".note-editable");

                    $editable.css({
                        fontFamily:
                            '"Inter", system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans KR", sans-serif',
                        fontSize: "18px",
                        lineHeight: "1.3",
                        textAlign: "justify",
                    });

                    // Save range at press time (mousedown)
                    $editor
                        .off("mousedown.keepRange")
                        .on(
                            "mousedown.keepRange",
                            ".note-toolbar, .dropdown-menu, .note-btn, [data-event]",
                            () => {
                                const ctx = $(element).data("summernote");
                                if (ctx) ctx.invoke("editor.saveRange");
                            }
                        );

                    // Save after Ctrl/Cmd + A
                    $editable
                        .off("keydown.keepRange")
                        .on("keydown.keepRange", (e) => {
                            const isCtrlA =
                                (e.ctrlKey || e.metaKey) &&
                                (e.key === "a" || e.key === "A");
                            if (isCtrlA)
                                setTimeout(
                                    () => $(element).summernote("saveRange"),
                                    0
                                );
                        });

                    // 폰트 사이즈
                    self.hijackToolbar(
                        $editor,
                        '[data-event="fontSize"]',
                        (e) => {
                            const val = (
                                e.currentTarget.getAttribute("data-value") || ""
                            ).trim();
                            if (!val) return;
                            const ctx = $(element).data("summernote");
                            self.ensureValidRange(ctx);
                            ctx.invoke("editor.beforeCommand");
                            self.applyBlockStyles(ctx, {
                                fontSize: `${val}px`,
                            });
                            ctx.invoke("editor.afterCommand");
                            ctx.invoke("editor.saveRange");
                        }
                    );

                    // 폰트명
                    self.hijackToolbar(
                        $editor,
                        '[data-event="fontName"]',
                        (e) => {
                            const val = (
                                e.currentTarget.getAttribute("data-value") || ""
                            ).trim();
                            if (!val) return;
                            const ctx = $(element).data("summernote");
                            self.ensureValidRange(ctx);
                            ctx.invoke("editor.beforeCommand");
                            self.applyBlockStyles(ctx, { fontFamily: val });
                            ctx.invoke("editor.afterCommand");
                            ctx.invoke("editor.saveRange");
                        }
                    );

                    // 라인하이트
                    self.hijackToolbar(
                        $editor,
                        '[data-event="lineHeight"]',
                        (e) => {
                            const val = (
                                e.currentTarget.getAttribute("data-value") || ""
                            ).trim();
                            if (!val) return;
                            const ctx = $(element).data("summernote");
                            self.ensureValidRange(ctx);
                            ctx.invoke("editor.beforeCommand");
                            self.applyBlockStyles(ctx, { lineHeight: val });
                            ctx.invoke("editor.afterCommand");
                            ctx.invoke("editor.saveRange");
                        }
                    );

                    // 정렬 (좌/중/우/양쪽)
                    const ALIGN_MAP = {
                        justifyLeft: "left",
                        justifyCenter: "center",
                        justifyRight: "right",
                        justifyFull: "justify",
                    };
                    self.hijackToolbar(
                        $editor,
                        '[data-event^="justify"]',
                        (e) => {
                            const evt =
                                e.currentTarget.getAttribute("data-event");
                            const align = ALIGN_MAP[evt];
                            if (!align) return;
                            const ctx = $(element).data("summernote");
                            self.ensureValidRange(ctx);
                            ctx.invoke("editor.beforeCommand");
                            self.applyBlockAlign(ctx, align);
                            ctx.invoke("editor.afterCommand");
                            ctx.invoke("editor.saveRange");
                        }
                    );

                    /* Heading transform (safe) */
                    $editor
                        .find('.dropdown-menu [data-event="formatBlock"]')
                        .off("click.safeHeading")
                        .on("click.safeHeading", (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const value = (
                                e.currentTarget.getAttribute("data-value") || ""
                            )
                                .toUpperCase()
                                .trim();
                            if (!value) return;
                            self.applyHeadingSafely(
                                $(element).data("summernote"),
                                value
                            );
                        });
                },
                onKeyup() {
                    $(element).summernote("saveRange");
                },
                onMouseUp() {
                    $(element).summernote("saveRange");
                },
                onImageUpload(files) {
                    self.onImageUpload(files, element);
                },
                onDrop(e) {
                    self.onDrop(e, element);
                },
                onPaste(e) {
                    self.onPaste(e, element);
                },
                onVideoUpload(files) {
                    if (!files || !files.length) return;
                    $(element).summernote("saveRange");
                    self.uploadVideoFile(files[0], element).catch((err) =>
                        alert(err.message)
                    );
                },
            },
        });
    }

    // Summernote 기본 Editor.fontSize를 안전 버전으로 교체
    patchEditorCore(context) {
        const editor = context?.modules?.editor;
        if (!editor) return;

        const self = this;

        // 원본을 보존하려면 주석 해제
        // const _origFontSize = editor.fontSize.bind(editor);

        editor.fontSize = function (value) {
            // value는 "18" 같은 문자열이 들어옵니다.
            const px = parseInt(value, 10);
            if (!px || Number.isNaN(px)) return;

            // 여기서부터는 전부 우리 블록 스타일 적용 로직만 탑니다.
            const ctx = context;
            self.ensureValidRange(ctx);

            ctx.invoke("editor.beforeCommand");
            // 선택 영역 전체 블록에 안전 적용 (IMG 등 void 요소 자동 스킵)
            self.applyBlockStyles(ctx, { fontSize: `${px}px` });
            ctx.invoke("editor.afterCommand");
            ctx.invoke("editor.saveRange");
        };

        // 🔥 강제: lineHeight도 블록 단위 일괄 적용으로 오버라이드
        editor.lineHeight = function (value) {
            if (!value) return;
            const ctx = context;
            self.ensureValidRange(ctx);
            ctx.invoke("editor.beforeCommand");
            self.applyBlockStyles(ctx, { lineHeight: String(value) });
            ctx.invoke("editor.afterCommand");
            ctx.invoke("editor.saveRange");
        };
    }

    /* --------------------- Callbacks & Upload --------------------- */
    onImageUpload(files, element) {
        const formData = new FormData();
        formData.append("file", files[0]);
        fetch("/admin/uploads/summernote?type=image", {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success)
                    $(element).summernote("insertImage", data.path);
                else alert(data.message || "이미지 업로드에 실패했습니다.");
            })
            .catch((err) => console.error(err));
    }

    onDrop(e, element) {
        const dt = e.originalEvent?.dataTransfer;
        if (!dt || !dt.files || !dt.files.length) return;
        const videos = [...dt.files].filter((f) => f.type.startsWith("video/"));
        if (videos.length) {
            e.preventDefault();
            e.stopPropagation();
            $(element).summernote("saveRange");
            this.uploadVideoFile(videos[0], element).catch((err) =>
                alert(err.message)
            );
        }
    }

    onPaste(e, element) {
        const cd = e.originalEvent?.clipboardData;
        if (cd && cd.files && cd.files.length) {
            const videos = [...cd.files].filter((f) =>
                f.type.startsWith("video/")
            );
            if (videos.length) {
                e.preventDefault();
                e.stopPropagation();
                $(element).summernote("saveRange");
                this.uploadVideoFile(videos[0], element).catch((err) =>
                    alert(err.message)
                );
                return;
            }
        }
        setTimeout(() => {
            const ctx = $(element).data("summernote");
            if (!ctx) return;
            this.applyDefaultFormattingGlobal(ctx, {
                fontFamily: this.DEFAULT_FONT_FAMILY,
                fontSizePx: this.DEFAULT_FONT_SIZE_PX,
                lineHeight: this.DEFAULT_LINE_HEIGHT,
                textAlign: this.DEFAULT_ALIGN,
            });
        }, 0);
    }

    uploadVideoFile(file, noteEl) {
        const formData = new FormData();
        formData.append("file", file);
        return fetch("/admin/uploads/summernote?type=video", {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.success)
                    throw new Error(data.message || "비디오 업로드 실패");
                $(noteEl).summernote("focus");
                $(noteEl).summernote("restoreRange");
                const video = document.createElement("video");
                video.src = data.path;
                video.controls = true;
                video.preload = "metadata";
                video.playsInline = true;
                video.style.maxWidth = "100%";
                const wrapper = document.createElement("p");
                wrapper.appendChild(video);
                $(noteEl).summernote("insertNode", wrapper);
            });
    }

    /* -------------------- Styling Utilities -------------------- */
    stripProblematicClasses(nodes) {
        nodes.forEach((el) => {
            if (!(el instanceof Element)) return;
            const cls = (el.getAttribute("class") || "")
                .split(/\s+/)
                .filter(Boolean);
            if (!cls.length) return;
            const keep = cls.filter(
                (c) => !this.BAD_CLASS_REGEX.some((re) => re.test(c))
            );
            if (keep.length) el.setAttribute("class", keep.join(" "));
            else el.removeAttribute("class");
        });
    }

    stripInlineFontStyles(nodes) {
        const PROPS = [
            "font-size",
            "font-family",
            "line-height",
            "letter-spacing",
            "text-align",
        ];
        nodes.forEach((el) => {
            if (!(el instanceof Element)) return;
            const style = el.getAttribute("style");
            if (!style) return;
            let s = style;
            PROPS.forEach((p) => {
                const re = new RegExp(`(?:^|;)\\s*${p}\\s*:[^;"]*;?`, "gi");
                s = s.replace(re, ";");
            });
            s = s.replace(/;{2,}/g, ";").replace(/^\s*;\s*|\s*;\s*$/g, "");
            if (s) el.setAttribute("style", s);
            else el.removeAttribute("style");
        });
        // <font> unwrap
        nodes.forEach((el) => {
            if (el.tagName && el.tagName.toLowerCase() === "font") {
                const parent = el.parentNode;
                while (el.firstChild) parent.insertBefore(el.firstChild, el);
                parent.removeChild(el);
            }
        });
    }

    collectNodesInRange(context) {
        const editable = context?.layoutInfo?.editable?.[0];
        if (!editable) return [];
        const rng = context.invoke("editor.getLastRange");
        if (!rng) return [];

        // W3C Range 확보
        const sel = window.getSelection();
        if (!sel || !sel.rangeCount) return [];
        const nr = sel.getRangeAt(0).cloneRange();

        const out = new Set();
        const VOID = new Set([
            "IMG",
            "VIDEO",
            "AUDIO",
            "IFRAME",
            "CANVAS",
            "HR",
            "BR",
        ]);
        const walker = document.createTreeWalker(
            editable,
            NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT,
            null
        );

        for (let cur = walker.currentNode; cur; cur = walker.nextNode()) {
            const el = cur.nodeType === 1 ? cur : cur.parentElement;
            if (!el || !editable.contains(el)) continue;
            if (VOID.has(el.tagName)) continue;

            try {
                const r = document.createRange();
                if (cur.nodeType === 3) r.selectNodeContents(cur);
                else r.selectNode(cur);
                const overlaps =
                    nr.compareBoundaryPoints(Range.END_TO_START, r) < 0 &&
                    nr.compareBoundaryPoints(Range.START_TO_END, r) > 0;
                if (overlaps) out.add(el);
            } catch (_) {
                /* skip */
            }
        }
        return Array.from(out);
    }

    getSelectedBlocks(context) {
        const editable = context?.layoutInfo?.editable?.[0];
        if (!editable) return [];
        const sel = window.getSelection();
        if (!sel || !sel.rangeCount) return [];

        // ✨ 선택이 없으면(커서만) 현재 블록 하나만 대상으로
        const curRange = sel.getRangeAt(0);
        if (curRange.collapsed) {
            const anchor = sel.anchorNode;
            const el = anchor?.nodeType === 1 ? anchor : anchor?.parentElement;
            const b = el?.closest(this.TAGS);
            if (b && editable.contains(b)) return [b];
        }

        // ✅ “블록 그 자체”로 겹침 판정: VOID-only 블록도 100% 포착
        const nr = sel.getRangeAt(0).cloneRange();
        const blocks = Array.from(editable.querySelectorAll(this.TAGS)).filter(
            (b) => {
                try {
                    const r = document.createRange();
                    r.selectNode(b);
                    const overlaps =
                        nr.compareBoundaryPoints(Range.END_TO_START, r) < 0 &&
                        nr.compareBoundaryPoints(Range.START_TO_END, r) > 0;
                    return overlaps;
                } catch {
                    return false;
                }
            }
        );

        const uniq = Array.from(new Set(blocks));
        if (uniq.length) return uniq;

        return [...editable.querySelectorAll(this.TAGS)];
    }

    applyBlockStyles(context, styleObj) {
        context.invoke("editor.focus");
        context.invoke("editor.restoreRange");
        let blocks = this.getSelectedBlocks(context);
        if (!blocks.length) {
            const editable = context?.layoutInfo?.editable?.[0];
            if (!editable) return;
            blocks = [...editable.querySelectorAll(this.TAGS)];
        }

        context.invoke("editor.beforeCommand");
        this.stripProblematicClasses(blocks);

        // 부모에 적용할 속성 키를 CSS 표기법으로 정규화
        const toCssKey = (k) =>
            k.replace(/[A-Z]/g, (m) => "-" + m.toLowerCase());
        const propKeys = Object.keys(styleObj).map(toCssKey);

        // 상속형 폰트 관련 속성들: 자식에서 제거 → 부모에서 한 번만 지정
        const INHERITED_PROPS = new Set([
            "font-size",
            "font-family",
            "line-height",
            "letter-spacing",
            "text-align",
        ]);
        const propsToStripDeep = propKeys.filter((p) => INHERITED_PROPS.has(p));

        if (propsToStripDeep.length) {
            // 루트 블록의 기존 font-size를 보존하고 싶다면 childrenOnly 사용
            this.deepStripInlineProps(blocks, propsToStripDeep, {
                unwrapFont: true,
                childrenOnly: true, // ← 새 옵션
            });
        }

        blocks.forEach((b) => {
            // 부모 블록의 동일 속성 제거 후 새 값만 강제
            propKeys.forEach((p) => b.style.removeProperty(p));
            Object.entries(styleObj).forEach(([k, v]) => {
                b.style.setProperty(toCssKey(k), v, "important");
            });
        });

        context.invoke("editor.afterCommand");
    }

    applyBlockAlign(context, align) {
        context.invoke("editor.focus");
        context.invoke("editor.restoreRange");
        const editable = context?.layoutInfo?.editable?.[0];
        const rng = context.invoke("editor.getLastRange");
        if (!editable || !rng) return;
        const nodes = this.collectNodesInRange(context);
        const targets = [];
        nodes.forEach((n) => {
            const el = n.nodeType === 1 ? n : n.parentElement;
            if (!el || !editable.contains(el)) return;
            const b = el.closest(this.TAGS);
            if (b && editable.contains(b) && this.BLOCK_TAG_RE.test(b.tagName))
                targets.push(b);
        });
        const uniq = Array.from(new Set(targets));
        this.stripProblematicClasses(uniq);
        uniq.forEach((b) => {
            b.removeAttribute("align");
            b.style.removeProperty("text-align");

            // 자식들의 text-align 인라인값 제거 → 부모에만 지정
            this.deepStripInlineProps([b], ["text-align"], {
                unwrapFont: false,
            });

            b.style.setProperty("text-align", align, "important");
        });
        context.invoke("editor.afterCommand");
    }

    applyDefaultFormattingGlobal(
        context,
        {
            fontFamily = this.DEFAULT_FONT_FAMILY,
            fontSizePx = this.DEFAULT_FONT_SIZE_PX,
            lineHeight = this.DEFAULT_LINE_HEIGHT,
            textAlign = this.DEFAULT_ALIGN,
        } = {}
    ) {
        context.invoke("editor.focus");
        const editable = context?.layoutInfo?.editable?.[0];
        if (!editable) return;

        const SKIP = new Set([
            "IMG",
            "VIDEO",
            "AUDIO",
            "IFRAME",
            "CANVAS",
            "CODE",
            "PRE",
            "SVG",
            "MATH",
            "SUP",
            "SUB",
        ]);
        const all = [editable, ...editable.querySelectorAll("*")];
        const uniq = Array.from(new Set(all));

        context.invoke("editor.beforeCommand");
        this.stripProblematicClasses(uniq);
        uniq.forEach((el) => el.removeAttribute && el.removeAttribute("align"));
        this.stripInlineFontStyles(uniq);

        uniq.forEach((el) => {
            if (this.BLOCK_TAG_RE.test(el.tagName))
                el.style.setProperty("text-align", textAlign, "important");
            if (el.tagName === "TD" || el.tagName === "TH")
                el.style.setProperty("text-align", "left", "important");
            if (el.tagName === "SPAN") {
                const style = (el.getAttribute("style") || "").trim();
                const attrs = el.attributes;
                if (
                    (!style &&
                        attrs.length === 1 &&
                        attrs[0].name === "style") ||
                    attrs.length === 0
                ) {
                    while (el.firstChild)
                        el.parentNode.insertBefore(el.firstChild, el);
                    el.remove();
                }
            }
        });

        uniq.forEach((el) => {
            if (SKIP.has(el.tagName)) return;
            el.style.setProperty("font-family", fontFamily, "important");
            el.style.setProperty("font-size", `${fontSizePx}px`, "important");
            el.style.setProperty("line-height", lineHeight, "important");
            if (this.BLOCK_TAG_RE.test(el.tagName))
                el.style.setProperty("text-align", textAlign, "important");
        });

        context.invoke("editor.afterCommand");
    }

    applyDefaultFormatting(context) {
        context.invoke("editor.focus");
        context.invoke("editor.restoreRange");
        const rng = context.invoke("editor.getLastRange");
        const hasSelection = rng && !rng.isCollapsed();
        if (!hasSelection) {
            if (
                !confirm(
                    "선택 영역이 없습니다. 에디터 전체에 기본값(Inter, 18, 1.3, 양쪽정렬)을 적용할까요?"
                )
            )
                return;
            context.invoke("editor.selectAll");
            context.invoke("editor.saveRange");
        }

        const blocks = this.getSelectedBlocks(context);
        const targets = [];
        blocks.forEach((b) => {
            targets.push(b);
            targets.push(...Array.from(b.querySelectorAll("*")));
        });

        context.invoke("editor.beforeCommand");
        this.stripInlineFontStyles(targets);
        this.stripProblematicClasses(blocks);
        context.invoke("editor.afterCommand");

        this.applyBlockStyles(context, {
            fontFamily: this.DEFAULT_FONT_FAMILY,
            fontSize: "18px",
            lineHeight: "1.3",
        });
        this.applyBlockAlign(context, "justify");

        if (!hasSelection) {
            const editable = context?.layoutInfo?.editable?.[0];
            if (editable) {
                const sel = window.getSelection();
                const r = document.createRange();
                r.selectNodeContents(editable);
                r.collapse(false);
                sel.removeAllRanges();
                sel.addRange(r);
            }
        }
    }

    /* ---------------------- Heading / Inline ---------------------- */
    applyHeadingSafely(context, tagNameUpper) {
        context.invoke("editor.focus");
        context.invoke("editor.restoreRange");
        const rng = context.invoke("editor.getLastRange");
        const editable = context?.layoutInfo?.editable?.[0];
        if (!rng || !editable) return;

        let nodes = [];
        if (typeof rng.nodes === "function") nodes = rng.nodes(() => true);
        else {
            const sel = window.getSelection();
            if (!sel.rangeCount) return;
            const nr = sel.getRangeAt(0).cloneRange();
            const walker = document.createTreeWalker(
                editable,
                NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT,
                null
            );
            let cur = walker.currentNode;
            while (cur) {
                const nodeRange = document.createRange();
                try {
                    nodeRange.selectNode(
                        cur.nodeType === 3 ? cur.parentNode : cur
                    );
                } catch {}
                if (nr.compareBoundaryPoints && nodeRange.startContainer) {
                    const overlaps =
                        nr.compareBoundaryPoints(
                            Range.END_TO_START,
                            nodeRange
                        ) < 0 &&
                        nr.compareBoundaryPoints(
                            Range.START_TO_END,
                            nodeRange
                        ) > 0;
                    if (overlaps)
                        nodes.push(cur.nodeType === 3 ? cur.parentNode : cur);
                }
                cur = walker.nextNode();
            }
        }

        const blocks = [];
        nodes.forEach((n) => {
            const el = n.nodeType === 1 ? n : n.parentElement;
            if (!el) return;
            if (!editable.contains(el)) return;
            const b = el.closest("p,div,h1,h2,h3,h4,h5,h6,blockquote,li");
            if (!b || !editable.contains(b)) return;
            if (b.tagName === "LI") return; // keep list structure
            if (this.BLOCK_TAG_RE.test(b.tagName)) blocks.push(b);
        });

        if (!blocks.length) return;
        const uniq = Array.from(new Set(blocks));

        uniq.forEach((oldEl) => {
            if (oldEl.tagName === tagNameUpper) return;
            const newEl = document.createElement(tagNameUpper);
            newEl.className = oldEl.className;
            if (oldEl.getAttribute("style"))
                newEl.setAttribute("style", oldEl.getAttribute("style"));
            for (const attr of Array.from(oldEl.attributes)) {
                const name = attr.name.toLowerCase();
                if (name === "class" || name === "style") continue;
                try {
                    newEl.setAttribute(attr.name, attr.value);
                } catch {}
            }
            while (oldEl.firstChild) newEl.appendChild(oldEl.firstChild);
            oldEl.parentNode.replaceChild(newEl, oldEl);
        });

        context.invoke("editor.afterCommand");
    }

    applyInlineStyleToSelection(context, styleObj) {
        context.invoke("editor.focus");
        context.invoke("editor.restoreRange");
        const rng = context.invoke("editor.getLastRange");
        if (!rng || rng.isCollapsed()) return;
        const nodes = this.collectNodesInRange(context);
        this.stripInlineFontStyles(nodes);
        const span = document.createElement("span");
        Object.entries(styleObj).forEach(([k, v]) => {
            span.style[k] = v;
        });
        if (typeof rng.wrapBodyInlineWith === "function")
            rng.wrapBodyInlineWith(span);
        else {
            try {
                const native = rng.nativeRange
                    ? rng.nativeRange()
                    : window.getSelection().getRangeAt(0);
                native.surroundContents(span);
            } catch {
                const native = rng.nativeRange
                    ? rng.nativeRange()
                    : window.getSelection().getRangeAt(0);
                const frag = native.extractContents();
                span.appendChild(frag);
                native.insertNode(span);
            }
        }
        context.invoke("editor.afterCommand");
    }

    /* ---------------- Letter Spacing (robust) ---------------- */
    applyLetterSpacing(context, pxValue) {
        context.invoke("editor.focus");
        context.invoke("editor.restoreRange");
        const rng = context.invoke("editor.getLastRange");
        if (!rng || rng.isCollapsed()) {
            alert("자간을 적용할 텍스트를 먼저 선택해주세요.");
            return;
        }

        const editable = context?.layoutInfo?.editable?.[0];
        if (!editable) return;

        // 기준 네이티브 Range
        const sel = window.getSelection();
        if (!sel || !sel.rangeCount) return;
        const base = sel.getRangeAt(0).cloneRange();

        const walker = document.createTreeWalker(
            editable,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: (node) => {
                    if (!node.nodeValue || !node.nodeValue.trim())
                        return NodeFilter.FILTER_REJECT;
                    const p = node.parentElement;
                    if (!p) return NodeFilter.FILTER_REJECT;
                    const tag = p.tagName;
                    if (
                        tag === "CODE" ||
                        tag === "PRE" ||
                        tag === "SCRIPT" ||
                        tag === "STYLE"
                    )
                        return NodeFilter.FILTER_REJECT;

                    try {
                        const whole = document.createRange();
                        whole.selectNodeContents(node);
                        const overlaps =
                            base.compareBoundaryPoints(
                                Range.END_TO_START,
                                whole
                            ) < 0 &&
                            base.compareBoundaryPoints(
                                Range.START_TO_END,
                                whole
                            ) > 0;
                        return overlaps
                            ? NodeFilter.FILTER_ACCEPT
                            : NodeFilter.FILTER_REJECT;
                    } catch {
                        return NodeFilter.FILTER_REJECT;
                    }
                },
            },
            false
        );

        const targets = [];
        for (let n = walker.nextNode(); n; n = walker.nextNode())
            targets.push(n);
        if (!targets.length) return;

        context.invoke("editor.beforeCommand");

        for (let i = targets.length - 1; i >= 0; i--) {
            const textNode = targets[i];
            const whole = document.createRange();
            whole.selectNodeContents(textNode);

            // 노드별 교차 구간으로 안전한 부분 Range 계산
            const part = document.createRange();

            // start
            if (base.compareBoundaryPoints(Range.START_TO_START, whole) <= 0) {
                part.setStart(textNode, 0);
            } else if (base.startContainer === textNode) {
                part.setStart(
                    textNode,
                    Math.min(base.startOffset, textNode.length)
                );
            } else {
                // base 시작이 이 노드 내부가 아니면, 노드 전체 시작
                part.setStart(textNode, 0);
            }

            // end
            if (base.compareBoundaryPoints(Range.END_TO_END, whole) >= 0) {
                part.setEnd(textNode, textNode.length);
            } else if (base.endContainer === textNode) {
                part.setEnd(
                    textNode,
                    Math.min(base.endOffset, textNode.length)
                );
            } else {
                part.setEnd(textNode, textNode.length);
            }

            if (part.collapsed) continue;

            const span = document.createElement("span");
            span.style.letterSpacing = pxValue;

            const frag = part.extractContents();
            span.appendChild(frag);
            part.insertNode(span);
        }

        context.invoke("editor.afterCommand");
    }

    /* ---------------- Toolbar Buttons ---------------- */
    btnDefaultReset(context) {
        const ui = $.summernote.ui;
        return ui
            .button({
                contents: '<i class="note-icon-eraser"></i> 기본값',
                tooltip: "선택 또는 전체를 Inter/18/1.3/양쪽정렬로 강제",
                click: () => {
                    context.invoke("editor.focus");
                    this.ensureValidRange(context);
                    context.invoke("editor.beforeCommand");
                    this.applyDefaultFormattingGlobal(context, {
                        fontFamily: this.DEFAULT_FONT_FAMILY,
                        fontSizePx: this.DEFAULT_FONT_SIZE_PX,
                        lineHeight: this.DEFAULT_LINE_HEIGHT,
                        textAlign: this.DEFAULT_ALIGN,
                    });
                    context.invoke("editor.afterCommand");
                    context.invoke("editor.saveRange");
                },
            })
            .render();
    }

    btnVideoUpload(context) {
        const ui = $.summernote.ui;
        const self = this;
        const $file = $(
            '<input type="file" accept="video/*" style="display:none" />'
        );
        $(document.body).append($file);
        $file.on("change", function () {
            const file = this.files && this.files[0];
            if (!file) return;
            context.invoke("editor.focus");
            context.invoke("editor.restoreRange");
            self.uploadVideoFile(file, context.layoutInfo.note[0])
                .catch((err) => alert(err.message || "비디오 업로드 실패"))
                .finally(() => {
                    $file.val("");
                });
        });
        return ui
            .button({
                contents: '<i class="note-icon-video"></i> 업로드',
                tooltip: "비디오 업로드",
                click: function () {
                    context.invoke("editor.saveRange");
                    $file.trigger("click");
                },
            })
            .render();
    }
    hijackToolbar($editor, selector, onClick) {
        $editor.find(selector).each((_, el) => {
            const clone = el.cloneNode(true);
            clone.setAttribute("data-hijacked", "1");

            // 기본 위임 이벤트 키 제거 → Context.createInvokeHandler 차단
            const origEvent = clone.getAttribute("data-event");
            if (origEvent) {
                clone.setAttribute("data-orig-event", origEvent);
                clone.removeAttribute("data-event");
            }

            el.parentNode.replaceChild(clone, el);

            // Range 보존
            clone.addEventListener(
                "mousedown",
                () => {
                    const ctx = $editor.prev(".summernote").data("summernote");
                    if (ctx) ctx.invoke("editor.saveRange");
                },
                { passive: true }
            );

            clone.addEventListener("click", (e) => {
                e.preventDefault();

                // ✅ 모든 위임/기본 핸들러 완전 차단
                if (typeof e.stopImmediatePropagation === "function") {
                    e.stopImmediatePropagation();
                }
                e.stopPropagation();

                onClick(e, clone);
                return false;
            });
        });
    }

    // 선택 블록들의 모든 자손에서 해당 CSS 속성들만 깔끔히 제거 (inherit 유도)
    deepStripInlineProps(
        roots,
        props,
        { unwrapFont = true, childrenOnly = false } = {}
    ) {
        const SKIP = new Set([
            "IMG",
            "VIDEO",
            "AUDIO",
            "IFRAME",
            "CANVAS",
            "HR",
            "BR",
            "CODE",
            "PRE",
            "SVG",
            "MATH",
        ]);
        const list = [];
        roots.forEach((r) => {
            if (!r || !(r instanceof Element)) return;
            if (!childrenOnly) list.push(r);
            list.push(...r.querySelectorAll("*"));
        });

        list.forEach((el) => {
            if (!(el instanceof Element)) return;
            if (SKIP.has(el.tagName)) return;

            // align 속성 제거 (예전 콘텐츠 호환)
            if (props.includes("text-align") && el.hasAttribute("align"))
                el.removeAttribute("align");

            const style = el.getAttribute("style") || "";
            if (!style) return;
            let s = style;
            props.forEach((p) => {
                const re = new RegExp(`(?:^|;)\\s*${p}\\s*:[^;"]*;?`, "gi");
                s = s.replace(re, ";");
            });
            s = s.replace(/;{2,}/g, ";").replace(/^\s*;\s*|\s*;\s*$/g, "");
            if (s) el.setAttribute("style", s);
            else el.removeAttribute("style");
        });

        // <font> 언랩(옵션)
        if (unwrapFont) {
            list.forEach((el) => {
                if (el.tagName && el.tagName.toLowerCase() === "font") {
                    const parent = el.parentNode;
                    while (el.firstChild)
                        parent.insertBefore(el.firstChild, el);
                    parent.removeChild(el);
                }
            });
        }
    }

    installHardEventBlockers($editor) {
        const root = $editor[0];
        if (!root) return;

        const BLOCK_SEL = [
            '[data-event="fontSize"]',
            '[data-event="lineHeight"]',
            '[data-event="fontName"]',
            '[data-event^="justify"]',
            // 필요 시 추가
            // '[data-event="formatBlock"]',
        ].join(",");

        const blocker = (e) => {
            const t = e.target;
            if (
                t &&
                (t.matches('[data-hijacked="1"]') ||
                    t.closest('[data-hijacked="1"]'))
            ) {
                return; // 우리 커스텀 버튼은 통과
            }

            // 드롭다운 내부 아이템이나 버튼 어디를 눌러도 상위 위임 전에 차단
            if (t && (t.matches(BLOCK_SEL) || t.closest(BLOCK_SEL))) {
                e.preventDefault();
                if (typeof e.stopImmediatePropagation === "function") {
                    e.stopImmediatePropagation();
                }
                e.stopPropagation();
            }
        };

        // ✅ 캡처 단계에서 선차단 (true)
        root.addEventListener("mousedown", blocker, false);
        root.addEventListener("click", blocker, false);
    }

    btnLetterSpacing(context) {
        const ui = $.summernote.ui;
        const self = this;
        const values = [
            "0px",
            "1px",
            "2px",
            "3px",
            "4px",
            "6px",
            "8px",
            "10px",
        ];
        const itemsHtml =
            values
                .map(
                    (v) => `
      <a class="dropdown-item ls-item" href="#" data-value="${v}">
        <span class="ls-preview" style="letter-spacing:${v}">가Aa 가Aa</span>
        <span class="ls-value">${v}</span>
      </a>`
                )
                .join("") +
            `
      <div class="dropdown-divider"></div>
      <a class="dropdown-item ls-item" href="#" data-value="0px">
        <span class="ls-preview" style="letter-spacing:0px">Reset</span>
        <span class="ls-value">0px</span>
      </a>`;

        const $group = ui.buttonGroup([
            ui.button({
                className: "dropdown-toggle note-btn-letters",
                contents:
                    '<i class="note-icon-magic note-icon"></i>자간<span class="caret"></span>',
                tooltip: "Letter spacing (px)",
                data: { toggle: "dropdown" },
            }),
            ui.dropdown({
                className: "note-letterspacing-menu",
                contents: itemsHtml,
                callback: function ($dropdown) {
                    const $toggle = $dropdown.prev(".dropdown-toggle");
                    $toggle
                        .off("mousedown.keepRange")
                        .on("mousedown.keepRange", function () {
                            context.invoke("editor.saveRange");
                        });
                    $dropdown
                        .find("a.ls-item")
                        .off("mousedown.keepRangeFix")
                        .on("mousedown.keepRangeFix", function () {
                            context.invoke("editor.saveRange");
                        });
                    $dropdown.find("a.ls-item").on("click", function (e) {
                        e.preventDefault();
                        const value = $(this).data("value");
                        context.invoke("editor.focus");
                        context.invoke("editor.restoreRange");
                        self.applyLetterSpacing(context, value);
                        $(this)
                            .closest(".note-letterspacing-menu")
                            .find(".ls-item")
                            .removeClass("active");
                        $(this).addClass("active");
                    });
                },
            }),
        ]);
        return $group.render();
    }
}

// Boot
SummernoteAdmin.boot();
