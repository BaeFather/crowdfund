<div id="content">
    <div class="location"><span><a href="<?=G5_URL?>/investment/invest_list.php">투자상품보기</a></span><b class="blue"><?=$subtitle?></b></div>
    <div class="content invest_list2" style="min-height:500px">
        
        <? if($tmp_special_user) { ?>
            <div class="list_info" style="color:#284893;">
                <ul>
                    <!--
                    <li>본 상품은 펀딩심사 진행중입니다. 안전성 확보등 추가내용은 업데이트 되며, 내부 심사평가에 따라 펀딩진행이 불가 할 수 있습니다.</li>
                    <li>본 상품은 헬로펀딩 VIP투자자분만 확인 가능합니다.<br>
                        <span style="color:red">[주의] 정식 투자시작 전 상품의 정보가 외부로 유출되지 않도록 주의 부탁드립니다.</span>
                    <li>
                    -->
                    <li style="border-bottom:1px solid red;"><b>투자시작일은 대출자와의 일정협의를 통해 조정될 수 있습니다.</b></li>
                </ul>
            </div>
        <? } ?>

        <!--<div class="list_info">총 <span class="blue2" id="product_total_count"><?=$product_cnt + $event_count?>개</span>의 상품이 함께하고 있습니다.</div>-->

        <ul class="tab_type03">
            <li onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" <?=($category=='')?'class="on"':'';?>>전체</li>
            <li onClick="location.href='?CA=A'" <?=($CA=='A')?'class="on"':'';?>>부동산</li>
            <li onClick="location.href='?CA=A2'" <?=($CA=='A2')?'class="on"':'';?>>주택담보</li>
            <li onClick="location.href='?CA=B'" <?=($CA=='B')?'class="on"':'';?>>동산</li>
            <li onClick="location.href='?CA=C'" <?=($CA=='C')?'class="on"':'';?>>매출채권</li>
        </ul>
        <!-- 탭메뉴 -->
        <div>
            <? if($CA=='') { ?>
                <div id="CAALL" title="전체">
                    <span class="unfold"></span>
                    <img src="/images/investment/ca_ban_tit01_m.jpg" width="100%">
                    <p class="hide"><img src="/images/investment/ca_ban01_m.jpg" width="100%"></p>
                </div>
            <? } else if($CA=='A') {?>
                <div id="CAA" title="부동산">
                    <span class="unfold"></span>
                    <img src="/images/investment/ca_ban_tit02_m.jpg" width="100%">
                    <p class="hide"><img src="/images/investment/ca_ban02_m.jpg" width="100%"></p>
                </div>
            <? } else if($CA=='A2') {?>
                <div id="CAA2" title="주택담보">
                    <span class="unfold"></span>
                    <img src="/images/investment/ca_ban_tit03_m.jpg" width="100%">
                    <p class="hide"><img src="/images/investment/ca_ban03_m.jpg" width="100%"></p>
                </div>
            <? } else if($CA=='B') {?>
                <div id="CAB" title="동산">
                    <span class="unfold"></span>
                    <img src="/images/investment/ca_ban_tit04_m.jpg" width="100%">
                    <p class="hide"><img src="/images/investment/ca_ban04_m.jpg" width="100%"></p>
                </div>
            <? } else if($CA=='C') {?>
                <div id="CAC" title="확정매출채권">
                    <span class="unfold"></span>
                    <img src="/images/investment/ca_ban_tit05_m.jpg" width="100%">
                    <p class="hide"><img src="/images/investment/ca_ban05_m.jpg" width="100%"></p>
                </div>
            <? } ?>
        </div>
        <!-- 펼치기메뉴 시작 -->

        <script>
            $(document).ready(function(){
                $('.unfold').click(function(){
                    if($('.unfold').hasClass('unfold')){
                        $('.unfold').addClass('fold').removeClass('unfold');
                        $('.hide').slideUp();
                    }else if($('.fold').hasClass('fold')){
                        $('.fold').addClass('unfold').removeClass('fold');
                        $('.hide').slideDown();
                    }
                });
            });
        </script>

        <!-- 펼치기메뉴 끝 -->
        <!-- boxArea 전체 -->
        <div class="boxArea" id="list_area">
            
            <?
            if(!$count_sum) {
                echo '
			<div class="box product_count" style="padding:150px 0;background:#FAFAFA;text-align:center;">
				<p>등록된 상품이 없습니다.</p>
			</div>'. PHP_EOL;
            }
            else {
                //-- 이벤트 투자리스트 시작 ------------------------
                for($i=0; $i<count($event_row); $i++) {
                    $EVENT = $event_row[$i];
                    
                    if($EVENT['main_image_m'])    $event_main_image_path = G5_DATA_PATH."/product_special/".$EVENT['main_image_m'];
                    else if($EVENT['main_image']) $event_main_image_path = G5_DATA_PATH."/product_special/".$EVENT['main_image'];
                    else                          $event_main_image_path = "";
                    
                    if($event_main_image_path) {
                        $target_str = preg_replace("/\//", "\/", G5_DATA_PATH);
                        $event_main_image_url = preg_replace('/'.$target_str.'/', G5_DATA_URL, $event_main_image_path);
                        $event_main_image_tag = "<img src='".$event_main_image_url."' style='width:100%;min-height:100%'>";
                    }
                    $target_str = $event_main_image_url = NULL;
                    
                    if($EVENT["recruit_amount"]>0) {
                        $event_invest_percent = ($EVENT["total_invest_amount"]>0) ? round((($EVENT["total_invest_amount"]/$EVENT["recruit_amount"])*100),2) : 0;
                    }
                    else {
                        $event_invest_percent = 0;
                    }
                    
                    $event_open_date    = preg_replace("/-|:| /", "", $EVENT["open_datetime"]);		//상점오픈 (투자시작가능)
                    $event_invest_sdate = preg_replace("/-|:| /", "", $EVENT["start_datetime"]);	//상품오픈 (투자시작가능)
                    $event_invest_edate = preg_replace("/-|:| /", "", $EVENT["end_datetime"]);		//상품종료 (투자모집완료)
                    
                    $event_end_date     = preg_replace("/-/", "", $PLIST[$i]["invest_end_date"]);
                    
                    $event_state = get_product_state(
                        $EVENT["recruit_period_start"],
                        $EVENT["recruit_period_end"],
                        $event_open_date,
                        $event_invest_sdate,
                        $event_invest_edate,
                        $EVENT["state"],
                        $EVENT["recruit_amount"],
                        $EVENT["total_invest_amount"],
                        $event_end_date
                    );
                    
                    $event_detail_url = "/event_invest/event_invest.php?prd_idx=".$EVENT['idx'];
                    
                    $event_cover_display = "none";
                    $event_invest_button = "<a href='$event_detail_url' class='btn_big_blue' style='margin:0;'>상품상세보기</a>";
                    if($event_invest_sdate <= $YmdHis && $event_invest_edate >= $YmdHis) {
                        if($EVENT["recruit_amount"] > $EVENT["total_invest_amount"]) {
                            $event_invest_button = "<a href='$event_detail_url' class='btn_big_blue' style='margin:0;'>상품상세보기</a>";
                        }
                        else {
                            $event_cover_display = "block";
                            $event_cover_caption = "펀딩성공";
                            $event_invest_button = "<a href='$event_detail_url' class='btn_big_gray' style='margin:0;'>투자모집완료</a>\n";
                        }
                    }
                    else {
                        if($EVENT["recruit_amount"] > $EVENT["total_invest_amount"]) {
                            if( preg_replace("/-/", "", $EVENT["recruit_period_start"]) > date("Ymd") ) {
                                $event_invest_button = "<a href='$event_detail_url' class='btn_big_blue' style='margin:0;'>투자대기</a>\n"; //투자대기상태
                            }
                            else if( preg_replace("/-/", "", $EVENT["recruit_period_end"]) < date("Ymd") ) {
                                $event_cover_display = "block";
                                $event_cover_caption = "펀딩성공";
                                $event_invest_button = "<a href='$event_detail_url' class='btn_big_gray' style='margin:0;'>투자모집완료</a>";
                            }
                        }
                        else {
                            $event_cover_display = "block";
                            $event_cover_caption = "펀딩성공";
                            $event_invest_button = "<a href='$event_detail_url' class='btn_big_gray' style='margin:0;'>투자모집완료</a>\n";
                        }
                    }
                    
                    $event_period_days = ceil(((strtotime($EVENT["recruit_period_end"]) - strtotime($EVENT["recruit_period_start"]))+86400) / 86400);
                    
                    $start_timestamp = strtotime($EVENT["start_datetime"]);
                    $print_sdate = date('Y년 m월 d일', $start_timestamp);
                    $print_sdate.= ' ' . get_yoil($EVENT["start_datetime"]).'요일 ';
                    $print_sdate.= (date(H, $start_timestamp) < 12) ? ' 오전' : ' 오후';
                    $print_sdate.= date('H시', $start_timestamp);
                    
                    ?>
                    <div class="box product_count" <? if($EVENT['display']=='N' && $special_user) { ?>style="opacity:0.5;"<? } ?>>
                        <div class="imgArea" onClick="location.href='<?=$event_detail_url?>';">
                            <div class="main_image"><?=$event_main_image_tag?></div>
                            <div class="cover" style="display:<?=$event_cover_display?>;"></div>
                            <div class="cover_text" style="display:<?=$event_cover_display?>;"><?=$event_cover_caption?></div>
                            <? if(!$EVENT['main_image']){ ?><a href='<?=$event_detail_url?>' class='btn_more'>더보기</a><? echo "\n"; }?>
                        </div>
                        <div class="con">
                            <div class="title"><?=$EVENT["title"]?></div>
                            <div class="subtext">투자시작일 : <?=$print_sdate?></div>
                            <ul class="info">
                                <li>
                                    <div class="subject">투자자 수익률(회)</div>
                                    <div class="value"><?=$EVENT["invest_return"]?>%</div>
                                </li>
                                <li class="right_end">
                                    <div class="subject">투자기간</div>
                                    <div class="value"><?=$event_period_days?>일</div>
                                </li>
                                <li class="bottom_end">
                                    <div class="subject">목표금액</div>
                                    <div class="value"><?=number_format($EVENT["recruit_amount"])?>원</div>
                                </li>
                                <li class="right_end bottom_end">
                                    <div class="subject">모집금액</div>
                                    <div class="value"><?=number_format($EVENT["total_invest_amount"])?>원</div>
                                </li>
                            </ul>
                            <ul class="progress">
                                <li>진행률<b><?=$event_invest_percent?>%</b>
                                    <div class="rate"><img src="../images/investment/rate_blue.gif" alt="진행률" style="width:<?=($event_invest_percent)?$event_invest_percent:0.2;?>%;"></div>
                                </li>
                            </ul>
                            <div style="width:100%;text-align:center;">
                                <?=$event_invest_button?>
                            </div>
                        </div>
                    </div>
                    <div class="box_end"></div>
                    
                    <?
                }
                //-- 이벤트 투자리스트 끝 ------------------------
                
                //-- 투자상품리스트 시작 -------------------------
                for($i=0; $i<$plist_count; $i++) {
                    
                    if($PLIST[$i]["recruit_amount"] > 0) {
                        $product_invest_percent = ($PLIST[$i]["total_invest_amount"]>0) ? round((($PLIST[$i]["total_invest_amount"]/$PLIST[$i]["recruit_amount"])*100),2) : 0;
                    }
                    else{
                        $product_invest_percent = 0;
                    }
                    
                    ###################################
                    ## 리턴 상태코드(code) 예시 : getProductStat($PLIST[$i]['idx']) 리턴 배열
                    ## A01 : 이자상환중
                    ## A02 : 투자상환완료 (상품마감)
                    ## A03 : 투자모집실패
                    ## A04 : 부실
                    ## A05 : 중도일시상환
                    ## B00 : 상품준비중
                    ## B01 : 투자대기중
                    ## B02 : 투자모집중
                    ## B03 : 투자모집완료
                    ## B04 : 투자모집실패
                    ###################################
                    $PRDT_STATE = getProductStat($PLIST[$i]['idx']);
                    
                    if (preg_match('/(B00|B01|B02)/', $PRDT_STATE['code'])) {
                        $button_size     = '47%';
                        $invest_finished = false;
                    }
                    else {
                        $button_size     = '100%';
                        $invest_finished = true;
                    }
                    
                    switch($PRDT_STATE['code']) {
                        case "A01" :
                            $button_caption = $PRDT_STATE['code_str'];
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩성공";
                            $cover_display  = "block";
                            break;
                        case "A02" :
                            $button_caption = '원금상환완료';
                            $button_class   = 'btn_big_gray';
                            $button_size    = '100%';
                            $cover_caption  = "펀딩성공";
                            $cover_display  = "block";
                            break;
                        case "A03" :
                            $button_caption = $PRDT_STATE['code_str'];
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩종료";
                            $cover_display  = "block";
                            break;
                        case "A04" :
                            $button_caption = $PRDT_STATE['code_str'];
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩종료";
                            $cover_display  = "block";
                            break;
                        case "A05" :
                            $button_caption = "원금상환완료";
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩성공";
                            $cover_display  = "block";
                            break;
                        case "A06" :
                            $button_caption = "투자금 반환 완료";
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩성공";
                            $cover_display  = "block";
                            break;
                        case "B00" :
                            $button_caption = '상품상세보기';
                            $button_class   = 'btn_big_blue';
                            $cover_caption  = "";
                            $cover_display  = "none";
                            break;
                        case "B01" :
                            $button_caption = '상품상세보기';
                            $button_class   = 'btn_big_green';
                            $cover_caption  = "";
                            $cover_display  = "none";
                            break;
                        case "B02" :
                            $button_caption = '상품상세보기';
                            $button_class   = 'btn_big_blue';
                            $cover_caption  = "";
                            $cover_display  = "none";
                            break;
                        case "B03" :
                            $button_caption = $PRDT_STATE['code_str'];
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩성공";
                            $cover_display  = "block";
                            break;
                        case "B04" :
                            $button_caption = $PRDT_STATE['code_str'];
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "펀딩종료";
                            $cover_display  = "block";
                            break;
                        default    :
                            $button_caption = '상품상세보기';
                            $button_class   = 'btn_big_gray';
                            $cover_caption  = "";
                            $cover_display  = "none";
                            break;
                    }
                    
                    $grade = "";
                    if($PLIST[$i]['evaluate_score4']) {
                        //-- 개정 등급 산정방식 --------------------------------------------------//
                        $level_score = round(($PLIST[$i]["evaluate_score1"] + $PLIST[$i]["evaluate_score2"] + $PLIST[$i]["evaluate_score3"] + $PLIST[$i]["evaluate_score4"]) / 5);
                        $grade = $_gudge_grade_array[$level_score];
                        //-- 개정 등급 산정방식 --------------------------------------------------//
                    }
                    else if($PLIST[$i]["evaluate_star1"] && $PLIST[$i]["evaluate_star2"] && $PLIST[$i]["evaluate_star3"]){
                        //-- 기존 등급 산정방식 --------------------------------------------------//
                        $level_score = $PLIST[$i]["evaluate_star1"] + $PLIST[$i]["evaluate_star2"] + $PLIST[$i]["evaluate_star3"];
                        $grade = $_evaluation_grade_array[$level_score];
                        //-- 기존 등급 산정방식 --------------------------------------------------//
                    }
                    
                    
                    $detail_url = "/investment/investment.php?prd_idx=".$PLIST[$i]['idx'];
                    $detail_url_script = "location.href='$detail_url'";
                    
                    // 지정투자상품 설정
                    if( in_array($PLIST[$i]['idx'], array('148','157','171')) ) {
                        if($PLIST[$i]['idx']=='148') {
                            if( !$is_admin && !in_array($member['mb_id'], array('moreamc','uildnm2012','yr4msp','sori9th','master')) ) {
                                $detail_url_script = "alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 사전에 협의완료된 대출자와 투자자가 제3자에 의한 체계적 담보권리확보 및 자금관리를 목적으로 헬로펀딩을 통해 펀딩을 진행합니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');";
                            }
                        }
                        else if($PLIST[$i]['idx']=='157') {
                            if( !$is_admin && !in_array($member['mb_id'], array('fintech05','yr4msp','sori9th','master')) ) {
                                $detail_url_script = "alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');";
                            }
                        }
                        else if($PLIST[$i]['idx']=='171') {
                            if( !$is_admin && !in_array($member['mb_id'], array('KJHInvest1019','GraceInvest1102','master')) ) {
                                $detail_url_script = "alert('[본 투자상품 관련 공지]\\n\\n본 투자상품은 투자자와 사전에 협의가 완료된 지정투자상품입니다.\\n따라서 지정된 투자자 외 분들의 상품열람 및 투자가 제한되는 점 양해부탁드립니다.');";
                            }
                        }
                    }
                    
                    
                    $main_image_path = ($PLIST[$i]['main_image']) ? G5_DATA_PATH."/product/".$PLIST[$i]['main_image'] : "";
                    if($main_image_path){
                        $target_str     = preg_replace("/\//", "\/", G5_DATA_PATH);
                        $main_image_url = preg_replace('/'.$target_str.'/', G5_DATA_URL, $main_image_path);
                        $main_image_tag = "<img src='".$main_image_url."' style='width:100%;min-height:100%'>";
                    }
                    $target_str = $main_image_url = NULL;
                    
                    // 대출실행 완료건에 대하여 이자지급 차수 표시
                    if($PLIST[$i]['loan_start_date'] && $PLIST[$i]['loan_start_date']!='0000-00-00') {
                        $loan_start_date_day = (int)substr($PLIST[$i]['loan_start_date'], -2);
                        $total_repay_count = ((int)$loan_start_date_day < 5) ? $PLIST[$i]['invest_period'] : $PLIST[$i]['invest_period'] + 1; //총 지급횟수
                        $paied_sql = "SELECT MAX(turn) AS max_turn FROM cf_product_success WHERE idx > 100 AND product_idx='".$PLIST[$i]['idx']."' AND invest_give_state='Y'";
                        //if($_COOKIE['debug_mode']) { echo $paied_sql."<br><br>\n"; }
                        $PAIED = sql_fetch($paied_sql);
                        $repay_count = ($PAIED['max_turn']) ? $PAIED['max_turn'] : 0;
                    }
                    
                    $start_timestamp  = strtotime($PLIST[$i]["start_datetime"]);
                    $print_sdate = date('Y년 m월 d일', $start_timestamp);
                    $print_sdate.= ' ' . get_yoil($PLIST[$i]["start_datetime"]).'요일 ';
                    $print_sdate.= (date(H, $start_timestamp) < 12) ? ' 오전' : ' 오후';
                    $print_sdate.= date('H시', $start_timestamp);
                    
                    // 플래그 태그
                    $FLAG = NULL;
                    if($PLIST[$i]['advance_invest']=='Y') {
                        $FLAG['A'] = '<div class="_flag_green">사전투자 '.(int)$PLIST[$i]['advance_invest_ratio'].'%</div>';
                    }
                    if($PLIST[$i]['purchase_guarantees']=='Y' && preg_match("/dev\.hello/", G5_URL)) {
                        $FLAG['B'] = '<div class="_flag_red">채권매입계약</div>';
                    }
                    if($PLIST[$i]['advanced_payment']=='Y') {
                        $FLAG['C'] = '<div class="_flag_orange">이자 선지급</div>';
                    }
                    if($PLIST[$i]['stream_url1'] || $PLIST[$i]['stream_url2']) {
                        $FLAG['D'] = '<div class="_flag_onair"><img src="/images/investment/live_icon01.gif"></div>';
                    }
                    
                    $category_flag = '';
                    if($PLIST[$i]['category']=='1') {
                        $category_flag = '<div class="cflag ca-B">동산</div>';
                    }
                    else if($PLIST[$i]['category']=='2') {
                        $category_flag = ($PLIST[$i]['mortgage_guarantees']) ? '<div class="cflag ca-A2">주택담보대출</div>' : '<div class="cflag ca-A">부동산</div>';
                    }
                    else if($PLIST[$i]['category']=='3') {
                        $category_flag = '<div class="cflag ca-C">확정매출채권</div>';
                    }
                    
                    $auto_invest_flag = ($PLIST[$i]['ai_grp_idx']) ? '<div class="cflag ai">자동투자</div>' : '';
                    $new_flag = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($PLIST[$i]['open_datetime']))) && ($PLIST[$i]['recruit_amount'] > $PLIST[$i]['total_invest_amount'])) ? '<div class="nflag">N</div>' : '';
                    
                    if($PLIST[$i]['invest_days'] > 0 && $PLIST[$i]['invest_days'] < 30) {
                        $invest_period = $PLIST[$i]['invest_days'];
                        $invest_period_unit = '일';
                    }
                    else {
                        $invest_period = $PLIST[$i]['invest_period'];
                        $invest_period_unit = '개월';
                    }
                    
                    ?>

                    <style>
                        .flags { float:left; z-index:1; position:relative; top:6px; margin-left:6px; width:130px; font-size:12px; line-height:22px; color:#FFF; text-align:center; }
                        .flags ul { clear:both; display:inline-block; list-style:none; }
                        .flags li { margin-bottom:2px }
                        .flags .cflag { width:130px; height:24px; font-size:12px; line-height:22px; color:#FFF; text-align:center; }
                        .flags .ca-A  { background:#38B41E; }
                        .flags .ca-A2 { background:#FD3B84; }
                        .flags .ca-B  { background:#3196EE; }
                        .flags .ca-C  { background:#EA1C31; }
                        .flags .ai            { background:purple; }

                        .flags ._flag_red     { margin:0; width:130px; height:24px; background:#DC2F43; font-size:12px; line-height:24px; color:#FFF; text-align:center; }
                        .flags ._flag_green   { margin:0; width:130px; height:24px; background:#00C73C; font-size:12px; line-height:24px; color:#FFF; text-align:center; }
                        .flags ._flag_orange  { margin:0; width:130px; height:24px; background:#FF7419; font-size:12px; line-height:24px; color:#FFF; text-align:center; }
                        .flags ._flag_onair   { margin:0; width:130px; height:24px; }
                        .nflag { float:right; z-index:1; position:relative; top:6px; margin-right:6px; width:24px; height:24px; font-family:'NGB'; font-size:1.1em; line-height:22px; color:#FFF; text-align:center; background:#FF2222; border-radius:12px; }
                    </style>

                    <div class="box product_count" <? if($PLIST[$i]['display']=='N' && ($special_user || $tmp_special_user)) { ?>style="opacity:0.5;"<? } ?>>
                        <div class="imgArea" onClick="<?=$detail_url_script?>">
                            <div class="main_image"><?=$main_image_tag?></div>

                            <div class="flags">
                                <ul>
                                    <li><?=$category_flag?></li>
                                    <? if($FLAG['A']) { ?><li><?=$FLAG['A']?></li><? } ?>
                                    <? if($FLAG['B']) { ?><li><?=$FLAG['B']?></li><? } ?>
                                    <? if($FLAG['C']) { ?><li><?=$FLAG['C']?></li><? } ?>
                                    <? if($FLAG['D']) { ?><li><?=$FLAG['D']?></li><? } ?>
                                    <li><?=$auto_invest_flag?></li>
                                </ul>
                            </div>
                            
                            <?=$new_flag?>

                            <div class="cover" style="display:<?=$cover_display?>;"></div>
                            <div class="cover_text" style="display:<?=$cover_display?>;"><?=$cover_caption?></div>
                            <div class="detail_state" style="display:<?=$cover_display?>;"><?=$PRDT_STATE['code_str']?></div>
                            <? if(!$PLIST[$i]['main_image']){ ?><a href='javascript:;' onClick="<?=$detail_url_script?>" class='btn_more'>더보기</a><? echo "\n"; }?>
                            <? if(preg_match("/dev\.hello/", G5_URL) && $grade) { ?><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" class="label" alt="로컬등급"><? } ?>
                        </div>
                        <div class="con">
                            <div class="title"><?=$PLIST[$i]["title"]?></div>
                            <div class="subtext">투자시작일 : <?=$print_sdate?></div>
                            <ul class="info">
                                <li>
                                    <div class="subject">투자자 수익률(연)</div>
                                    <div class="value"><?=$PLIST[$i]["invest_return"]?>%</div>
                                </li>
                                <li class="right_end">
                                    <div class="subject">투자기간</div>
                                    <div class="value"><?=$invest_period?><?=$invest_period_unit?></div>
                                </li>
                                <?
                                if( in_array($PRDT_STATE['code'], array('A01','A02','A05')) ) {
                                    ?>
                                    <li class="bottom_end">
                                        <div class="subject">지급회차</div>
                                        <div class="value"><span style="color:<?=($repay_count)?'#FF6633':'#AAA'?>"><?=$repay_count?></span> / <?=$total_repay_count?></div>
                                    </li>
                                    <?
                                }
                                else {
                                    ?>
                                    <li class="bottom_end">
                                        <div class="subject">목표금액</div>
                                        <div class="value"><?=price_cutting($PLIST[$i]["recruit_amount"])?>원</div>
                                    </li>
                                    <?
                                }
                                ?>
                                <li class="right_end bottom_end">
                                    <div class="subject">모집금액</div>
                                    <div class="value"><?=price_cutting($PLIST[$i]["total_invest_amount"])?>원</div>
                                </li>
                            </ul>
                            <ul class="progress">
                                <li>진행률<b><?=$product_invest_percent?>%<!--(<?=number_format($PLIST[$i]["total_invest_count"])?>명)--></b>
                                    <div class="rate"><img src="../images/investment/rate_blue.gif" alt="진행률" style="width:<?=($product_invest_percent)?$product_invest_percent:0.2;?>%"></div>
                                </li>
                            </ul>
                            <div style="width:100%;text-align:center;">
                                <? if($invest_finished==false) { ?><!--<a href="./simulation.php?prd_idx=<?=$PLIST[$i]["idx"]?>" class="btn_big_link" style="width:47%;margin:0;">투자시뮬레이션</a>--><? } ?>
                                <a href='javascript:;' onClick="<?=$detail_url_script?>" class='<?=$button_class?>' style='margin:0;'><?=$button_caption?></a>
                            </div>
                        </div>
                    </div>
                    <div class="box_end"></div>
                    <?
                }
                //-- 투자상품리스트 끝 -------------------------
            }
            ?>
        </div>
    </div>
</div>

<!-- 본문내용 E N D -->
<?php

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>