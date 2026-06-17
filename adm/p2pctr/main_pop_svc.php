<?
include_once('./_common.php');
auth_check($auth[$sub_menu], "w");

$g5['title'] = '중앙기록관리 - 실서비스';

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<?
$sql = "SELECT idx, recruit_amount, loan_register_id, goods_id, loan_contract_id, title FROM cf_product WHERE idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

//$invr_sql = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest_detail WHERE product_idx='$product_idx' AND investment_register_id<>'' AND invest_state<>'N' ";
$invr_sql = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest WHERE product_idx='$product_idx' AND investment_register_id<>'' AND invest_state='Y' ";
$invr_res = sql_query($invr_sql);
$invr_row = sql_fetch_array($invr_res);

//$invr_sql2 = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest_detail WHERE product_idx='$product_idx' AND contract_id<>'' AND invest_state<>'N' ";
$invr_sql2 = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest WHERE product_idx='$product_idx' AND contract_id<>'' AND invest_state='Y' ";
$invr_res2 = sql_query($invr_sql2);
$invr_row2 = sql_fetch_array($invr_res2);

$bill_table = getBillTable($product_idx);
$bt_sql = "SELECT turn, turn_sno, repay_date, bill_date FROM $bill_table WHERE product_idx='$product_idx' 
			GROUP BY turn, turn_sno
			ORDER BY IF(turn_sno=0,repay_date,bill_date) ASC";
$bt_res = sql_query($bt_sql);
$bt_cnt = sql_num_rows($bt_res);
$BILL_LIST = array();

$p_sql = "SELECT * FROM p2pctr_product WHERE product_idx='$product_idx'";
$p_row = sql_fetch($p_sql);

$tot_turn = 1;

for ($i=0 ; $i<$bt_cnt ; $i++) {

	$bt_row = sql_fetch_array($bt_res);

	$gsql = "SELECT date,turn,turn_sno,sum(principal) FROM cf_product_give WHERE product_idx='$product_idx' AND turn='$bt_row[turn]' AND turn_sno='$bt_row[turn_sno]'";
	$grow = sql_fetch($gsql);


	$BILL_LIST[$i]["total_turn"] = $tot_turn++;
	$BILL_LIST[$i]["turn"] = $bt_row["turn"];
	$BILL_LIST[$i]["turn_sno"] = $bt_row["turn_sno"];
	if ($grow["date"]) $BILL_LIST[$i]["repay_date"] = $grow["date"];
	else $BILL_LIST[$i]["repay_date"] = $bt_row["repay_date"];
	//$BILL_LIST[$i]["repay_date"] = $bt_row["repay_date"];
	$BILL_LIST[$i]["p2pctr"]="N";
	$BILL_LIST[$i]["p2pctrG"]=0;

	$susql = "SELECT * FROM cf_product_success WHERE product_idx='$product_idx' AND turn='$bt_row[turn]' AND turn_sno='$bt_row[turn_sno]'";
	$sures = sql_query($susql);
	$sucnt = sql_num_rows($sures);
	if ($sucnt) {
		$surow = sql_fetch_array($sures);
		if ($surow["p2pCtr_date"]) $BILL_LIST[$i]["p2pctr"]="Y";
	} 

	$gvsql = "SELECT count(*) gcnt FROM cf_product_give WHERE product_idx='$product_idx' AND turn='$bt_row[turn]' AND turn_sno='$bt_row[turn_sno]' AND p2pCtr_date<>''";
	$gvres = sql_query($gvsql);
	$gvrow = sql_fetch_array($gvres);
	$BILL_LIST[$i]["p2pctrG"] = $gvrow["gcnt"];

}
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$product_idx?> <?=$row["title"]?></h3>

<div style="text-align:right;margin-bottom:5px;">

<? if ($p_row["memo"]) { ?>
<div style="text-align:left;">
	<pre><?=$p_row["memo"]?></pre>
</div>
<? } ?>

<input type="button" class="btn btn-sm btn-default" onclick="go_log('<?=$product_idx?>');" value="로그 보기"/></a></div>
<table class="table table-bordered table-condensed">
	<tr>
		<th style="width:120px;">대출신청</th>
		<td style="text-align:center;">
		<?
		if ($row["loan_register_id"]) {
			?>	
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_regist('<?=$product_idx?>');" value="<?=$row[loan_register_id]?>"/>
			&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_regist_mod('<?=$product_idx?>');" value="신청 취소"/>
			<?
		} else {
			?>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_loan_regist('<?=$product_idx?>');" value="기록"/>
			<?
		}
		?>
		</td>
		<td>공시후</td>
	</tr>
	<tr>
		<th>상품모집</th>
		<td style="text-align:<?=$row['goods_id']?'left':'center';?>;">
		<?
		if ($row["goods_id"]) {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_goods_regist('<?=$product_idx?>');" value="<?=$row["goods_id"]?>"/>	
			&nbsp;&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-success" onclick="go_goods_regist_srch('<?=$product_idx?>');" value="상품조회" />
			&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-warning" onclick="go_goods_mod2('<?=$row['loan_contract_id']?>');" value="모집정보 수정" />
			<input type="button" class="btn btn-sm btn-warning" onclick="go_goods_mod('<?=$row['loan_contract_id']?>','canc');" value="모집취소" />
			<?
		} else {
			?>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_goods_regist('<?=$product_idx?>');" value="기록"/>
			<?
		}
		?>
		</td>
		<td>공시후</td>
	</tr>
	<tr>
		<th>상품설명서</th>
		<td style="text-align:center;">
			<input type="button" class="btn btn-sm btn-warning" onclick="go_file_reg('<?=$product_idx?>');" value="기록"/>
		</td>
	</tr>
	<tr>
		<th>투자신청</th>
		<td style="text-align:center;">
			<? $txt1 = substr($invr_row["invr_amount"],0,-4)." / ". substr($row["recruit_amount"],0,-4); ?>
			<input type="button" class="btn btn-sm btn-<?=$invr_row["invr_amount"]==$row["recruit_amount"]?'default':'warning'?>" onclick="go_invest_regist('<?=$product_idx?>');" value="<?=$txt1?>"/>
			<? if (($invr_row["invr_amount"] AND $row["recruit_amount"] AND $invr_row["invr_amount"]<>$row["recruit_amount"]) or $product_idx=="6917") { ?>
			&nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-sm btn-warning" onclick="go_invest_regist_imsi('<?=$product_idx?>');" value="이관(임시)"/>
			<? } ?>
		</td>
		<td>모집완료후</td>
	</tr>
	<tr>
		<th>상품모집 갱신</th>
		<td style="text-align:center;">
			<input type="button" class="btn btn-sm btn-default" onclick="go_goods_mod('<?=$row['loan_contract_id']?>', 'end');" value="모집완료" />
		</td>
		<td>모집완료후</td>
	</tr>
	<tr>
		<th>대출계약</th>
		<td style="text-align:center;">
		<?
		if ($row["loan_contract_id"]) {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_contract('<?=$row['loan_contract_id']?>');" value="<?=$row["loan_contract_id"]?>"/>	
			&nbsp;&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-success" onclick="go_loan_contract_srch('<?=$row['loan_contract_id']?>');" value="대출계약조회" />
			<?
		} else {
			?>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_loan_contract('<?=$row['loan_contract_id']?>');" value="기록"/>
			&nbsp;&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-success" onclick="go_loan_contract_srch('<?=$row[loan_contract_id]?>');" value="대출계약조회" />
			<?
		}
		?>
		</td>
		<td>기표실행후</td>
	</tr>
	<tr>
		<th>투자계약</th>
		<td style="text-align:center;">
			<form name=fi>
			<? $txt2 = substr($invr_row2["invr_amount"],0,-4)." / ". substr($row["recruit_amount"],0,-4); ?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_invest_contract('<?=$product_idx?>');" value="<?=$txt2?>" />
			<?= $invr_row2["invr_cnt"];?> / <?=$invr_row["invr_cnt"];?>
			<? if ($invr_row2["invr_amount"] AND $row["recruit_amount"] AND $invr_row2["invr_amount"]<>$row["recruit_amount"]) { ?>
			&nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-sm btn-warning" onclick="go_invest_contract_imsi('<?=$product_idx?>');" value="이관(임시)" />
			<? } ?>
<?
$sqlinv = "SELECT A.*, B.mb_name FROM cf_product_invest A 
			 LEFT JOIN g5_member B ON(A.member_idx=B.mb_no)
			WHERE A.product_idx='$product_idx' AND invest_state='Y'";
$resinv = sql_query($sqlinv);
$cntinv = $resinv->num_rows;
$no = $cntinv;
?>
			&nbsp;&nbsp;&nbsp;
			<select name=inv_list class="input-sm">
			<? for ($m=0 ; $m<$cntinv; $m++) { $rowinv= sql_fetch_array($resinv); ?>
				<option value="<?=$rowinv['contract_id']?>"><?=$no--?> <?=$rowinv["member_idx"]?> <?=$rowinv["mb_name"]?> <?=$rowinv["contract_id"]?"O":"X";?> </option>
			<? } ?>
			</select>
			&nbsp;&nbsp;
			<input type="button" class="btn btn-success btn-default" onclick="go_srch_invest();" value="조회"/>
			</form>
		</td>
		<td>기표실행후</td>
	</tr>
	<tr>
		<th>
			대출상환 기록<br/>
			<!--input type="button" class="btn btn-sm btn-default" onclick="go_loan_repayment_update();" value="대출상환 예정정보 갱신"/><br/><br/-->
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_repayment_update2();" value="상환 예정정보 갱신"/>
		</th>
<? $bnum=5; ?>
		<td style="text-align:<?=count($BILL_LIST)<=$bnum?'center':'left'?>; vertical-align:middle;">
			<!--input type="button" class="btn btn-sm btn-default" onclick="go_loan_repayment();" value="기록" /-->
			<? for ($i=0 ; $i<count($BILL_LIST) ; $i++) { ?> 
					<? if ($i%$bnum==0 and $i<>0) echo "<br/>"; ?>
					<? if ($BILL_LIST[$i]["p2pctr"]=="Y") { ?>
					<input type="button" class="btn btn-sm btn-default" style="width:110px; cursor:default;margin-bottom:5px;" value="<?=$BILL_LIST[$i]['total_turn']?>&nbsp;&nbsp;<?=$BILL_LIST[$i]['repay_date']?>" >
					<? } else { ?>
					<input type="button" class="btn btn-sm btn-warning" style="width:110px; margin-bottom:5px;" onclick="go_loan_repayment(<?=$BILL_LIST[$i]['turn']?>,<?=$BILL_LIST[$i]['turn_sno']?>);" value="<?=$BILL_LIST[$i]['total_turn']?>&nbsp;&nbsp;&nbsp; <?=$BILL_LIST[$i]['repay_date']?>" >
					<? } ?>
			<? } ?>
		</td>
		<td>이자<br/>배분후</td>
	</tr>
	<tr>
		<th>원리금지급</th>
		<td style="text-align:<?=count($BILL_LIST)<=$bnum?'center':'left'?>;">
			<? for ($i=0 ; $i<count($BILL_LIST) ; $i++) { ?> 
				<? if ($i%$bnum==0 and $i<>0) echo "<br/>"; ?>
				<? if ($BILL_LIST[$i]["p2pctrG"]>=$invr_row["invr_cnt"]) { ?>
				<input type="button" class="btn btn-sm btn-default" style="width:110px; margin-bottom:5px; padding:5px 5px;" onclick="go_invest_pay(<?=$BILL_LIST[$i]['turn']?>, <?=$BILL_LIST[$i]['turn_sno']?>);" value="<?=$BILL_LIST[$i]['turn']?>&nbsp;&nbsp;<?=$BILL_LIST[$i]['repay_date']?> <?//=$BILL_LIST[$i]['p2pctrG']?> <?//=$invr_row['invr_cnt']?>" />
				<? } else { ?>
				<input type="button" class="btn btn-sm btn-warning" style="width:110px; margin-bottom:5px; padding:5px 5px;" onclick="go_invest_pay(<?=$BILL_LIST[$i]['turn']?>, <?=$BILL_LIST[$i]['turn_sno']?>);" value="<?=$BILL_LIST[$i]['turn']?>&nbsp;<?=$BILL_LIST[$i]['repay_date']?>"&nbsp;&nbsp;<?=$BILL_LIST[$i]['p2pctrG']?>/<?=$invr_row['invr_cnt']?>" />
				<? } ?>
			<? } ?>
		</td>
		<td>이자<br/>배분후</td>
	</tr>
	<tr>
		<th>대출계약 갱신</th>
		<td style="text-align:center;">
			<input type="button" class="btn btn-sm btn-default" onclick="loan_contract_mod('<?=$product_idx?>');" value="상환완료" />
		</td>
		<td>대출종료후</td>
	</tr>
	<tr>
		<th>투자계약 갱신</th>
		<td style="text-align:center;">
		<? if ($p_row["p2pctr_end"]=="Y") { ?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_contract_end('<?=$product_idx?>');" value="종료" />
		<? } else { ?>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_contract_end('<?=$product_idx?>');" value="미종료" />
		<? } ?>
		</td>
		<td>대출종료후</td>
	</tr>
</table>
<div style="margin-top:-15px;">
<font style="color:red; font-weight:bold; ">※ 상품모집중 상태에서 모집취소를 하면 대출신청기록은 자동 취소되고 투자한도가 복원됩니다.</font>
</div>
</div>

<script>
// 로그보기
function go_log(product_idx) {
	window.open("/adm/p2pctr/view_log.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 대출신청 기록
function go_loan_regist(product_idx) {  
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/loan_register_svc.php?product_idx=<?=$product_idx?>","","width=800, height=850");
}

// 대출신청 갱신 (취소)
function go_loan_regist_mod(product_idx) {  
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/loan_register_mod_svc.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}


// 상품모집 기록
function go_goods_regist(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/goods_register_svc.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}


// 상품조회
function go_goods_regist_srch(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/goods_register_srch_svc.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 상품설명서 기록
function go_file_reg(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	
	window.open("/adm/p2pctr/goods_file_register_svc.php?product_idx="+product_idx,"","width=800, height=800");
}

// 투자신청 기록
function go_invest_regist(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}

	window.open("/adm/p2pctr/invest_register_svc.php?product_idx=<?=$product_idx?>","","width=1300, height=800");
}

// 투자신청 기록 이관(임시)
function go_invest_regist_imsi(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}

	window.open("/adm/p2pctr/invest_register_svc.php?imsi=Y&product_idx=<?=$product_idx?>","","width=1300, height=800");
}

// 상품모집 갱신
function go_goods_mod(product_idx, mod_stat) {
	//var alt_msg = "";
	//if (mod_stat=="canc") alt_msg="상품모집을 취소 하시겠습니까?";
	//else if (mod_stat=="end") alt_msg="상품모집을 모집완료로 갱신하시겠습니까?";

	//var yn = confirm(alt_msg);
	//if (!yn) return;

	window.open("/adm/p2pctr/goods_mod_svc.php?product_idx=<?=$product_idx?>&mod_stat="+mod_stat,"","width=800, height=800");
}

// 새로 추가된 상품 모집정보 갱신
function go_goods_mod2(product_idx) {
	alert("2021-11 API 신규로 추가되어서\n작업 해야함");
	return;

	window.open("/adm/p2pctr/goods_mod2_svc.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}


// 대출계약 기록
function go_loan_contract() {
	window.open("/adm/p2pctr/loan_contract_svc.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 투자계약 기록
function go_invest_contract(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/invest_contract_svc.php?product_idx=<?=$product_idx?>","","width=1300, height=800");
}

// 투자계약 기록 이관(임시)
function go_invest_contract_imsi(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/invest_contract_svc.php?imsi=Y&product_idx=<?=$product_idx?>","","width=1300, height=800");
}

// 투자계약 조회
function go_srch_invest(contract_id) {
	
	var f = document.fi;
	var contract_id = f.inv_list.value;

	if (!contract_id) {
		alert("투자계약 ID 가 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/invest_register_srch_svc.php?contract_id="+contract_id,"","width=800, height=800");
}

// 대출계약 조회
function go_loan_contract_srch(loan_contract_id) {
	if (!loan_contract_id) {
		alert("대출계약 ID 가 없습니다.");
		return;
	}
	window.open("/adm/p2pctr/loan_contract_srch_svc.php?loan_contract_id="+loan_contract_id,"","width=800, height=800");
}


// 대출상환 예정정보 갱신
function go_loan_repayment_update() {
	window.open("/adm/p2pctr/loan_repayment_update_svc.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 대출상환 예정정보 갱신 신규
function go_loan_repayment_update2() {
	window.open("/adm/p2pctr/loan_repayment_update_svc2.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 대출상환 기록
function go_loan_repayment(turn, turn_sno) {
	window.open("/adm/p2pctr/loan_repayment_svc.php?product_idx=<?=$product_idx?>&turn="+turn+"&turn_sno="+turn_sno,"","width=800, height=800");
}

// 원리금지급 기록
function go_invest_pay(turn , turn_sno) {
	window.open("/adm/p2pctr/investments_payment_svc.php?product_idx=<?=$product_idx?>&turn="+turn+"&turn_sno="+turn_sno,"","width=800, height=800");
}

// 대출계약 상태갱신
function loan_contract_mod(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}

	window.open("/adm/p2pctr/loan_contract_mod_svc.php?product_idx="+product_idx,"","width=800, height=800");
}

// 투자계약 상태갱신
function go_contract_end(product_idx) {
	if (!product_idx) {
		alert("품번이 없습니다.");
		return;
	}

	window.open("/adm/p2pctr/invest_contract_mod_svc.php?product_idx="+product_idx,"","width=800, height=800");
}
</script>