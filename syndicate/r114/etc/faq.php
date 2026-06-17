<?
include_once('./_common.php');

// FAQ MASTER
$faq_master_list = array();
$sql    = "SELECT * FROM {$hf['faq_master_table']} ORDER BY fm_order,fm_id ";
$result = sql_query($sql);
while($row=sql_fetch_array($result)) {
	$key = $row['fm_id'];
	if (!$fm_id) $fm_id = $key;
	$faq_master_list[$key] = $row;
}

if($fm_id){
	$qstr.= '&amp;fm_id=' . $fm_id; // 마스터faq key_id
}

$fm = $faq_master_list[$fm_id];
if (!$fm['fm_id'])
    alert('등록된 내용이 없습니다.');

$g5['title'] = $fm['fm_subject'];

if(G5_IS_MOBILE) {
	$skin_file = HF_PATH."/view/faq.skin.m.php";
}
else {
	$skin_file = HF_PATH."/view/faq.skin.php";
}

include_once(HF_PATH.'/hf_head.php');

if(is_file($skin_file)) {
	$admin_href = '';
	$himg_src = '';
	$timg_src = '';

	if($is_admin) $admin_href = G5_ADMIN_URL.'/faqmasterform.php?w=u&amp;fm_id='.$fm_id;

	if(!G5_IS_MOBILE) {
		$himg = HF_DATA_PATH.'/faq/'.$fm_id.'_h';
		if(is_file($himg)){
			$himg_src = HF_DATA_URL.'/faq/'.$fm_id.'_h';
		}

		$timg = HF_DATA_PATH.'/faq/'.$fm_id.'_t';
		if(is_file($timg)){
			$timg_src = HF_DATA_URL.'/faq/'.$fm_id.'_t';
		}
	}

	$category_href = '/etc/faq.php';
	$category_stx = '';
	$faq_list = array();

	$stx = trim($stx);
	$sql_search = '';

	if($stx) {
		$sql_search = " AND ( INSTR(fa_subject, '$stx') > 0 or INSTR(fa_content, '$stx') > 0 ) ";
	}

	if($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
	$page_rows = 100;

	$sql = "
		SELECT
			COUNT(*) AS cnt
		FROM
			{$hf['faq_table']}
		WHERE (1)
			AND fm_id = '$fm_id'
			$sql_search ";
	$total = sql_fetch($sql);
	$total_count = $total['cnt'];

	$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
	$from_record = ($page - 1) * $page_rows; // 시작 열을 구함

	$sql = "
		SELECT
			*
		FROM
			{$hf['faq_table']}
		WHERE (1)
			AND fm_id = '$fm_id'
			$sql_search
		ORDER BY
			fa_order, fa_id
		LIMIT
			$from_record, $page_rows";
	$result = sql_query($sql);
	for ($i=0;$row=sql_fetch_array($result);$i++) {
		$faq_list[] = $row;
		if($stx) {
			$faq_list[$i]['fa_subject'] = search_font($stx, conv_content($faq_list[$i]['fa_subject'], 1));
			$faq_list[$i]['fa_content'] = search_font($stx, conv_content($faq_list[$i]['fa_content'], 1));
		}
	}

	include_once($skin_file);

}
else {
	echo '<p>'.str_replace(G5_PATH.'/', '', $skin_file).'이 존재하지 않습니다.</p>';
}

include_once(HF_PATH.'/tail.php');

?>
