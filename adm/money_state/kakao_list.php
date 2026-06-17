<?
/**
 * 관리자 > 예치금 관리
 */
$sub_menu = "500800";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') {
	alert('최고관리자만 접근 가능합니다.');
}

$sql_common = "A.*, B.mb_name
			   FROM cf_kakao_remit A left join g5_member B on(A.mb_no=B.mb_no) ";
//$sql_search = " WHERE 1=1 and send_result<>'' and send_result is not null";
if ($keyword) $sql_search = " WHERE 1=1 and send_result is not null";
else $sql_search = " WHERE 1=1 and send_result='SUCCESS' and send_result is not null";


$search_field = array('mb_id','sdate','edate','tid');
foreach ($_GET as $key => $val) {
	if ($key=='sdate' and $sdate)   $sql_search .= " and substring(A.insert_datetime,1,10) >= '$sdate' ";
	if ($key=='edate' and $edate)   $sql_search .= " and substring(A.insert_datetime,1,10) <= '$edate' ";
	if ($key=='keyword' and $keyword) $sql_search .= " and (A.mb_id like '%$keyword%' or B.mb_name like '%$keyword%' or A.tid like '%$keyword%' ) ";
}


$sql_order = " ORDER BY A.insert_datetime DESC ";

$sql = "SELECT COUNT(*) AS cnt, {$sql_common} {$sql_search} ";

$row = sql_fetch($sql);

$sum_sql = "select count(*) s_cnt, sum(A.sent_amount) s_amt from cf_kakao_remit A $sql_search";
$sum_res = sql_query($sum_sql);
$sum_row = sql_fetch_array($sum_res);

$total_count = $row['cnt'];
$rows = 20;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함
$num = $total_count - $from_record;


$sql = "
	SELECT {$sql_common}
	{$sql_search}
	{$sql_order}
LIMIT
	$from_record, $rows";

$result = sql_query($sql);
$list_count = $result->num_rows;

$page_total_point = 0;
for($i=0; $i<$list_count; $i++) {
	$LIST[$i] = sql_fetch_array($result);
}

$param = array();
foreach ((array)$_REQUEST as $key => $val) {
	if (empty($val) || in_array($key, array('page'))) {
		continue;
	}
	$param[] = $key.'='.$val;
}

$qstr = join('&amp;', $param);


$g5['title'] = '카카오 송금 내역';
include_once('../admin.head.php');
?>

	<div class="row">
		<div class="col-lg-12">
			<div class="col-sm-6" style="width:90%;">
					<form method="get" class="form-horizontal">
					<ul class="list-inline">
						<li>처리일</li>
						<li>
							<div class="form-inline">
								<input type="text" id="sdate" name="sdate" value="<? echo ($_GET["sdate"]) ? $_GET["sdate"] : $_GET["po_datetime"];?>" class="form-control input-sm datepicker" autocomplete="off" placeholder="대상일자(시작)"> ~
								<input type="text" id="edate" name="edate" value="<? echo $_GET["edate"];?>" class="form-control input-sm datepicker" autocomplete="off" placeholder="대상일자(종료)">
							</div>
						</li>
						<li style="margin-left:20px;">
							검색어(id,이름)
						</li>
						<li>
							<input type="text" name="keyword" value="<?=$_GET['keyword']?>" class="form-control input-sm" style="display:inline;">
						</li>
						<li style="margin-left:20px;">
							<button type="submit" class="btn btn-primary btn-sm">검색</button>
						</li>
						<li>
							<button type="button" id="excelDownTokakao" class="btn btn-sm btn-success" style="width:120px;margin-left:20px;">엑셀 다운로드</button>
						</li>
						<li style="margin-left:120px;">
							총
							<?=number_format($sum_row['s_cnt'])?> 건
							&nbsp;&nbsp;&nbsp;&nbsp;
							<?=number_format($sum_row['s_amt'])?> 원
						</li>
					</ul>
				</form>
			</div>

			<div class="panel-body" style="clear:both">
				<form id="frmDownload" name="frmDownload" method="get" class="form-horizontal">
					<input type="hidden" name="sdate" value="<? echo ($_GET["sdate"]) ? $_GET["sdate"] : $_GET["po_datetime"];?>">
					<input type="hidden" name="edate" value="<? echo $_GET["edate"];?>">
					<input type="hidden" name="keyword" value="<?=$_GET['keyword']?>">
					<div class="dataTable_wrapper">
						<table class="table table-striped table-bordered table-hover table-condensed" style="font-size:14px">
							<thead>
								<tr class="bg-primary">
									<th class="text-center"><input type="checkbox" name="chkall" id="chkall" value="1"></th>
									<th class="text-center">NO.</th>
									<th class="text-center">ID</th>
									<th class="text-center">이름</th>
									<th class="text-center">결과</th>
									<th class="text-center">송금액</th>
									<th class="text-center">처리시간</th>
									<th class="text-center">TID</th>
									<th class="text-center">실시간 조회</th>
								</tr>
							</thead>
							<tbody>
<?
for ($i=0; $i<$list_count; $i++) {

	$print_member_type = '';
	switch($LIST[$i]['member_type']) {
		case '1' : $print_member_type = '개인'; $fcolor = "royalblue"; break;
		case '2' : $print_member_type = '법인'; $fcolor = "red";	   break;
		case '3' : $print_member_type = 'SNS';  $fcolor = "green";	 break;
	}
	if($LIST[$i]['is_creditor']=='Y') $print_member_type.= '-대부';

	if($LIST[$i]['member_type']=='2') {
		$print_mb_name = $LIST[$i]['mb_co_name'];
	}
	else {
		$print_mb_name = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_name'] : hanStrMasking($LIST[$i]['mb_name']);
	}

	$print_flag = ($LIST[$i]["po_point"] > 0) ? '<label class="label label-success">충전</label>' : '<label class="label label-info">차감</label>';
?>
								<tr class="odd">
									<td align="center"><input type="checkbox" name="chk[]" value="<? echo $LIST[$i]['po_id']?>"></td>
									<td align="center"><?=$num?></td>
									<td align="center"><a href="/adm/member/member_view.php?&mb_id=<? echo $LIST[$i]['mb_id']?>"><?=$LIST[$i]['mb_id']?></a></td>
									<td align="center"><a href="/adm/member/member_view.php?&mb_id=<? echo $LIST[$i]['mb_id']?>"><?=$print_mb_name?></a></td>
									<td align="center"><?=$LIST[$i]['send_result']?></td>
									<td style="text-align:right;padding-right:30px;"><?=number_format($LIST[$i]['sent_amount']);?> 원</td>
									<td align="center"><?=$LIST[$i]['insert_datetime']?></td>
									<td align="center"><?=$LIST[$i]['tid']?></td>
									<td align="center">
										<a onclick="go_kakao_check('<?=$LIST[$i]['token']?>')" class="btn btn-sm btn-default" <?=!$LIST[$i]['token'] ? "disabled":""?> >결과 조회</a>
									</td>
								</tr>
<?
	$num--;
}
?>


							</tbody>
						</table>
					</div>
				</form>
			</div>

			<? echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');?>
		</div>
	</div>

	<script type="text/javascript">
	$('#excelDownTokakao').click(function() {
		if(confirm('검색결과를 엑셀시트로 다운로드 받으시겠습니까?')) {
			var f = document.frmDownload;
			f.method = 'get';
			f.target = 'axFrame';
			f.action = 'kakao_list_download.php';
			f.submit();
		}
	});

	function go_kakao_check(token) {
		if (!token) return;
		window.open("./kakao_check.php?token="+token, "kakao_remit_check", "left=10,top=10,width=500,height=400");
	}

		$(function() {
			$("input[name=chkall]").click(function() {
				$("input[name='chk[]']").prop('checked', this.checked);
			});
		});

		$('input:checkbox[name="po_point_use_type"]').on('change', function() {
			$('input:checkbox[name="po_point_use_type"]').not(this).prop('checked', false);
		});

		$("#submit1").on('click', function() {
			f = document.point_form;
			if(f.member_select.value=='') { alert('회원범위를 선택하십시요.'); f.member_select.focus(); }
			else if(f.balance.value=='') { alert('금액을 입력하십시요.'); f.balance.focus(); }
			else if(f.balance_select.value=='') { alert('지급 또는 차감 선택하십시요.'); f.balance_select.focus(); }
			else {
				if(confirm(' 실행 하시겠습니까? ')) {
					f.method = 'post';
					f.action = 'register_process.php';
					//f.submit();
				}
			}
		});
	</script>

<? include_once ('../admin.tail.php'); ?>