<HTML>
<head>
<title>[나이스평가정보(주)]계좌확인서비스</title></head>
<script language="javascript">
<!--
	function check_value1() {
		var numbers = document.regform1.JUMINNO.value;
		var strings = document.regform1.USERNM.value;

		if( numbers == ""){
			alert("주민등록번호를 입력해 주십시요.");
			document.regform1.JUMINNO.focus(); 	
			return;
		}
	
		if( strings == ""){
			alert("계좌소유주명을 입력하세요.");
			document.regform1.USERNM.focus(); 	
			return;
		}

		for(i=0;i<numbers.length;i++) {
			c = numbers.charAt(i);   
			if((c < '0' || c > '9')){
				alert("주민등록번호는 숫자입니다.");
				document.regform1.JUMINNO.value="";
				document.regform1.JUMINNO.focus();        
				return;        
			}
		}
		
		if( document.regform1.strBankCode.value == ""){
			alert("은행을 선택하세요..");
			return;
		}
		
	
		if( document.regform1.strAccountNo.value == ""){
			alert("계좌번호를 입력하세요..");
			document.regform1.strAccountNo.focus(); 	
			return;
		}
		
		regform1.submit();
	}
	
	function check_value2() {
		if( document.regform2.strBankCode.value == ""){
			alert("은행을 선택하세요..");
			return;
		}
	
		if( document.regform2.strAccountNo.value == ""){
			alert("계좌번호를 입력하세요..");
			document.regform2.strAccountNo.focus(); 	
			return;
		}
		
		if( document.regform2.USERNM.value == ""){
			alert("계좌소유주명을 입력하세요..");
			document.regform2.USERNM.focus(); 	
			return;
		}
	          	    
		regform2.submit();
	} 
	
	function check_value3() {

		if( document.regform3.strBankCode.value == ""){
			alert("은행을 선택하세요..");
			return;
		}
	
		if( document.regform3.strAccountNo.value == ""){
			alert("계좌번호를 입력하세요..");
			document.regform3.strAccountNo.focus(); 	
			return;
		}    
		regform3.submit();
	} 
	
-->
</script>
<body>
<table align="center" border="0" cellspacing="1" cellpadding="1" bgcolor="#EEEEEE" >
<form name="regform1" method="post" action="request.php">   <!--업체 전체 URL -->
<input type="hidden" name="service" value="1">				<!-- 계좌 소유주 확인 서비스 구분 -->
<input type="hidden" name="svcGbn"  value="4">				<!-- 업무구분 -->
<input type="hidden" name="svc_cls" value="">
	<tr>
		<td>
			<table width="100%" bgcolor="#FFFFFF" cellspacing="1" cellpadding="1" border="0">
				<tr height="50">
					<td align="center" bgcolor="#E3E3E3"><b>나이스평가정보 계좌확인서비스</b></td>
				</tr>
				<tr>
					<td bgcolor="#E3E3E3"><font color="blue">계좌 소유주 확인</font>  </td>
				</tr>
				<tr height="20">
					<td bgcolor="#E3E3E3">고객님의 계좌확인을 위하여 아래 정보를 정확하게 입력해 주십시오.</td>
				</tr>
				<tr>
					<td>
						<table  bgcolor="#E3E3E3" width="100%" cellpadding="1" cellspacing="1">
							<tr>
								<td>주민등록번호</td>
								<td><input type="password" name="JUMINNO" maxlength="13" size="13" value=""></td>
							</tr>
							<tr>
								<td>계좌소유주명</td>
								<td><input type="text" name="USERNM" maxlength="40" size="40" value=""></td>
							</tr>
							<tr>
								<td>은행명</td>
								<td>
									<select name="strBankCode">
										<option value="02">산업</option>
										<option value="03">기업</option>
										<option value="04">국민</option>
										<option value="05">외환</option>
										<option value="07">수협</option>
										<option value="08">수출입</option>
										<option value="10">농협</option>
										<option value="020">우리</option>
										<option value="21">신한</option>
										<option value="23">SC제일</option>
										<option value="25">하나</option>
										<option value="27">한국씨티</option>
										<option value="31">대구</option>
										<option value="32">부산</option>
										<option value="34">광주</option>
										<option value="35">제주</option>
										<option value="37">전북</option>
										<option value="39">경남</option>
										<option value="45">새마을금고</option>
										<option value="48">신협</option>
										<option value="50">상호저축은행</option>
										<option value="54">HSBC</option>
										<option value="71">우체국</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>계좌번호</td>
								<td><input type="text" name="strAccountNo" maxlength="25" size="25"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" >
						<input type="button" name="confirm" value="확인" onclick="javascript:check_value1();">
						<input type="button" name="cancel" value="취소" onclick="javascript:reset();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</form>
</table>
<br><br>
<table align="center" border="0" cellspacing="1" cellpadding="1" bgcolor="#EEEEEE" >
<form name="regform2" method="post" action="request.php">   <!--업체 전체 URL -->
<input type="hidden" name="service" value="2">				<!-- 계좌소유주 성명확인 서비스 구분 -->
<input type="hidden" name="svcGbn"  value="2">				<!-- 업무구분 -->			
<input type="hidden" name="svc_cls" value="">
	<tr>
		<td>
			<table width="100%" bgcolor="#FFFFFF" cellspacing="1" cellpadding="1" border="0">
				<tr height="50">
					<td align="center" bgcolor="#E3E3E3"><b>나이스평가정보 계좌확인서비스</b></td>
				</tr>
				<tr>
					<td bgcolor="#E3E3E3"><font color="blue">계좌 소유주 성명 확인</font>  </td>
				</tr>
				<tr height="20">
					<td bgcolor="#E3E3E3">고객님의 계좌-성명 확인을 위하여 아래 정보를 정확하게 입력해 주십시오.</td>
				</tr>
				<tr>
					<td>
						<table  bgcolor="#E3E3E3" width="100%" cellpadding="1" cellspacing="1">
							<tr>
								<td>은행명</td>
								<td>
									<select name="strBankCode">
										<option value="02">산업</option>
										<option value="03">기업</option>
										<option value="04">국민</option>
										<option value="05">외환</option>
										<option value="07">수협</option>
										<option value="08">수출입</option>
										<option value="10">농협</option>
										<option value="20">우리</option>
										<option value="21">신한</option>
										<option value="23">SC제일</option>
										<option value="25">하나</option>
										<option value="27">한국씨티</option>
										<option value="31">대구</option>
										<option value="32">부산</option>
										<option value="34">광주</option>
										<option value="35">제주</option>
										<option value="37">전북</option>
										<option value="39">경남</option>
										<option value="45">새마을금고</option>
										<option value="48">신협</option>
										<option value="50">상호저축은행</option>
										<option value="54">HSBC</option>
										<option value="71">우체국</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>계좌번호</td>
								<td><input type="text" name="strAccountNo" maxlength="25" size="25"></td>
							</tr>
							<tr>
								<td>계좌소유주명</td>
								<td><input type="text" name="USERNM" maxlength="40" size="40" value=""></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" >
						<input type="button" name="confirm" value="확인" onclick="javascript:check_value2();">
						<input type="button" name="cancel" value="취소" onclick="javascript:reset();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</form>
</table>
<br><br>
<table align="center" border="0" cellspacing="1" cellpadding="1" bgcolor="#EEEEEE" >
<form name="regform3" method="post" action="request.php">   <!--업체 전체 URL -->
<input type="hidden" name="service" value="3">				<!-- 계좌번호 유효성확인 서비스 구분 -->
<input type="hidden" name="svcGbn"  value="4">				<!-- 업무구분 -->
<input type="hidden" name="svc_cls" value="">
	<tr>
		<td>
			<table width="100%" bgcolor="#FFFFFF" cellspacing="1" cellpadding="1" border="0">
				<tr height="50">
					<td align="center" bgcolor="#E3E3E3"><b>나이스평가정보 계좌확인서비스</b></td>
				</tr>
				<tr>
					<td bgcolor="#E3E3E3"><font color="blue">계좌번호 유효성확인</font>  </td>
				</tr>
				<tr height="20">
					<td bgcolor="#E3E3E3">고객님의 계좌번호 유효성 확인을 위하여 아래 정보를 정확하게 입력해 주십시오.</td>
				</tr>
				<tr>
					<td>
						<table  bgcolor="#E3E3E3" width="100%" cellpadding="1" cellspacing="1">
							<tr>
								<td>은행명</td>
								<td>
									<select name="strBankCode">
										<option value="02">산업</option>
										<option value="03">기업</option>
										<option value="04">국민</option>
										<option value="05">외환</option>
										<option value="07">수협</option>
										<option value="08">수출입</option>
										<option value="10">농협</option>
										<option value="20">우리</option>
										<option value="21">신한</option>
										<option value="23">SC제일</option>
										<option value="25">하나</option>
										<option value="27">한국씨티</option>
										<option value="31">대구</option>
										<option value="32">부산</option>
										<option value="34">광주</option>
										<option value="35">제주</option>
										<option value="37">전북</option>
										<option value="39">경남</option>
										<option value="45">새마을금고</option>
										<option value="48">신협</option>
										<option value="50">상호저축은행</option>
										<option value="54">HSBC</option>
										<option value="71">우체국</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>계좌번호</td>
								<td><input type="text" name="strAccountNo" maxlength="25" size="25"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" >
						<input type="button" name="confirm" value="확인" onclick="javascript:check_value3();">
						<input type="button" name="cancel" value="취소" onclick="javascript:reset();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</form>
</table>
</body>
</html>
