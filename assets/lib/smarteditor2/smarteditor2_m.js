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
		$(this).closest('form').on('submit', submitContents);
	});
});

var oEditors = []; // 에디터 저장변수
// 네이버 에디터 초기화
function SEditor_init(){
	// 에디터 아이디적용을위한 인덱스
	var sedit_idx = 0;
	$('.SEditor').each(function(){
		sedit_idx++;
		var sedit_id = 'ir' + sedit_idx;
		// 아이디 적용
		$(this).attr('id' , sedit_id).css({"width":"100%" , "min-width":"100%"});
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
		sSkinURI: "/resource/vendor/smarteditor2/SmartEditor2Skin_m.html",
		htParams : {
			bUseToolbar : TextMode == 'true' ? false : true, // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true, // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : TextMode == 'true' ? false : true, // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			bSkipXssFilter : true, // client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
			//aAdditionalFontList : aAdditionalFontSet, // 추가 글꼴 목록
			fOnBeforeUnload : function(){},
			I18N_LOCALE : sLang
		}, //boolean
		fOnAppLoad : function(){
		//	$('#'+id).show();
		},
		fCreator: "createSEditor2_m"
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