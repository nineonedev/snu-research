/*
* 네이버 스마트 에디터 적용방법
* 1. textarea에 SEditor 클래스 적용
* 2. textarea에 id적용 금지 - id는 자동생성
* 3. 다른 에디터와 중복금지 -> geditor 속성이 있다면 삭제
*/
$(document).ready(SEditor_init); // 네이버 에디터 적용
//$(document).delegate('form', 'submit', submitContents); // 폼전송시 시 textarea에 내용 적용 // 2017-09-18 LDD
//$('form').submit(submitContents);
// SSJ : 2017-11-13  validate 적용시 작동하도록 수정 
$(document).ready(function(){
	$('.SEditor').each(function(i,v){
		// $(this).closest('form').on('submit', submitContents);
	});
});

var oEditors = []; // 에디터 저장변수

// 기본값
var DEFAULT_STYLE_CSS =
  "font-family:'Inter','Noto Sans KR','Malgun Gothic',sans-serif;" +
  "font-size:14pt; line-height:1.3; text-align:justify;";


// 드롭다운에 표시할 폰트 목록
var FONT_LIST = [
  ['Inter','Inter'],
];

/** 에디터 기본 세팅 – 폰트/크기/줄간격/정렬을 통일 */
function setSE2Defaults(ed){
  // 1) 새 문단의 기본 스타일
  ed.exec("SET_DEFAULT_STYLE", [DEFAULT_STYLE_CSS]);

  // 2) 에디터 iframe 내부 보기 스타일(기존/빈 문서 즉시 반영)
  ed.exec("ADD_APP_STYLE", [
    "html,body,.se2_inputarea,.se2_inputarea p,.se2_inputarea div{"+DEFAULT_STYLE_CSS+"}"
  ]);

  // 3) 툴바 드롭다운도 Inter / 18pt 상태로 맞추기 (가능한 스킨에서 동작)
  try {
    ed.exec("FONT_FAMILY", ["Inter"]);
    ed.exec("FONT_SIZE", ["14pt"]);
  } catch(e){}

  // 4) 완전 빈 문서면 기본 스타일 문단 하나 생성
  var ir = ed.getIR && ed.getIR();
  if (!ir || /^(<p><br><\/p>|<p>&nbsp;<\/p>)$/i.test(ir)) {
    ed.exec("SET_IR", ['<p style="'+DEFAULT_STYLE_CSS+'"><br></p>']);
  }
}

// 네이버 에디터 초기화
function SEditor_init(){
	// 에디터 아이디적용을위한 인덱스
	var sedit_idx = 0;
	$('.SEditor').each(function(){
		sedit_idx++;
		var sedit_id = 'ir' + sedit_idx;
		// 아이디 적용
		$(this).attr('id' , sedit_id).css({"width":"100%" , "min-width":"560px"});
		// 에디터 적용

		var TextMode = $(this).attr('data-text-mode');
		if( TextMode == undefined || TextMode == ''){  TextMode = '';}
		SEditor(sedit_id,TextMode);

		

	});
}
// 네이버 에디터 적용 - 아이디기준 개별적용
function SEditor(id,TextMode){
	var sLang = "ko_KR";	// 언어 (ko_KR/ en_US/ ja_JP/ zh_CN/ zh_TW), default = ko_KR
	// 추가 글꼴 목록
	//var aAdditionalFontSet = [["MS UI Gothic", "MS UI Gothic"], ["Comic Sans MS", "Comic Sans MS"],["TEST","TEST"]];
	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: id,
		sSkinURI: "/assets/lib/smarteditor2/SmartEditor2Skin.html?v=221312",
		htParams : {
			bUseToolbar : TextMode == 'true' ? false : true, // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true, // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : TextMode == 'true' ? false : true, // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			bSkipXssFilter : true, // client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
			//aAdditionalFontList : aAdditionalFontSet, // 추가 글꼴 목록
			fOnBeforeUnload : function(){},
			I18N_LOCALE : sLang,
			aAdditionalFontList : FONT_LIST

		}, //boolean
		fOnAppLoad : function(){
			var ed = oEditors.getById[id];
			setSE2Defaults(ed);

			// 툴바/라벨까지 실제 선택 상태로 반영
			setTimeout(function(){
				try {
				ed.exec("FOCUS", []);
				ed.exec("SELECT_ALL", []);           // 선택을 만들어줌
				ed.exec("FONT_FAMILY", ["Inter"]);   // 글꼴
				ed.exec("FONT_SIZE", ["14pt"]);      // 글자 크기
				ed.exec("JUSTIFYFULL", []);          // 양쪽정렬

				// 커서를 본문 끝으로 돌려놓기
				var win = ed.getWYSIWYGWindow();
				if (win && win.getSelection) {
					var sel = win.getSelection();
					sel.removeAllRanges();
					var body = win.document.body;
					var rng = win.document.createRange();
					rng.selectNodeContents(body);
					rng.collapse(false); // 끝으로
					sel.addRange(rng);
				}
				} catch(e){}
			}, 0);

			// (선택 UI가 select인 커스텀 스킨 대응) 드롭다운 값도 눈에 보이게 맞춤
			setTimeout(function(){
				var $container = $('#'+id).closest('.se2_container, #smart_editor2');
				// 폰트/크기 드롭다운(스킨에 따라 버튼/셀렉트가 다름)
				$container.find('.husky_seditor_ui_fontName select').val('Inter');
				$container.find('.husky_seditor_ui_fontSize select').val('14pt');
				// 버튼형 스킨이면 라벨 텍스트도 교체
				$container.find('.husky_se2m_current_fontName').text('Inter');
				$container.find('.husky_se2m_current_fontSize').text('14pt');
			}, 10);
		},
		fCreator: "createSEditor2"
	});

}
// 에디터의 내용이 textarea에 적용
//function submitContents(elClickedObj) {
function submitContents() {
	
	for(var i=0; i<oEditors.length; i++){
		//oEditors[i].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.
		// 2017-09-18 LDD
		if(oEditors[i].getContents() == null || oEditors[i].getContents() == '<p><br></p>' || oEditors[i].getContents() == '<p>&nbsp;</p>') {
			$(oEditors[i].elPlaceHolder).val('');
		}
		else {
			oEditors[i].exec("UPDATE_CONTENTS_FIELD", []);
		}
	}
	try {
	} catch(e) {}
}