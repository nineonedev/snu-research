function App() {
    console.log("Admin - App()");
    document.addEventListener("DOMContentLoaded", () => {
        initSummerNote();
        initMobile();
        initTabMenu();
    });
}
App();

function initTabMenu() {
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

function initMobile() {
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

function initSummerNote() {
    // ===== Insert custom CSS once (dropdown + active-state) =====
    if (!document.getElementById("sn-letterspacing-style")) {
        const style = document.createElement("style");
        style.id = "sn-letterspacing-style";
        style.textContent = `
        /* Letter-spacing dropdown UI */
        .note-editor .dropdown-menu.note-letterspacing-menu { 
            padding: 8px; min-width: 240px; max-height: 260px; overflow:auto; 
        }
        .note-editor .note-letterspacing-menu .ls-item {
            position: relative;
            display:flex; align-items:center; justify-content:space-between;
            gap:12px; padding:8px 10px; border-radius:8px; text-decoration:none; color:inherit;
        }
        .note-editor .note-letterspacing-menu .ls-item:hover { 
            background: rgba(0,0,0,0.06);
        }
        .note-editor .note-letterspacing-menu .ls-item.active { 
            background: rgba(0,0,0,0.09);
        }
        .note-editor .note-letterspacing-menu .ls-item.active::before {
            content: "✓";
            position:absolute; left:8px; font-size:12px; opacity:0.8;
        }
        .note-editor .note-letterspacing-menu .ls-preview {
            flex:1; font-size:14px; line-height:1; white-space:nowrap;
            padding:4px 8px; border-radius:6px; background: rgba(0,0,0,0.04);
            margin-left:12px; /* room for checkmark */
        }
        .note-editor .note-letterspacing-menu .ls-value {
            font-variant-numeric: tabular-nums; min-width:48px; text-align:right;
            font-size:12px; opacity:0.85;
        }
        .note-editor .note-btn-letters .note-icon { margin-right:6px; }
        `;
        document.head.appendChild(style);
    }

    const summernotes = document.querySelectorAll(".summernote");

    // Helper: escape HTML
    const escapeHtml = (s) =>
        s.replace(/[&<>"']/g, ch =>
            ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch])
        );

    // Helper: find current letter-spacing at caret/selection start (returns '0px' if normal)
    function getCurrentLetterSpacingPx(node, editableRoot) {
        let el = node && (node.nodeType === 1 ? node : node.parentElement);
        while (el && el !== editableRoot && el.nodeType === 1) {
            const ls = window.getComputedStyle(el).letterSpacing;
            if (ls && ls !== "normal") return ls; // computed in px
            el = el.parentElement;
        }
        const rootLS = editableRoot ? window.getComputedStyle(editableRoot).letterSpacing : "normal";
        return (rootLS && rootLS !== "normal") ? rootLS : "0px";
    }

    // ===== Apply letter-spacing preserving existing inline styles (bold/italic/underline etc.) =====
    function applyLetterSpacing(context, pxValue) {
        context.invoke('editor.focus');
        context.invoke('editor.restoreRange');
        const rng = context.invoke('editor.getLastRange');

        if (!rng || rng.isCollapsed()) {
            alert('자간을 적용할 텍스트를 먼저 선택해주세요.');
            return;
        }

        // Use Summernote range API to WRAP the selection with <span style="letter-spacing:...">
        // This preserves existing inline tags inside selection (b/i/u/span...)
        const span = document.createElement('span');
        span.style.letterSpacing = pxValue;

        // Prefer Summernote's safe wrapper
        if (typeof rng.wrapBodyInlineWith === 'function') {
            rng.wrapBodyInlineWith(span);
        } else {
            // Fallback: try native surroundContents (may fail on partial non-text selection)
            try {
                const native = rng.nativeRange ? rng.nativeRange() : window.getSelection().getRangeAt(0);
                native.surroundContents(span);
            } catch (e) {
                // As a last resort (avoid losing styles): insert wrapper & move contents
                const native = rng.nativeRange ? rng.nativeRange() : window.getSelection().getRangeAt(0);
                const frag = native.extractContents();
                span.appendChild(frag);
                native.insertNode(span);
            }
        }

        // Notify editor for undo stack & UI refresh
        if (typeof context.invoke === 'function') {
            context.invoke('editor.afterCommand'); // add to history
        }
    }

    // ===== Letter-spacing dropdown button (pixels only) =====
    const LetterSpacingDropdown = function (context) {
        const ui = $.summernote.ui;
        const values = ['0px', '1px', '2px', '3px', '4px', '6px', '8px', '10px'];

        const itemsHtml = values.map(v => `
            <a class="dropdown-item ls-item" href="#" data-value="${v}">
                <span class="ls-preview" style="letter-spacing:${v}">가Aa 가Aa</span>
                <span class="ls-value">${v}</span>
            </a>
        `).join('') + `
            <div class="dropdown-divider"></div>
            <a class="dropdown-item ls-item" href="#" data-value="0px">
                <span class="ls-preview" style="letter-spacing:0px">Reset</span>
                <span class="ls-value">0px</span>
            </a>
        `;

        const $group = ui.buttonGroup([
            ui.button({
                className: 'dropdown-toggle note-btn-letters',
                contents: '<i class="note-icon-magic note-icon"></i>자간<span class="caret"></span>',
                tooltip: 'Letter spacing (px)',
                data: { toggle: 'dropdown' }
            }),
            ui.dropdown({
                className: 'note-letterspacing-menu',
                contents: itemsHtml,
                callback: function ($dropdown) {
                    const $toggle = $dropdown.prev('.dropdown-toggle');

                    function markActive(currentPx) {
                        const $links = $dropdown.find('a.ls-item');
                        $links.removeClass('active');
                        const $match = $links.filter((_, a) => $(a).data('value') === currentPx);
                        if ($match.length) $match.addClass('active');
                    }

                    $toggle.on('click', function () {
                        context.invoke('editor.focus');
                        context.invoke('editor.restoreRange');
                        const rng = context.invoke('editor.getLastRange');
                        let anchorNode = (rng && rng.sc) ? rng.sc : (document.getSelection()?.anchorNode || null);
                        const editableRoot = context?.layoutInfo?.editable?.[0] || null;
                        const currentPx = getCurrentLetterSpacingPx(anchorNode, editableRoot);
                        markActive(currentPx);
                    });

                    $dropdown.find('a.ls-item').on('click', function (e) {
                        e.preventDefault();
                        const value = $(this).data('value');

                        applyLetterSpacing(context, value);

                        // update active state
                        $(this).closest('.note-letterspacing-menu').find('.ls-item').removeClass('active');
                        $(this).addClass('active');
                    });
                }
            })
        ]);

        return $group.render();
    };

    document.querySelectorAll(".summernote").forEach((element) => {
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
                ["insert", ["link", "picture", "video"]],
                ["height", ["height", "letterSpacing"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],

            fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '32', '36', '40', '48'],
            lineHeights: ['1', '1.2', '1.3', '1.5', '1.75', '2', '2.5', '3'],

            buttons: { letterSpacing: LetterSpacingDropdown },

            callbacks: {
                onInit: function () {
                    $(element).summernote('focus');
                    $(element).summernote('saveRange');
                },
                onKeyup: function () {
                    $(element).summernote('saveRange');
                },
                onMouseUp: function () {
                    $(element).summernote('saveRange');
                },
                onImageUpload: function (files) {
                    const formData = new FormData();
                    formData.append("file", files[0]);

                    fetch("/admin/uploads/summernote", {
                        method: "POST",
                        body: formData,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                $(element).summernote("insertImage", data.path);
                            } else {
                                alert(data.message || "이미지 업로드에 실패했습니다.");
                            }
                        })
                        .catch((error) => console.error(error));
                },
            },
        });
    });
}
