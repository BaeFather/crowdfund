<?php
$sub_menu = '500500';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "가상계좌입금내역 (세틀뱅크)";
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

/* 검색 필드 조합 START */

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) {
    $$_GET[$k] = $v;
}

if($sdate) $_sdate = preg_replace("/-/", "", $sdate);
if($edate) $_edate = preg_replace("/-/", "", $edate);
if($_sdate && $_edate) {
    if($_sdate > $_edate) alert("일자검색조건이 정상적이지 않습니다.");
}

if(!$inp_st) $inp_st = 1;

$sql_search = " 1=1 ";
//$sql_search.= " AND A.org_cd > 0 ";
//$sql_search.= " AND B.mb_no > 1";
$sql_search.= ($inp_st) ? " AND A.inp_st='$inp_st'" : " AND A.inp_st='1'";
if($iacct_no) $sql_search.= " AND A.iacct_no='$iacct_no'";
if($bank_cd)  $sql_search.= " AND A.bank_cd='$bank_cd'";
if($_sdate)   $sql_search.= " AND A.tr_il>='".$_sdate."'";
if($_edate)   $sql_search.= " AND A.tr_il<='".$_edate."'";
if($key_search && $keyword) $sql_search .= " AND {$key_search} LIKE '%{$keyword}%' ";

/*$sql = "
	SELECT
		COUNT(A.org_cd) AS cnt,
		SUM(tr_amt) AS total_amount
	FROM
		vacs_ahst A
	LEFT JOIN
		g5_member B
	ON
		A.iacct_no = B.virtual_account
	WHERE
		$sql_search";*/
$sql = "
SELECT
  COUNT(A.org_cd) AS cnt,
  SUM(A.tr_amt) AS total_amount
FROM
  (SELECT mb_no, mb_name, virtual_account FROM g5_member) B LEFT JOIN vacs_ahst A ON A.iacct_no = B.virtual_account
WHERE
  $sql_search";

$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_amount = $row['total_amount'];

$rows = 50;
//$rows = $config['cf_page_rows'];

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

/*$sql = "
	SELECT
		A.tr_il, A.tr_si, A.bank_cd, A.iacct_no, A.iacct_nm, A.tr_amt, A.tr_no, A.inp_st, A.iorg_cd, A.media_gb,
		B.mb_id, B.mb_name, B.mb_co_name, B.member_type
	FROM
		vacs_ahst A
	LEFT JOIN
		g5_member B
	ON
		A.iacct_no = B.virtual_account
	WHERE
		$sql_search
	ORDER BY
		A.tr_il DESC, A.tr_si DESC
	LIMIT
		$from_record, $rows";*/
$sql = "
SELECT
      A.tr_il, A.tr_si, A.bank_cd, A.iacct_no, A.iacct_nm, A.tr_amt, A.tr_no, A.inp_st, A.iorg_cd, A.media_gb,
	  B.mb_id, B.mb_name, B.mb_co_name, B.member_type
FROM
  (SELECT mb_id, mb_name, member_type, mb_co_name, mb_no, virtual_account FROM g5_member) B LEFT JOIN vacs_ahst A ON A.iacct_no = B.virtual_account
WHERE
		$sql_search
	ORDER BY
		A.tr_il DESC, A.tr_si DESC
	LIMIT
		$from_record, $rows";
$result = sql_query($sql);
$rcount = sql_num_rows($result);

$page_total_amount = 0;
for($i=0; $i<$rcount; $i++) {
    $LIST[$i] = sql_fetch_array($result);
    $page_total_amount+= $LIST[$i]['tr_amt'];
}

$num = $total_count - $from_record;

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js

?>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <script src="js/jquery-ui.min.js"></script>

    <style>
        .btn_area {
            text-align:left;
        }
    </style>

    <div class="tbl_head02 tbl_wrap">

        <!-- 검색영역 START -->
        <div>
            <form id="member_list_frm" method="get" class="form-horizontal" style="margin:0;">
                <div class="form-group">
                    <ul class="col-sm-10 list-inline" style="margin-bottom: 4px;">
                        <li>
                            <input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control datepicker" placeholder="입금일(시작)">
                        </li>
                        <li>~</li>
                        <li>
                            <input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control datepicker" placeholder="입금일(종료)">
                        </li>
                        <li>까지</li>
                        <li>|</li>
                        <li>
                            <select name="inp_st" class="form-control">
                                <option value="1" <?=($inp_st=='1')?'selected':''?>>입금</option>
                                <option value="2" <?=($inp_st=='2')?'selected':''?>>취소</option>
                                <option value="3" <?=($inp_st=='3')?'selected':''?>>정산</option>
                            </select>
                        </li>
                        <li>
                            <select name="bank_cd" class="form-control">
                                <option value="">가상계좌은행</option>
                                <?
                                $VBANK_ARR = array_keys($VBANK);
                                for($i=0; $i<count($VBANK); $i++) {
                                    $selected = ($bank_cd==$VBANK_ARR[$i]) ? "selected" : "";
                                    echo "<option value='".$VBANK_ARR[$i]."' $selected>".$VBANK[$VBANK_ARR[$i]]."</option>\n";
                                }
                                ?>
                            </select>
                        </li>
                    </ul>
                    <ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
                        <li>
                            <select name="key_search" class="form-control">
                                <option value="">검색항목</option>
                                <option value="B.mb_id" <?=($key_search=='B.mb_id')?'selected':''; ?>>아이디</option>
                                <option value="B.mb_name" <?=($key_search=='B.mb_name')?'selected':''; ?>>성명/담당자명</option>
                                <option value="A.iacct_no" <?=($key_search=='A.iacct_no')?'selected':''; ?>>가상계좌번호</option>
                            </select>
                        </li>
                        <li><input type="text" class="form-control" name="keyword" size="30" value="<?=$keyword?>" placeholder="키워드"></li>
                        <li><input type="submit" class="btn btn-primary" value="검색" onclick="form_change();"></li>
                    </ul>
                </div>
            </form>
        </div>

        <!-- 검색영역 E N D -->

        <!-- 리스트 START -->
        <table class="table_hover">
            <caption><?=$g5['title']?> 목록</caption>
            <thead>
            <tr>
                <th scope="col" style="text-align:center;">번호</th>
                <th scope="col" style="text-align:center;">법인명</th>
                <th scope="col" style="text-align:center;">아이디</th>
                <th scope="col" style="text-align:center;">이름</th>
                <th scope="col" style="text-align:center;">가상계좌은행</th>
                <th scope="col" style="text-align:center;">가상계좌번호</th>
                <th scope="col" style="text-align:center;">입금액</th>
                <th scope="col" style="text-align:center;">상태</th>
                <th scope="col" style="text-align:center;">처리은행</th>
                <th scope="col" style="text-align:center;">입금일시</th>
                <th scope="col" style="text-align:center;">단일내역</th>
            </tr>
            </thead>
            <tbody>
            <tr bgcolor="#EEEEFF">
                <td align="center"><span style="color:brown">전체</span></td>
                <td></td>
                <td align="center"><span style="color:brown"><?=number_format($total_count)?>건</span></td>
                <td></td>
                <td></td>
                <td align="right"><span style="color:#FF0000"><?=number_format($total_amount);?>원</span></td>
                <td align="right"><span style="color:brown">현재 페이지합계 : <?=number_format($page_total_amount);?>원</span></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php
            $list_count = count($LIST);

            if($list_count > 0) {
                for ($i=0; $i<$list_count; $i++) {

                    if($LIST[$i]['member_type']=='2') {
                        $mb_co_name = $LIST[$i]['mb_co_name'];
                    }
                    else {
                        $mb_co_name = ($LIST[$i]['member_type']=='3') ? '<span style="color:#ccc">SNS회원</span>' : '<span style="color:#ccc">개인회원</span>';
                    }

                    switch($LIST[$i]['inp_st']) {
                        case 1  : $state_txt = '입금';		break;
                        case 2  : $state_txt = '<font style="color:#FF2222">취소</font>';		break;
                        case 3  : $state_txt = '정산';		break;
                        default : $state_txt = 'Unknown';		break;
                    }

                    $tr_style = ($LIST[$i]['inp_st']=='2') ? "background-color:#FFEEEE" : "";

                    $trans_date = substr($LIST[$i]['tr_il'], 0, 4)."-".substr($LIST[$i]['tr_il'], 4, 2)."-".substr($LIST[$i]['tr_il'], 6, 2);
                    $trans_date.= " ".substr($LIST[$i]['tr_si'], 0, 2).":".substr($LIST[$i]['tr_si'], 2, 2);

                    // 환경설정에 등록되지 않은 은행코드의 표기 처리
                    if($LIST[$i]['iorg_cd']=='006') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">국민 (구 주택은행)</font>';
                    else if($LIST[$i]['iorg_cd']=='009') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">수협중앙회</font>';
                    else if($LIST[$i]['iorg_cd']=='010') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">농협은행</font>';
                    else if($LIST[$i]['iorg_cd']=='013') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">지역 농축협</font>';
                    else if($LIST[$i]['iorg_cd']=='014') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">지역 농축협</font>';
                    else if($LIST[$i]['iorg_cd']=='017') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">지역 농축협</font>';
                    else if($LIST[$i]['iorg_cd']=='018') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">지역 농축협</font>';
                    else if($LIST[$i]['iorg_cd']=='019') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">국민 (구 대동은행)</font>';
                    else if($LIST[$i]['iorg_cd']=='021') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">신한은행</font>';
                    else if($LIST[$i]['iorg_cd']=='026') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">신한 (구 조흥은행)</font>';
                    else if($LIST[$i]['iorg_cd']=='072')  $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">우체국</font>';
                    else if($LIST[$i]['iorg_cd']=='080') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">KEB하나은행</font>';
                    else if($LIST[$i]['iorg_cd']=='084') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">우리은행</font>';
                    else if($LIST[$i]['iorg_cd']=='046') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">새마을금고중앙회</font>';
                    else if($LIST[$i]['iorg_cd']=='047') $BANK[$LIST[$i]['iorg_cd']] = '<font style="color:#aaa">신협중앙회</font>';

                    ?>
                    <tr style="<?=$tr_style?>">
                        <td align="center"><?=number_format($num);?></td>
                        <td align="center"><?=$mb_co_name?></td>
                        <td align="center"><a href="/adm/member/member_view.php?&mb_id=<?=urlencode($LIST[$i]['mb_id'])?>"><?=$LIST[$i]['mb_id']?></a></td>
                        <td align="center"><a href="vact_log.php?key_search=B.mb_name&keyword=<?=urlencode($LIST[$i]['mb_name'])?>"><?=$LIST[$i]['mb_name']?></a></td>
                        <td align="center"><a href="vact_log.php?bank_cd=<?=urlencode($LIST[$i]['bank_cd'])?>"><?=$VBANK[$LIST[$i]['bank_cd']]?></a></td>
                        <td align="center"><a href="vact_log.php?key_search=A.iacct_no&keyword=<?=urlencode($LIST[$i]['iacct_no'])?>"><?=$LIST[$i]['iacct_no']?></a></td>
                        <td align="right"><?=number_format($LIST[$i]['tr_amt'])?>원</td>
                        <td align="center"><?=$state_txt?></td>
                        <td align="center"><?=$BANK[$LIST[$i]['iorg_cd']]?></td>
                        <td align="center"><?=$trans_date?></td>
                        <td align="center"><button onClick="location.href='vact_log.php?key_search=B.mb_id&keyword=<?=urlencode($LIST[$i]['mb_id'])?>'" class="btn btn-sm btn-default">내역보기</button></td>
                    </tr>
                    <?php
                    $num--;
                }
            }else {
                ?>

                <tr>
                    <td colspan="15" align="center" height="300px";>검색된 데이터가 없습니다.</td>
                </tr>

                <?php
            }
            ?>
        </table>
        <!-- 리스트 E N D -->

    </div>

<?php
$qstr = preg_replace("/&page=[0-9]/", "", $_SERVER['QUERY_STRING']);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");
?>


<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>