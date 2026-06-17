<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$funding_story_skin_url.'/style.css">', 0);

?>

<style>
	#content {background-image: none; margin:0 auto;}
	#content .content{margin:0 auto;}
	#content .top_title {font-size:36px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 60px 0 10px; background-color: #fff;}
	#content .top_title .sky {color:#33a5ed;}
	#content .top_text {font-size:18px; color:#777; padding-bottom: 20px; font-family:'SpoqaHanSans','sanserif'}

</style>


<div id="content">
    <input type="hidden" name="tvListCount" value="<?php echo $tvListCount;?>"/>
    <input type="hidden" name="columnListCount" value="<?php echo $columnListCount;?>"/>
    <input type="hidden" name="seminarListCount" value="<?php echo $seminarListCount;?>"/>
	
	<div>
		<h2 class="top_title">헬로펀딩 <span class="sky">펀딩디자이너 스토리</span></h2>
		<p class="top_text">헬로펀딩의 TV출연 및 강연, 인터뷰 등을 확인할 수 있습니다.<br class="br"></p>
	</div>
	
    <!--div class="location">
        <span><a href="<?=G5_URL?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">펀딩디자이너 스토리</b>
    </div-->

    <div class="content">
        <div class="funding_story">
            <!--p><img src="/images/funding_designer/top_img01.jpg" alt="금융을 디자인 합니다." class="main-banner"/></p-->
            <br/>
            <div class="list" id="tv_list">
                <!--img src="/images/funding_designer/tit01.jpg" alt="펀딩디자이너 스토리"/-->

                <div class="latest">
                    <div class="left">
                        <iframe src="<?php echo $latest_tv_data["iframe"]; ?>" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class="right">
                        <strong><?php echo $latest_tv_data["subject"]; ?></strong>
                        <h5><?php echo $latest_tv_data["subheading"]; ?></h5>
                        <span><?php echo $latest_tv_data["contents"]; ?></span>
                    </div>
                </div>

                <img src="/images/funding_designer/tit02.jpg" alt="TV출연"/>

                <?php if(count($tv_list) > 0) : ?>
                <ul>
                    <?php
                    $nLoop = 1;
                    foreach($tv_list as $list) :
                        if($nLoop % 4 == 0){
                            echo '</ul><br/><ul>';
                        }

                        $list["target_link"] = ($list["target_link"]) ? $list["target_link"] : "";

                        ?>
                        <li>
                            <table>
                                <thead>
                                    <tr>
                                        <td><iframe src="<?php echo $list["iframe"]; ?>" width="100%" height="100%" frameborder="0" allowfullscreen></iframe></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php if(isset($list["target_link"]) && !empty($list["target_link"])) { ?>
                                                <a href="<?php echo $list["target_link"];?>" target="_blank">
                                            <?php } ?>
                                                <span class="subject"><?php echo $list["subject"];?></span>
                                            <?php if(isset($list["target_link"]) && !empty($list["target_link"])) { ?>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="subheading"><?php echo $list["subheading"];?></span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="contents"><?php echo $list["contents"];?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    <?php $nLoop++; endforeach; ?>
                </ul>
                <?php endif; ?>

                <p class="design_more">
                    <a href="#" data-target="1" onclick="more_list(this, 'tv');return false;"><span>더보기</span></a>
                </p>
                <div class="m_more_list_loading">
                    <img src="/shop/img/loading.gif" alt="loading.." width="20"/>
                </div>
				<br/>
                <br/>
            </div>

            <div class="list" id="seminar_list">
                <img src="/images/funding_designer/tit03.jpg" alt="세미나&강의"/>

                <?php if(count($seminar_list) > 0) : ?>
                    <ul>
                        <?php
                        $nLoop = 1;
                        foreach($seminar_list as $list) :
                            if($nLoop % 4 == 0){
                                echo '</ul><br/><ul>';
                            }


                            $list["target_link"] = (!$list["target_link"]) ? "" : $list["target_link"];

                            ?>
                            <li>
                                <table>
                                    <thead>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $list["thumb_url"];?>" alt="<?php echo $list["subject"];?>" class="thumbnail"/>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php if(isset($list["target_link"]) && !empty($list["target_link"])) { ?>
                                                    </a>
                                                <?php } ?>
                                                <span class="subject"><?php echo $list["subject"];?></span>
                                                <?php if(isset($list["target_link"]) && !empty($list["target_link"])) { ?>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="subheading"><?php echo $list["subheading"];?></span></td>
                                        </tr>
                                        <tr>
                                            <td><span class="contents"><?php echo $list["contents"];?></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                            <?php $nLoop++; endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p class="design_more">
                    <a href="#" data-target="1" onclick="more_list(this, 'seminar');return false;"><span>더보기</span></a>
                </p>
                <div class="m_more_list_loading">
                    <img src="/shop/img/loading.gif" alt="loading.." width="20"/>
                </div>
                <br/>
                <br/>
            </div>
			<div class="list" id="column_list">
                <img src="/images/funding_designer/tit05.jpg" alt="컬럼&인터뷰"/>

                <?php if(count($column_list) > 0) : ?>
                    <ul>
                        <?php
                        $nLoop = 1;
                        foreach($column_list as $list) :
                            if($nLoop % 4 == 0){
                                echo '</ul><br/><ul>';
                            }

                            ?>
                            <li>
                                <table>
                                    <thead>
                                        <tr>
                                            <td>
                                                <img src="<?php echo $list["thumb_url"];?>" alt="<?php echo $list["subject"];?>" class="thumbnail"/>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php if(isset($list["target_link"]) && !empty($list["target_link"])) { ?>
                                                    <a href="<?php echo $list["target_link"];?>" target="_blank">
                                                <?php } ?>
                                                    <span class="subject"><?php echo $list["subject"];?></span>
                                                <?php if(isset($list["target_link"]) && !empty($list["target_link"])) { ?>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="subheading"><?php echo $list["subheading"];?></span></td>
                                        </tr>
                                        <tr>
                                            <td><span class="contents"><?php echo $list["contents"];?></span></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </li>
                            <?php $nLoop++; endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p class="design_more">
                    <a href="#" data-target="1" onclick="more_list(this, 'column');return false;"><span>더보기</span></a>
                </p>
                <div class="m_more_list_loading">
                    <img src="/shop/img/loading.gif" alt="loading.." width="20"/>
                </div>
				<br/>
                <br/>
            </div>
        </div>
    </div>

    <div id="oimg" class="mfp-hide white-popup"></div>
</div>
<div id="preview"></div>
<div id="mask"> </div>
<script type="text/javascript">
    $(document).ready(function() {

        var yOffset = 10;
        var xOffset = 30;
        $(document).on("click", "img.thumbnail", function(e)
        {
            var maskHeight = $(document).height();
            var maskWidth = $(window).width();

            $("#mask").css({'width': maskWidth, 'height': maskHeight});
            $("#mask").fadeTo('fast', 0.8);
            $("#preview").html('<img src="'+$(this).attr("src")+'" alt="'+$(this).attr("alt")+'" class="thumbnail"/><img src="/images/main/close_btn01.png" alt="close" class="close"/>');
            $("#preview")
                .css("top", Math.max(($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop() + "px")
                .css("left", Math.max(($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft() + "px")
                .fadeIn("fast");

            e.preventDefault();
        });

        $(document).on("click", "#mask", function(e){
           if($(this).css("display") == "block"){
               $(this).hide();
               $("#preview").hide();
           }else{
               $(this).show();
           }
        });

        $(document).on("click", "img.close", function(e){
            $("#mask").hide();
            $("#preview").hide();
        });
    });

    function more_list(obj, type)
    {
        var obj = (obj || "");
        var page = parseInt(obj.getAttribute('data-target')) + 1;
        $("#"+type+"_list").find('div.m_more_list_loading').show();
        $("#"+type+"_list").find('p.design_more').hide();
        $.ajax({
            url: g5_bbs_url + "/funding_story.php",
            type: "POST",
            data: {is_ajax: 1, page: page, type: type},
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                if (data.error) {
                    alert(alert.message);
                    return false;
                } else {

                    if(data.list.length <= 0){
                        $("#"+type+"_list").find('p.design_more').hide();
                        $("#"+type+"_list").find('div.m_more_list_loading').hide();
                        return false;
                    }

                    var html = "<br/><ul>";
                    var nLoop = 0;
                    var iframe = "";
                    var img = "";
                    for (var index in data.list)
                    {
                        if(nLoop % 3 == 0 && nLoop > 0){
                            html += '</ul><br/><ul>';
                        }

                        if(type == "tv"){
                            iframe = '<iframe src="'+data.list[index].iframe+'" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
                        }else{
                            img = '<img src="'+data.list[index].thumb_url+'" alt="'+data.list[index].subject+'" class="thumbnail"/>';
                        }

                        html += '<li>';

                        if(data.list[index].target_link){
                            html += '<a href="'+data.list[index].target_link+'" target="_blank">';
                        }

                        html += '<table>\
                                    <thead>\
                                        <tr>\
                                            <td>'+((iframe) ? iframe : img)+'</td>\
                                        </tr>\
                                    </thead>\
                                    <tbody>\
                                        <tr>\
                                            <td><span class="subject">'+data.list[index].subject+'</span></td>\
                                        </tr>\
                                        <tr>\
                                            <td><span class="subheading">'+data.list[index].subheading+'</span></td>\
                                        </tr>\
                                        <tr>\
                                            <td><span class="contents">'+data.list[index].contents+'</span></td>\
                                        </tr>\
                                    </tbody>\
                                </table>';
                        if(data.list[index].target_link){
                            html += '</a>';
                        }
                        html += '</li>';
                        nLoop++;
                    }
                    html += "</ul>";
                    setTimeout(function(){
                        $("#"+type+"_list").find('p.design_more').show();
                        $("#"+type+"_list").find('div.m_more_list_loading').hide();
                        $("#" + type + "_list ul").last().after(html);
                    }, 1200);
                    obj.setAttribute("data-target", data.page);
                    return false;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }
</script>
