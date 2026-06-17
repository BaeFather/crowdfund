<?php

	include_once('./_common.php');
	include_once('../lib/function_prc.php');
	include_once('./review.class.php');

	$section = $_POST["section"];
	$page	=	$_POST["page"];
	$pkd	=	$_POST["pkd"];	// 리스트 백버튼시 더보기 구현

	IF(!$page) { $page = 1; }

	$img_url = G5_DATA_URL . '/review';
	$img_dir = G5_DATA_PATH . '/review';
	$img_path = $img_dir.'/';


	$strColumn = ARRAY("id","thumbnail","mem_id","mem_name","subject","contents","target_link","target_att","section","snskind");

	$strVal = new strReviewClass();

	IF($section == "1")
	{
		$num_per_page = 6;
		IF($pkd == "1")
		{
			$num_per_page = $num_per_page * $page;
			$page = 1;
		}
	} ELSE {
		$num_per_page = 12;
	}

	$strList = $strVal->fn_list($section, $num_per_page);

	$intTotalPage = $strList["tpage"];
	$intTotal	  = $strList["tcnt"];

	IF($page > $intTotalPage) { ECHO "end"; exit; }

	FOR($i=0;$i<COUNT($strList["val"]);$i++)
	{
		FOR($j=0;$j<COUNT($strColumn);$j++)
		{
			${$strColumn[$j]} = $strList["val"][$i][$j];
		}

		IF($section == "1")		// 인터뷰
		{

?>
		<li class="inlist<? IF($i % 3 <> 2) { ECHO " mg-r35"; } ?>">
			<?
				IF($id == 180)
				{
					IF($target_link) {
						ECHO "<a href='".$target_link."'";
						IF($target_att) { ECHO " target='".$target_att."'";  }
						ECHO ">";
					}
				}
				ELSE {
					ECHO "<a href='#none' OnClick=\"check_view('".$id."','".$pkd."',event);\">";
				}
			?>
			<p class="inimg"><img src="<?=$img_url.'/'.$thumbnail;?>" style="width:100%;height:100%;"></p>
			<p class="inname">투자자 <? ECHO $mem_name;?> 회원님</p>
			<p class="intitle"><? ECHO strcut_utf8_2(strip_tags(strip_str($subject)),28,"","..");?></p>
			<p class="intext"><? ECHO strcut_utf8_2(strip_tags(strip_str($contents)),65,"","..");?></p>
			</a>
		</li>
<?
			}
			ELSEIF($section == "2") {  // SNS리뷰


				if (mb_substr($mem_name,0,3)=="(주)") $dis_char=4;
				else $dis_char=1;
				$mem_name2 = mb_substr($mem_name,0,$dis_char).str_repeat(" *",mb_strlen(mb_substr($mem_name,$dis_char)));
?>
			<li class="snslist<? IF($i % 4 <> 3) { ECHO " mg-r30"; } ?>">
				<?
					IF($target_link) {
						ECHO "<a href='".$target_link."'";
						IF($target_att) { ECHO " target='".$target_att."'";  }
						ECHO ">";
					}
				?>
				<ul class="snsicon">
					<li class="icon"><img src="<? ECHO $strVal->fn_rep_img($snskind);?>"></li>
					<li class="snsname"><? ECHO $mem_name2;?> 회원님</li>
					<li class="snstitle"><? ECHO strcut_utf8_2(strip_tags(strip_str($subject)),100,"","..");?></li>
					<li class="arrow">></li>
				</ul>
				<!--p class="snstitle"><? ECHO strcut_utf8_2(strip_tags(strip_str($subject)),24,"","..");?><span class="m_arrow">></span></p-->
				</a>
			</li>
<?
			} ELSEIF($section == "3") { // 추천평
?>
			<li class="relist<? IF($i % 4 <> 3) { ECHO " mg-r30"; } ?> btnServiceOpen">
				<input type="hidden" name="SE[]" value="<? ECHO $id;?>" />
				<p class="reimg"><img src="/img/review/<? ECHO $thumbnail;?>" style="width:100%;height:100%;"></p>
				<p class="rename"><? ECHO utf8_strcut($mem_name,3,"");?>** 회원님</p>
				<p class="retitle"><? ECHO strcut_utf8_2(strip_tags(strip_str($contents)),42,"","..");?>&nbsp;</p>
			</li>
<?
			}
		}

		sql_close($connect_for);

?>