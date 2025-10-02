# 2019-02-28 SSJ :: IsInstalledFont() 수정
- 브라우저에 폰트가 설치되어있는지 확인하는 함수
- IsInstalledFont() 함수는 document.body에 각각의 font를 적용한 element를 추가하여 높이, 넓이를 비교하여 해당 font가 적용되는지 여부를 판단
- 에디터 적용 시 부모 element가 display:none; 되어 있을 경우 비교대상이 되는 element의 높이, 넓이가 0으로 나와 모든 폰트가 없는것으로 인식하는 오류 발생
- 에디터는 iframe안에서 구동되기때문에 document.body가 아닌 parent.document.body에 element를 추가하여 비교하는 방식으로 변경하여 부모 element가 display:none; 되어 있어도 체크가 되도록 수정함


> 수정전
```
	IsInstalledFont = function(sFont) {

		var sDefFont = sFont == 'Comic Sans MS' ? 'Courier New' : 'Comic Sans MS';
		if (!oDummy) {
			oDummy = document.createElement('div');
		}

		var sStyle = 'position:absolute !important; font-size:200px !important; left:-9999px !important; top:-9999px !important;';
		oDummy.innerHTML = 'mmmmiiiii'+unescape('%uD55C%uAE00');
		oDummy.style.cssText = sStyle + 'font-family:"' + sDefFont + '" !important';

		var elBody = document.body || document.documentElement;
		if(elBody.firstChild){
			elBody.insertBefore(oDummy, elBody.firstChild);
		}else{
			document.body.appendChild(oDummy);
		}

		var sOrg = oDummy.offsetWidth + '-' + oDummy.offsetHeight;

		oDummy.style.cssText = sStyle + 'font-family:"' + sFont.replace(rx, '","') + '", "' + sDefFont + '" !important';

		var bInstalled = sOrg != (oDummy.offsetWidth + '-' + oDummy.offsetHeight);

		document.body.removeChild(oDummy);

		if(!bInstalled){
			console.log(sFont);
		}

		return bInstalled;

	};
```

> 수정후
```
	IsInstalledFont = function(sFont) {

		var sDefFont = sFont == 'Comic Sans MS' ? 'Courier New' : 'Comic Sans MS';
		if (!oDummy) {
			oDummy = parent.document.createElement('div');
		}

		var sStyle = 'position:absolute !important; font-size:200px !important; left:-9999px !important; top:-9999px !important;';
		oDummy.innerHTML = 'mmmmiiiii'+unescape('%uD55C%uAE00');
		oDummy.style.cssText = sStyle + 'font-family:"' + sDefFont + '" !important';

		var elBody = parent.document.body || parent.document.documentElement;
		if(elBody.firstChild){
			elBody.insertBefore(oDummy, elBody.firstChild);
		}else{
			parent.document.body.appendChild(oDummy);
		}

		var sOrg = oDummy.offsetWidth + '-' + oDummy.offsetHeight;

		oDummy.style.cssText = sStyle + 'font-family:"' + sFont.replace(rx, '","') + '", "' + sDefFont + '" !important';

		var bInstalled = sOrg != (oDummy.offsetWidth + '-' + oDummy.offsetHeight);

		parent.document.body.removeChild(oDummy);

		return bInstalled;

	};
```