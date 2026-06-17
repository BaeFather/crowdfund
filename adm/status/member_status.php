<?php
			$oneDay = 86400;
			$dTimeF = '00:00:00';
			$dTimeL = '23:59:59';
			// 오늘(D)
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time())." {$dTimeF}' and '".date("Y-m-d",time())." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[0] = $row['cnt'];
			$member_href_today = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time());

			// D-1
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time()-$oneDay)." {$dTimeF}' and '".date("Y-m-d",time()-$oneDay)." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[1] = $row['cnt'];
			$member_href_l1 = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time()-$oneDay);

			// D-2
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time()-$oneDay*2)." {$dTimeF}' and '".date("Y-m-d",time()-$oneDay*2)." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[2] = $row['cnt'];
			$member_href_l2 = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time()-$oneDay*2);

			// D-3
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time()-$oneDay*3)." {$dTimeF}' and '".date("Y-m-d",time()-$oneDay*3)." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[3] = $row['cnt'];
			$member_href_l3 = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time()-$oneDay*3);

			// D-4
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time()-$oneDay*4)." {$dTimeF}' and '".date("Y-m-d",time()-$oneDay*4)." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[4] = $row['cnt'];
			$member_href_l4 = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time()-$oneDay*4);

			// D-5
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time()-$oneDay*5)." {$dTimeF}' and '".date("Y-m-d",time()-$oneDay*5)." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[5] = $row['cnt'];
			$member_href_l5 = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time()-$oneDay*5);

			// D-6
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-d",time()-$oneDay*6)." {$dTimeF}' and '".date("Y-m-d",time()-$oneDay*6)." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[6] = $row['cnt'];
			$member_href_l6 = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m-d",time()-$oneDay*6);

			// 금월
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-m-01",time())." {$dTimeF}' and '".date("Y-m-d",time())." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[month] = $row['cnt'];
			$member_href_thismonth = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-m",time());

			// 금년
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."
						where mb_datetime between '".date("Y-01-01",time())." {$dTimeF}' and '".date("Y-m-d",time())." {$dTimeL}' ";
			$row = sql_fetch($sql);
			$member_cnt[year] = $row['cnt'];
			$member_href_thisyear = G5_ADMIN_URL."/member_list.php?sfl=mb_datetime&stx=".date("Y-",time());

			// 전체
			$sql = " select count(mb_no) as cnt from ".$g5['member_table']."  ";
			$row = sql_fetch($sql);
			$member_cnt[total] = $row['cnt'];
			$member_href_total = G5_ADMIN_URL."/member_list.php";

			$member_cnt['max'] = max($member_cnt[0], $member_cnt[1], $member_cnt[2], $member_cnt[3], $member_cnt[4], $member_cnt[5], $member_cnt[6]);
			$member_cnt['sum'] = $member_cnt[0] + $member_cnt[1] + $member_cnt[2] + $member_cnt[3] + $member_cnt[4] + $member_cnt[5] + $member_cnt[5];
			for ($i=0; $i<=6; $i++) {
				$member_cnt_percent[$i] = 0;
				$member_cnt_height[$i] = 0;
				if ($member_cnt['sum']) {
					$member_cnt_percent[$i] = round(($member_cnt[$i] / $member_cnt['sum'] * 100), 1);
				}
				if ($member_cnt_percent[$i])
					$member_cnt_height[$i] = round((($member_cnt[$i] / $member_cnt['max'] * 100) * 0.85), 0);
			}

		?>
		  <TABLE cellSpacing=0 cellPadding=0 border=0>
			<TR height="20" valign="middle" style="PADDING-LEFT: 0px;">
			    <TD colspan=7>최근회원가입현황</TD>
			</TR>
			<TR>
				<TD bgColor=#2c8ee5 colSpan=7 height=2></TD></TR>
			<TR>
			<TR align=middle>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35" height=25><?php echo $member_cnt[6];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $member_cnt[5];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $member_cnt[4];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $member_cnt[3];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $member_cnt[2];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $member_cnt[1];?></TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666" width="35"><?php echo $member_cnt[0];?></TD></TR>
			<TR vAlign=bottom align=middle>
				<TD height=110><IMG height="<?php echo $member_cnt_height[6];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height="<?php echo $member_cnt_height[5];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height="<?php echo $member_cnt_height[4];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height="<?php echo $member_cnt_height[3];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height="<?php echo $member_cnt_height[2];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
				<TD><IMG height="<?php echo $member_cnt_height[1];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status.gif" width=10></TD>
			<TD><IMG height="<?php echo $member_cnt_height[0];?>" src="<?php echo $DracoCounter_URL; ?>/img/graph_status_t.gif" width=10></TD></TR>
			<TR bgColor=#cccccc>
				<TD colSpan=7 height=1></TD></TR>
			<TR>
				<TD colSpan=7 height=5></TD></TR>
			<TR align=middle>
				<TD height=40><A HREF="<?php echo $member_href_l6;?>"><SPAN <?php echo $mouseEventwebcText;?> >D-6<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*6));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $member_href_l5;?>"><SPAN <?php echo $mouseEventwebcText;?> >D-5<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*5));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $member_href_l4;?>"><SPAN  >D-1<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*4)); ?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $member_href_l3;?>"><SPAN <?php echo $mouseEventwebcText;?> >D-2<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*3));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $member_href_l2;?>"><SPAN <?php echo $mouseEventwebcText;?> >D-3<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*2));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $member_href_l1;?>"><SPAN <?php echo $mouseEventwebcText;?> >D-4<br>(<?php echo get_yoil(date("Y-m-d",time()-$oneDay*1));?>)</SPAN></A></TD>
				<TD><A HREF="<?php echo $member_href_today; ?>"><SPAN style="LETTER-SPACING: -0.1EM;" <?php echo $mouseEventwebcText;?> >오늘<br>(<?php echo get_yoil(date("Y-m-d",time()));?>)</SPAN></A></TD></TR>
			<TR align=middle>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;" height=25><?php echo $member_cnt_percent[6];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $member_cnt_percent[5];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $member_cnt_percent[4];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $member_cnt_percent[3];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $member_cnt_percent[2];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $member_cnt_percent[1];?>%</TD>
				<TD style="FONT-SIZE: 8pt; COLOR: #666;"><?php echo $member_cnt_percent[0];?>%</TD></TR>
		  </TABLE>

