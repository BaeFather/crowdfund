<!-- 본문내용 START -->

<script type="text/javascript" src="/js/jquery.bxslider.min.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<script src="<?php echo G5_URL;?>/js/jquery.blink.js"></script>

<link rel="stylesheet" type="text/css" href="/investment/css/investment_info_old_m.css">

<div id="content">
    <div class="location"><span><a href="<?=G5_URL?>/investment/invest_list.php">투자상품보기</a></span><b class="blue">상품상세보기</b></div>
    <div class="content investment">

        <!-- view_head 수정 2017.01.05 -->
        <div class="boxArea">
            <style>
                .bx-wrapper {width:100%; height:100%;}
                .bx-wrapper .bx-controls-direction a {position:absolute; top:46%; margin-top:-16px; outline:0; width:32px; height:32px; text-indent:-9999px; z-index:210;}
                .bx-wrapper .bx-controls-direction .bx-next{right:10px; background:url(/investment/controls.png) no-repeat -43px -32px;}
                .bx-wrapper .bx-controls-direction .bx-prev{left:10px; background:url(/investment/controls.png) no-repeat 0 -32px;}
            </style>
            <div class="box">
                <div class="con" id="flexslider1" style="height:242px;overflow:hidden;">
                    <div class="open_tit"><span style="padding:0 8px 3px 0;"><img src="/images/investment/timer_icon01_m.png" ></span>투자 시작일 : <?=$print_sdate?></div>
                    <ul class="<?=(count($PRDTIMG)>1)?'slides':''?>">
                        <?
                        for($i=0; $i<$product_image_count; $i++) {
                            echo '	<li><img src="'.$PRDTIMG[$i].'" style="width:100%;height:245px;margin-left:0;"></li>'.PHP_EOL;
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <script type="text/javascript">
                $(window).load(function() {
                    $('#flexslider1 .slides').bxSlider({
                        speed: 800,
                        auto: true,
                        pause: 5000,
                        pager: false,
                        controls: true,
                        autoHover:true
                    });
                });
            </script>
        </div>

        <div class="detail_info">
            <div class="detail_cont">

                <input type="hidden" id="invest_finished" value="<?=($invest_finished)?'true':'false';?>">
                <div class="detail_tit"><?=$PRDT["title"]?></div>
                <div class="flag_area">
                    <? if($PRDT['advance_invest']=='Y') { ?><div class="flag_green">사전투자 <?=(int)$PRDT['advance_invest_ratio']?>% <span id="advance_invest_help" class="help">?</span></div><? echo "\n"; }?>
                    <? if($PRDT['purchase_guarantees']=='Y') { ?><!--<div class="flag_red">채권매입계약</div>--><? echo "\n"; }?>
                    <? if($PRDT['advanced_payment']=='Y') { ?><div class="flag_orange">이자 선지급</div><? echo "\n"; } ?>
                    <? if($live_link){ ?><a href="javascript:;" onClick="<?=$live_link?>"><img src="/images/investment/live_icon01.gif"></a><? echo "\n"; } ?>
                </div>

                <div id="advance_invest_guide" style="display:none; position:absolute; z-index:3; left:10px; top:70px; width:90%; padding:12px 8px; border:1px solid #aaa; border-radius:5px; background-color:#FFFF99; font-size:12px; line-height:18px;">
                    <span style="font-size:14px; line-height:24px;font-weight:bold;">사전 투자 서비스란?</span><br>
                    펀딩오픈 시간에 투자참여가 어려운 회원분들을 위하여 사전에 투자할 수 있는 서비스입니다.<br><br>

                    <span style="font-size:14px; line-height:24px;font-weight:bold;">사전 투자 유의사항</span><br>
                    본 상품은 사전 투자가 가능한 상품으로 목표금액의 <b><?=(int)$PRDT['advance_invest_ratio']?>%</b> 까지 사전 투자가 진행됩니다.<br>
                    <div style="font-size:12px; line-height:18px; margin-top:8px;">
                        1. 사전 투자는 가상계좌의 예치금으로 투자 가능합니다.<br>
                        2. 사전 투자는 신청순으로 적용됩니다.
                        <!--3.사전 투자 취소는 펀딩 완료전까지 가능합니다.-->
                    </div>
                </div>

                <ul class="detail_table" style="clear: both;margin-top:9px;">
                    <li>
                        <p>투자자 수익률(연)</p>
                        <p><?=$PRDT["invest_return"]?>%</p>
                    </li>
                    <li>
                        <p>투자기간</p>
                        <p><?=$PRDT["invest_period"]?>개월</p>
                    </li>
                </ul>
                <ul class="detail_table">
                    <? if( in_array($PRDT_STATE['code'], array('A01','A02','A05')) ) { ?>
                        <li>
                            <p id="area3_title">지급회차</p>
                            <p id="area3_data"><span style="color:<?=($repay_count)?'#FF6633':'#AAA'?>"><?=$repay_count?></span> / <?=$total_repay_count?></p>
                        </li>
                    <? } else { ?>
                        <li>
                            <p id="area3_title">목표금액</p>
                            <p id="area3_data"><?=price_cutting($PRDT["recruit_amount"])?>원</p>
                        </li>
                    <? } ?>
                    <li>
                        <p>모집금액</p>
                        <p id="area4_data"><?=price_cutting($PRDT["total_invest_amount"])?>원</p>
                    </li>
                </ul>

                <div class="detail_progress">
                    <p class="prog_tit1">진행률</p>
                    <p class="prog_tit2" id="progress_data"><?=$product_invest_percent?>%</p>
                    <ul class="progress">
                        <li class="rate2"><img id="progress_bar" src="/images/investment/rate_blue.gif" alt="진행률" style="width:<?=$product_invest_percent?>%;"></li>
                    </ul>
                </div>


                <div class="detail_btn" id="button_area1">
                    <? if($invest_finished==false) { ?><a href="./simulation.php?prd_idx=<?=$PRDT["idx"]?>" class="btn_big_link">투자시뮬레이션</a><? } ?>
                    <?=$invest_button?>
                    <?=$advance_invest_button?>
                    <? if(!$is_member && $invest_finished) { ?><a href="#" id="reqsms_btn2" class="btn_big_blue" style="width:100%;">다음 상품 알림받기</a><? } ?>
                </div>

            </div>
            <div class="detail_guide" style="background-color:#f2f2f2;">
                <p class="guide1" onClick="location.href='<?=G5_URL?>/investment/guide.php';"><span style="padding-left:38px;">투자가 처음이신가요?</span></p>
                <p class="guide2" onClick="location.href='<?=G5_URL?>/company.php#d2';" style="width:49.65%;"><span style="padding-left:35px;margin-top:-11px;line-height:18px;display:inline-block;">헬로펀딩의 안전성</span></p>
            </div>
        </div>
        <!-- view_head 수정 2017.01.05 -->

        <script type="text/javascript">
            $(document).ready(function() {
                $('.flag_green, #advance_invest_guide').on('click', function() {
                    $('#advance_invest_guide').fadeToggle('slow');
                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                setInterval(function() {
                    if( $('#invest_finished').val()=='false' ) {
                        $.ajax({
                            type: "GET",
                            url: "/investment/ajax_investment.php",
                            dataType: "json",
                            data: {prd_idx:<?=$prd_idx?>},
                            success: function(json) {
                                $('#ajax_return_txt').val(
                                    'version: ' + json.data.version + '\n' +
                                    'referer: ' + json.data.referer + '\n' +
                                    'invest_finished: ' + json.data.invest_finished + '\n' +
                                    'area3_title: ' + json.data.area3_title + '\n' +
                                    'area3_data: ' + json.data.area3_data + '\n' +
                                    'area4_data: ' + json.data.area4_data + '\n' +
                                    'progress: ' + json.data.progress + '\n' +
                                    'progress_width: ' + json.data.progress_width + '\n' +
                                    'button_data1: ' + json.data.button_data1 + '\n' +
                                    'advance_invest_button_data: ' + json.data.advance_invest_button_data + '\n' +
                                    'button_data2: ' + json.data.button_data2
                                );

                                $('#invest_finished').val(json.data.invest_finished);
                                $('#area3_title').html(json.data.area3_title);
                                $('#area3_data').html(json.data.area3_data);
                                $('#area4_data').html(json.data.area4_data);
                                $('#progress_data').html(json.data.progress);
                                $('#progress_bar').attr('style', "width:" + json.data.progress_width);
                                $('#button_area1').html(json.data.button_data1);
                                $('#button_area2').html(json.data.button_data2);
                            },
                            error: function(e) { }
                        });
                    }
                }, 3 * 1000);
            });
        </script>

        <div style="clear:both;height:20px;"></div>

        <ul class="tab_type03">
            <li data-gubun="tab1" class="on" style="width:100%;">투자상품 기본정보</li>
            <li data-gubun="tab2" style="float:left;width:49.4%">증빙자료</li>
            <li data-gubun="tab3" style="float:right;width:49.4%">안전장치 업데이트<?=($PRDT["extend_8"])?" <span style='font-size:8pt;color:red'>new</span>" : "";?></li>
        </ul>
        <script>
            //탭 기능
            $(document).ready(function(){
                $('.tabArea:eq(0)').show();
                $('.tab_type03 li').click(function(){
                    $(this).addClass('on').siblings().removeClass('on');
                    var cur = $(this).index();
                    $('.tabArea').hide();
                    $('.tabArea:eq('+cur+')').slideToggle('slow');
                    //$('.tabArea:eq('+cur+')').show();
                });
            });
        </script>

        <div class="tabArea">
            <? if($PRDT["extend_6"]!="") { ?>
                <dl class="profit_title">
                    <dt>채권매입계약</dt>
                    <dd>
                        <? if( $PRDT["purchase_guarantees"]=='Y' ) { ?><div style='margin:0 0 16px;'><img src='/images/investment/guarantee_system_m.jpg' width='100%'></div><? } ?>
                        <?=$PRDT["extend_6"]?>
                    </dd>
                </dl>
            <? } ?>

            <?
            if($invest_summary){
                //$invest_summary = @preg_replace("/script/i", "script.", $invest_summary);
                $invest_summary = @preg_replace("/live_ban01\.gif/i", "live_ban01_m.gif", $invest_summary);
                ?>
                <h3 style="padding-top:20px">투자설명</h3>
                <div class="point">
                    <?
                    //동산일때
                    if($PRDT["category"]=='1' && $PRDT['portfolio']=='Y') {
                        echo "<div style='margin:0 0 16px;'><img src='/images/investment/guarantee_port_m.jpg' width='100%'></div>";
                    }

                    // 주택담보대출일때 2018-01-10
                    if($PRDT['mortgage_guarantees']=='1') {
                        echo "<div style='margin:10px auto 20px;'><center><img src='/images/investment/morgage_guarantees_m.jpg' width='100%'></center></div>";
                    }
                    ?>
                    <?=$invest_summary?>
                    <? if($prd_idx=="102") { ?><div><img src="/images/investment/102_event_m.jpg" width="100%"></div><? } ?>
                </div>
                <?
            }
            ?>

            <? if($PRDT["core_invest_point"]!=""){ ?>
                <h3><?=($PRDT['category']=='1') ? '대출자 정보' : '핵심 투자포인트';?></h3>
                <div class="point"><?=$PRDT["core_invest_point"]?></div>
            <? } ?>

            <? if($PRDT["extend_4"]!=""){ ?>
                <h3><?=($PRDT['category']=='1') ? '담보물 정보' : '투자자 보호장치';?></h3>
                <div class="point"><?=$PRDT["extend_4"]?></div>
            <? } ?>

            <? if($PRDT["extend_1"]!=""){ ?>
                <h3><?=($PRDT['category']=='1') ? '투자자보호장치' : '담보 분석 및 평가';?></h3>
                <div class="point"><?=$PRDT["extend_1"]?></div>
            <? } ?>


            <? if($grade) { ?>
                <h3>평가등급</h3>
                <? if( $grade_type=="v1" ) {	?>
                    <div class="level_info">
                        <div class="label"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>"></div>
                        <ul class="info">
                            <li style="height:20px;"><li>
                            <li>안정성 <span class="star<?=$PRDT["evaluate_star1"]?>"></span> <?=$PRDT["evaluate_score1"]?>/100</li>
                            <li>수익성 <span class="star<?=$PRDT["evaluate_star2"]?>"></span> <?=$PRDT["evaluate_score2"]?>/100</li>
                            <li>환급성 <span class="star<?=$PRDT["evaluate_star3"]?>"></span> <?=$PRDT["evaluate_score3"]?>/100</li>
                            <!--<li>합계 <b class="green"><?=$_evaluation_grade_array[$total_evaluate_star]?></b></li>-->
                        </ul>
                    </div>
                <? } else if( $grade_type=="v2" ) { ?>
                    <div style="width:100%;font-size:11px;color:brown">헬로펀딩은 안전투자를 위해 <span style="color:#FF2222"><b>A등급</b></span> 이상의 상품만 취급합니다.</div>
                    <div class="level_info" style="height:130px">
                        <div class="label" style="padding-top:15px"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" ></div>
                        <div class="invest_graph">
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score1?>%">&nbsp;안전성</div></div>
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score4?>%">&nbsp;상환성</div></div>
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score2?>%">&nbsp;수익성</div></div>
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score3?>%">&nbsp;환금성</div></div>
                        </div>
                    </div>
                <? } else if( $grade_type=="v3") { ?>
                    <div style="width:100%;font-size:11px;color:brown">헬로펀딩은 안전투자를 위해 <span style="color:#FF2222"><b>A등급</b></span> 이상의 상품만 취급합니다.</div>
                    <div class="level_info">
                        <div class="label"><img src="/images/investment/level_<?=strtolower($grade)?>.png" alt="<?=$grade?>" ></div>
                        <div class="invest_graph">
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score1?>%">&nbsp;안전성</div></div>
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score4?>%">&nbsp;상환성</div></div>
                            <div class="invest_graph_bg"><div class="invest_graph_rate" style="width:<?=$evaluate_score3?>%">&nbsp;환금성</div></div>
                        </div>
                    </div>
                <? } ?>
            <? } ?>


            <? if($PRDT["extend_2"]!=""){ ?>
                <h3><?=($PRDT['category']=='1') ? 'Q&A' : '신용 및 부채정보';?></h3>
                <div class="point"><?=$PRDT["extend_2"]?></div>
            <? } ?>

            <? if($PRDT["extend_3"]!=""){ ?>
                <h3>투자 구조도</h3>
                <div class="point"><?=$PRDT["extend_3"]?></div>
            <? } ?>

            <? if($PRDT["category"]!=1 && $PRDT["extend_5"]!=""){ ?>
                <h3>평가기관 의견</h3>
                <div class="point"><?=$PRDT["extend_5"]?></div>
            <? } ?>

            <? if($PRDT["category"]!=1 && $PRDT["screening"]!="") { ?>
                <h3>심사총평</h3>
                <div class="point">
                    <?
                    if( $PRDT["judge"] ) {
                        $judge_profile_image_name = (G5_IS_MOBILE) ? $PRDT["judge"]."_m.jpg" : $PRDT["judge"].".jpg";
                        $judge_profile_image = "../images/judge/".$judge_profile_image_name;
                        if( file_exists($judge_profile_image) ) { echo "<div style='margin:0 0 20px 0; width:100%;'><img src='$judge_profile_image'></div>"; }
                    }
                    ?>
                    <div style='padding:10px;'><?=$PRDT["screening"]?></div>
                </div>
            <? } ?>


            <? if($PRDT["lat"]>1 &&$PRDT["lng"]>1 ){ ?>
                <h3>지도</h3>
                <div class="boxArea">
                    <div class="box">
                        <?
                        //$client_id = "wgdMUaKHdFdJ8M6hMFJ_";  // https 로 등록된 코드
                        //$client_id = "JFBRTNU1_g1m81uONlva";  // http 로 등록된 코드 (미러)
                        $client_id = (preg_match("/mirror.hellofunding.co.kr/i", G5_URL)) ? "JFBRTNU1_g1m81uONlva" : "wgdMUaKHdFdJ8M6hMFJ_";

                        ///////////////////////////////////
                        // 지도 API(StaticMap API) 이용시
                        ///////////////////////////////////
                        $static_api_url = "https://openapi.naver.com/v1/map/staticmap.bin" .
                            "?clientId=" . $client_id .
                            "&url=" . G5_URL . $_SERVER['REQUEST_URI'] .
                            "&crs=EPSG:4326" .
                            "&exception=inimage" .
                            "&center={$PRDT['lng']},{$PRDT['lat']}" .
                            "&level=10" .
                            "&w=588&h=420" .
                            "&baselayer=default" .
                            "&format=png" .
                            "&markers={$PRDT['lng']},{$PRDT['lat']}";
                        ?>
                        <div class="con" id="testMap"><img src='<?=$static_api_url?>' width='100%' height='100%'></div>
                    </div>
                </div>
            <? } ?>

            <!--
			<h3>기존 담보대출내역</h3>
			<div class="point"><?=$PRDT["security_loan"]?></div>

			<h3>전문정보</h3>
			<div class="point"><?=$PRDT["special_info"]?></div>
//-->

            <?
            /*
            if($is_admin=='super') {
                if($PRDT["evidence"]!="") {
            ?>
                        <h3>증빙서류</h3>
            <?
                        $evidence_array  = explode("|",$PRDT["evidence"]);
                    for($i=0; $i<count($evidence_array);$i++){
                        if(is_file(G5_DATA_PATH."/product/".$evidence_array[$i])){
                        //echo "<a href=\"".G5_DATA_URL."/product/".$evidence_array[$i]."\" target=\"_blank\">증빙서류[".($i+1)."]</a><br>";
                            echo "<a href=\"".G5_DATA_URL."/product/".$evidence_array[$i]."\" target=\"_blank\"><img src='/images/investment/icon_file.png'></a>";
                        }
                    }
                }
            }
            */
            ?>

            <?
            if( !preg_match("/\<div class=\"invest_cont\"\>/i", $invest_summary) ) {

                $invest_period_month= ceil($PRDT["invest_period"]/12);
                $invest_period_month= $invest_period_month*12;
                ?>
                <div style="clear:both;height:20px;"></div>
                <h3>투자안내</h3>
                <div class="invest_info">
                    <div class="table">
                        <div class="title">투자금액별 총예상수익</div>
                        <table>
                            <tbody>
                            <tr>
                                <th>투자금액</th>
                                <th>예상수익<br >(연 수익 기준 / 세전)</th>
                                <!--<th>총예상수익 (세전)</th>-->
                            </tr>
                            <tr>
                                <td>100,000원</td>
                                <td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*100000))))?>원</td>
                            </tr>
                            <tr>
                                <td>500,000원</td>
                                <td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*500000))))?>원</td>
                            </tr>
                            <tr>
                                <td>1,000,000원</td>
                                <td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*1000000))))?>원</td>
                            </tr>
                            <tr>
                                <td>10,000,000원</td>
                                <td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*10000000))))?>원</td>
                            </tr>
                            <tr>
                                <td>50,000,000원</td>
                                <td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*50000000))))?>원</td>
                            </tr>
                            <tr>
                                <td>100,000,000원</td>
                                <td><?=number_format(floor(floor((($PRDT["invest_return"]/100)*100000000))))?>원</td>
                            </tr>
                            </tbody>
                        </table>
                        <p style="margin-top:5px;color:#777">
                            * 상환일: 매월 5일 (영업일기준)<br >
                            * 연 수익률 기준
                        </p>
                    </div>
                    <div class="notes">
                        <div class="title">투자시 참고사항</div>
                        <div class="text" style="font-size:10pt">
                            ○ 투자수익 시뮬레이션<br >
                            <ul>
                                <li style="list-style:disc;margin-left:24px;">투자수익 시뮬레이션은 예상수익을 표기해주는 것으로 펀딩완료 후 대출실행일과의 일수차이, 조기상환 및 기타 이유로 기재된 예상수익은 변동될 수 있습니다.</li>
                            </ul>
                            <br>
                            ○ 원금 및 이자 보장에 대한 사항<br>
                            <ul>
                                <li style="list-style:disc;margin-left:24px;">헬로펀딩은 투자금에 대하여 원금 및 이자수익을 보장하지 않습니다.</li>
                                <li style="list-style:disc;margin-left:24px;">채무자의 채무 불이행시 경,공매등의 절차 과정에서 원금의 일부 손실이 발생할 수 있습니다.</li>
                            </ul>
                            <br>
                            ○ 이자소득세 원천징수<br>
                            <ul>
                                <li style="list-style:disc;margin-left:24px;">일반투자자의 투자수익은 '비영업대금의 이익'으로 소득세법 제 16조 제 1항 제 11호에 의해 25%의 소득세가 발생되며, 주민세 2.5%가 추가되어 총 27.5%의 세금을 납부해야 합니다. 이러한 세금납부에 대해 헬로핀테크에서 원천징수를 하므로 일반투자자는 별도로 세금신고를 하실 필요가 없습니다.</li>
                            </ul>
                            <br>
                            ○ 투자일과 원금상환 입금날짜가 다른 이유<br>
                            <ul>
                                <li style="list-style:disc;margin-left:24px;">투자금이 100% 펀딩된 이후 대출약정을 통해 대출이 실행되며 이 기간에 수일이 소요될 수 있으며, 대출자 분이 대출금을 받은날 부터 이자가 계산되어지기 때문에 실 투자일과 상환일에 차이가 발생합니다.</li>
                            </ul>
                            <!--
                            ○ 이자소득세 원천징수<br>
                            <ul>
                                <li style="list-style:disc;margin-left:24px;">이자소득에 대하여 이자소득세25% + 주민세2.5%가 가산되어, 총 이자소득의27.5%가 원천징수 됩니다.</li>
                            </ul>
                            <br>
                            ○ 플랫폼이용료<br>
                            <ul>
                                <li style="list-style:disc;margin-left:24px;">투자자 플랫폼 이용료는 상품 투자금액의0~3%(연) 수수료를 매월 분할 차감</li>
                            </ul>
                            -->
                        </div>
                        <? if($invest_finished==false) { ?><a href="./simulation.php?prd_idx=<?=$PRDT["idx"]?>" class="btn_big_blue">투자 수익 시뮬레이션</a><? } ?>
                    </div>
                </div>
                <?
            }
            ?>

            <? if($PRDT['extend_7']) { ?>
                <h3>투자관련 도움말</h3>
                <div class="point invest_info"><?=$PRDT['extend_7']?></div>
            <? } ?>

            <div class="btnArea" id="button_area2">
                <?=$invest_button2?>
            </div>

        </div>

        <div class="tabArea" style="padding-top:20px;">
            <h3>증빙자료</h3>
            <div class="point"><?=($PRDT["extend_9"])?$PRDT["extend_9"]:'<p style="text-align:center;color:#aaa">내용이 없습니다.</p>';?></div>
        </div>

        <div class="tabArea" style="padding-top:20px;">
            <h3>안전장치 업데이트</h3>
            <div class="point"><?=($PRDT["extend_8"])?$PRDT["extend_8"]:'<p style="text-align:center;color:#aaa">내용이 없습니다.</p>';?></div>
        </div>

    </div>
</div>

</div>
<?

// 투자위험고지 팝업
include_once(G5_PATH."/popup/inc_invest_warning_agree_form.php");

if($prd_idx=='119') {
    include_once(G5_PATH.'/popup/inc_product_119_notice.php');
}

// 라이브스트림 준비중 팝업
if($PRDT['stream_url1']=='ready') {
    include_once(G5_PATH.'/popup/inc_stream_ready.php');
}
?>

<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>