				<div class="left_title_guide"><?=$gstrAdminMenuTitle?></div>
				<div id="id_clear"></div>
				<ul>
<?php
				// 왼쪽메뉴
					FOR($i=0;$i<COUNT($gstrAdminLeftMemu);$i++)
					{
						IF($gstrAdminLevel >= $gstrAdminLeftMemu[$i][5])
						{
							ECHO "<li><a href='".$PHP_SELF."?KD=".$gstrAdminLeftMemu[$i][0]."'><img src='/images/adm/adm_left_txt_icon.jpg'>".$gstrAdminLeftMemu[$i][1]."</a></li>";
						}
					}
?>
				</ul>