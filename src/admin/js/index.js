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

function uploadVideoFile(file, noteEl) {
  const formData = new FormData();
  formData.append("file", file);

  return fetch("/admin/uploads/summernote?type=video", {
    method: "POST",
    body: formData,
  })
  .then((res) => res.json())
  .then((data) => {
    if (!data.success) throw new Error(data.message || "비디오 업로드 실패");

    $(noteEl).summernote('focus');
    $(noteEl).summernote('restoreRange');

    const video = document.createElement("video");
    video.src = data.path;
    video.controls = true;
    video.preload = "metadata";
    video.playsInline = true;
    video.style.maxWidth = "100%";

    // (선택) 블록 정렬/줄바꿈 안정화를 위해 p로 감싸 삽입
    const wrapper = document.createElement('p');
    wrapper.appendChild(video);

    $(noteEl).summernote("insertNode", wrapper);
  });
}


// ==== [GLOBAL] Summernote range guards (파일에 1번) ====
let __SN_GLOBAL_BOUND__ = false;

// 클릭된 요소 → 소속 note-editor → 앞의 .summernote → context
function __getSummernoteContextFromTarget__(target) {
  const $editor = $(target).closest('.note-editor');
  if (!$editor.length) return null;
  const $note = $editor.prev('.summernote');
  if (!$note.length) return null;
  return $note.data('summernote') || null;
}

// 문서 전역으로 "누르는 순간" 현재 selection을 저장
function __bindSummernoteGlobalRangeGuards__() {
  if (__SN_GLOBAL_BOUND__) return;
  __SN_GLOBAL_BOUND__ = true;

  // toolbar, popover, dropdown, note-btn 등 전부 커버
  $(document)
    .on(
      'mousedown.keepRange.summer',
      '.note-toolbar, .note-popover, .note-air-popover, .note-editor .dropdown-menu, .note-editor .note-btn, [data-event]',
      function (e) {
        const ctx = __getSummernoteContextFromTarget__(e.target);
        if (ctx) ctx.invoke('editor.saveRange');
      }
    )
    // 부트스트랩 계열 드롭다운이 닫히면서 selection이 날아가는 이슈 보완
    .on(
      'show.bs.dropdown.keepRange.summer',
      '.note-editor [data-toggle="dropdown"], .note-editor .dropdown-toggle',
      function (e) {
        const ctx = __getSummernoteContextFromTarget__(e.target);
        if (ctx) ctx.invoke('editor.saveRange');
      }
    );
}


function initSummerNote() {
    __bindSummernoteGlobalRangeGuards__();

    // === [NEW] 기본값 프리셋 ===
    const DEFAULTS = {
    fontName: 'Inter',   // 폰트
    fontSize: '18',      // Summernote는 숫자 문자열 (단위 없이) 사용
    lineHeight: '1.3',   // 줄간격
    align: 'justify',    // 'justify'를 버튼에서 처리 (양쪽정렬)
    };

    // 선택 범위에 걸친 블록(P, DIV, H1~H6, BLOCKQUOTE, LI) 수집
    function getSelectedBlocks(context) {
        const editable = context?.layoutInfo?.editable?.[0];
        if (!editable) return [];
        const rng = context.invoke('editor.getLastRange');
        if (!rng) return [];

        // 서머노트 Range API 우선
        let nodes = [];
         if (typeof rng.nodes === 'function') {
           // 올바른 시그니처: (pred, options)
           nodes = rng.nodes(() => true);
         } else {
            const sel = window.getSelection();
            if (!sel.rangeCount) return [];
            const nr = sel.getRangeAt(0).cloneRange();
            const walker = document.createTreeWalker(
            editable,
            NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT,
            null
            );
            let cur = walker.currentNode;
            while (cur) {
            try {
                const r = document.createRange();
                r.selectNode(cur.nodeType === 3 ? cur.parentNode : cur);
                const overlaps =
                nr.compareBoundaryPoints(Range.END_TO_START, r) < 0 &&
                nr.compareBoundaryPoints(Range.START_TO_END, r) > 0;
                if (overlaps) nodes.push(cur.nodeType === 1 ? cur : cur.parentNode);
            } catch (e) {}
            cur = walker.nextNode();
            }
        }

        const BLOCKS = new Set(['P','DIV','H1','H2','H3','H4','H5','H6','BLOCKQUOTE','LI']);
        const out = [];
        nodes.forEach(n => {
            const el = (n && n.nodeType === 1) ? n : n?.parentElement;
            if (!el || !editable.contains(el)) return;
            const b = el.closest('p,div,h1,h2,h3,h4,h5,h6,blockquote,li');
            if (b && editable.contains(b) && BLOCKS.has(b.tagName)) out.push(b);
        });
        return Array.from(new Set(out)); // 중복 제거
        }

        // 블록에 직접 스타일을 부여 (여러 문단 선택 대응)
        function applyBlockStyles(context, styleObj) {
        context.invoke('editor.focus');
        context.invoke('editor.restoreRange');
        const blocks = getSelectedBlocks(context);
        if (!blocks.length) return;

        // (선택) 블록 내부에 이미 박힌 인라인 font 스타일 제거하고 싶다면 주석 해제
        // stripInlineFontStyles(blocks.flatMap(b => Array.from(b.querySelectorAll('*'))));

        context.invoke('editor.beforeCommand');
        blocks.forEach(b => {
            Object.entries(styleObj).forEach(([k, v]) => { b.style[k] = v; });
        });
        context.invoke('editor.afterCommand');
    }


    // === [NEW] 기본값 적용 함수 ===
    function applyDefaultFormatting(context) {
        context.invoke('editor.focus');
        context.invoke('editor.restoreRange');
        const rng = context.invoke('editor.getLastRange');
        const hasSelection = rng && !rng.isCollapsed();

        console.log('appling,,,,,,,,');
        

        // 선택 없으면 전체 적용 여부 확인
        if (!hasSelection) {
            const ok = confirm('선택 영역이 없습니다. 에디터 전체에 기본값(Inter, 18, 1.3, 양쪽정렬)을 적용할까요?');
            if (!ok) return;
            context.invoke('editor.selectAll');
            context.invoke('editor.saveRange');
        }

        // ✅ 1) 선택된 블록 수집
        const blocks = getSelectedBlocks(context);

         // ✅ 2) 블록 자신  자손 요소에서 인라인 타이포 스타일 제거 (flatMap 사용 금지)
         const targets = [];
         blocks.forEach(b => {
           targets.push(b);
           targets.push(...Array.from(b.querySelectorAll('*')));
         });

         // (선택) 한 덩어리로 히스토리에 남기고 싶으면 before/after로 감싸기
         context.invoke('editor.beforeCommand');
         stripInlineFontStyles(targets);
         context.invoke('editor.afterCommand');

        // 글꼴, 크기, 줄간격, 정렬 순서대로 적용
        applyBlockStyles(context, {
        fontFamily: DEFAULTS.fontName,
        fontSize: `${DEFAULTS.fontSize}px`,
        lineHeight: DEFAULTS.lineHeight
        });
        applyBlockAlign(context, 'justify');

        // 전체선택으로 적용했으면 커서 복구(선택 깔끔하게)
        if (!hasSelection) {
            // 전체 적용 후 맨 끝으로 커서 둠 (선택 해제 느낌)
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

    // === [NEW] 툴바 버튼 ===
    const DefaultResetButton = function (context) {
    const ui = $.summernote.ui;
    return ui.button({
        contents: '<i class="note-icon-eraser"></i> 기본값',
        tooltip: '선택 영역을 Inter/18/1.3/양쪽정렬로 맞추기',
        click: function () {
            setTimeout(() => {
           context.invoke('editor.focus');
           context.invoke('editor.restoreRange'); // 전역 mousedown 가드 or 위에서 saveRange한 범위 복구
           applyDefaultFormatting(context);
         }, 0);
        }
    }).render();
    };

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

    const VideoUploadButton = function (context) {
        const ui = $.summernote.ui;

        // 숨김 파일 입력
        const $file = $('<input type="file" accept="video/*" style="display:none" />');
        $(document.body).append($file);

        $file.on("change", function () {
            const file = this.files && this.files[0];
            if (!file) return;
            // (선택) 클라 사이즈 제한: 200MB 예시
            // if (file.size > 200 * 1024 * 1024) { alert("200MB 이하만 업로드 가능합니다."); return; }

            // 커서 복구 후 업로드
            context.invoke('editor.focus');
            context.invoke('editor.restoreRange');
            uploadVideoFile(file, context.layoutInfo.note[0])
                .catch(err => alert(err.message || "비디오 업로드 실패"))
                .finally(() => { $file.val(""); });
        });

        return ui.button({
            contents: '<i class="note-icon-video"></i> 업로드',
            tooltip: "비디오 업로드",
            click: function () {
                context.invoke('editor.saveRange');
                $file.trigger("click");
            }
        }).render();
    };

    function applyHeadingSafely(context, tagNameUpper /* 'H1' | 'H2' ... | 'P' */) {
        context.invoke('editor.focus');
        context.invoke('editor.restoreRange');
        const rng = context.invoke('editor.getLastRange');
        const editable = context?.layoutInfo?.editable?.[0];
        if (!rng || !editable) return;

        // 선택에 걸친 모든 노드 수집 (Summernote Range API)
        let nodes = [];
          if (typeof rng.nodes === 'function') {
           nodes = rng.nodes(() => true);           // 올바른 호출
         } else {
            // 폴백: 네이티브 range로 노드 수집 (간단 버전)
            const sel = window.getSelection();
            if (!sel.rangeCount) return;
            const nr = sel.getRangeAt(0).cloneRange();
            const walker = document.createTreeWalker(editable, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT, null);
            let cur = walker.currentNode;
            while (cur) {
            const nodeRange = document.createRange();
            try {
                nodeRange.selectNode(cur.nodeType === 3 ? cur.parentNode : cur);
            } catch(e) {}
            if (nr.compareBoundaryPoints && nodeRange.startContainer) {
                // 겹침 여부 판정
                const overlaps =
                nr.compareBoundaryPoints(Range.END_TO_START, nodeRange) < 0 &&
                nr.compareBoundaryPoints(Range.START_TO_END, nodeRange) > 0;
                if (overlaps) nodes.push(cur.nodeType === 3 ? cur.parentNode : cur);
            }
            cur = walker.nextNode();
            }
        }

        // 블록 레벨 후보만 추출 (LI는 스킵: 리스트 구조 보호)
        const BLOCKS = new Set(['P','DIV','H1','H2','H3','H4','H5','H6','BLOCKQUOTE']);
        const blocks = [];
        nodes.forEach(n => {
            const el = (n.nodeType === 1) ? n : n.parentElement;
            if (!el) return;
            // 에디터 루트 밖/Toolbar 등 무시
            if (!editable.contains(el)) return;

            // 가장 가까운 블록 엘리먼트
            const b = el.closest('p,div,h1,h2,h3,h4,h5,h6,blockquote,li');
            if (!b || !editable.contains(b)) return;

            // 리스트 항목은 구조 깨질 수 있으니 건너뜀
            if (b.tagName === 'LI') return;

            if (BLOCKS.has(b.tagName)) {
            blocks.push(b);
            }
        });

        if (!blocks.length) return;

        // 중복 제거 + 문서 순서 유지
        const uniq = Array.from(new Set(blocks));

        // 치환 실행: 순서대로 replaceChild -> 순서 보전
        uniq.forEach(oldEl => {
            // 이미 원하는 태그면 패스
            if (oldEl.tagName === tagNameUpper) return;

            const newEl = document.createElement(tagNameUpper); // 'H1' or 'P' ...
            // 스타일/클래스/속성 유지
            newEl.className = oldEl.className;
            if (oldEl.getAttribute('style')) newEl.setAttribute('style', oldEl.getAttribute('style'));
            // id, data-* 등 일반 속성 복사
            for (const attr of Array.from(oldEl.attributes)) {
            const name = attr.name.toLowerCase();
            if (name === 'class' || name === 'style') continue;
            try { newEl.setAttribute(attr.name, attr.value); } catch(e){}
            }
            // 내용 이동 (childNodes를 그대로 이동 → 순서 보전)
            while (oldEl.firstChild) newEl.appendChild(oldEl.firstChild);
            oldEl.parentNode.replaceChild(newEl, oldEl);
        });

        // 히스토리/리프레시
        context.invoke('editor.afterCommand');
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

                    $toggle.off('mousedown.keepRange').on('mousedown.keepRange', function () {
                        context.invoke('editor.saveRange');
                    });

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

                       context.invoke('editor.focus');
                        context.invoke('editor.restoreRange');
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

    // === [ADD] Range/노드 수집 + 인라인 스타일 정리 + 스타일 적용 유틸 ===

    // 선택영역의 Element 노드들을 에디터 루트 기준으로 수집
    function collectNodesInRange(context) {
        const editable = context?.layoutInfo?.editable?.[0];
        if (!editable) return [];
        const rng = context.invoke('editor.getLastRange');
        if (!rng) return [];

        // Summernote Range API 우선
          if (typeof rng.nodes === 'function') {
           return rng.nodes(() => true)
                     .map(n => (n.nodeType === 1 ? n : n.parentElement))
                     .filter(Boolean);
         }

        // Fallback: 네이티브
        const sel = window.getSelection();
        if (!sel.rangeCount) return [];
        const nr = sel.getRangeAt(0).cloneRange();

        const out = new Set();
        const walker = document.createTreeWalker(editable, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT, null);
        let cur = walker.currentNode;
        while (cur) {
            const el = (cur.nodeType === 1) ? cur : cur.parentElement;
            if (el) {
            try {
                const r = document.createRange();
                r.selectNode(cur.nodeType === 3 ? cur.parentNode : cur);
                const overlaps = nr.compareBoundaryPoints(Range.END_TO_START, r) < 0
                            && nr.compareBoundaryPoints(Range.START_TO_END, r) > 0;
                if (overlaps) out.add(el);
            } catch (e) {}
            }
            cur = walker.nextNode();
        }
        return Array.from(out);
    }

    // 선택영역에서 인라인 스타일(폰트/크기/줄간격/자간) 제거
    function stripInlineFontStyles(nodes) {
        const PROPS =  ['font-size','font-family','line-height','letter-spacing','text-align'];
        nodes.forEach(el => {
            if (!(el instanceof Element)) return;
            const style = el.getAttribute('style');
            if (!style) return;

            // 개별 속성 제거 (세미콜론 정리)
            let s = style;
            PROPS.forEach(p => {
            const re = new RegExp(`(?:^|;)\\s*${p}\\s*:[^;"]*;?`, 'gi');
            s = s.replace(re, ';');
            });
            s = s.replace(/;{2,}/g, ';').replace(/^\s*;\s*|\s*;\s*$/g, '');
            if (s) el.setAttribute('style', s); else el.removeAttribute('style');
        });

        // <font> 태그도 제거(내부만 살림)
        nodes.forEach(el => {
            if (el.tagName && el.tagName.toLowerCase() === 'font') {
            const parent = el.parentNode;
            while (el.firstChild) parent.insertBefore(el.firstChild, el);
            parent.removeChild(el);
            }
        });
    }

    // 선택영역 전체를 안전하게 <span style="...">로 래핑(문장부호/영문 포함)
    function applyInlineStyleToSelection(context, styleObj) {
        context.invoke('editor.focus');
        context.invoke('editor.restoreRange');
        const rng = context.invoke('editor.getLastRange');
        if (!rng || rng.isCollapsed()) return;

        const nodes = collectNodesInRange(context);
        stripInlineFontStyles(nodes); // 기존 인라인 우선 제거

        // span 래핑
        const span = document.createElement('span');
        Object.entries(styleObj).forEach(([k, v]) => { span.style[k] = v; });

        if (typeof rng.wrapBodyInlineWith === 'function') {
            rng.wrapBodyInlineWith(span);
        } else {
            try {
            const native = rng.nativeRange ? rng.nativeRange() : window.getSelection().getRangeAt(0);
            native.surroundContents(span);
            } catch (e) {
            const native = rng.nativeRange ? rng.nativeRange() : window.getSelection().getRangeAt(0);
            const frag = native.extractContents();
            span.appendChild(frag);
            native.insertNode(span);
            }
        }
        context.invoke('editor.afterCommand');
    }

    // 블록 정렬은 블록 요소에 직접 적용 (괄호/따옴표 이슈 무관)
    function applyBlockAlign(context, align /* 'left'|'center'|'right'|'justify' */) {
        context.invoke('editor.focus');
        context.invoke('editor.restoreRange');
        const editable = context?.layoutInfo?.editable?.[0];
        const rng = context.invoke('editor.getLastRange');
        if (!editable || !rng) return;

        const nodes = collectNodesInRange(context);
        const BLOCKS = new Set(['P','DIV','H1','H2','H3','H4','H5','H6','BLOCKQUOTE','LI']);
        const targets = [];

        nodes.forEach(n => {
            const el = (n.nodeType === 1) ? n : n.parentElement;
            if (!el || !editable.contains(el)) return;
            const b = el.closest('p,div,h1,h2,h3,h4,h5,h6,blockquote,li');
            if (b && editable.contains(b) && BLOCKS.has(b.tagName)) targets.push(b);
        });

        Array.from(new Set(targets)).forEach(b => { b.style.textAlign = align; });
        context.invoke('editor.afterCommand');
    }


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
                ["insert", ["link", "picture", "video", "videoUpload"]],
                ["height", ["height", "letterSpacing"]],
                ["view", ["fullscreen", "codeview", "help"]],
                ["preset", ["defaultReset"]],
            ],

            fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '32', '36', '40', '48'],
            lineHeights: ['1', '1.2', '1.3', '1.5', '1.75', '2', '2.5', '3'],

            buttons: { 
                letterSpacing: LetterSpacingDropdown,
                videoUpload: VideoUploadButton,
                defaultReset: DefaultResetButton,
             },

            callbacks: {
                onInit: function () {
                    $(element).summernote('focus');
                    $(element).summernote('saveRange');

                    const $editor   = $(element).next('.note-editor');
                    const $editable = $editor.find('.note-editable');

                    $editable.css({
                        fontFamily: '"Inter", system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans KR", sans-serif',
                        fontSize: '18px',
                        lineHeight: '1.3',
                        textAlign: 'justify',
                    });

                    // ✅ [ADD] 툴바를 누르는 '순간'에 Range 저장 (click 아님! mousedown)
                    $editor.off('mousedown.keepRange')
                        .on('mousedown.keepRange', '.note-toolbar, .dropdown-menu, .note-btn, [data-event]', () => {
                        const ctx = $(element).data('summernote');
                        if (ctx) ctx.invoke('editor.saveRange');
                        });

                    // ✅ [ADD] Ctrl/⌘ + A 직후에도 Range 저장 (키 이벤트 → 다음 틱에 저장)
                    $editable.off('keydown.keepRange').on('keydown.keepRange', (e) => {
                        const isCtrlA = (e.ctrlKey || e.metaKey) && (e.key === 'a' || e.key === 'A');
                        if (isCtrlA) {
                        setTimeout(() => { $(element).summernote('saveRange'); }, 0);
                        }
                    });

                    $editor.find('[data-event="fontSize"]').off('click.fixFontSize').on('click.fixFontSize', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        const val = (e.currentTarget.getAttribute('data-value') || '').trim(); // 예: '18'
                        if (!val) return;

                        const ctx = $(element).data('summernote');
                        setTimeout(() => {
                           ctx.invoke('editor.focus');
                           ctx.invoke('editor.restoreRange');
                           const rng = ctx.invoke('editor.getLastRange');
                           if (!rng || rng.isCollapsed()) ctx.invoke('editor.selectAll');
                           applyBlockStyles(ctx, { fontSize: `${val}px` });
                         }, 0);
                    });

                    // === [ADD] 폰트명 후킹 (영어만/일부만 안 바뀌던 문제 방지)
                    $editor.find('[data-event="fontName"]').off('click.fixFontName').on('click.fixFontName', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        const val = (e.currentTarget.getAttribute('data-value') || '').trim();
                        if (!val) return;

                        const ctx = $(element).data('summernote');
                         setTimeout(() => {
                           ctx.invoke('editor.focus');
                           ctx.invoke('editor.restoreRange');
                           const rng = ctx.invoke('editor.getLastRange');
                           if (!rng || rng.isCollapsed()) ctx.invoke('editor.selectAll');
                           applyBlockStyles(ctx, { fontFamily: val });
                         }, 0);
                    });

                    // === [ADD] 줄간격 후킹
                    $editor.find('[data-event="lineHeight"]').off('click.fixLH').on('click.fixLH', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        const val = (e.currentTarget.getAttribute('data-value') || '').trim(); // '1.3'
                        if (!val) return;

                        const ctx = $(element).data('summernote');
                       setTimeout(() => {
                         ctx.invoke('editor.focus');
                         ctx.invoke('editor.restoreRange');
                         const rng = ctx.invoke('editor.getLastRange');
                         if (!rng || rng.isCollapsed()) ctx.invoke('editor.selectAll');
                         applyBlockStyles(ctx, { lineHeight: val });
                       }, 0);
                    });

                    // === [ADD] 정렬 버튼 후킹(양쪽/좌/우/가운데)
                    const ALIGN_MAP = {
                        justifyLeft: 'left',
                        justifyCenter: 'center',
                        justifyRight: 'right',
                        justifyFull: 'justify'
                    };
                    $editor.find('[data-event^="justify"]').off('click.fixAlign').on('click.fixAlign', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        const evt = e.currentTarget.getAttribute('data-event');
                        const align = ALIGN_MAP[evt];
                        if (!align) return;

                        const ctx = $(element).data('summernote');
                       setTimeout(() => {
                          ctx.invoke('editor.focus');
                          ctx.invoke('editor.restoreRange');
                          const rng = ctx.invoke('editor.getLastRange');
                          if (!rng || rng.isCollapsed()) ctx.invoke('editor.selectAll');
                          applyBlockAlign(ctx, align);
                        }, 0);
                    });

                    // (기존) 안전 헤딩 변환 유지
                    $editor.find('.dropdown-menu [data-event="formatBlock"]').off('click.safeHeading')
                        .on('click.safeHeading', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        const value = (e.currentTarget.getAttribute('data-value') || '').toUpperCase().trim();
                        if (!value) return;
                        applyHeadingSafely($(element).data('summernote'), value);
                        });
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

                    fetch("/admin/uploads/summernote?type=image", {
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
                // (옵션) 드롭/붙여넣기에서 비디오면 업로드
                onDrop: function(e) {
                    const dt = e.originalEvent.dataTransfer;
                    if (!dt || !dt.files || !dt.files.length) return;
                    const videos = [...dt.files].filter(f => f.type.startsWith("video/"));
                    if (videos.length) {
                        e.preventDefault(); e.stopPropagation();
                        $(element).summernote('saveRange');
                        uploadVideoFile(videos[0], element).catch(err => alert(err.message));
                    }
                },
                onPaste: function(e) {
                    const cd = e.originalEvent.clipboardData;
                    if (!cd || !cd.files || !cd.files.length) return;
                    const videos = [...cd.files].filter(f => f.type.startsWith("video/"));
                    if (videos.length) {
                        e.preventDefault(); e.stopPropagation();
                        $(element).summernote('saveRange');
                        uploadVideoFile(videos[0], element).catch(err => alert(err.message));
                    }
                },

                // (선택) Summernote 일부 버전에서 제공되는 onVideoUpload 훅을 함께 사용하고 싶다면:
                onVideoUpload: function(files) {
                    if (!files || !files.length) return;
                    $(element).summernote('saveRange');
                    uploadVideoFile(files[0], element).catch(err => alert(err.message));
                },
            },
        });
    });
}
