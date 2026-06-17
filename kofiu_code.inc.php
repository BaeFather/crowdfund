<?

include_once("_common.php");

// 국가코드
$country_sql = "
	SELECT
		*
	FROM
		aml_kofiu_country_code
	ORDER BY
		(CASE FAVORITE_YN WHEN 'Y' THEN 1 ELSE 2 END),
		NM ASC";
$country_res = sql_query($country_sql);
$country_cnt = $country_res->num_rows;

for($cc=0; $cc<$country_cnt; $cc++) {
	$KOFIU_COUNTRY_CODE[] = sql_fetch_array($country_res);
}
$KOFIU_COUNTRY_COUNT = count($KOFIU_COUNTRY_CODE);
$KCCD_ARRKEY = array_keys($KOFIU_COUNTRY_CODE);


// KOFIU 업종코드
$industry_sql = "
	SELECT
		*
	FROM
		aml_kofiu_industry_code
	ORDER BY
		C_CD,
		P_CD";
$industry_res = sql_query($industry_sql);
$industry_cnt = $industry_res->num_rows;

for($ic=0; $ic<$industry_cnt; $ic++) {
	$KOFIU_INDUSTRY_CODE[] = sql_fetch_array($industry_res);
}
$KOFIU_INDUSTRY_COUNT = count($KOFIU_INDUSTRY_CODE);
$KICD_ARRKEY = array_keys($KOFIU_INDUSTRY_CODE);


// KOFIU 직업코드 --------------------------------------------------------------------
$KOFIU_JOB_DIV_CD = array(
	array('CD' => '01', 'NM' => '회사원'),
	array('CD' => '02', 'NM' => '전문직'),
	array('CD' => '03', 'NM' => '공무원'),
	array('CD' => '05', 'NM' => '농축산업종사자'),
	array('CD' => '06', 'NM' => '자유직/프리랜서'),
	array('CD' => '07', 'NM' => '전업주부'),
	array('CD' => '08', 'NM' => '학생/군인'),
	array('CD' => '09', 'NM' => '무직'),
	array('CD' => '04', 'NM' => '개인사업자/자영업자'),
	array('CD' => '91', 'NM' => '카지노사업'),
	array('CD' => '92', 'NM' => '대부업'),
	array('CD' => '93', 'NM' => '환전업'),
	array('CD' => '94', 'NM' => '고가귀금속판매업'),
	array('CD' => '95', 'NM' => '가상통화사업 관련 종사자')
);
$KOFIU_JOB_DIV_CD_COUNT = count($KOFIU_JOB_DIV_CD);
$KJDC_KEY = array_keys($KOFIU_JOB_DIV_CD);

?>